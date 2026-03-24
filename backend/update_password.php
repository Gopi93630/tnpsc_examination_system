<?php
include("db.php");

$token = $_POST['token'];
$new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

$conn->query("UPDATE users 
SET password='$new_password', reset_token=NULL 
WHERE reset_token='$token'");

echo "<script>alert('Password Updated!'); window.location.href='../login.php';</script>";
?>