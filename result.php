<?php session_start();
require_once 'mail_config.php';

if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit();
}

$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$timeSlots = ['10:00 AM', '11:00 AM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM'];
?>
<!DOCTYPE html>
<html>

<head>
    <title>Schedule Councelling- Zell Education</title>
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background: #f4f4f4;
            color: #333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 600px;
        }

        h1 {
            color: #800080;
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .schedule-section {
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        select {
            background: white;
            cursor: pointer;
        }

        button {
            background: #800080;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #660066;
        }

        /* Flatpickr customization */
        .flatpickr-day.selected {
            background: #800080;
            border-color: #800080;
        }

        .flatpickr-day.selected:hover {
            background: #660066;
            border-color: #660066;
        }

        /* Success message */
        .success-message {
            background: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Error message */
        .error-message {
            background: #f44336;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 20px;
            }

            h1 {
                font-size: 20px;
            }

            h2 {
                font-size: 18px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Thank You for Completing the Test</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="<?php echo $_SESSION['message_type']; ?>-message">
                <?php
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?>

        <div class="schedule-section">
            <h2>Schedule Your Counselling Session</h2>
            <form action="save_schedule.php" method="POST">
                <div class="form-group">
                    <label>Select Date:</label>
                    <input type="text" id="datePicker" name="date" required>
                </div>
                <div class="form-group">
                    <label>Select Time:</label>
                    <select name="time" required>
                        <?php foreach ($timeSlots as $slot): ?>
                            <option value="<?php echo $slot; ?>"><?php echo $slot; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit">Confirm Schedule</button>
            </form>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if (isset($_SESSION['message'])): ?>
        Swal.fire({
            title: '<?php echo $_SESSION['message_type'] === 'success' ? 'Success!' : 'Error!'; ?>',
            text: '<?php echo addslashes($_SESSION['message']); ?>',
            icon: '<?php echo $_SESSION['message_type'] === 'success' ? 'success' : 'error'; ?>',
            confirmButtonColor: '#800080'
        });
        <?php 
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        endif; 
        ?>

        flatpickr("#datePicker", {
            enable: [
                "<?php echo date('Y-m-d',strtotime('+1 day')) ?>",
                "<?php echo date('Y-m-d', strtotime('+2 day')) ?>"
            ],
            dateFormat: "Y-m-d",
            minDate: "<?php echo date('Y-m-d'),strtotime('+1 day') ?>",
            maxDate: "<?php echo date('Y-m-d', strtotime('+2 day')) ?>",
            disableMobile: true,
            inline: true
        });

        // Form submission with SweetAlert
        document.getElementById('scheduleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            
            // Show loading state
            Swal.fire({
                title: 'Scheduling...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('save_schedule.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message || 'Your interview has been scheduled successfully!',
                        icon: 'success',
                        confirmButtonColor: '#800080'
                    }).then(() => {
                        window.location.href = data.redirect || 'thank_you.php';
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
        });
    </script>
</body>

</html>