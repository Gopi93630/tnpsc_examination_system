<?php
session_start();
include("../backend/db.php");

// 🔐 USER CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['user_name'] ?? "User";

// 📊 FETCH RESULTS
$result = $conn->query("SELECT * FROM results WHERE user_id='$user_id' ORDER BY date DESC");

// Prepare data for chart
$labels = [];
$scores = [];

$total_exams = 0;
$total_score = 0;
$best_score = 0;

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['date'];
    $scores[] = $row['score'];

    $total_exams++;
    $total_score += $row['score'];
    if ($row['score'] > $best_score) {
        $best_score = $row['score'];
    }
}

$avg_score = $total_exams > 0 ? round($total_score / $total_exams, 2) : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Results</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: url('../assets/bg.jpg') no-repeat center/cover fixed;
        }

        /* overlay */
        body::before {
            content:"";
            position:fixed;
            width:100%;
            height:100%;
            background:rgba(0,0,0,0.6);
            z-index:-1;
        }

        /* sidebar */
        .sidebar {
            position: fixed;
            width: 220px;
            height: 100%;
            background: rgba(0,0,0,0.9);
            padding-top: 20px;
        }

        .sidebar a {
            display:flex;
            gap:10px;
            padding:14px;
            color:#ccc;
            text-decoration:none;
        }

        .sidebar a.active,
        .sidebar a:hover {
            background:#00c6ff;
            color:white;
        }

        /* main */
        .main {
            margin-left:220px;
            padding:20px;
        }

        /* topbar */
        .topbar {
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:20px;
            color:white;
        }

        /* summary cards */
        .cards {
            display:grid;
            grid-template-columns: repeat(auto-fit,minmax(200px,1fr));
            gap:15px;
            margin-bottom:20px;
        }

        .card {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(12px);
            padding:20px;
            border-radius:12px;
            color:white;
            text-align:center;
        }

        .card h3 {
            margin:10px 0;
        }

        /* table */
        .table-box {
            max-width: 900px;
            margin: auto;
            background: white;
            color: black;
            padding: 20px;
            border-radius: 10px;
            overflow-x:auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        table th {
            background: #28a745;
            color: white;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: white;
            text-decoration: none;
        }

        canvas {
            background: white;
            border-radius: 10px;
            padding: 10px;
        }

        @media(max-width:768px){
            .sidebar{
                width:100%;
                height:auto;
                position:relative;
            }
            .main{
                margin-left:0;
            }
            .cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<!-- 📌 SIDEBAR -->
<div class="sidebar">
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="instructions.php"><i class="fas fa-book"></i> Instructions</a>
    <a href="result.php" class="active"><i class="fas fa-chart-line"></i> Results</a>
    <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- 📦 MAIN -->
<div class="main">

<div class="topbar">
    <h2>📊 Your Results</h2>
    <div>👤 <?= htmlspecialchars($name) ?></div>
</div>

<!-- 🔹 SUMMARY CARDS -->
<div class="cards">
    <div class="card">
        <h3>Total Exams</h3>
        <p><?= $total_exams ?></p>
    </div>
    <div class="card">
        <h3>Average Score</h3>
        <p><?= $avg_score ?></p>
    </div>
    <div class="card">
        <h3>Best Score</h3>
        <p><?= $best_score ?></p>
    </div>
</div>

<!-- 🔹 RESULTS TABLE -->
<div class="table-box">
    <?php if($total_exams > 0): ?>
        <table>
            <tr>
                <th>Exam ID</th>
                <th>Score</th>
                <th>Total</th>
                <th>Date</th>
            </tr>
            
            <?php
            
            $result2 = $conn->query("SELECT * FROM results WHERE user_id='$user_id' ORDER BY date DESC");
            
            while($row = $result2->fetch_assoc()){
                echo "<tr>
                        <td>{$row['exam_id']}</td>
                        <td>{$row['score']}</td>
                        <td>{$row['total']}</td>
                        <td>{$row['date']}</td>
                      </tr>";
            }
            ?>
        </table>

        <!-- 📈 Chart -->
        <canvas id="resultChart"></canvas>
    <?php else: ?>
        <p style="text-align:center; font-size:18px;">You have not taken any exams yet.</p>
    <?php endif; ?>
</div>

<a href="dashboard.php" class="back">⬅ Back</a>

</div>

<script>
let labels = <?php echo json_encode($labels); ?>;
let scores = <?php echo json_encode($scores); ?>;

const ctx = document.getElementById('resultChart');

if(labels.length > 0){
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Score',
                data: scores,
                borderWidth: 2,
                tension: 0.3,
                backgroundColor: 'rgba(0,198,255,0.2)',
                borderColor: '#00c6ff',
                fill:true,
                pointBackgroundColor:'#28a745',
            }]
        },
        options: {
            responsive: true,
            plugins:{
                legend:{display:true},
            },
            scales:{
                y:{
                    beginAtZero:true,
                    suggestedMax: Math.max(...scores)+2
                }
            }
        }
    });
}
</script>

</body>
</html>