(function() {
  "use strict";

  // Toggle between student and professional form sections
  window.toggleForm = function() {
    const studentForm = document.getElementById('studentForm');
    const professionalForm = document.getElementById('professionalForm');
    const userType = document.querySelector('input[name="userType"]:checked').value;

    // Toggle form visibility
    if (userType === 'student') {
      studentForm.classList.add('active');
      professionalForm.classList.remove('active');
      
      // Handle required fields for student form
      toggleRequiredFields('student');
    } else {
      professionalForm.classList.add('active');
      studentForm.classList.remove('active');
      
      // Handle required fields for professional form
      toggleRequiredFields('professional');
    }
  }

  // Function to toggle required fields based on form type
  function toggleRequiredFields(formType) {
    // Student form fields
    const studentFields = {
      'studentName': true,
      'studentEmail': true,
      'studentQualification': true,
      'studentUniversity': true,
      'guardianNumber': true
    };

    // Professional form fields
    const professionalFields = {
      'professionalName': true,
      'professionalEmail': true,
      'professionalDesignation': true,
      'currentCompany': true,
      'currentCTC': true
    };

    // Set required attribute based on form type
    if (formType === 'student') {
      // Enable student form fields
      Object.keys(studentFields).forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
          field.required = studentFields[fieldId];
          field.disabled = false;
        }
      });

      // Disable professional form fields
      Object.keys(professionalFields).forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
          field.required = false;
          field.disabled = true;
          field.value = ''; // Clear values
        }
      });
    } else {
      // Enable professional form fields
      Object.keys(professionalFields).forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
          field.required = professionalFields[fieldId];
          field.disabled = false;
        }
      });

      // Disable student form fields
      Object.keys(studentFields).forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
          field.required = false;
          field.disabled = true;
          field.value = ''; // Clear values
        }
      });
    }
  }

  // Submit function
  window.submitForm = function(event) {
    event.preventDefault();
    const userType = document.querySelector('input[name="userType"]:checked').value;
    
    // Get active form
    const activeForm = userType === 'student' ? 
      document.getElementById('studentForm') : 
      document.getElementById('professionalForm');

    // Get all enabled fields from active form
    const formData = new FormData();
    const inputs = activeForm.querySelectorAll('input:not([disabled])');
    
    inputs.forEach(input => {
      formData.append(input.name, input.value);
    });
    formData.append('user_type', userType);

    // Show loading indicator
    const loadingIndicator = document.createElement('div');
    loadingIndicator.textContent = 'Submitting...';
    loadingIndicator.style.position = 'fixed';
    loadingIndicator.style.top = '50%';
    loadingIndicator.style.left = '50%';
    loadingIndicator.style.transform = 'translate(-50%, -50%)';
    loadingIndicator.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
    loadingIndicator.style.color = 'white';
    loadingIndicator.style.padding = '10px';
    loadingIndicator.style.borderRadius = '5px';
    document.body.appendChild(loadingIndicator);

    // Submit form data
    fetch('submit_form.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(result => {
      loadingIndicator.remove();
      if (result.success) {
        alert('Form submitted successfully!');
        // Reset form and close modal
        document.getElementById('userForm').reset();
        const modal = bootstrap.Modal.getInstance(document.getElementById('quizModal'));
        modal.hide();
      } else {
        alert('Error submitting form: ' + result.error);
      }
    })
    .catch(error => {
      loadingIndicator.remove();
      alert('Error submitting form: ' + error.message);
    });
  }

  // Initialize form on page load
  document.addEventListener('DOMContentLoaded', function() {
    toggleForm(); // Set initial state
  });

})();