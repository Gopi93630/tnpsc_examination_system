<?php
session_start();
include("../backend/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../index.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>TNPSC Exam</title>

    <link rel="stylesheet" href="../assets/style.css">
    <script src="../assets/script.js"></script>

    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: url('../assets/bg.jpg') no-repeat center center/cover;
        }

        .overlay {
            background: rgba(0,0,0,0.7);
            min-height: 100vh;
            padding: 30px;
            color: white;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
        }

        /* CARDS */
        .card-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .card {
            background: white;
            color: black;
            padding: 20px;
            border-radius: 10px;
            cursor: pointer;
            width: 200px;
            transition: 0.3s;
        }

        .card:hover {
            transform: scale(1.1);
            background: #28a745;
            color: white;
        }

        /* TIMER */
        #timer-box {
            position: fixed;
            top: 20px;
            right: 30px;
            background: #28a745;
            padding: 10px 20px;
            border-radius: 8px;
        }

        /* QUESTIONS */
        .question-box {
            background: white;
            color: black;
            padding: 20px;
            margin: 20px auto;
            border-radius: 10px;
            max-width: 700px;
            text-align: left;
        }

        /* SUBMIT BUTTON */
        .submit-btn {
            margin-top: 20px;
            padding: 15px 30px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
        }

        .submit-btn:hover {
            background: #218838;
            transform: scale(1.1);
        }
    </style>
</head>

<body>

<div class="overlay">

    <!-- STEP 1: GROUP SELECTION -->
    <div id="group-section">
        <h2>Select TNPSC Group</h2>
        <div class="card-container">
            <div class="card" onclick="selectGroup('Group 1')">Group 1</div>
            <div class="card" onclick="selectGroup('Group 2')">Group 2</div>
            <div class="card" onclick="selectGroup('Group 4')">Group 4</div>
        </div>
    </div>

    <!-- STEP 2: SUBJECT SELECTION -->
    <div id="subject-section" style="display:none;">
        <h2>Select Subject</h2>
        <div class="card-container">
            <div class="card" onclick="startExam('History')">History</div>
            <div class="card" onclick="startExam('Polity')">Polity</div>
            <div class="card" onclick="startExam('Geography')">Geography</div>
            <div class="card" onclick="startExam('Economy')">Economy</div>
        </div>
    </div>

    <!-- TIMER -->
    <div id="timer-box" style="display:none;">
        Time Left: <span id="timer">5:00</span>
    </div>

    <!-- STEP 3: QUESTIONS -->
    <div id="exam-section" style="display:none;">
        <h2 id="exam-title"></h2>

        <form id="examForm" action="../backend/submit_exam.php" method="POST">

        <?php
        // Fetch 10 random questions
        $questions = $conn->query("SELECT * FROM questions ORDER BY RAND() LIMIT 10");
        $i = 1;
        while($q = $questions->fetch_assoc()){
        ?>

        <div class="question-box">
            <p>Q<?php echo $i++; ?>. <?php echo $q['question']; ?></p>

            <label><input type="radio" name="q<?php echo $q['id']; ?>" value="1"> <?php echo $q['option1']; ?></label><br>
            <label><input type="radio" name="q<?php echo $q['id']; ?>" value="2"> <?php echo $q['option2']; ?></label><br>
            <label><input type="radio" name="q<?php echo $q['id']; ?>" value="3"> <?php echo $q['option3']; ?></label><br>
            <label><input type="radio" name="q<?php echo $q['id']; ?>" value="4"> <?php echo $q['option4']; ?></label>
        </div>

        <?php } ?>

        <button type="submit" class="submit-btn">🚀 Submit Exam</button>

        </form>
    </div>

</div>
<script>
function selectGroup(group){
    document.getElementById("group-section").style.display="none";
    document.getElementById("subject-section").style.display="block";
}

function startExam(subject){
    document.getElementById("subject-section").style.display="none";
    document.getElementById("exam-section").style.display="block";
    document.getElementById("timer-box").style.display="block";
    document.getElementById("exam-title").innerText = subject + " Test";

    startTimer(5); // 5 minutes
}
</script>

</body>
</html>