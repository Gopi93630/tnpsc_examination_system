<?php
session_start();
include("db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get subject from form
if(!isset($_POST['subject'])){
    echo "Subject not found!";
    exit();
}

$subject = $_POST['subject'];

// OPTIONAL: you can make this dynamic later
$exam_id = 1;

// Fetch only selected subject questions (10)
$stmtQ = $conn->prepare("SELECT id, correct_option FROM questions WHERE subject=? LIMIT 10");
$stmtQ->bind_param("s", $subject);
$stmtQ->execute();
$result = $stmtQ->get_result();

$score = 0;
$total = 0;

while($row = $result->fetch_assoc()){

    $qid = $row['id'];
    $correct = $row['correct_option'];

    $total++;

    // Check user answer
    if(isset($_POST["q$qid"])){

        $user_answer = $_POST["q$qid"];

        if($user_answer == $correct){
            $score++;
        }
    }
}

// Insert result into DB
$stmt = $conn->prepare("INSERT INTO results (user_id, exam_id, score, total) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiii", $user_id, $exam_id, $score, $total);
$stmt->execute();

$stmt->close();
$stmtQ->close();

// Redirect to result page
header("Location: ../user/result.php");
exit();
?>