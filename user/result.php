<?php
session_start();
include("../backend/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch results
$result = $conn->query("SELECT * FROM results WHERE user_id='$user_id' ORDER BY date DESC");

// Prepare data for graph
$labels = [];
$scores = [];

while($row = $result->fetch_assoc()){
    $labels[] = $row['date'];
    $scores[] = $row['score'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Results</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/style.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: url('../assets/bg.jpg') no-repeat center center/cover;
        }

        .overlay {
            background: rgba(0,0,0,0.7);
            min-height: 100vh;
            padding: 30px;
            color: white;
        }

        h2 {
            text-align: center;
        }

        .table-box {
            max-width: 900px;
            margin: auto;
            background: white;
            color: black;
            padding: 20px;
            border-radius: 10px;
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
    </style>
</head>

<body>

<div class="overlay">

    <h2>Your Results 📊</h2>

    <div class="table-box">

        <table>
            <tr>
                <th>Exam ID</th>
                <th>Score</th>
                <th>Total</th>
                <th>Date</th>
            </tr>

            <?php
            // Re-fetch results for table
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

        <!-- 📈 Graph -->
        <canvas id="resultChart"></canvas>

    </div>

    <a href="dashboard.php" class="back">⬅ Back</a>

</div>

<script>
let labels = <?php echo json_encode($labels); ?>;
let scores = <?php echo json_encode($scores); ?>;

const ctx = document.getElementById('resultChart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Score',
            data: scores,
            borderWidth: 2,
            tension: 0.3
        }]
    },
    options: {
        responsive: true
    }
});
</script>

</body>
</html>