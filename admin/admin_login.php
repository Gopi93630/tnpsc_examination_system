<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/style.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

<!-- 🌑 OVERLAY -->
<div class="overlay">

    <!-- 🧊 FORM BOX -->
    <div class="form-box">

        <h2>Admin Login</h2>

        <!-- SUCCESS MESSAGE -->
        <div id="success-msg" class="success" style="display:none;"></div>

        <!-- FORM -->
        <form action="../backend/login.php" method="POST" onsubmit="return validateLogin()">

            <!-- EMAIL -->
            <div class="input-group">
                <i class="fa fa-envelope left-icon"></i>
                <input 
                    type="email" 
                    id="login_email" 
                    name="email" 
                    placeholder="Enter Email" 
                    required
                >
            </div>

            <!-- PASSWORD -->
            <div class="input-group">
                <i class="fa fa-lock left-icon"></i>
                <input 
                    type="password" 
                    id="login_password" 
                    name="password" 
                    placeholder="Enter Password" 
                    required
                >

                <i 
                    class="fa fa-eye right-icon" 
                    id="eyeAdmin"
                    onclick="togglePassword('login_password','eyeAdmin')">
                </i>
            </div>

            <!-- BUTTON -->
            <button type="submit">Login</button>

        </form>

        <!-- LINKS -->
        <p style="text-align:center; margin-top:15px;">
            <a href="admin_register.php">Create Admin</a>
        </p>

        <p style="text-align:center;">
            <a href="../index.php">⬅ Back</a>
        </p>

    </div>

</div>

<!-- JS -->
<script src="../assets/script.js"></script>

</body>
</html>