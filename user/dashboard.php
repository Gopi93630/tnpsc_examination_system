<?php
session_start();
include("../backend/db.php");

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>
<head>
<title>User Dashboard</title>
<link rel="stylesheet" href="../assets/style.css">
</head>

<body>

<h2>Welcome User 👋</h2>

<div class="card">
<?php
$res = $conn->query("SELECT COUNT(*) as total FROM results WHERE user_id=$user_id");
$row = $res->fetch_assoc();
echo "Tests Attended: " . $row['total'];
?>
</div>

<div class="card">
<a href="exam.php">Start Exam</a>
</div>

<div class="card">
<a href="result.php">View Results</a>
</div>

<div class="card">
<a href="profile.php">Profile</a>
</div>

<a href="../logout.php">Logout</a>

<script src="../assets/script.js"></script>
</body>
</html>