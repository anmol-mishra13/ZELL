<?php
session_start();
require_once 'assets/vendor/setup.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>ZELL EDUCATION</title>
    <meta name="description" content="ZELL EDUCATION - Your path to global opportunities">
    <meta name="keywords" content="education, ACCA, professional development">

    <!-- Favicon -->
    <link href="assets/img/favicon.ico" rel="icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Jost:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <?php
    foreach ($vendor_files as $vendor => $types) {
        if (isset($types['css'])) {
            $css_url = get_asset_url($vendor, 'css');
            if ($css_url) {
                echo "<link href=\"{$css_url}\" rel=\"stylesheet\">\n";
            }
        }
    }
    ?>

    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        .form-section {
            display: none;
        }
        .form-section.active {
            display: block;
        }
        .user-type-label {
            margin-right: 30px;
            font-family: Arial, sans-serif;
            font-size: 16px;
            font-weight: 600;
        }
        .form-label {
            font-weight: 500;
            color: #333;
        }
        input:disabled {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
        .modal-title {
            color: #800080;
            font-weight: bold;
        }
        .alert {
            margin-bottom: 20px;
        }
        .btn-getstarted {
            background-color: #800080;
            color: white;
            padding: 8px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-getstarted:hover {
            background-color: #660066;
            color: white;
        }
    </style>
</head>

<body class="index-page">
    <header id="header" class="header d-flex align-items-center fixed-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center">
            <a href="index.php" class="logo d-flex align-items-center me-auto">
                <h1 class="sitename">ZELL EDUCATION</h1>
            </a>
            <a class="btn-getstarted" data-bs-toggle="modal" data-bs-target="#quizModal">Start Assessment</a>
        </div>
    </header>

    <main class="main">
        <!-- Hero Section -->
        <section id="hero" class="hero section dark-background">
            <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center">
                        <h1>Kickstart your Career in Accounting & Finance with Zell Education</h1>
                        <p>your path to global opportunities<br> Get access to <span class="change_content"></span></p>
                        <div class="d-flex">
                            <a class="btn-getstarted" data-bs-toggle="modal" data-bs-target="#quizModal">Start Assessment</a>
                            <a href="https://youtu.be/jhhu451wzag" target="_blank" class="glightbox btn-watch-video d-flex align-items-center">
                                <i class="bi bi-play-circle"></i><span>WHY ZELL?</span>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6 order-1 order-lg-2 hero-img">
                        <img src="assets/img/scholar.jpg" class="img-fluid animated" alt="">
                    </div>
                </div>
            </div>
        </section>

        <!-- Form Modal -->
        <div class="modal fade" id="quizModal" tabindex="-1" aria-labelledby="quizModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="quizModalLabel">Complete your Profile</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php if (isset($_SESSION['message'])): ?>
                            <div class="alert alert-<?php echo $_SESSION['message_type']; ?>" role="alert">
                                <?php
                                echo $_SESSION['message'];
                                unset($_SESSION['message']);
                                unset($_SESSION['message_type']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <form action="submit_form.php" method="POST" id="userForm" onsubmit="submitForm(event)">
                            <!-- User Type Selection -->
                            <div class="mb-4">
                                <label class="user-type-label">
                                    <input type="radio" name="userType" value="student" checked onchange="toggleForm()"> Student
                                </label>
                                <label class="user-type-label">
                                    <input type="radio" name="userType" value="professional" onchange="toggleForm()"> Working Professional
                                </label>
                            </div>

                            <!-- Student Form Section -->
                            <div id="studentForm" class="form-section active">
                                <div class="mb-3">
                                    <label for="studentName" class="form-label" style="color:white">Name</label>
                                    <input type="text" class="form-control" name="name" id="studentName" required
                                           oninvalid="this.setCustomValidity('Please enter your name')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                                <div class="mb-3">
                                    <label for="studentEmail" class="form-label" style="color:white">Registered Email ID</label>
                                    <input type="email" class="form-control" name="email" id="studentEmail" required
                                           oninvalid="this.setCustomValidity('Please enter a valid email address')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                                <div class="mb-3">
                                    <label for="studentQualification" class="form-label" style="color:white">Qualification</label>
                                    <input type="text" class="form-control" name="qualification" id="studentQualification" required
                                           oninvalid="this.setCustomValidity('Please enter your qualification')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                                <div class="mb-3">
                                    <label for="studentUniversity" class="form-label" style="color:white">University/School Name</label>
                                    <input type="text" class="form-control" name="university" id="studentUniversity" required
                                           oninvalid="this.setCustomValidity('Please enter your university/school name')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                                <div class="mb-3">
                                    <label for="guardianNumber" class="form-label" style="color:white">Guardian's Number</label>
                                    <input type="tel" class="form-control" name="guardian_number" id="guardianNumber" required
                                           pattern="[0-9]{10}"
                                           oninvalid="this.setCustomValidity('Please enter a valid 10-digit phone number')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>

                            <!-- Professional Form Section -->
                            <div id="professionalForm" class="form-section">
                                <div class="mb-3">
                                    <label for="professionalName" class="form-label" style="color:white">Name</label>
                                    <input type="text" class="form-control" name="professional_name" id="professionalName"
                                           oninvalid="this.setCustomValidity('Please enter your name')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                                <div class="mb-3">
                                    <label for="professionalEmail" class="form-label" style="color:white">Registered Email ID</label>
                                    <input type="email" class="form-control" name="professional_email" id="professionalEmail"
                                           oninvalid="this.setCustomValidity('Please enter a valid email address')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                                <div class="mb-3">
                                    <label for="professionalDesignation" class="form-label" style="color:white">Designation</label>
                                    <input type="text" class="form-control" name="designation" id="professionalDesignation">
                                </div>
                                <div class="mb-3">
                                    <label for="currentCompany" class="form-label" style="color:white">Current Company</label>
                                    <input type="text" class="form-control" name="company" id="currentCompany">
                                </div>
                                <div class="mb-3">
                                    <label for="currentCTC" class="form-label" style="color:white">Current CTC</label>
                                    <input type="text" class="form-control" name="ctc" id="currentCTC">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer id="footer" class="footer">
        <div class="container copyright text-center mt-4">
            <p>© <span>Copyright</span> <strong class="px-1 sitename">ZELL 2025</strong> <span>All Rights Reserved</span></p>
            <div class="credits">Designed by <a href="">ZELL</a></div>
        </div>
    </footer>

    <!-- Scroll Top Button -->
    <a href="#" class="scroll-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function toggleForm() {
            const userType = document.querySelector('input[name="userType"]:checked').value;
            
            if (userType === 'student') {
                document.getElementById('studentForm').classList.add('active');
                document.getElementById('professionalForm').classList.remove('active');
            } else {
                document.getElementById('studentForm').classList.remove('active');
                document.getElementById('professionalForm').classList.add('active');
            }
        }
        
        function submitForm(event) {
            event.preventDefault();
            
            const userType = document.querySelector('input[name="userType"]:checked').value;
            const form = document.getElementById('userForm');
            
            // Validation
            let isValid = true;
            let errorMessage = '';
            
            if (userType === 'student') {
                const name = document.getElementById('studentName').value;
                const email = document.getElementById('studentEmail').value;
                const qualification = document.getElementById('studentQualification').value;
                const university = document.getElementById('studentUniversity').value;
                const guardianNumber = document.getElementById('guardianNumber').value;
                
                if (!name) {
                    isValid = false;
                    errorMessage = 'Please enter your Student Name';
                } else if (!email) {
                    isValid = false;
                    errorMessage = 'Please enter a valid email address';
                } else if (!qualification) {
                    isValid = false;
                    errorMessage = 'Please enter your qualification';
                } else if (!university) {
                    isValid = false;
                    errorMessage = 'Please enter your university/school name';
                } else if (!guardianNumber || !/^[0-9]{10}$/.test(guardianNumber)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid 10-digit phone number';
                }
            } else {
                const name = document.getElementById('professionalName').value;
                const email = document.getElementById('professionalEmail').value;
                const designation = document.getElementById('professionalDesignation').value;
                const company = document.getElementById('currentCompany').value;
                const ctc = document.getElementById('currentCTC').value;
                
                if (!name) {
                    isValid = false;
                    errorMessage = 'Please enter your name';
                } else if (!email) {
                    isValid = false;
                    errorMessage = 'Please enter a valid email address';
                } else if (!designation) {
                    isValid = false;
                    errorMessage = 'Please enter your designation';
                } else if (!company) {
                    isValid = false;
                    errorMessage = 'Please enter your company name';
                } else if (!ctc) {
                    isValid = false;
                    errorMessage = 'Please enter your current CTC';
                }
            }
            
            if (!isValid) {
                Swal.fire({
                    title: 'Validation Error',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonColor: '#800080'
                });
                return;
            }
            
            // Show loading indicator
            Swal.fire({
                title: 'Submitting...',
                text: 'Please wait while we process your information.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Form submission
            const formData = new FormData(form);
            formData.append('user_type', userType);
            
            fetch('submit_form.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message || 'Form submitted successfully!',
                        icon: 'success',
                        confirmButtonColor: '#800080'
                    }).then(() => {
                        window.location.href = data.redirect || 'test.php';
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.error || 'Something went wrong. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#800080'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#800080'
                });
            });
        }
        
        <?php if (isset($_SESSION['message'])): ?>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '<?php echo $_SESSION['message_type'] === 'success' ? 'Success!' : 'Error!'; ?>',
                text: '<?php echo addslashes($_SESSION['message']); ?>',
                icon: '<?php echo $_SESSION['message_type'] === 'success' ? 'success' : 'error'; ?>',
                confirmButtonColor: '#800080'
            });
        });
        <?php endif; ?>
    </script>
</body>

</html>