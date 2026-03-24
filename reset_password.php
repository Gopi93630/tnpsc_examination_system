<?php
include("backend/db.php");

$token = $_GET['token'];

$result = $conn->query("SELECT * FROM users WHERE reset_token='$token'");

if($result->num_rows == 0){
    die("Invalid Token!");
}

?>

<form method="POST" action="backend/update_password.php">
    <input type="hidden" name="token" value="<?php echo $token; ?>">
    <input type="password" name="new_password" placeholder="New Password">
    <button type="submit">Update Password</button>
</form>