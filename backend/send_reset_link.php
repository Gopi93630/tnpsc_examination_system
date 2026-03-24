<?php
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // ✅ Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        echo "<script>
                alert('Email not found!');
                window.location.href='../forgot_password.php';
              </script>";
        exit();
    }

    // 🔐 Generate secure token
    $token = bin2hex(random_bytes(50));

    // ⏳ Token expiry (1 hour)
    $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // 💾 Save token + expiry + new password (TEMP STORE)
    $stmt2 = $conn->prepare("UPDATE users SET reset_token=?, token_expiry=?, temp_password=? WHERE email=?");
    $stmt2->bind_param("ssss", $token, $expiry, $new_password, $email);

    if ($stmt2->execute()) {

        // 🔗 Reset link (LOCALHOST)
        $link = "http://localhost:8080/TNPSC_EXAMINATION_SYSTEM/reset_password.php?token=$token";

        // 📧 DEMO (instead of email, show link)
        echo "<script>
                alert('Reset Link:\\n$link');
                window.location.href='../login.php';
              </script>";

    } else {
        echo "<script>
                alert('Error generating reset link!');
                window.location.href='../forgot_password.php';
              </script>";
    }

    $stmt->close();
    $stmt2->close();
}
?>