<?php
session_start();
include("../backend/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}

$subject = $_GET['subject'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
<title>TNPSC Exam</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

body{
    margin:0;
    font-family:'Poppins',sans-serif;
    background:url('../assets/bg.jpg') no-repeat center/cover fixed;
}

/* overlay */
.overlay{
    background:rgba(0,0,0,0.6);
    min-height:100vh;
    padding:20px;
    color:white;
}

/* layout */
.container{
    display:flex;
    gap:20px;
}

/* sidebar */
.sidebar{
    width:200px;
    background:rgba(0,0,0,0.8);
    padding:15px;
    border-radius:10px;
    height:fit-content;
}

.sidebar h3{
    text-align:center;
}

.q-nav{
    display:grid;
    grid-template-columns:repeat(5,1fr);
    gap:5px;
}

.q-nav button{
    padding:8px;
    border:none;
    border-radius:5px;
    cursor:pointer;
}

.q-nav button.answered{
    background:#28a745;
    color:white;
}

/* main */
.main{
    flex:1;
}

/* question */
.question-box{
    background:rgba(255,255,255,0.15);
    backdrop-filter:blur(10px);
    padding:20px;
    margin-bottom:15px;
    border-radius:10px;
}

.question-box label{
    display:block;
    margin:5px 0;
}

/* timer */
.timer{
    position:fixed;
    top:20px;
    right:20px;
    background:#28a745;
    padding:10px 20px;
    border-radius:10px;
}

/* submit */
.submit-btn{
    padding:15px 30px;
    border:none;
    border-radius:50px;
    background:#00c6ff;
    color:white;
    font-size:16px;
    cursor:pointer;
}

/* cards */
.card-container{
    display:flex;
    gap:20px;
    justify-content:center;
    flex-wrap:wrap;
}

.card{
    background:white;
    color:black;
    padding:20px;
    border-radius:10px;
    cursor:pointer;
}

.card:hover{
    background:#00c6ff;
    color:white;
}

/* mobile */
@media(max-width:768px){
    .container{
        flex-direction:column;
    }
    .sidebar{
        width:100%;
    }
}

</style>
</head>

<body>

<div class="overlay">

<?php if(!$subject){ ?>

<h2>Select TNPSC Group</h2>

<div class="card-container">
    <div class="card" onclick="showSubjects()">Group 1</div>
    <div class="card" onclick="showSubjects()">Group 2</div>
</div>

<div id="subjects" style="display:none;">
    <h2>Select Subject</h2>

    <div class="card-container">
        <div class="card" onclick="goExam('History')">History</div>
        <div class="card" onclick="goExam('Polity')">Polity</div>
        <div class="card" onclick="goExam('Geography')">Geography</div>
        <div class="card" onclick="goExam('Economy')">Economy</div>
    </div>
</div>

<?php } else { ?>

<div class="timer">⏱ <span id="time">05:00</span></div>

<div class="container">

<!-- Sidebar -->
<div class="sidebar">
    <h3>Questions</h3>
    <div class="q-nav">
        <?php
        $q = $conn->prepare("SELECT id FROM questions WHERE subject=? LIMIT 10");
        $q->bind_param("s",$subject);
        $q->execute();
        $res = $q->get_result();
        $ids=[];
        while($row=$res->fetch_assoc()){
            $ids[]=$row['id'];
        }

        foreach($ids as $index=>$id){
            echo "<button onclick='goToQ($index)' id='nav$id'>".($index+1)."</button>";
        }
        ?>
    </div>
</div>

<!-- Main -->
<div class="main">

<form id="examForm" action="../backend/submit_exam.php" method="POST">

<input type="hidden" name="subject" value="<?= $subject ?>">

<?php
$stmt = $conn->prepare("SELECT * FROM questions WHERE subject=? LIMIT 10");
$stmt->bind_param("s",$subject);
$stmt->execute();
$result = $stmt->get_result();

$i=0;
while($q=$result->fetch_assoc()){
?>

<div class="question-box" id="q<?= $i ?>">
    <p>Q<?= $i+1 ?>. <?= $q['question'] ?></p>

    <?php for($opt=1;$opt<=4;$opt++){ ?>
    <label>
        <input type="radio" name="q<?= $q['id'] ?>" value="<?= $opt ?>" 
        onclick="markAnswered(<?= $q['id'] ?>)">
        <?= $q['option'.$opt] ?>
    </label>
    <?php } ?>

</div>

<?php $i++; } ?>

<button class="submit-btn">Submit Exam</button>

</form>

</div>
</div>

<?php } ?>

</div>

<script>

// show subjects
function showSubjects(){
    document.getElementById("subjects").style.display="block";
}

// go exam
function goExam(sub){
    window.location.href="exam.php?subject="+sub;
}

// scroll to question
function goToQ(i){
    document.getElementById("q"+i).scrollIntoView({behavior:"smooth"});
}

// mark answered
function markAnswered(id){
    document.getElementById("nav"+id).classList.add("answered");
}

// timer
let time=300;
let timer=setInterval(()=>{
    let m=Math.floor(time/60);
    let s=time%60;
    document.getElementById("time").innerHTML=
        (m<10?"0":"")+m+":"+(s<10?"0":"")+s;

    time--;

    if(time<0){
        clearInterval(timer);
        alert("Time Up!");
        document.getElementById("examForm").submit();
    }
},1000);

</script>

</body>
</html>