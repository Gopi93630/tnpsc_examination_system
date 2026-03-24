<?php
include 'db.php';

$error = ""; // store any PHP error

if(isset($_POST['name'], $_POST['email'], $_POST['password'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // hash password

    // Check if email already exists
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if($check->num_rows > 0){
        $error = "Email already exists!";
    } else {
        // Insert new user
        $sql = "INSERT INTO users(name,email,password) VALUES('$name','$email','$password')";
        if($conn->query($sql)){
            // ✅ Auto redirect after success
            echo "<script>
                    alert('Registration Successful! Please login.');
                    window.location.href='../index.html';
                  </script>";
            exit();
        } else {
            $error = "Error! Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - TNPSC Examination System</title>
    <!-- Shared CSS -->
    <link rel="stylesheet" href="assets/style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="form-box">
    <h2>Register</h2>

    <!-- Show PHP error -->
    <?php if($error != "") { ?>
        <div class="success" style="background-color:#f8d7da; color:#721c24;"><?php echo $error; ?></div>
    <?php } else { ?>
        <div id="success-msg" class="success" style="display:none;"></div>
    <?php } ?>

    <form action="" method="POST" onsubmit="return validateRegister()">
        <div class="input-group">
            <i class="fa fa-user"></i>
            <input type="text" id="name" name="name" placeholder="Full Name" required>
        </div>

        <div class="input-group">
            <i class="fa fa-envelope"></i>
            <input type="email" id="register_email" name="email" placeholder="Enter Email" required>
        </div>

        <div class="input-group">
            <i class="fa fa-lock"></i>
            <input type="password" id="register_password" name="password" placeholder="Create Password" required>
            <i class="fa fa-eye" id="toggleRegPass" onclick="togglePassword('register_password','toggleRegPass')"></i>
        </div>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="index.html">Login here</a></p>
</div>

<!-- Shared JS -->
<script src="assets/script.js"></script>

</body>
</html>