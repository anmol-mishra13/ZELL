(function() {
  "use strict";

  // Toggle between student and professional form sections
  window.toggleForm = function() {
    const studentForm = document.getElementById('studentForm');
    const professionalForm = document.getElementById('professionalForm');
    const userType = document.querySelector('input[name="userType"]:checked').value;

    if (userType === 'student') {
      studentForm.classList.add('active');
      professionalForm.classList.remove('active');
    } else {
      professionalForm.classList.add('active');
      studentForm.classList.remove('active');
    }
  }

  // Submit function
  window.submitForm = function(event) {
    event.preventDefault(); // Prevent default form submission
    const userType = document.querySelector('input[name="userType"]:checked').value;
    let formData;

    if (userType === 'student') {
      formData = new FormData(document.getElementById('quizFormStudent'));
    } else {
      formData = new FormData(document.getElementById('quizFormProfessional'));
    }

    // Convert FormData to JSON
    const data = {};
    formData.forEach((value, key) => {
      data[key] = value;
    });
    data.user_type = userType; // Add user type to data

    // Show loading indicator (optional)
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

    // Submit the data using fetch to PHP endpoint
    fetch('submit_form.php', {
      method: 'POST',
      body: JSON.stringify(data),
      headers: {
        'Content-Type': 'application/json'
      }
    })
    .then(response => response.json())
    .then(result => {
      loadingIndicator.remove(); // Remove loading indicator
      if (result.success) {
        alert('Form submitted successfully!');
      } else {
        alert('Error submitting form: ' + result.error);
      }
    })
    .catch(error => {
      loadingIndicator.remove();
      alert('Error submitting form: ' + error.message);
    });
  }

  // Other UI and utility functions (e.g., animations, scroll events)
  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    if (!selectHeader.classList.contains('scroll-up-sticky') && !selectHeader.classList.contains('sticky-top') && !selectHeader.classList.contains('fixed-top')) return;
    window.scrollY > 100 ? selectBody.classList.add('scrolled') : selectBody.classList.remove('scrolled');
  }

  document.addEventListener('scroll', toggleScrolled);
  window.addEventListener('load', toggleScrolled);

  const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');

  function mobileNavToggle() {
    document.querySelector('body').classList.toggle('mobile-nav-active');
    mobileNavToggleBtn.classList.toggle('bi-list');
    mobileNavToggleBtn.classList.toggle('bi-x');
  }
  mobileNavToggleBtn.addEventListener('click', mobileNavToggle);

  document.querySelectorAll('#navmenu a').forEach(navmenu => {
    navmenu.addEventListener('click', () => {
      if (document.querySelector('.mobile-nav-active')) {
        mobileNavToggle();
      }
    });
  });

  const preloader = document.querySelector('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove();
    });
  }

  let scrollTop = document.querySelector('.scroll-top');

  function toggleScrollTop() {
    if (scrollTop) {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    }
  }
  scrollTop.addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });

  window.addEventListener('load', toggleScrollTop);
  document.addEventListener('scroll', toggleScrollTop);

  function aosInit() {
    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  }
  window.addEventListener('load', aosInit);

  const glightbox = GLightbox({
    selector: '.glightbox'
  });

  function initSwiper() {
    document.querySelectorAll(".init-swiper").forEach(function(swiperElement) {
      let config = JSON.parse(
        swiperElement.querySelector(".swiper-config").innerHTML.trim()
      );

      if (swiperElement.classList.contains("swiper-tab")) {
        initSwiperWithCustomPagination(swiperElement, config);
      } else {
        new Swiper(swiperElement, config);
      }
    });
  }

  window.addEventListener("load", initSwiper);

  document.querySelectorAll('.faq-item h3, .faq-item .faq-toggle').forEach((faqItem) => {
    faqItem.addEventListener('click', () => {
      faqItem.parentNode.classList.toggle('faq-active');
    });
  });

})();
