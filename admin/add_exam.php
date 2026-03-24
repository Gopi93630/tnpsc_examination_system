<?php
include("../backend/db.php");

if(isset($_POST['add_exam'])){
    $title = $_POST['title'];
    $duration = $_POST['duration'];

    $conn->query("INSERT INTO exams (title, duration) VALUES ('$title', '$duration')");
    echo "Exam Added Successfully";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Exam</title>
</head>
<body>

<h2>Add Exam</h2>

<form method="POST">
<input type="text" name="title" placeholder="Exam Title" required>
<input type="number" name="duration" placeholder="Duration (minutes)" required>
<button name="add_exam">Add Exam</button>
</form>

<script src="../assets/script.js"></script>
</body>
</html>