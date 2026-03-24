<?php
session_start();
include("../backend/db.php");

// 🔐 USER CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 📊 FETCH TEST COUNT
$result = $conn->query("SELECT COUNT(*) AS total_tests FROM results WHERE user_id='$user_id'");
$row = $result->fetch_assoc();
$total_tests = $row['total_tests'];

// 📊 FETCH USER INFO
$user_result = $conn->query("SELECT name,email FROM users WHERE id='$user_id'");
$user_row = $user_result->fetch_assoc();
$user_name = $user_row['name'];
$user_email = $user_row['email'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* ================== DASHBOARD CARDS ================== */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin: 30px;
        }

        .card {
            background: #fff;
            padding: 25px 20px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.15);
        }

        .card h2, .card h3 {
            margin-bottom: 15px;
            color: #333;
        }

        .card p {
            font-size: 16px;
            color: #555;
        }

        /* BUTTONS INSIDE CARD */
        .card button {
            margin-top: 15px;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            background-color: #1f1f2e;
            color: #fff;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .card button:hover {
            background-color: #35354a;
        }

        /* BUTTON GROUP */
        .btn-group {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .btn-group a button {
            width: 180px;
        }
    </style>
</head>
<body>

<div class="dashboard-cards">

    <!-- 👋 WELCOME CARD -->
    <div class="card">
        <h2>Welcome, <?php echo $user_name; ?> 👋</h2>
        <p>Email: <?php echo $user_email; ?></p>
        <p>Tests Attended: <strong><?php echo $total_tests; ?></strong></p>
    </div>

    <!-- 📋 INSTRUCTION CARD -->
    <div class="card">
        <h3>📋 Instructions</h3>
        <p>Click below to view exam instructions before starting.</p>
        <a href="instructions.php"><button>View Instructions</button></a>
    </div>

    <!-- 🔘 ACTION BUTTONS CARD -->
    <div class="card">
        <h3>🚀 Start & Manage</h3>
        <div class="btn-group">
            <a href="exam.php"><button>Start Exam</button></a>
            <a href="result.php"><button>View Results</button></a>
            <a href="profile.php"><button>Profile</button></a>
            <a href="../logout.php"><button>Logout</button></a>
        </div>
    </div>

</div>

</body>
</html>