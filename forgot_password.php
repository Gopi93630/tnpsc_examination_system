

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>

    <!-- CSS -->
    <link rel="stylesheet" href="assets/style.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* 🌄 BACKGROUND IMAGE (FORCE APPLY) */
        body {
            background: url('assets/bg.jpg') no-repeat center center/cover;
        }
    </style>
</head>

<body>

<!-- 🌑 OVERLAY -->
<div class="overlay">

    <!-- 🧊 FORM BOX -->
    <div class="form-box">

        <h2>Forgot Password</h2>

        <!-- ❌ ERROR -->
        <div id="error-msg" class="error" style="display:none;"></div>

        <!-- FORM -->
        <form action="backend/send_reset_link.php" method="POST" onsubmit="return validateReset()">

            <!-- EMAIL -->
            <div class="input-group">
                <i class="fa fa-envelope left-icon"></i>
                <input type="email" id="email" name="email" placeholder="Enter Email" required>
            </div>

            <!-- PASSWORD -->
            <div class="input-group">
                <i class="fa fa-lock left-icon"></i>
                <input type="password" id="password" name="password" placeholder="New Password" required>

                <i class="fa fa-eye right-icon" id="eye1"
                   onclick="togglePassword('password','eye1')"></i>
            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="input-group">
                <i class="fa fa-lock left-icon"></i>
                <input type="password" id="confirm_password" placeholder="Confirm Password" required>
            </div>

            <!-- BUTTON -->
            <button type="submit">Send Reset Link</button>

        </form>

        <!-- BACK -->
        <p style="text-align:center; margin-top:15px;">
            <a href="login.php">⬅ Back to Login</a>
        </p>

    </div>

</div>

<!-- JS -->
<script src="assets/script.js"></script>

</body>
</html>