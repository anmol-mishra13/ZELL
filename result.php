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
        <?php if(isset($_SESSION['message'])): ?>
            <div class="<?php echo $_SESSION['message_type']; ?>-message">
                <?php 
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?>
        
        <div class="schedule-section">
            <h2>Schedule Your Interview</h2>
            <form action="save_schedule.php" method="POST">
                <div class="form-group">
                    <label>Select Date:</label>
                    <input type="text" id="datePicker" name="date" required>
                </div>
                <div class="form-group">
                    <label>Select Time:</label>
                    <select name="time" required>
                        <?php foreach($timeSlots as $slot): ?>
                            <option value="<?php echo $slot; ?>"><?php echo $slot; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit">Confirm Schedule</button>
            </form>
        </div>
    </div>

    <script>
        flatpickr("#datePicker", {
            enable: [
                "<?php echo $today ?>",
                "<?php echo $tomorrow ?>"
            ],
            dateFormat: "Y-m-d",
            minDate: "today",
            maxDate: "<?php echo $tomorrow ?>",
            disableMobile: "true"
        });
    </script>
</body>
</html>