<!DOCTYPE html>
<html>
<head>
    <title>Create Admin</title>

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

        <h2>Create Admin</h2>

        <!-- SUCCESS MESSAGE -->
        <div id="success-msg" class="success" style="display:none;"></div>

        <!-- FORM -->
        <form action="../backend/register.php" method="POST" onsubmit="return validateRegister()">

            <!-- ROLE HIDDEN -->
            <input type="hidden" name="role" value="admin">

            <!-- NAME -->
            <div class="input-group">
                <i class="fa fa-user left-icon"></i>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    placeholder="Full Name" 
                    required
                >
            </div>

            <!-- EMAIL -->
            <div class="input-group">
                <i class="fa fa-envelope left-icon"></i>
                <input 
                    type="email" 
                    id="register_email" 
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
                    id="register_password" 
                    name="password" 
                    placeholder="Create Password" 
                    required
                >

                <i 
                    class="fa fa-eye right-icon" 
                    id="eyeAdminReg"
                    onclick="togglePassword('register_password','eyeAdminReg')">
                </i>
            </div>

            <!-- BUTTON -->
            <button type="submit">Create Admin</button>

        </form>

        <!-- LINKS -->
        <p style="text-align:center; margin-top:15px;">
            <a href="admin_login.php">Already have admin? Login</a>
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