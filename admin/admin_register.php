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
    <title>Admin Register | TNPSC Examination</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: url('../assets/bg1.png') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            color: #fff;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: -1;
        }

        .page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .register-layout {
            width: 100%;
            max-width: 1100px;
            display: grid;
            grid-template-columns: 1fr 460px;
            gap: 24px;
        }

        .info-panel,
        .register-card {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(14px);
            border-radius: 22px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        }

        .info-panel {
            padding: 36px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(255,255,255,0.14);
            margin-bottom: 20px;
            font-size: 14px;
            width: fit-content;
        }

        .info-panel h1 {
            margin: 0 0 12px;
            font-size: 34px;
            line-height: 1.25;
        }

        .info-panel p {
            margin: 0 0 24px;
            color: #ececec;
            line-height: 1.8;
            font-size: 15px;
        }

        .feature-list {
            display: grid;
            gap: 14px;
        }

        .feature-item {
            display: flex;
            gap: 14px;
            padding: 14px;
            border-radius: 14px;
            background: rgba(255,255,255,0.08);
        }

        .feature-item i {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: rgba(255,255,255,0.12);
            flex-shrink: 0;
        }

        .feature-item h4 {
            margin: 0 0 4px;
            font-size: 15px;
        }

        .feature-item p {
            margin: 0;
            font-size: 13px;
            line-height: 1.6;
        }

        .register-card {
            padding: 30px 24px;
        }

        .register-header {
            text-align: center;
            margin-bottom: 22px;
        }

        .register-icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 14px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.14);
            font-size: 28px;
        }

        .register-header h2 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .register-header p {
            margin: 0;
            color: #ececec;
            font-size: 14px;
        }

        .alert {
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .alert.success {
            background: rgba(40, 167, 69, 0.22);
            border: 1px solid rgba(40, 167, 69, 0.45);
            color: #d4ffd9;
        }

        .alert.error {
            background: rgba(220, 53, 69, 0.22);
            border: 1px solid rgba(220, 53, 69, 0.45);
            color: #ffd7dc;
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
            font-size: 14px;
            outline: none;
        }

        .input-group input::placeholder {
            color: #ddd;
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
        }

        .left-icon {
            left: 16px;
        }

        .right-icon {
            right: 16px;
            cursor: pointer;
        }

        .register-btn {
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
        }

        .register-btn:hover {
            transform: translateY(-2px);
        }

        .extra-links {
            margin-top: 18px;
            display: grid;
            gap: 10px;
            text-align: center;
        }

        .extra-links a {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
        }

        .extra-links a:hover {
            color: #9feaff;
        }

        .small-note {
            text-align: center;
            margin-top: 14px;
            font-size: 12px;
            color: #ddd;
        }

        @media (max-width: 900px) {
            .register-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 500px) {
            .info-panel,
            .register-card {
                padding: 22px;
            }

            .info-panel h1 {
                font-size: 26px;
            }

            .register-header h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <div class="register-layout">

        <div class="info-panel">
            <div class="badge">
                <i class="fas fa-user-shield"></i>
                <span>TNPSC Admin Registration</span>
            </div>

            <h1>Create a New Admin Account</h1>
            <p>
                Register a secure administrator account to manage exams, questions, users,
                and results in your TNPSC Examination System.
            </p>

            <div class="feature-list">
                <div class="feature-item">
                    <i class="fas fa-lock"></i>
                    <div>
                        <h4>Secure Access</h4>
                        <p>Create admin credentials with encrypted password storage.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <i class="fas fa-users-cog"></i>
                    <div>
                        <h4>System Control</h4>
                        <p>Admins can control exams, users, and dashboard analytics.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <i class="fas fa-chart-pie"></i>
                    <div>
                        <h4>Management Dashboard</h4>
                        <p>Access premium UI tools to monitor the full examination workflow.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="register-card">
            <div class="register-header">
                <div class="register-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h2>Admin Register</h2>
                <p>Create your admin account</p>
            </div>

            <?php if (!empty($success)): ?>
                <div class="alert success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="../backend/admin_register.php" method="POST" onsubmit="return validateRegister()">

                <div class="form-group">
                    <label class="form-label" for="admin_name">Full Name</label>
                    <div class="input-group">
                        <i class="fa fa-user left-icon"></i>
                        <input
                            type="text"
                            id="admin_name"
                            name="name"
                            placeholder="Enter full name"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="admin_email">Email</label>
                    <div class="input-group">
                        <i class="fa fa-envelope left-icon"></i>
                        <input
                            type="email"
                            id="admin_email"
                            name="email"
                            placeholder="Enter admin email"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="admin_password">Password</label>
                    <div class="input-group">
                        <i class="fa fa-lock left-icon"></i>
                        <input
                            type="password"
                            id="admin_password"
                            name="password"
                            placeholder="Enter password"
                            required
                        >
                        <i
                            class="fa fa-eye right-icon"
                            id="eyePassword"
                            onclick="togglePassword('admin_password','eyePassword')"
                        ></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm Password</label>
                    <div class="input-group">
                        <i class="fa fa-lock left-icon"></i>
                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            placeholder="Confirm password"
                            required
                        >
                        <i
                            class="fa fa-eye right-icon"
                            id="eyeConfirm"
                            onclick="togglePassword('confirm_password','eyeConfirm')"
                        ></i>
                    </div>
                </div>

                <button type="submit" class="register-btn">
                    <i class="fas fa-user-plus"></i> Create Admin
                </button>
            </form>

            <div class="extra-links">
                <a href="admin_login.php"><i class="fas fa-right-to-bracket"></i> Already have an admin account?</a>
                <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
            </div>

            <div class="small-note">
                Admin access should be given only to authorized users
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

function validateRegister() {
    const name = document.getElementById("admin_name").value.trim();
    const email = document.getElementById("admin_email").value.trim();
    const password = document.getElementById("admin_password").value.trim();
    const confirmPassword = document.getElementById("confirm_password").value.trim();

    if (name === "" || email === "" || password === "" || confirmPassword === "") {
        alert("Please fill in all fields.");
        return false;
    }

    if (password.length < 6) {
        alert("Password must be at least 6 characters.");
        return false;
    }

    if (password !== confirmPassword) {
        alert("Password and confirm password do not match.");
        return false;
    }

    return true;
}
</script>

</body>
</html>