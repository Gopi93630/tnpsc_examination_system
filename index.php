<!DOCTYPE html>
<html>
<head>
    <title>TNPSC Examination System</title>

    <!-- CSS -->
    <link rel="stylesheet" href="assets/style.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        .role-box {
            text-align: center;
        }

        .role-btn {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            font-size: 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .user-btn {
            background: linear-gradient(45deg, #28a745, #218838);
            color: white;
        }

        .admin-btn {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
        }

        .role-btn:hover {
            transform: scale(1.05);
        }

        h2 {
            margin-bottom: 20px;
        }

        .icon {
            margin-right: 8px;
        }
    </style>
</head>

<body>

<!-- 🌑 OVERLAY -->
<div class="overlay">

    <!-- 🧊 GLASS BOX -->
    <div class="form-box role-box">

        <h2>Welcome to TNPSC Examination System</h2>

        <p style="margin-bottom:15px;">Select your role to continue</p>

        <!-- USER -->
        <a href="login.php">
            <button class="role-btn user-btn">
                <i class="fa fa-user icon"></i> User
            </button>
        </a>

        <!-- ADMIN -->
        <a href="admin/admin_login.php">
            <button class="role-btn admin-btn">
                <i class="fa fa-user-shield icon"></i> Admin
            </button>
        </a>

    </div>

</div>

</body>
</html>