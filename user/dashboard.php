<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: ../index.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/style.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('../assets/bg.jpg') no-repeat center center/cover;
            height: 100vh;
        }

        .overlay {
            background: rgba(0,0,0,0.6);
            height: 100%;
            padding: 30px;
            color: white;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
        }

        .stats {
            text-align: center;
            margin-bottom: 30px;
            font-size: 18px;
        }

        .dashboard {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            max-width: 600px;
            margin: auto;
        }

        .card {
            background: white;
            color: black;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            transition: 0.3s;
            cursor: pointer;
        }

        .card i {
            font-size: 30px;
            margin-bottom: 10px;
            color: #28a745;
        }

        .card:hover {
            transform: scale(1.05);
            background: #28a745;
            color: white;
        }

        .card:hover i {
            color: white;
        }

        a {
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="overlay">

    <!-- HEADER -->
    <div class="header">
        <h1>Welcome User 👋</h1>
    </div>

    <!-- STATS -->
    <div class="stats">
        Tests Attended: 5
    </div>

    <!-- DASHBOARD CARDS -->
    <div class="dashboard">

        <a href="exam.php">
            <div class="card">
                <i class="fa fa-play"></i>
                <h3>Start Exam</h3>
            </div>
        </a>

        <a href="result.php">
            <div class="card">
                <i class="fa fa-chart-bar"></i>
                <h3>View Results</h3>
            </div>
        </a>

        <a href="profile.php">
            <div class="card">
                <i class="fa fa-user"></i>
                <h3>Profile</h3>
            </div>
        </a>

        <a href="../logout.php">
            <div class="card">
                <i class="fa fa-sign-out-alt"></i>
                <h3>Logout</h3>
            </div>
        </a>

    </div>
</div></body>
</html>