<!DOCTYPE html>
<html>
<head>
    <title>Register</title>

    <!-- ✅ ADD HERE -->
    <link rel="stylesheet" href="assets/style.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('assets/bg.jpg') no-repeat center center/cover;
        }

        .overlay {
            background: rgba(0,0,0,0.7);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body>

<div class="overlay">

    <div class="form-box">

        <h2>Register</h2>

        <!-- Success Message -->
        <div id="success-msg" class="success" style="display:none;"></div>

        <!-- REGISTER FORM -->
        <form action="backend/register.php" method="POST" onsubmit="return validateRegister()">

            <!-- NAME -->
            <div class="input-group">
                <i class="fa fa-user left-icon"></i>
                <input type="text" name="name" placeholder="Full Name">
            </div>

            <!-- EMAIL -->
            <div class="input-group">
                <i class="fa fa-envelope left-icon"></i>
                <input type="email" name="email" placeholder="Enter Email">
            </div>

            <!-- PASSWORD -->
            <div class="input-group">

                <i class="fa fa-lock left-icon"></i>

                <input type="password" id="login_password" name="password" placeholder="Enter Password">

                <i class="fa fa-eye right-icon" id="eye1"
                onclick="togglePassword('login_password','eye1')"></i>

            </div>

            <!-- BUTTON -->
            <button type="submit">Register</button>

        </form>

        <!-- LOGIN LINK -->
        <p style="text-align:center; margin-top:10px;">
            Already have an account?
            <a href="login.php">Login here</a>
        </p>

    </div>

</div>

<script src="assets/script.js"></script>

</body>
</html>