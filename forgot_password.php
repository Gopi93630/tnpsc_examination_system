<?php
include("backend/db.php");
?>

<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="form-box">
    <h2>Forgot Password</h2>

    <form action="backend/forgot_password.php" method="POST">
        <input type="email" name="email" placeholder="Enter your email" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <button type="submit">Reset Password</button>
    </form>
</div>

</body>
</html>