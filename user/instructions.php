<?php
session_start();

// 🔐 USER CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$name = $_SESSION['user_name'] ?? "User";
?>

<!DOCTYPE html>
<html>
<head>
<title>Exam Instructions</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
/* SAME CSS */
body{
    margin:0;
    font-family:'Poppins',sans-serif;
    background:url('../assets/bg.jpg') no-repeat center/cover fixed;
}
body::before{
    content:"";
    position:fixed;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.5);
    z-index:-1;
}
.sidebar{
    position:fixed;
    width:220px;
    height:100%;
    background:rgba(0,0,0,0.9);
    padding-top:20px;
}
.sidebar a{
    display:flex;
    gap:10px;
    padding:14px;
    color:#ccc;
    text-decoration:none;
}
.sidebar a.active,
.sidebar a:hover{
    background:#00c6ff;
    color:white;
}
.main{
    margin-left:220px;
    padding:20px;
}
.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
    color:white;
}
.card{
    background:rgba(255,255,255,0.15);
    backdrop-filter:blur(12px);
    padding:25px;
    border-radius:15px;
    color:white;
    max-width:800px;
    margin:auto;
}
.instructions li{
    margin:10px 0;
}
.start-btn{
    margin-top:20px;
    padding:12px 25px;
    border:none;
    border-radius:30px;
    background:#28a745;
    color:white;
    cursor:pointer;
}
</style>
</head>

<body>

<div class="sidebar">
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="instructions.php" class="active"><i class="fas fa-book"></i> Instructions</a>
    <a href="result.php"><i class="fas fa-chart-line"></i> Results</a>
    <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">

<div class="topbar">
    <h2>📋 Exam Instructions</h2>
    <div>👤 <?= htmlspecialchars($name) ?></div>
</div>

<div class="card">

    <h3>Before You Start</h3>

    <ul class="instructions">
        <li>📝 Each exam contains <b>10 questions</b></li>
        <li>⏱️ Time limit is <b>5 minutes</b></li>
        <li>✅ Each correct answer = <b>1 mark</b></li>
        <li>❌ No negative marking</li>
        <li>🔒 Do not refresh or switch tabs</li>
        <li>📊 Result will be shown instantly</li>
    </ul>

    <!-- ✅ ONLY BUTTON -->
    <button class="start-btn" onclick="startExam()">🚀 Start Exam</button>

</div>

</div>

<script>
function startExam(){
    window.location.href = "exam.php";
}
</script>

</body>
</html>