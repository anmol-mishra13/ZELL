<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit();
}

// Get questions from questions.php
$questions = json_decode(include 'questions.php', true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment - Zell Education</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
        }

        .container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: #800080;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 100;
        }

        .content {
            display: flex;
            flex: 1;
            padding: 20px;
            gap: 20px;
            max-width: 1400px;
            margin: 80px auto 20px;
            width: 100%;
        }

        .left {
            width: 300px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 100px;
            height: fit-content;
        }

        .right {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            min-height: 500px;
        }

        .question-section {
            flex: 1;
            padding: 20px;
            margin-bottom: 20px;
        }

        .question-section h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.5em;
        }

        .question {
            font-size: 1.1em;
            line-height: 1.6;
            margin-bottom: 30px;
            color: #444;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-top: 20px;
        }

        .grid-box {
            background: white;
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .grid-box:hover {
            background: #f0f0f0;
        }

        .grid-box.answered {
            background: #4CAF50;
            color: white;
        }

        .grid-box.flagged {
            background: #FFC107;
            color: black;
        }

        .options-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        .options-form label {
            display: block;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .options-form label:hover {
            background: #e9ecef;
        }

        .options-form input[type="radio"] {
            margin-right: 10px;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            margin: 5px;
            color: white;
            cursor: pointer;
            font-weight: 500;
        }

        .btn.answered { background: #4CAF50; }
        .btn.flagged { background: #FFC107; color: black; }
        .btn.pending { background: #9E9E9E; }

        .badge {
            background: white;
            color: black;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: 5px;
            font-size: 0.9em;
        }

        .navigation-buttons {
            display: flex;
            gap: 10px;
            padding: 20px;
            justify-content: center;
            background: #f8f9fa;
            border-top: 1px solid #ddd;
        }

        .nav-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background: #800080;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .nav-btn:hover {
            background: #660066;
        }

        .nav-btn:disabled {
            background: #cccccc;
            cursor: not-allowed;
        }

        .webcam-preview {
            position: fixed;
            top: 80px;
            right: 20px;
            width: 160px;
            height: 120px;
            border: 2px solid #800080;
            border-radius: 4px;
            z-index: 1000;
        }

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
            font-weight: bold;
        }

        #timer {
            font-size: 1.2em;
            font-weight: bold;
            padding: 5px 10px;
            background: rgba(0,0,0,0.2);
            border-radius: 4px;
        }

        .footer {
            background: #f5f5f5;
            padding: 20px;
            text-align: center;
            margin-top: auto;
        }

        @media (max-width: 768px) {
            .content {
                flex-direction: column;
                margin-top: 120px;
            }
            
            .left {
                width: 100%;
                position: static;
            }

            .header {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            .webcam-preview {
                top: auto;
                bottom: 20px;
                right: 20px;
            }
        }
    </style>
</head>
<body>
    <div id="warning-message">Warning: Please return to the test window!</div>
    <div class="container">
        <header class="header">
            <div class="header-column">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
            </div>
            <div class="header-column">
                <h2>Online Assessment Test</h2>
            </div>
            <div class="header-column">
                <span>Time Remaining: <span id="timer">20:00</span></span>
            </div>
        </header>

        <main class="content">
            <section class="left">
                <div class="left-section-2">
                    <h3>Attempt Status</h3>
                    <br>
                    <button class="btn answered">Answered <span class="badge answered-count">0</span></button>
                    <button class="btn flagged">Flagged <span class="badge flagged-count">0</span></button>
                    <button class="btn pending">Pending <span class="badge pending-count">10</span></button>
                </div>
                <div class="left-section-3">
                    <h3>Questions</h3>
                    <br>
                    <hr>
                    <br>
                    <div class="grid-container">
                        <?php foreach ($questions as $index => $question): ?>
                            <div class="grid-box" data-question="<?php echo $question['id']; ?>">
                                <?php echo $question['id']; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <section class="right">
                <div class="question-section">
                    <h2>Question No. <span id="current-question">1</span></h2>
                    <p id="question-text" class="question"></p>
                    <form class="options-form">
                        <div id="options-container">
                            <!-- Options will be dynamically inserted here -->
                        </div>
                        <button type="button" class="clear-btn nav-btn">Clear Response</button>
                    </form>
                </div>
                <div class="navigation-buttons">
                    <button id="prevBtn" class="nav-btn">Previous</button>
                    <button id="nextBtn" class="nav-btn">Next</button>
                    <button id="flagBtn" class="nav-btn">Flag for Review</button>
                    <button id="submitTest" class="nav-btn">Submit Test</button>
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

    <script>
        const securityConfig = {
            mediaConstraints: {
                video: true,
                audio: true
            },
            violations: {
                maxTabSwitches: 3,
                maxWarnings: 5
            }
        };

        let violationCount = 0;
        let tabSwitchCount = 0;
        let currentQuestion = 1;
        let userAnswers = {};
        let flaggedQuestions = new Set();
        const questions = <?php echo include 'questions.php'; ?>;
        const totalQuestions = questions.length;

        async function initializeProctoring() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia(securityConfig.mediaConstraints);
                setupWebcamPreview(stream);
                setupSecurityMeasures();
                return true;
            } catch (error) {
                alert('Camera and microphone access are required for the test.');
                window.location.href = 'index.php';
                return false;
            }
        }

        function setupWebcamPreview(stream) {
            const video = document.createElement('video');
            video.srcObject = stream;
            video.className = 'webcam-preview';
            video.autoplay = true;
            document.body.appendChild(video);
        }

        function setupSecurityMeasures() {
            document.addEventListener('visibilitychange', handleTabSwitch);
            document.addEventListener('contextmenu', e => e.preventDefault());
            document.addEventListener('copy', e => e.preventDefault());
            document.addEventListener('paste', e => e.preventDefault());
            document.addEventListener('keydown', handleKeyboardShortcuts);
            
            document.documentElement.requestFullscreen()
                .catch(err => console.log('Fullscreen request failed'));
        }

        function handleTabSwitch() {
            if (document.hidden) {
                tabSwitchCount++;
                logViolation('Tab switch detected');
                showWarning();

                if (tabSwitchCount >= securityConfig.violations.maxTabSwitches) {
                    autoSubmitTest('Multiple tab switches detected');
                }
            }
        }

        function handleKeyboardShortcuts(e) {
            if ((e.ctrlKey && e.key === 'c') || 
                (e.ctrlKey && e.key === 'v') || 
                (e.altKey && e.key === 'Tab')) {
                e.preventDefault();
                logViolation('Keyboard shortcut attempted');
            }
        }

        function showWarning() {
            const warning = document.getElementById('warning-message');
            warning.style.display = 'block';
            setTimeout(() => {
                warning.style.display = 'none';
            }, 3000);
        }

        function logViolation(violation) {
            violationCount++;
            
            if (violationCount >= securityConfig.violations.maxWarnings) {
                autoSubmitTest('Too many security violations');
                return;
            }

            fetch('log_violation.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    email: '<?php echo $_SESSION['user_email']; ?>',
                    violation,
                    timestamp: new Date().toISOString()
                })
            });
        }

        function updateQuestion(questionNum) {
            const question = questions.find(q => q.id === questionNum);
            if (!question) return;

            document.getElementById('current-question').textContent = questionNum;
            document.getElementById('question-text').textContent = question.text;
            
            const optionsContainer = document.getElementById('options-container');
            optionsContainer.innerHTML = question.options.map((option, index) => `
                <label>
                    <input type="radio" name="answer" value="${option}"> ${option}
                </label>
            `).join('');

            if (userAnswers[questionNum]) {
                const savedAnswer = optionsContainer.querySelector(`input[value="${userAnswers[questionNum]}"]`);
                if (savedAnswer) savedAnswer.checked = true;
            }

            document.getElementById('prevBtn').disabled = questionNum === 1;
            document.getElementById('nextBtn').disabled = questionNum === totalQuestions;
            
            const flagBtn = document.getElementById('flagBtn');
            flagBtn.textContent = flaggedQuestions.has(questionNum) ? 'Unflag Question' : 'Flag for Review';
        }

        function updateStats() {
            document.querySelector('.answered-count').textContent = Object.keys(userAnswers).length;
            document.querySelector('.flagged-count').textContent = flaggedQuestions.size;
            document.querySelector('.pending-count').textContent = 
                totalQuestions - Object.keys(userAnswers).length;
        }

        function startTimer(duration, display) {
            let timer = duration * 60;
            const timerInterval = setInterval(() => {
                const minutes = parseInt(timer / 60, 10);
                const seconds = parseInt(timer % 60, 10);

                display.textContent = 
                    (minutes < 10 ? "0" + minutes : minutes) + ":" +
                    (seconds < 10 ? "0" + seconds : seconds);

                if (timer <= 300) {
                    display.style.color = '#ff4444';
                }

                if (--timer < 0) {
                    clearInterval(timerInterval);
                    submitTest(true);
                }
            }, 1000);
        }

        function autoSubmitTest(reason) {
            alert(`Test will be automatically submitted. Reason: ${reason}`);
            submitTest(true);
        }

        async function submitTest(isAutoSubmit = false) {
            if (!isAutoSubmit && !confirm('Are you sure you want to submit the test?')) {
                return;
            }

            const testData = {
                answers: userAnswers,
                flagged: Array.from(flaggedQuestions),
                email: '<?php echo $_SESSION['user_email']; ?>',
                violations: {
                    tabSwitches: tabSwitchCount,
                    totalViolations: violationCount
                }
            };

            try {
                const response = await fetch('submit_test.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(testData)
                });

                const result = await response.json();

                if (result.success) {
                    if (document.fullscreenElement) {
                        await document.exitFullscreen();
                    }
                    
                    const videoElement = document.querySelector('.webcam-preview');
                    if (videoElement) {
                        const stream = videoElement.srcObject;
                        const tracks = stream.getTracks();
                        tracks.forEach(track => track.stop());
                        videoElement.remove();
                    }

                    window.location.href = 'result.php';
                } else {
                    alert('Error submitting test: ' + result.error);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error submitting test. Please try again.');
            }
        }

        // Event Listeners
        document.getElementById('prevBtn').addEventListener('click', () => {
            if (currentQuestion > 1) {
                currentQuestion--;
                updateQuestion(currentQuestion);
            }
        });

        document.getElementById('nextBtn').addEventListener('click', () => {
            if (currentQuestion < totalQuestions) {
                currentQuestion++;
                updateQuestion(currentQuestion);
            }
        });

        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('grid-box')) {
                currentQuestion = parseInt(e.target.dataset.question);
                updateQuestion(currentQuestion);
            }
        });

        document.getElementById('options-container').addEventListener('change', (e) => {
            if (e.target.type === 'radio') {
                userAnswers[currentQuestion] = e.target.value;
                document.querySelector(`[data-question="${currentQuestion}"]`).classList.add('answered');
                updateStats();
            }
        });

        document.getElementById('flagBtn').addEventListener('click', () => {
            const questionBox = document.querySelector(`[data-question="${currentQuestion}"]`);
            if (flaggedQuestions.has(currentQuestion)) {
                flaggedQuestions.delete(currentQuestion);
                questionBox.classList.remove('flagged');
            } else {
                flaggedQuestions.add(currentQuestion);
                questionBox.classList.add('flagged');
            }
            updateQuestion(currentQuestion);
            updateStats();
        });

        document.querySelector('.clear-btn').addEventListener('click', () => {
            document.querySelectorAll('input[type="radio"]').forEach(radio => radio.checked = false);
            delete userAnswers[currentQuestion];
            document.querySelector(`[data-question="${currentQuestion}"]`).classList.remove('answered');
            updateStats();
        });

        document.getElementById('submitTest').addEventListener('click', () => submitTest());

        window.addEventListener('beforeunload', (e) => {
            if (Object.keys(userAnswers).length > 0) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Initialize
        window.onload = async function() {
            const proctorInitialized = await initializeProctoring();
            if (!proctorInitialized) return;
            
            startTimer(20, document.querySelector('#timer'));
            updateQuestion(1);
            updateStats();

            document.addEventListener('fullscreenchange', () => {
                if (!document.fullscreenElement) {
                    logViolation('Fullscreen mode exited');
                    document.documentElement.requestFullscreen().catch(err => {
                        autoSubmitTest('Failed to maintain fullscreen mode');
                    });
                }
            });
        };
    </script>
</body>
</html>