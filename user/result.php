<?php
session_start();
include("../backend/db.php");

$user_id = $_SESSION['user_id'];

$res = $conn->query("SELECT * FROM results WHERE user_id=$user_id ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Results</title>
</head>

<body>

<h2>Your Results</h2>

<table border="1">
<tr>
<th>Exam ID</th>
<th>Score</th>
<th>Total</th>
<th>Date</th>
</tr>

<?php while($row = $res->fetch_assoc()) { ?>
<tr>
<td><?php echo $row['exam_id']; ?></td>
<td><?php echo $row['score']; ?></td>
<td><?php echo $row['total']; ?></td>
<td><?php echo $row['created_at']; ?></td>
</tr>
<?php } ?>

</table>

<a href="dashboard.php">Back</a>

<script src="../assets/script.js"></script>
</body>
</html>