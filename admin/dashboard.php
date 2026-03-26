<?php
session_start();
require '../backend/db.php';

// 🔐 ADMIN CHECK
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_name = $_SESSION['admin_name'] ?? "Admin";

// 📊 (Replace later with DB queries)
$total_users = 150;
$active_exams = 12;
$completed_exams = 87;
$revenue = 12450;

$user_chart_data = [12, 19, 3, 5, 2, 3];
$exam_chart_data = [5, 10, 7, 12];

// 📍 ACTIVE PAGE
$page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

/* 🌄 BACKGROUND */
body {
    margin:0;
    font-family:'Poppins',sans-serif;
    background:url('../assets/bg.jpg') no-repeat center/cover fixed;
}

/* OVERLAY */
body::before {
    content:"";
    position:fixed;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.45);
    z-index:-1;
}

/* 🌙 DARK MODE */
body.dark { background:#121212; }

/* 📌 SIDEBAR */
.sidebar {
    position: fixed;
    width: 240px;
    height: 100%;
    background: rgba(0,0,0,0.9);
    backdrop-filter: blur(10px);
    padding-top: 20px;
    transition: 0.3s;
}

.sidebar.collapsed { width:80px; }

.sidebar .logo {
    text-align:center;
    color:white;
    font-size:20px;
    margin-bottom:30px;
}

/* MENU */
.sidebar a {
    display:flex;
    align-items:center;
    gap:15px;
    padding:14px 20px;
    color:#ccc;
    text-decoration:none;
}

.sidebar a i {
    min-width:20px;
}

.sidebar a span { white-space:nowrap; }

.sidebar a:hover,
.sidebar a.active {
    background:linear-gradient(90deg,#00c6ff,#0072ff);
    color:white;
    border-radius:8px;
    margin:5px 10px;
}

.sidebar.collapsed a span { display:none; }
.sidebar.collapsed a { justify-content:center; }

/* 📦 MAIN */
.main {
    margin-left:240px;
    transition:0.3s;
    padding:20px;
}

.main.full { margin-left:80px; }

/* 🔝 TOPBAR */
.topbar {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.menu-btn {
    font-size:20px;
    cursor:pointer;
    color:white;
}

/* 👤 PROFILE */
.profile {
    position:relative;
    color:white;
    cursor:pointer;
}

.profile-box {
    position:absolute;
    right:0;
    top:45px;
    min-width:160px;

    background:rgba(255,255,255,0.15);
    backdrop-filter:blur(12px);
    border-radius:10px;
    padding:10px;

    display:none;
    z-index:999;
}

.profile-box a {
    display:block;
    padding:8px;
    color:white;
    text-decoration:none;
}

.profile-box a:hover {
    background:rgba(255,255,255,0.2);
}

.profile:hover .profile-box {
    display:block;
}

/* 📊 CARDS */
.cards {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:15px;
}

.card {
    background:rgba(255,255,255,0.15);
    padding:20px;
    border-radius:10px;
    text-align:center;
    color:white;
}

/* 📈 CHARTS */
.charts {
    margin-top:20px;
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}

canvas {
    background:rgba(255,255,255,0.15);
    padding:10px;
    border-radius:10px;
}

/* 📱 MOBILE */
@media(max-width:768px){
    .sidebar { left:-240px; }
    .sidebar.active { left:0; }
    .main { margin-left:0; }
    .charts { grid-template-columns:1fr; }
}

</style>
</head>

<body>

<!-- 📌 SIDEBAR -->
<div class="sidebar" id="sidebar">

    <div class="logo">🎓 ADMIN</div>

    <a href="dashboard.php" class="<?= ($page=='dashboard.php')?'active':'' ?>">
        <i class="fas fa-home"></i><span>Dashboard</span>
    </a>

    <a href="add_exam.php" class="<?= ($page=='add_exam.php')?'active':'' ?>">
        <i class="fas fa-plus"></i><span>Add Exam</span>
    </a>

    <a href="add_question.php" class="<?= ($page=='add_question.php')?'active':'' ?>">
        <i class="fas fa-question"></i><span>Add Questions</span>
    </a>

    <a href="manage_users.php" class="<?= ($page=='manage_users.php')?'active':'' ?>">
        <i class="fas fa-users"></i><span>Manage Users</span>
    </a>

    <a href="view_results.php" class="<?= ($page=='view_results.php')?'active':'' ?>">
        <i class="fas fa-chart-line"></i><span>Results</span>
    </a>

    <a href="../logout.php">
        <i class="fas fa-sign-out-alt"></i><span>Logout</span>
    </a>

</div>

<!-- 📦 MAIN -->
<div class="main" id="main">

<!-- 🔝 TOPBAR -->
<div class="topbar">

    <div class="menu-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>

    <button onclick="toggleDark()">🌙</button>

    <div class="profile">
        <i class="fas fa-user-shield"></i> <?= htmlspecialchars($admin_name) ?>
        <i class="fas fa-chevron-down"></i>

        <div class="profile-box">
            <a href="#">Admin Panel</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

</div>

<h2 style="color:white;">Welcome, <?= htmlspecialchars($admin_name) ?> 👋</h2>

<!-- 📊 CARDS -->
<div class="cards">
    <div class="card"><h3>Total Users</h3><p><?= $total_users ?></p></div>
    <div class="card"><h3>Active Exams</h3><p><?= $active_exams ?></p></div>
    <div class="card"><h3>Completed</h3><p><?= $completed_exams ?></p></div>
    <div class="card"><h3>Revenue</h3><p>$<?= $revenue ?></p></div>
</div>

<!-- 📈 CHARTS -->
<div class="charts">
    <canvas id="userChart"></canvas>
    <canvas id="examChart"></canvas>
</div>

</div>

<script>

function toggleSidebar(){
    let sidebar = document.getElementById("sidebar");
    let main = document.getElementById("main");

    if (window.innerWidth <= 768) {
        sidebar.classList.toggle("active");
    } else {
        sidebar.classList.toggle("collapsed");
        main.classList.toggle("full");
    }
}

function toggleDark(){
    document.body.classList.toggle("dark");
}

// 📊 USER CHART
new Chart(document.getElementById('userChart'), {
    type:'line',
    data:{
        labels:['Jan','Feb','Mar','Apr','May','Jun'],
        datasets:[{
            label:'Users',
            data: <?= json_encode($user_chart_data) ?>,
            borderWidth:2
        }]
    }
});

// 📊 EXAM CHART
new Chart(document.getElementById('examChart'), {
    type:'bar',
    data:{
        labels:['Test1','Test2','Test3','Test4'],
        datasets:[{
            data: <?= json_encode($exam_chart_data) ?>
        }]
    }
});

</script>

</body>
</html>