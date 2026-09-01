<!DOCTYPE html>
<html lang="hi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CoachingKhoj - Top NEET & JEE Coaching in Delhi & Noida</title>
  <style>
    * { box-sizing: border-box; font-family: "Segoe UI", sans-serif; margin: 0; padding: 0; }
    body { background-color: #f1f5f9; color: #0f172a; }
    .header { background: #1e293b; color: white; padding: 20px; text-align: center; }
    .hero-container { max-width: 950px; margin: 30px auto; padding: 0 15px; display: flex; flex-wrap: wrap; gap: 30px; justify-content: center; }
    .hero-text { flex: 1; min-width: 300px; }
    .form-card { flex: 1; min-width: 320px; max-width: 420px; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 5px; }
    .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; }
    .btn-submit { width: 100%; padding: 12px; background: #2563eb; color: white; font-size: 16px; font-weight: 700; border: none; border-radius: 6px; cursor: pointer; }
    #statusMsg { text-align: center; margin-top: 12px; font-weight: 600; }
  </style>
</head>
<body>
  <div class="header">
    <h1>CoachingKhoj</h1>
    <p>Delhi & Noida's Trusted NEET & JEE Discovery Platform</p>
  </div>
  <div class="hero-container">
    <div class="hero-text">
      <h2>Get Up to 40% Scholarship in Top Coaching Institutes</h2>
      <p>Compare top-rated NEET & JEE coaching centers in Delhi & Noida. Get free expert counseling and discount offers directly on your phone.</p>
    </div>
    <div class="form-card">
      <h3 style="text-align:center; margin-bottom:15px;">Find Best Coaching Near You</h3>
      <form id="enquiryForm">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="student_name" placeholder="Enter Full Name" required>
        </div>
        <div class="form-group">
          <label>Phone Number</label>
          <input type="tel" name="phone_number" pattern="[0-9]{10}" placeholder="10-Digit Mobile Number" required>
        </div>
        <div class="form-group">
          <label>Target Exam</label>
          <select name="target_exam" required>
            <option value="">-- Select Exam --</option>
            <option value="NEET">NEET (Medical)</option>
            <option value="JEE Main">JEE Main</option>
            <option value="JEE Advanced">JEE Advanced</option>
          </select>
        </div>
        <div class="form-group">
          <label>Preferred Coaching Location</label>
          <select name="preferred_city" required>
            <option value="">-- Select Location --</option>
            <option value="Delhi">Delhi / NCR</option>
            <option value="Noida">Noida</option>
          </select>
        </div>
        <div class="form-group">
          <label>Current Class</label>
          <select name="current_class" required>
            <option value="">-- Select Class --</option>
            <option value="Class 11">Class 11th</option>
            <option value="Class 12">Class 12th</option>
            <option value="12th Pass / Dropper">12th Pass / Dropper</option>
          </select>
        </div>
        <button type="submit" class="btn-submit">Get Free Counseling</button>
      </form>
      <div id="statusMsg"></div>
    </div>
  </div>
  <script>
    document.getElementById("enquiryForm").addEventListener("submit", function(e) {
      e.preventDefault();
      const status = document.getElementById("statusMsg");
      status.style.color = "#0284c7";
      status.innerText = "Submitting details...";
      fetch("save_lead.php", { method: "POST", body: new FormData(this) })
      .then(res => res.json())
      .then(data => {
        if(data.status === "success") {
          status.style.color = "#16a34a";
          status.innerText = "Success! Top coaching institutes will contact you shortly.";
          this.reset();
        } else {
          status.style.color = "#dc2626";
          status.innerText = data.message || "Submission failed.";
        }
      }).catch(() => {
        status.style.color = "#dc2626";
        status.innerText = "Server error. Please try again.";
      });
    });
  </script>
</body>
</html>