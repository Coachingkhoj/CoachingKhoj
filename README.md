# CoachingKhoj v5 — hardened MVP

JEE/NEET coaching discovery + lead-generation platform prototype.

## Run locally
1. Copy `.env.example` to `.env` and set a strong `COACHINGKHOJ_ADMIN_PASSWORD`.
2. `python server.py`
3. Open `http://localhost:8080`

## Docker
1. Copy `.env.example` to `.env` and set a strong password.
2. `docker compose up --build`
3. Open `http://localhost:8080`

## v5 improvements
- Expiring admin sessions (8 hours)
- Basic login/API rate limiting
- Security response headers
- Lead consent requirement
- Lead status workflow: New / Contacted / Converted / Closed
- Persistent data directory support for containers
- Docker deployment package
- Health endpoint: `/api/health`

## Before public launch
Use HTTPS, a managed PostgreSQL database, proper user authentication/roles, CSRF protection, CAPTCHA/anti-spam, backups, audit logs, privacy policy/consent records, verified coaching data, and production monitoring. Do not use sample institute information as verified claims without checking it.
