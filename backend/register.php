<?php

include("db.php");



if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Default verification status
    $is_verified = 0;

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>
                alert('Email already exists!');
                window.location.href='../register.php';
              </script>";
        exit();
    }

    $stmt->close();

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, is_verified) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $email, $password, $is_verified);

    if ($stmt->execute()) {

        // ✅ SUCCESS MESSAGE + REDIRECT
        echo "<script>
                alert('Registration Successful! Please verify your email (demo).');
                window.location.href='../login.php';
              </script>";

    } else {
        echo "<script>
                alert('Error! Try again.');
                window.location.href='../register.php';
              </script>";
    }

    $stmt->close();
}
?>

