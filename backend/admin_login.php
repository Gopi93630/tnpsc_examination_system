<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // 🔍 CHECK ONLY ADMINS TABLE
    $stmt = $conn->prepare("SELECT id, name, password FROM admins WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['password'])) {

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];

            header("Location: ../admin/dashboard.php");
            exit();

        } else {
            echo "<script>
                    alert('Invalid Password!');
                    window.location.href='../admin/admin_login.php';
                  </script>";
        }

    } else {
        echo "<script>
                alert('Admin not found!');
                window.location.href='../admin/admin_login.php';
              </script>";
    }

}
?>