// validation.js
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector('form');
    if (!form) return;
  
    form.addEventListener('submit', function (e) {
      const password = document.querySelector("input[name='password']").value;
  
      const passwordRegex = /^(?=.*[A-Z])(?=.*[\d\W]).{8,}$/;
  
      if (!passwordRegex.test(password)) {
        alert("Password must be at least 8 characters, include one uppercase letter, and one number or special character.");
        e.preventDefault();
      }
    });
  });
  