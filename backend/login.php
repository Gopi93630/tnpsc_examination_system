<?php
session_start();
include("db.php");

$error = ""; // for error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password, $role);

    if ($stmt->fetch()) {
        if (password_verify($password, $hashed_password)) {

            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;

            // Redirect based on role
            if ($role == "admin") {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../user/dashboard.php");
            }
            exit();

        } else {
            $error = "Invalid Password";
        }
    } else {
        $error = "User not found";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - TNPSC Examination System</title>
    <!-- Link shared CSS -->
    <link rel="stylesheet" href="assets/style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="form-box">
    <h2>Login</h2>

    <!-- Show error from PHP -->
    <?php if($error != "") { ?>
        <div class="success" style="background-color:#f8d7da; color:#721c24;"><?php echo $error; ?></div>
    <?php } else { ?>
        <div id="success-msg" class="success" style="display:none;"></div>
    <?php } ?>

    <form action="" method="POST" onsubmit="return validateLogin()">
        <div class="input-group">
            <i class="fa fa-envelope"></i>
            <input type="email" id="login_email" name="email" placeholder="Enter Email" required>
        </div>

        <div class="input-group">
            <i class="fa fa-lock"></i>
            <input type="password" id="login_password" name="password" placeholder="Enter Password" required>
            <i class="fa fa-eye" id="toggleLoginPass" onclick="togglePassword('login_password','toggleLoginPass')"></i>
        </div>

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>

<!-- Link shared JS -->
<script src="assets/script.js"></script>

</body>
</html>