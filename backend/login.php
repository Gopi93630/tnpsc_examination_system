<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password, is_verified FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();

        if ($user['is_verified'] == 0) {
            echo "<script>alert('Verify email first'); window.location='../login.php';</script>";
            exit();
        }

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            header("Location: ../user/dashboard.php");
            exit();

        } else {
            echo "<script>alert('Invalid Password'); window.location='../login.php';</script>";
        }

    } else {
        echo "<script>alert('User not found'); window.location='../login.php';</script>";
    }

    }
?>

