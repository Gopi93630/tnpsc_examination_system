<?php
session_start();
include("../backend/db.php");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<link rel="stylesheet" href="../assets/style.css">
</head>

<body>

<h2>Admin Dashboard</h2>

<div class="card">Total Users: 
<?php
$res = $conn->query("SELECT COUNT(*) as total FROM users");
$row = $res->fetch_assoc();
echo $row['total'];
?>
</div>

<div class="card">Total Exams:
<?php
$res = $conn->query("SELECT COUNT(*) as total FROM exams");
$row = $res->fetch_assoc();
echo $row['total'];
?>
</div>

<div>
<a href="add_exam.php">Add Exam</a> |
<a href="add_question.php">Add Question</a> |
<a href="manage_users.php">Manage Users</a>
</div>

<script src="../assets/script.js"></script>
</body>
</html>