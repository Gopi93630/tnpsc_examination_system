<?php
session_start();

// 🔐 USER CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Exam Instructions</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/style.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        .instructions {
            text-align: left;
            line-height: 1.8;
        }

        .start-btn {
            margin-top: 20px;
        }
    </style>
</head>

<body>

<!-- 🌑 OVERLAY -->
<div class="overlay">

    <!-- 🧊 GLASS BOX -->
    <div class="form-box">

        <h2>📋 Exam Instructions</h2>

        <div class="instructions">

            <p>👉 Read all instructions carefully before starting the exam.</p>

            <ul>
                <li>📝 Each exam contains 10 questions.</li>
                <li>⏱️ Time limit is 5 minutes per subject.</li>
                <li>✅ Each correct answer gives 1 mark.</li>
                <li>❌ No negative marking.</li>
                <li>🔒 Do not refresh the page during the exam.</li>
                <li>📊 Results will be shown immediately after submission.</li>
            </ul>

        </div>

        <!-- START BUTTON -->
        <a href="exam.php">
            <button class="start-btn">🚀 Start Exam</button>
        </a>

        <!-- BACK -->
        <p style="text-align:center; margin-top:15px;">
            <a href="dashboard.php">⬅ Back to Dashboard</a>
        </p>

    </div>

</div>

</body>
</html>