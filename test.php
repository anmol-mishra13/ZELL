<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit();
}

$questions = json_decode(file_get_contents('questions.php'), true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Test</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #6a1b9a;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        .content {
            margin-top: 80px;
            display: flex;
            gap: 20px;
        }
        .question-panel {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .sidebar {
            width: 300px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: sticky;
            top: 100px;
            height: fit-content;
        }
        .question-nav {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-top: 15px;
        }
        .question-box {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            cursor: pointer;
            border-radius: 4px;
        }
        .question-box.answered { background: #4CAF50; color: white; }
        .question-box.flagged { background: #FFC107; }
        .navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-primary { background: #6a1b9a; color: white; }
        .btn-danger { background: #f44336; color: white; }
        .btn-warning { background: #FFC107; }
        #warning-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 0, 0, 0.9);
            color: white;
            padding: 20px;
            border-radius: 5px;
            z-index: 2000;
            display: none;
        }
        #timer {
            font-weight: bold;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div id="warning-message">Warning: Please return to the test window!</div>
    
    <div class="header">
        <div>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></div>
        <div>Online Assessment</div>
        <div>Time Left: <span id="timer">20:00</span></div>
    </div>

    <div class="container">
        <div class="content">
            <div class="question-panel">
                <h2>Question <span id="current-q">1</span></h2>
                <div id="question-text" class="question"></div>
                <form id="options-form">
                    <div id="options-container"></div>
                </form>
                <div class="navigation">
                    <button id="prev-btn" class="btn btn-primary">Previous</button>
                    <button id="next-btn" class="btn btn-primary">Next</button>
                    <button id="flag-btn" class="btn btn-warning">Flag Question</button>
                    <button id="submit-btn" class="btn btn-danger">Submit Test</button>
                </div>
            </div>

            <div class="sidebar">
                <h3>Question Navigation</h3>
                <div class="question-nav" id="question-nav">
                    <?php foreach ($questions as $q): ?>
                        <div class="question-box" data-id="<?= $q['id'] ?>"><?= $q['id'] ?></div>
                    <?php endforeach; ?>
                </div>
                <div style="margin-top: 20px;">
                    <div><span class="question-box answered"></span> Answered</div>
                    <div><span class="question-box flagged"></span> Flagged</div>
                    <div><span class="question-box"></span> Not Answered</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Security configuration
        const config = {
            maxTabSwitches: 3,
            maxWarnings: 5,
            testDuration: 20 // minutes
        };

        // Test data
        const questions = <?php echo json_encode($questions); ?>;
        let currentQuestion = 1;
        let userAnswers = {};
        let flaggedQuestions = new Set();
        let tabSwitchCount = 0;
        let violationCount = 0;

        // DOM elements
        const questionText = document.getElementById('question-text');
        const optionsContainer = document.getElementById('options-container');
        const currentQElement = document.getElementById('current-q');
        const warningMessage = document.getElementById('warning-message');
        const timerElement = document.getElementById('timer');

        // Initialize the test
        function initTest() {
            // Start security monitoring
            startProctoring();
            
            // Start timer
            startTimer(config.testDuration * 60);
            
            // Load first question
            showQuestion(currentQuestion);
            
            // Block navigation attempts
            blockNavigation();
        }

        // Show a question
        function showQuestion(id) {
            const question = questions.find(q => q.id === id);
            if (!question) return;

            currentQElement.textContent = id;
            questionText.textContent = question.text;
            
            // Render options
            optionsContainer.innerHTML = question.options.map(option => `
                <div style="margin: 10px 0;">
                    <input type="radio" name="answer" id="opt-${option}" value="${option}">
                    <label for="opt-${option}">${option}</label>
                </div>
            `).join('');

            // Restore selected answer if exists
            if (userAnswers[id]) {
                document.querySelector(`input[value="${userAnswers[id]}"]`).checked = true;
                document.querySelector(`.question-box[data-id="${id}"]`).classList.add('answered');
            }

            // Update flag button
            document.getElementById('flag-btn').textContent = flaggedQuestions.has(id) 
                ? 'Unflag Question' 
                : 'Flag Question';
            
            // Update navigation buttons
            document.getElementById('prev-btn').disabled = id === 1;
            document.getElementById('next-btn').disabled = id === questions.length;
        }

        // Block navigation attempts
        function blockNavigation() {
            // Block back button
            history.pushState(null, null, window.location.href);
            window.addEventListener('popstate', function(e) {
                history.pushState(null, null, window.location.href);
                showExitWarning();
                e.preventDefault();
            });

            // Block refresh
            document.addEventListener('keydown', function(e) {
                if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
                    e.preventDefault();
                    showExitWarning();
                }
            });

            // Block tab/window close
            window.addEventListener('beforeunload', function(e) {
                if (Object.keys(userAnswers).length > 0) {
                    e.preventDefault();
                    e.returnValue = 'Your test will be submitted if you leave this page.';
                    return e.returnValue;
                }
            });
        }

        // Show exit warning
        function showExitWarning() {
            Swal.fire({
                title: 'Leave Test?',
                text: 'Your progress will be submitted if you leave this page.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Leave',
                cancelButtonText: 'Stay'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitTest();
                }
            });
        }

        // Start timer
        function startTimer(duration) {
            let timer = duration, minutes, seconds;
            const interval = setInterval(function() {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                timerElement.textContent = minutes + ":" + seconds;

                if (--timer < 0) {
                    clearInterval(interval);
                    submitTest();
                }
            }, 1000);
        }

        // Proctoring functions
        function startProctoring() {
            // Track tab switches
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    tabSwitchCount++;
                    showWarning();
                    if (tabSwitchCount >= config.maxTabSwitches) {
                        submitTest('Too many tab switches');
                    }
                }
            });

            // Disable right click
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                violationCount++;
                showWarning();
            });

            // Try to enable fullscreen
            document.documentElement.requestFullscreen().catch(e => console.log(e));
        }

        function showWarning() {
            warningMessage.style.display = 'block';
            setTimeout(() => warningMessage.style.display = 'none', 3000);
            violationCount++;
            
            if (violationCount >= config.maxWarnings) {
                submitTest('Too many violations');
            }
        }

        // Submit test
        function submitTest(reason = '') {
            const data = {
                answers: userAnswers,
                flagged: Array.from(flaggedQuestions),
                violations: {
                    tabSwitches: tabSwitchCount,
                    total: violationCount
                },
                reason: reason
            };

            // In a real app, you would send this to your server
            console.log('Submitting test:', data);
            
            // Show confirmation
            Swal.fire({
                title: 'Test Submitted',
                text: reason || 'Your test has been submitted successfully',
                icon: 'success'
            }).then(() => {
                window.location.href = 'result.php';
            });
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', initTest);

        document.getElementById('prev-btn').addEventListener('click', function() {
            if (currentQuestion > 1) {
                showQuestion(--currentQuestion);
            }
        });

        document.getElementById('next-btn').addEventListener('click', function() {
            if (currentQuestion < questions.length) {
                showQuestion(++currentQuestion);
            }
        });

        document.getElementById('flag-btn').addEventListener('click', function() {
            const box = document.querySelector(`.question-box[data-id="${currentQuestion}"]`);
            if (flaggedQuestions.has(currentQuestion)) {
                flaggedQuestions.delete(currentQuestion);
                box.classList.remove('flagged');
                this.textContent = 'Flag Question';
            } else {
                flaggedQuestions.add(currentQuestion);
                box.classList.add('flagged');
                this.textContent = 'Unflag Question';
            }
        });

        document.getElementById('submit-btn').addEventListener('click', function() {
            Swal.fire({
                title: 'Submit Test?',
                text: 'Are you sure you want to submit your test?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitTest();
                }
            });
        });

        document.getElementById('options-form').addEventListener('change', function(e) {
            if (e.target.name === 'answer') {
                userAnswers[currentQuestion] = e.target.value;
                document.querySelector(`.question-box[data-id="${currentQuestion}"]`).classList.add('answered');
            }
        });

        document.querySelectorAll('.question-box').forEach(box => {
            box.addEventListener('click', function() {
                currentQuestion = parseInt(this.dataset.id);
                showQuestion(currentQuestion);
            });
        });
    </script>
</body>
</html>