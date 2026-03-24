<?php
session_start();
include("../backend/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$result = $conn->query("SELECT name, email FROM users WHERE id='$user_id'");
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/style.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: url('../assets/bg.jpg') no-repeat center center/cover;
        }

        .overlay {
            background: rgba(0,0,0,0.7);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .profile-box {
            background: white;
            padding: 30px;
            width: 350px;
            border-radius: 10px;
            text-align: center;
        }

        .profile-box h2 {
            margin-bottom: 20px;
        }

        .profile-box input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background: #28a745;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .btn:hover {
            background: #218838;
        }

        .back {
            display: block;
            margin-top: 10px;
            color: #555;
            text-decoration: none;
        }

        .back:hover {
            color: black;
        }
    </style>
</head>

<body>

<div class="overlay">

    <div class="profile-box">

        <h2>Your Profile</h2>

        <form action="" method="POST">

            <input type="text" name="name" value="<?php echo $user['name']; ?>">

            <input type="email" name="email" value="<?php echo $user['email']; ?>">

            <button type="submit" class="btn">Update</button>

        </form>

        <a href="dashboard.php" class="back">⬅ Back</a>

    </div>

</div>

</body>
</html>