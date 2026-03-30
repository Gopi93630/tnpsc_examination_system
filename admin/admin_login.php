<?php
session_start();

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | TNPSC Examination System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: url('../assets/bg1.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            color: #fff;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.50);
            z-index: -1;
        }

        .page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 16px;
        }

        .login-layout {
            width: 100%;
            max-width: 1100px;
            display: grid;
            grid-template-columns: 1fr 430px;
            gap: 24px;
            align-items: stretch;
        }

        .info-panel,
        .login-card {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(14px);
            border-radius: 22px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.22);
        }

        .info-panel {
            padding: 38px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            width: fit-content;
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(255,255,255,0.14);
            font-size: 14px;
            margin-bottom: 22px;
        }

        .info-panel h1 {
            margin: 0 0 14px;
            font-size: 36px;
            line-height: 1.2;
            font-weight: 700;
        }

        .info-panel p {
            margin: 0 0 24px;
            font-size: 15px;
            line-height: 1.8;
            color: #f0f0f0;
            max-width: 560px;
        }

        .feature-list {
            display: grid;
            gap: 14px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            background: rgba(255,255,255,0.08);
            padding: 14px 16px;
            border-radius: 14px;
        }

        .feature-item i {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.12);
            font-size: 16px;
            flex-shrink: 0;
        }

        .feature-item h4 {
            margin: 0 0 4px;
            font-size: 15px;
            font-weight: 600;
        }

        .feature-item p {
            margin: 0;
            font-size: 13px;
            line-height: 1.6;
            color: #e5e5e5;
        }

        .login-card {
            padding: 34px 28px;
            align-self: center;
        }

        .login-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .login-icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 14px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.14);
            font-size: 28px;
        }

        .login-header h2 {
            margin: 0 0 8px;
            font-size: 28px;
            font-weight: 700;
        }

        .login-header p {
            margin: 0;
            font-size: 14px;
            color: #ececec;
        }

        .alert {
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 14px;
        }

        .alert.success {
            background: rgba(40, 167, 69, 0.22);
            border: 1px solid rgba(40, 167, 69, 0.40);
            color: #d8ffe0;
        }

        .alert.error {
            background: rgba(220, 53, 69, 0.22);
            border: 1px solid rgba(220, 53, 69, 0.40);
            color: #ffdbe0;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .input-group {
            position: relative;
        }

        .input-group input {
            width: 100%;
            padding: 14px 46px 14px 46px;
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 14px;
            background: rgba(255,255,255,0.10);
            color: #fff;
            outline: none;
            font-size: 14px;
            font-family: inherit;
            transition: 0.3s;
        }

        .input-group input::placeholder {
            color: #dddddd;
        }

        .input-group input:focus {
            border-color: rgba(0, 198, 255, 0.75);
            box-shadow: 0 0 0 3px rgba(0, 198, 255, 0.14);
        }

        .left-icon,
        .right-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #e5e5e5;
            font-size: 15px;
        }

        .left-icon {
            left: 16px;
        }

        .right-icon {
            right: 16px;
            cursor: pointer;
        }

        .login-btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(90deg, #00c6ff, #0072ff);
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 4px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(0, 114, 255, 0.28);
        }

        .extra-links {
            margin-top: 20px;
            display: grid;
            gap: 10px;
            text-align: center;
        }

        .extra-links a {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .extra-links a:hover {
            color: #9feaff;
        }

        .bottom-note {
            text-align: center;
            margin-top: 18px;
            font-size: 12px;
            color: #dddddd;
        }

        @media (max-width: 960px) {
            .login-layout {
                grid-template-columns: 1fr;
            }

            .info-panel {
                padding: 28px;
            }

            .info-panel h1 {
                font-size: 30px;
            }
        }

        @media (max-width: 520px) {
            .login-card {
                padding: 28px 20px;
            }

            .info-panel {
                padding: 22px;
            }

            .info-panel h1 {
                font-size: 26px;
            }

            .login-header h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <div class="login-layout">

        <!-- Left Info Panel -->
        <div class="info-panel">
            <div class="brand-badge">
                <i class="fas fa-user-shield"></i>
                <span>TNPSC Admin Portal</span>
            </div>

            <h1>Manage Your Examination System with Confidence</h1>

            <p>
                Access the admin dashboard to create exams, add questions, manage users, and monitor results.
                This panel is designed to give administrators complete control over the TNPSC Examination System.
            </p>

            <div class="feature-list">
                <div class="feature-item">
                    <i class="fas fa-file-circle-plus"></i>
                    <div>
                        <h4>Create Exams</h4>
                        <p>Add new exam titles, subjects, duration, and question limits easily.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <i class="fas fa-circle-question"></i>
                    <div>
                        <h4>Add Questions</h4>
                        <p>Build question banks with options and correct answers for each exam.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <i class="fas fa-chart-line"></i>
                    <div>
                        <h4>Track Results</h4>
                        <p>View student performance, scores, and pass / fail insights in one place.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Login Card -->
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h2>Admin Login</h2>
                <p>Sign in to continue to the admin dashboard</p>
            </div>

            <?php if (!empty($success)): ?>
                <div class="alert success">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="../backend/admin_login.php" method="POST" onsubmit="return validateLogin()">

                <div class="form-group">
                    <label class="form-label" for="login_email">Email Address</label>
                    <div class="input-group">
                        <i class="fa fa-envelope left-icon"></i>
                        <input 
                            type="email"
                            id="login_email"
                            name="email"
                            placeholder="Enter admin email"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="login_password">Password</label>
                    <div class="input-group">
                        <i class="fa fa-lock left-icon"></i>
                        <input
                            type="password"
                            id="login_password"
                            name="password"
                            placeholder="Enter password"
                            required
                        >
                        <i
                            class="fa fa-eye right-icon"
                            id="eyeAdmin"
                            onclick="togglePassword('login_password','eyeAdmin')"
                        ></i>
                    </div>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-right-to-bracket"></i> Login
                </button>
            </form>

            <div class="extra-links">
                <a href="admin_register.php">
                    <i class="fas fa-user-plus"></i> Create Admin
                </a>

                <a href="../index.php">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
            </div>

            <div class="bottom-note">
                Authorized administrators only
            </div>
        </div>

    </div>
</div>

<script>
    function togglePassword(inputId, eyeId) {
        const input = document.getElementById(inputId);
        const eye = document.getElementById(eyeId);

        if (input.type === "password") {
            input.type = "text";
            eye.classList.remove("fa-eye");
            eye.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            eye.classList.remove("fa-eye-slash");
            eye.classList.add("fa-eye");
        }
    }

    function validateLogin() {
        const email = document.getElementById("login_email").value.trim();
        const password = document.getElementById("login_password").value.trim();

        if (email === "" || password === "") {
            alert("Please fill in all fields.");
            return false;
        }

        return true;
    }
</script>

</body>
</html>