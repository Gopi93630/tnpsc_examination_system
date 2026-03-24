<?php
session_start();
include("../backend/db.php");

$user_id = $_SESSION['user_id'];

if(isset($_POST['update'])){
    $name = $_POST['name'];
    $conn->query("UPDATE users SET name='$name' WHERE id=$user_id");
}

$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<title>Profile</title>
</head>

<body>

<h2>Your Profile</h2>

<form method="POST">
<input type="text" name="name" value="<?php echo $user['name']; ?>">
<input type="email" value="<?php echo $user['email']; ?>" disabled>
<button name="update">Update</button>
</form>

<a href="dashboard.php">Back</a>

<script src="../assets/script.js"></script>
</body>
</html>