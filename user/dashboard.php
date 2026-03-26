<?php
session_start();
include("../backend/db.php");


if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 👤 USER
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$name = $stmt->get_result()->fetch_assoc()['name'] ?? "User";

// 📊 DATA
$res = $conn->query("SELECT score, total, created_at FROM results WHERE user_id='$user_id' ORDER BY created_at DESC LIMIT 5");

$scores=[]; $labels=[]; $history=[]; $i=1;
while($r = $res->fetch_assoc()){
    $scores[] = $r['score'];
    $labels[] = "Test ".$i++;
    $history[] = $r;
}

// 📍 ACTIVE PAGE
$page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

/* 🌄 BACKGROUND */
body {
    margin:0;
    font-family:'Poppins',sans-serif;
    background:url('../assets/bg3.png') no-repeat center/cover fixed;
}

/* 🔥 OVERLAY */
body::before {
    content:"";
    position:fixed;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.45);
    z-index:-1;
}

/* 🌙 DARK MODE */
body.dark {
    background:#121212;
}

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

.sidebar.collapsed {
    width: 80px;
}

.sidebar .logo {
    text-align: center;
    color: white;
    font-size: 20px;
    margin-bottom: 30px;
}

/* MENU */
.sidebar a {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 14px 20px;
    color: #ccc;
    text-decoration: none;
    font-size: 15px;
    transition: 0.3s;
}

.sidebar a i {
    font-size: 18px;
    min-width: 20px;
    text-align: center;
}

.sidebar a span {
    white-space: nowrap;
}

.sidebar a:hover,
.sidebar a.active {
    background: linear-gradient(90deg, #00c6ff, #0072ff);
    color: white;
    border-radius: 8px;
    margin: 5px 10px;
}

.sidebar.collapsed a span {
    display: none;
}

.sidebar.collapsed a {
    justify-content: center;
}

/* 📦 MAIN */
.main {
    margin-left: 240px;
    transition: 0.3s;
    padding: 20px;
}

.main.full {
    margin-left: 80px;
}

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
    cursor:pointer;
    color:white;
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

/* 📦 CONTENT */
.container {
    background:rgba(255,255,255,0.12);
    padding:20px;
    border-radius:12px;
    backdrop-filter:blur(10px);
    color:white;
}

/* FEATURES */
.features {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:15px;
}

.feature-box {
    background:rgba(255,255,255,0.15);
    padding:15px;
    border-radius:10px;
    text-align:center;
}

/* CARD */
.card {
    margin-top:20px;
    padding:20px;
    border-radius:10px;
    background:rgba(255,255,255,0.15);
}

/* MOBILE */
@media(max-width:768px){
    .sidebar { left:-240px; }
    .sidebar.active { left:0; }
    .main { margin-left:0; }
}

</style>
</head>

<body>

<!-- 📌 SIDEBAR -->
<div class="sidebar" id="sidebar">

    <div class="logo">🎓 TNPSC</div>

    <a href="dashboard.php" class="<?= ($page=='dashboard.php')?'active':'' ?>">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
    </a>

    <a href="instructions.php" class="<?= ($page=='instructions.php')?'active':'' ?>">
        <i class="fas fa-play"></i>
        <span>Start Exam</span>
    </a>

    <a href="result.php" class="<?= ($page=='result.php')?'active':'' ?>">
        <i class="fas fa-chart-line"></i>
        <span>Results</span>
    </a>

    <a href="profile.php" class="<?= ($page=='profile.php')?'active':'' ?>">
        <i class="fas fa-user"></i>
        <span>Profile</span>
    </a>

    <a href="../logout.php">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
    </a>

</div>

<!-- 📦 MAIN -->
<div class="main" id="main">

<div class="topbar">
    <div class="menu-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>

    <button onclick="toggleDark()">🌙</button>

    <div class="profile">
        <i class="fas fa-user-circle"></i> <?= htmlspecialchars($name) ?>
        <i class="fas fa-chevron-down"></i>

        <div class="profile-box">
            <a href="profile.php">Profile</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>
</div>

<div class="container">

<h2>Welcome, <?= htmlspecialchars($name) ?> 👋</h2>

<p>Practice TNPSC exams and track your performance.</p>

<div class="features">
    <div class="feature-box"><i class="fas fa-book"></i><p>Online Exams</p></div>
    <div class="feature-box"><i class="fas fa-chart-bar"></i><p>Results</p></div>
    <div class="feature-box"><i class="fas fa-chart-line"></i><p>Performance</p></div>
    <div class="feature-box"><i class="fas fa-lock"></i><p>Secure</p></div>
</div>


<div class="card">
    
<canvas id="chart"></canvas>
</div>


<div class="card">
<?php
if (!empty($history)) {
    foreach($history as $h){
        echo "<p>{$h['score']}/{$h['total']} - {$h['created_at']}</p>";
    }
} else {
    echo "<p>No history available</p>";
}
?>
</div>

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

new Chart(document.getElementById('chart'), {
    type:'line',
    data:{
        labels: <?= json_encode($labels) ?>,
        datasets:[{
            data: <?= json_encode($scores) ?>,
            borderWidth:2
        }]
    }
});

</script>

</body>
</html>