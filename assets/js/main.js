(function() {
  "use strict";

  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    if (!selectHeader.classList.contains('scroll-up-sticky') && !selectHeader.classList.contains('sticky-top') && !selectHeader.classList.contains('fixed-top')) return;
    window.scrollY > 100 ? selectBody.classList.add('scrolled') : selectBody.classList.remove('scrolled');
  }

  document.addEventListener('scroll', toggleScrolled);
  window.addEventListener('load', toggleScrolled);

  // Mobile nav toggle
  const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');

  function mobileNavToggle() {
    document.querySelector('body').classList.toggle('mobile-nav-active');
    mobileNavToggleBtn.classList.toggle('bi-list');
    mobileNavToggleBtn.classList.toggle('bi-x');
  }
  mobileNavToggleBtn.addEventListener('click', mobileNavToggle);

  // Hide mobile nav on same-page/hash links
  document.querySelectorAll('#navmenu a').forEach(navmenu => {
    navmenu.addEventListener('click', () => {
      if (document.querySelector('.mobile-nav-active')) {
        mobileNavToggle();
      }
    });
  });

  // Preloader
  const preloader = document.querySelector('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove();
    });
  }

  // Scroll top button
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

  // Animation on scroll function and init
  function aosInit() {
    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  }
  window.addEventListener('load', aosInit);

  // Initiate glightbox
  const glightbox = GLightbox({
    selector: '.glightbox'
  });

  // Init swiper sliders
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

  // Frequently Asked Questions Toggle
  document.querySelectorAll('.faq-item h3, .faq-item .faq-toggle').forEach((faqItem) => {
    faqItem.addEventListener('click', () => {
      faqItem.parentNode.classList.toggle('faq-active');
    });
  });

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

    // Google Apps Script Web App URL
    const scriptURL = 'https://script.google.com/macros/s/AKfycby7Ye42crt4I3iuXhg16Lz16pjPDcXAxwHuwXKQYkxz-QzyGGqVAmGy3cIFxupJxl5_/exec';

    // Loading Indicator (optional)
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

    fetch(scriptURL, {
      method: 'POST',
      body: JSON.stringify(data),
      headers: {
        'Content-Type': 'application/json'
      }
    })
    .then(response => response.json())
    .then(result => {
      loadingIndicator.remove();
      alert('Form submitted successfully!');
    })
    .catch(error => {
      loadingIndicator.remove();
      alert('Error submitting form: ' + error);
    });
  }

  // Switching form function
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

})();
