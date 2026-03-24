<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "TNPSC_EXAMINATION_SYSTEM";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Set charset for security
$conn->set_charset("utf8mb4");
?>