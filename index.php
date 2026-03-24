<?php
session_start();

// If already logged in → redirect to dashboard
if(isset($_SESSION['user_id'])){

    if($_SESSION['role'] == "admin"){
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit();
}

// If not logged in → go to login page
header("Location: login.php");
exit();
?>