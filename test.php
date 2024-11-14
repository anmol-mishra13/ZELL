<?php
session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit();
}

// Timer settings
$test_duration = 20; // minutes
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment - Zell Education</title>
    <link rel="stylesheet" href="assets/css/test.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="header-column">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_email']); ?></span>
            </div>
            <div class="header-column">
                <h2>Online Assessment Test</h2>
            </div>
            <div class="header-column">
                <span>Time Remaining: <span id="timer"><?php echo $test_duration; ?>:00</span></span>
            </div>
        </header>
        <main class="content">
            <section class="left">
                <div class="left-section-2">
                    <h3>Attempt Status</h3>
                    <br>
                    <button class="btn answered">Answered <span class="badge answered-count">0</span></button>
                    <button class="btn flagged">Flagged <span class="badge flagged-count">0</span></button>
                    <button class="btn pending">Pending <span class="badge pending-count">20</span></button>
                </div>
                <div class="left-section-3">
                    <h3>Questions</h3>
                    <br>
                    <hr>
                    <br>
                    <div class="grid-container">
                        <?php
                        for ($i = 1; $i <= 20; $i++) {
                            echo "<div class='grid-box' data-question='$i'>$i</div>";
                        }
                        ?>
                    </div>
                </div>
            </section>
            <section class="right">
                <div id="question-container">
                    <!-- Questions will be loaded dynamically -->
                </div>
                <div class="navigation-buttons">
                    <button id="prevBtn" class="nav-btn">Previous</button>
                    <button id="nextBtn" class="nav-btn">Next</button>
                    <button id="flagBtn" class="nav-btn flag">Flag for Review</button>
                    <button id="submitTest" class="nav-btn submit">Submit Test</button>
                </div>
            </section>
        </main>
        <footer class="footer">
            <div class="container copyright text-center mt-4">
                <p>© <span>Copyright</span> <strong class="px-1 sitename">ZELL 2024</strong> <span>All Rights Reserved</span></p>
                <div class="credits">Designed by <a href="">ZELL</a></div>
            </div>
        </footer>
    </div>

    <!-- Question Template -->
    <template id="question-template">
        <div class="question-section">
            <h2>Question No. <span class="question-number"></span></h2>
            <p class="question-text"></p>
            <form class="options-form">
                <div class="options-container"></div>
                <button type="button" class="clear-btn">Clear Response</button>
            </form>
        </div>
    </template>

    <script>
        const questions = <?php include 'questions.php'; ?>;
        let currentQuestion = 1;
        let userAnswers = {};
        let flaggedQuestions = new Set();

        function startTimer(duration, display) {
            let timer = duration * 60;
            const timerInterval = setInterval(function() {
                const minutes = parseInt(timer / 60, 10);
                const seconds = parseInt(timer % 60, 10);

                display.textContent = 
                    (minutes < 10 ? "0" + minutes : minutes) + ":" +
                    (seconds < 10 ? "0" + seconds : seconds);

                if (--timer < 0) {
                    clearInterval(timerInterval);
                    submitTest();
                }
            }, 1000);
        }

        function submitTest() {
            const testData = {
                answers: userAnswers,
                flagged: Array.from(flaggedQuestions),
                email: '<?php echo $_SESSION['user_email']; ?>'
            };

            fetch('submit_test.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(testData)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    window.location.href = 'result.php';
                }
            })
            .catch(error => console.error('Error:', error));
        }

        window.onload = function() {
            const display = document.querySelector('#timer');
            startTimer(<?php echo $test_duration; ?>, display);
            loadQuestion(currentQuestion);
            updateStats();
        };

        // Add event listeners and implement other required functions
        // ...
    </script>
</body>
</html>