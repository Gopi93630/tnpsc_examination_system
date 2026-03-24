<?php
session_start();
include("../backend/db.php");

$exam_id = 1; // demo exam

$questions = $conn->query("SELECT * FROM questions WHERE exam_id=$exam_id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Exam - TNPSC Examination System</title>
    <!-- Shared CSS -->
    <link rel="stylesheet" href="../assets/style.css">
    <!-- Optional Font Awesome for icons if needed -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

<h2>Exam</h2>

<!-- ✅ STEP 1: ADD TIMER HERE -->
<div id="timer" style="font-weight:bold; color:red;"></div>

<!-- ✅ STEP 2: YOUR FORM STARTS -->
<form id="examForm" action="../backend/submit_exam.php" method="POST">

<?php while($q = $questions->fetch_assoc()) { ?>
<div class="card">
<p><?php echo $q['question']; ?></p>

<input type="radio" name="q<?php echo $q['id']; ?>" value="1"> <?php echo $q['option1']; ?><br>
<input type="radio" name="q<?php echo $q['id']; ?>" value="2"> <?php echo $q['option2']; ?><br>
<input type="radio" name="q<?php echo $q['id']; ?>" value="3"> <?php echo $q['option3']; ?><br>
<input type="radio" name="q<?php echo $q['id']; ?>" value="4"> <?php echo $q['option4']; ?><br>

</div>
<?php } ?>

<input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">

<button type="submit">Submit Exam</button>

</form>

<!-- ✅ STEP 3: JS FILE (ALWAYS FIRST) -->
<script src="../assets/script.js"></script>

<!-- ✅ STEP 4: FUNCTION CALL (AFTER JS FILE) -->
<script>
startTimer(10); // 10 minutes
</script>

</body>
</html>