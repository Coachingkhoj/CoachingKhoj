#!/usr/bin/env python3
import json, os, secrets, sqlite3, time, csv, io
from http.server import ThreadingHTTPServer, SimpleHTTPRequestHandler
from urllib.parse import urlparse
from datetime import datetime, timezone

BASE=os.path.dirname(os.path.abspath(__file__))
DATA_DIR=os.environ.get('COACHINGKHOJ_DATA_DIR',BASE); os.makedirs(DATA_DIR,exist_ok=True); DB=os.path.join(DATA_DIR,'coachingkhoj.db')
ADMIN_PASSWORD=os.environ.get('COACHINGKHOJ_ADMIN_PASSWORD','change-me-now')
SESSIONS={}
RATE={}
SESSION_TTL=8*60*60
RATE_WINDOW=60
RATE_LIMIT=8

def db():
    c=sqlite3.connect(DB
    c.row_factory=sqlite3.Row
    c.execute('''CREATE TABLE IF NOT EXISTS leads(
      id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT NOT NULL,phone TEXT NOT NULL,
      exam TEXT NOT NULL,city TEXT NOT NULL,institute TEXT NOT NULL,type TEXT NOT NULL,
      status TEXT NOT NULL DEFAULT 'New',created_at TEXT NOT NULL)''')
    try: c.execute("ALTER TABLE leads ADD COLUMN status TEXT NOT NULL DEFAULT 'New'")
    except sqlite3.OperationalError: pass
    c.commit(); return c

def valid_phone(p): return isinstance(p,str) and len(p)==10 and p.isdigit() and p[0] in '6789'

class Handler(SimpleHTTPRequestHandler):
    def end_headers(self):
        self.send_header('Cache-Control','no-store')
        self.send_header('X-Content-Type-Options','nosniff')
        self.send_header('X-Frame-Options','DENY')
        self.send_header('Referrer-Policy','same-origin')
        super().end_headers()
    def json(self,code,obj):
        raw=json.dumps(obj).encode(); self.send_response(code); self.send_header('Content-Type','application/json'); self.send_header('Content-Length',str(len(raw))); self.end_headers(); self.wfile.write(raw)
    def body(self):
        n=int(self.headers.get('Content-Length','0')); return json.loads(self.rfile.read(n) or b'{}')
    def authorized(self):
        t=self.headers.get('X-Admin-Session',''); exp=SESSIONS.get(t,0)
        if exp and exp>time.time(): return True
        if t in SESSIONS: SESSIONS.pop(t,None)
        return False
    def rate_ok(self):
        ip=self.client_address[0]; now=time.time(); arr=[x for x in RATE.get(ip,[]) if now-x<RATE_WINDOW]
        if len(arr)>=RATE_LIMIT: RATE[ip]=arr; return False
        arr.append(now); RATE[ip]=arr; return True
    def do_POST(self):
        path=urlparse(self.path).path
        try:
            if path=='/api/admin/login':
                if not self.rate_ok(): return self.json(429,{'error':'too many attempts'})
                x=self.body();
                if secrets.compare_digest(str(x.get('password','')),ADMIN_PASSWORD):
                    t=secrets.token_urlsafe(32); SESSIONS[t]=time.time()+SESSION_TTL; self.send_response(200); self.send_header('Content-Type','application/json'); self.send_header('X-Admin-Session',t); self.end_headers(); self.json(200,{'ok':True,'session':t}); return
                return self.json(401,{'error':'invalid credentials'})
            if path=='/api/leads':
                if not self.rate_ok(): return self.json(429,{'error':'too many requests'})
                x=self.body(); required=['name','phone','exam','city','institute','type']
                if any(not str(x.get(k,'')).strip() for k in required): return self.json(400,{'error':'missing fields'})
                if not valid_phone(str(x['phone'])): return self.json(400,{'error':'invalid phone'})
                if x.get('consent') is not True: return self.json(400,{'error':'consent required'})
                now=datetime.now(timezone.utc).isoformat()
                c=db(); c.execute('INSERT INTO leads(name,phone,exam,city,institute,type,status,created_at) VALUES(?,?,?,?,?,?,?,?)',(str(x['name'])[:100],str(x['phone']),str(x['exam'])[:30],str(x['city'])[:100],str(x['institute'])[:120],str(x['type'])[:20],'New',now)); c.commit(); lid=c.execute('SELECT last_insert_rowid()').fetchone()[0]; c.close()
                return self.json(201,{'ok':True,'id':lid})
            return self.json(404,{'error':'not found'})
        except Exception as e: return self.json(500,{'error':'server error'})
    def do_GET(self):
        path=urlparse(self.path).path
        if path=='/api/leads':
            if not self.authorized(): return self.json(401,{'error':'unauthorized'})
            c=db(); rows=[dict(r) for r in c.execute('SELECT * FROM leads ORDER BY id DESC')]; c.close(); return self.json(200,rows)
        if path=='/api/health': return self.json(200,{'ok':True})
        return super().do_GET()
    def do_PATCH(self):
        path=urlparse(self.path).path
        if path.startswith('/api/leads/'):
            if not self.authorized(): return self.json(401,{'error':'unauthorized'})
            try: lid=int(path.rsplit('/',1)[1])
            except: return self.json(400,{'error':'bad id'})
            x=self.body(); status=str(x.get('status',''))
            if status not in ('New','Contacted','Converted','Closed'): return self.json(400,{'error':'invalid status'})
            c=db(); c.execute('UPDATE leads SET status=? WHERE id=?',(status,lid)); c.commit(); c.close(); return self.json(200,{'ok':True})
        return self.json(404,{'error':'not found'})

    def do_DELETE(self):
        path=urlparse(self.path).path
        if path.startswith('/api/leads/'):
            if not self.authorized(): return self.json(401,{'error':'unauthorized'})
            try: lid=int(path.rsplit('/',1)[1])
            except: return self.json(400,{'error':'bad id'})
            c=db(); c.execute('DELETE FROM leads WHERE id=?',(lid,)); c.commit(); c.close(); return self.json(200,{'ok':True})
        return self.json(404,{'error':'not found'})

if __name__=='__main__':
    db(); os.chdir(BASE); print('CoachingKhoj running at http://localhost:8080'); print('Set COACHINGKHOJ_ADMIN_PASSWORD before production.')
    host=os.environ.get('HOST','0.0.0.0'); port=int(os.environ.get('PORT','8080')); ThreadingHTTPServer((host,port),Handler).serve_forever()
