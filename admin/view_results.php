<?php
session_start();
include("../backend/db.php");

// 🔐 ADMIN CHECK
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// FETCH RESULTS
$query = $conn->query("
    SELECT users.name, results.exam_id, results.score, results.total, results.created_at 
    FROM results 
    JOIN users ON results.user_id = users.id
    ORDER BY results.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Results</title>

    <link rel="stylesheet" href="../assets/style.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        table {
            width: 100%;
            background: rgba(255,255,255,0.1);
            border-collapse: collapse;
            color: white;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            text-align: center;
        }

        th {
            background: rgba(255,255,255,0.2);
        }

        .container {
            width: 90%;
        }
    </style>
</head>

<body>

<div class="overlay">

<div class="form-box container">

    <h2>All User Results</h2>

    <!-- 📊 TABLE -->
    <table>
        <tr>
            <th>User</th>
            <th>Exam ID</th>
            <th>Score</th>
            <th>Total</th>
            <th>Date</th>
        </tr>

        <?php 
        $scores = [];
        $labels = [];

        while($row = $query->fetch_assoc()) { 
            $scores[] = $row['score'];
            $labels[] = $row['name'];
        ?>
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['exam_id']; ?></td>
            <td><?php echo $row['score']; ?></td>
            <td><?php echo $row['total']; ?></td>
            <td><?php echo $row['created_at']; ?></td>
        </tr>
        <?php } ?>
    </table>

    <br>

    <!-- 📈 GRAPH -->
    <canvas id="resultChart"></canvas>

    <br>

    <!-- BACK -->
    <p style="text-align:center;">
        <a href="dashboard.php">⬅ Back to Dashboard</a>
    </p>

</div>

</div>

<script>
let ctx = document.getElementById('resultChart').getContext('2d');

let chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            label: 'Scores',
            data: <?php echo json_encode($scores); ?>,
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

</body>
</html>