<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($name === "" || $email === "" || $password === "" || $confirm_password === "") {
        header("Location: ../admin/admin_register.php?error=All+fields+are+required");
        exit();
    }

    if ($password !== $confirm_password) {
        header("Location: ../admin/admin_register.php?error=Passwords+do+not+match");
        exit();
    }

    if (strlen($password) < 6) {
        header("Location: ../admin/admin_register.php?error=Password+must+be+at+least+6+characters");
        exit();
    }

    $check = $conn->prepare("SELECT id FROM admins WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        header("Location: ../admin/admin_register.php?error=Admin+email+already+exists");
        exit();
    }
    $check->close();

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {
        header("Location: ../admin/admin_login.php?success=Admin+created+successfully");
        exit();
    } else {
        header("Location: ../admin/admin_register.php?error=Something+went+wrong");
        exit();
    }
}
?>