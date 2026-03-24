<?php
include("../backend/db.php");

if(isset($_POST['add_question'])){
    $exam_id = $_POST['exam_id'];
    $question = $_POST['question'];
    $o1 = $_POST['option1'];
    $o2 = $_POST['option2'];
    $o3 = $_POST['option3'];
    $o4 = $_POST['option4'];
    $correct = $_POST['correct_option'];

    $conn->query("INSERT INTO questions 
    (exam_id, question, option1, option2, option3, option4, correct_option)
    VALUES ('$exam_id','$question','$o1','$o2','$o3','$o4','$correct')");

    echo "Question Added";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Question</title>
</head>
<body>

<h2>Add Question</h2>

<form method="POST">
<input type="number" name="exam_id" placeholder="Exam ID" required><br>
<textarea name="question" placeholder="Enter Question"></textarea><br>

<input type="text" name="option1" placeholder="Option 1"><br>
<input type="text" name="option2" placeholder="Option 2"><br>
<input type="text" name="option3" placeholder="Option 3"><br>
<input type="text" name="option4" placeholder="Option 4"><br>

<input type="number" name="correct_option" placeholder="Correct Option (1-4)"><br>

<button name="add_question">Add Question</button>
</form>

<script src="../assets/script.js"></script>
</body>
</html>