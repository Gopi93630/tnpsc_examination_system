<?php
session_start();
require '../backend/db.php';

// Check if admin is logged in
if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

// Dummy stats - replace with DB queries
$total_users = 150;
$active_exams = 12;
$completed_exams = 87;
$revenue = 12450;

// For charts (can be dynamic using DB queries)
$user_chart_data = [12, 19, 3, 5, 2, 3];
$exam_chart_data = [5, 10, 7, 12];

$admin_name = $_SESSION['admin_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | TNPSC Exam</title>
    <link rel="stylesheet" href="../assets/style.css">
    <script src="../assets/chart.min.js"></script>
</head>
<body>
<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <h2>TNPSC Admin</h2>
        </div>
        <ul class="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="add_exam.php">Add Exam</a></li>
            <li><a href="add_question.php">Add Questions</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="view_results.php">View Results</a></li>
            <li><a href="../backend/logout.php">Logout</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header>
            <h1>Welcome, <?php echo $admin_name; ?> 👋</h1>
        </header>

        <!-- Cards -->
        <div class="cards">
            <div class="card">
                <h3>Total Users</h3>
                <p><?php echo $total_users; ?></p>
            </div>
            <div class="card">
                <h3>Active Exams</h3>
                <p><?php echo $active_exams; ?></p>
            </div>
            <div class="card">
                <h3>Completed Exams</h3>
                <p><?php echo $completed_exams; ?></p>
            </div>
            <div class="card">
                <h3>Revenue</h3>
                <p>$<?php echo $revenue; ?></p>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts">
            <canvas id="userChart"></canvas>
            <canvas id="examChart"></canvas>
        </div>
    </main>
</div>

<script>
const ctxUser = document.getElementById('userChart').getContext('2d');
const userChart = new Chart(ctxUser, {
    type: 'line',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun'],
        datasets: [{
            label: 'Users Registered',
            data: <?php echo json_encode($user_chart_data); ?>,
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.3
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'top' } } }
});

const ctxExam = document.getElementById('examChart').getContext('2d');
const examChart = new Chart(ctxExam, {
    type: 'bar',
    data: {
        labels: ['Test 1','Test 2','Test 3','Test 4'],
        datasets: [{
            label: 'Completed Exams',
            data: <?php echo json_encode($exam_chart_data); ?>,
            backgroundColor: ['#FF6384','#36A2EB','#FFCE56','#4BC0C0']
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});
</script>
</body>
</html>