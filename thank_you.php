<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Councelling Scheduled - Zell Education</title>
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 90%;
        }

        .success-icon {
            color: #4CAF50;
            font-size: 60px;
            margin-bottom: 20px;
        }

        h1 {
            color: #800080;
            margin-bottom: 20px;
            font-size: 24px;
        }

        p {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
            line-height: 1.6;
        }

        .info-box {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .info-box h2 {
            color: #333;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .info-box p {
            margin-bottom: 0;
        }

        .btn {
            display: inline-block;
            background: #800080;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #660066;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✓</div>
        <h1>Councelling Scheduled Successfully!</h1>
        <div class="info-box">
            <h2>Councelling Details</h2>
            <p>Date: <?php echo isset($_SESSION['interview_date']) ? $_SESSION['interview_date'] : 'Not available'; ?></p>
            <p>Time: <?php echo isset($_SESSION['interview_time']) ? $_SESSION['interview_time'] : 'Not available'; ?></p>
        </div>
        <p>A confirmation email has been sent to your registered email address with all the details. Please check your inbox.</p>
        <p>Make sure to join the Councelling on time. Best of luck!</p>
        <a href="index.php" class="btn">Back to Home</a>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if (isset($_SESSION['message'])): ?>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '<?php echo $_SESSION['message_type'] === 'success' ? 'Success!' : 'Error!'; ?>',
                text: '<?php echo addslashes($_SESSION['message']); ?>',
                icon: '<?php echo $_SESSION['message_type'] === 'success' ? 'success' : 'error'; ?>',
                confirmButtonColor: '#800080'
            });
        });
        <?php 
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        endif; 
        ?>
    </script>
</body>
</html>