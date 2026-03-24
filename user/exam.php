<?php
session_start();
include("../backend/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../index.html");
    exit();
}

// Get subject from URL
$subject = $_GET['subject'] ?? '';
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
        #timer-box {
            position: fixed;
            top: 20px;
            right: 30px;
            background: #28a745;
            padding: 10px 20px;
            border-radius: 8px;
            display: none;
        }
        .question-box {
            background: white;
            color: black;
            padding: 20px;
            margin: 20px auto;
            border-radius: 10px;
            max-width: 700px;
            text-align: left;
        }

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
            transform: scale(1.1);
            background: #218838;
        }
    </style>
</head>

<body>

<div class="overlay">

<?php if(!$subject) { ?>

    <!-- GROUP -->
    <h2>Select TNPSC Group</h2>
    <div class="card-container">
        <div class="card" onclick="showSubjects()">Group 1</div>
        <div class="card" onclick="showSubjects()">Group 2</div>
    </div>

    <!-- SUBJECT -->
    <div id="subject-section" style="display:none;">
        <h2>Select Subject</h2>
        <div class="card-container">
            <div class="card" onclick="goExam('History')">History</div>
            <div class="card" onclick="goExam('Polity')">Polity</div>
            <div class="card" onclick="goExam('Geography')">Geography</div>
            <div class="card" onclick="goExam('Economy')">Economy</div>
        </div>
    </div>

<?php } else { ?>

    <!-- TIMER -->
    <div id="timer-box">
        Time Left: <span id="timer">5:00</span>
    </div>

    <h2><?php echo $subject; ?> Test</h2>

    <form id="examForm" action="../backend/submit_exam.php" method="POST">

        <!-- ✅ IMPORTANT HIDDEN FIELD -->
        <input type="hidden" name="subject" value="<?php echo $subject; ?>">

        <?php
        $stmt = $conn->prepare("SELECT * FROM questions WHERE subject=? LIMIT 10");
        $stmt->bind_param("s", $subject);
        $stmt->execute();
        $result = $stmt->get_result();

        $i = 1;
        while($q = $result->fetch_assoc()){
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

<?php } ?>

</div>

<script>
function showSubjects(){
    document.getElementById("subject-section").style.display = "block";
}

function goExam(subject){
    window.location.href = "exam.php?subject=" + subject;
}

// Start timer if subject selected
<?php if($subject) { ?>
document.getElementById("timer-box").style.display = "block";
startTimer(5);
<?php } ?>
</script>

</body>
</html>