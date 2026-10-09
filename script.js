document.getElementById('coachingLeadForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const submitBtn = document.getElementById('submitBtn');
  const status = document.getElementById('formStatus');
  
  submitBtn.disabled = true;
  submitBtn.innerText = "Submitting...";
  status.innerText = "";

  const formData = {
    name: document.getElementById('name').value,
    phone: document.getElementById('phone').value,
    exam: document.getElementById('exam').value,
    year: document.getElementById('year').value,
    location: document.getElementById('location').value,
    date: new Date().toLocaleString()
  };

  try {
    // Aap direct Web3Forms, SheetDB ya apne Webhook endpoint par bhej sakte hain
    // Standard fetch example:
    console.log("Lead Data Captured:", formData);

    status.classList.add('text-green-600');
    status.innerText = "✅ Thank you! Our team will call you shortly.";
    document.getElementById('coachingLeadForm').reset();
  } catch (error) {
    status.classList.add('text-red-600');
    status.innerText = "❌ Something went wrong. Please try again.";
  } finally {
    submitBtn.disabled = false;
    submitBtn.innerText = "Request Callback Now 🚀";
  }
});
