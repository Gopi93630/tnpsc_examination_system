<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized Access");
}

$user_id = $_SESSION['user_id'];
$exam_id = $_POST['exam_id'];

$score = 0;
$total = 0;

// Fetch questions
$questions = $conn->prepare("SELECT id, correct_option FROM questions WHERE exam_id=?");
$questions->bind_param("i", $exam_id);
$questions->execute();
$result = $questions->get_result();

while ($q = $result->fetch_assoc()) {

    $total++;

    $qid = $q['id'];
    $correct = $q['correct_option'];

    $user_answer = $_POST["q$qid"] ?? 0;

    if ($user_answer == $correct) {
        $score++;
    }
}

// Save result
$stmt = $conn->prepare("INSERT INTO results (user_id, exam_id, score, total) VALUES (?,?,?,?)");
$stmt->bind_param("iiii", $user_id, $exam_id, $score, $total);
$stmt->execute();

// Redirect to result page
header("Location: ../user/result.php");
exit();
?>