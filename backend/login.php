<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare statement
    $stmt = $conn->prepare("SELECT id, password, role, is_verified FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password, $role, $is_verified);

    if ($stmt->fetch()) {

        // 🔐 EMAIL VERIFICATION CHECK
        if ($is_verified == 0) {
            echo "<script>
                    alert('Please verify your email!');
                    window.location.href='../login.php';
                  </script>";
            exit();
        }

        // 🔑 PASSWORD CHECK
        if (password_verify($password, $hashed_password)) {

            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;

            // 🚀 REDIRECT BASED ON ROLE
            if ($role == "admin") {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../user/dashboard.php");
            }
            exit();

        } else {
            echo "<script>
                    alert('Invalid Password!');
                    window.location.href='../login.php';
                  </script>";
        }

    } else {
        echo "<script>
                alert('User not found!');
                window.location.href='../login.php';
              </script>";
    }

    $stmt->close();
}
?>

