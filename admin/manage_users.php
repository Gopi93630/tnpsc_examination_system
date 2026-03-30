<?php
session_start();
include("../backend/db.php");

// 🔐 ADMIN CHECK
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_name = $_SESSION['admin_name'] ?? "Admin";
$page = basename($_SERVER['PHP_SELF']);

$success = "";
$error = "";

// 🗑️ DELETE USER
if (isset($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $success = "User deleted successfully.";
    } else {
        $error = "Failed to delete user.";
    }

    $stmt->close();
}

// 🔍 SEARCH + FILTER
$search = trim($_GET['search'] ?? '');
$role = trim($_GET['role'] ?? '');

$sql = "SELECT id, name, email, role, is_verified FROM users WHERE 1=1";
$params = [];
$types = "";

if ($search !== "") {
    $sql .= " AND (name LIKE ? OR email LIKE ?)";
    $searchLike = "%$search%";
    $params[] = $searchLike;
    $params[] = $searchLike;
    $types .= "ss";
}

if ($role !== "") {
    $sql .= " AND role = ?";
    $params[] = $role;
    $types .= "s";
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// 📊 STATS
$total_users = $conn->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'] ?? 0;
$verified_users = $conn->query("SELECT COUNT(*) AS count FROM users WHERE is_verified = 1")->fetch_assoc()['count'] ?? 0;
$admin_users = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'admin'")->fetch_assoc()['count'] ?? 0;
$normal_users = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'user'")->fetch_assoc()['count'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users | Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: url('../assets/bg3.png') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: -1;
        }

        body.dark { background: #121212; }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 240px;
            height: 100vh;
            background: rgba(0, 0, 0, 0.88);
            backdrop-filter: blur(10px);
            padding-top: 20px;
            transition: 0.3s;
            overflow: hidden;
            z-index: 1000;
        }

        .sidebar.collapsed { width: 80px; }

        .sidebar .logo {
            text-align: center;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 25px;
            color: #fff;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 20px;
            margin: 4px 10px;
            color: #d9d9d9;
            text-decoration: none;
            border-radius: 10px;
            transition: 0.3s;
            white-space: nowrap;
        }

        .sidebar a i {
            min-width: 22px;
            text-align: center;
            font-size: 16px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: linear-gradient(90deg, #00c6ff, #0072ff);
            color: #fff;
        }

        .sidebar.collapsed a span { display: none; }
        .sidebar.collapsed a { justify-content: center; }

        .main {
            margin-left: 240px;
            padding: 20px;
            transition: 0.3s;
            min-height: 100vh;
        }

        .main.full { margin-left: 80px; }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 22px;
        }

        .top-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .menu-btn {
            width: 42px;
            height: 42px;
            border: none;
            border-radius: 10px;
            background: rgba(255,255,255,0.12);
            color: #fff;
            cursor: pointer;
            font-size: 18px;
            backdrop-filter: blur(8px);
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }

        .top-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .theme-btn {
            border: none;
            background: rgba(255,255,255,0.12);
            color: #fff;
            padding: 10px 14px;
            border-radius: 10px;
            cursor: pointer;
            backdrop-filter: blur(8px);
        }

        .profile {
            position: relative;
            color: #fff;
            background: rgba(255,255,255,0.12);
            padding: 10px 14px;
            border-radius: 10px;
            backdrop-filter: blur(8px);
            cursor: pointer;
        }

        .profile-box {
            position: absolute;
            right: 0;
            top: 52px;
            min-width: 180px;
            background: rgba(255,255,255,0.16);
            backdrop-filter: blur(12px);
            border-radius: 12px;
            padding: 10px;
            display: none;
            z-index: 999;
        }

        .profile-box a {
            display: block;
            text-decoration: none;
            color: #fff;
            padding: 9px 10px;
            border-radius: 8px;
        }

        .profile-box a:hover {
            background: rgba(255,255,255,0.18);
        }

        .profile:hover .profile-box { display: block; }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 18px;
            margin-bottom: 20px;
        }

        .stat-card,
        .glass-card {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            padding: 22px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.18);
        }

        .stat-card h3 {
            margin: 0 0 8px;
            font-size: 16px;
            font-weight: 500;
        }

        .stat-card p {
            margin: 0;
            font-size: 26px;
            font-weight: 700;
        }

        .section-title {
            margin: 0 0 18px;
            font-size: 20px;
            font-weight: 600;
        }

        .alert {
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .alert.success {
            background: rgba(40, 167, 69, 0.22);
            border: 1px solid rgba(40, 167, 69, 0.45);
            color: #d4ffd9;
        }

        .alert.error {
            background: rgba(220, 53, 69, 0.22);
            border: 1px solid rgba(220, 53, 69, 0.45);
            color: #ffd7dc;
        }

        .filters {
            display: grid;
            grid-template-columns: 1.4fr 0.8fr auto;
            gap: 14px;
            margin-bottom: 18px;
        }

        .filters input,
        .filters select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 12px;
            background: rgba(255,255,255,0.10);
            color: #fff;
            outline: none;
            font-size: 14px;
            font-family: inherit;
        }

        .filters select option { color: #000; }
        .filters input::placeholder { color: #ddd; }

        .btn {
            border: none;
            border-radius: 12px;
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(90deg, #00c6ff, #0072ff);
            color: #fff;
        }

        .btn-danger {
            background: rgba(220, 53, 69, 0.85);
            color: #fff;
            padding: 8px 12px;
            border-radius: 10px;
        }

        .btn:hover { transform: translateY(-2px); }

        .table-wrap {
            overflow-x: auto;
            border-radius: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 820px;
        }

        th, td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.10);
            color: #fff;
            font-size: 14px;
        }

        th {
            font-weight: 600;
            background: rgba(255,255,255,0.08);
        }

        tr:hover {
            background: rgba(255,255,255,0.05);
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-role-admin {
            background: rgba(255, 193, 7, 0.22);
            color: #ffe08a;
            border: 1px solid rgba(255, 193, 7, 0.35);
        }

        .badge-role-user {
            background: rgba(0, 198, 255, 0.22);
            color: #b8f0ff;
            border: 1px solid rgba(0, 198, 255, 0.35);
        }

        .badge-verified {
            background: rgba(40, 167, 69, 0.22);
            color: #d4ffd9;
            border: 1px solid rgba(40, 167, 69, 0.35);
        }

        .badge-unverified {
            background: rgba(220, 53, 69, 0.22);
            color: #ffd7dc;
            border: 1px solid rgba(220, 53, 69, 0.35);
        }

        .empty-box {
            padding: 20px;
            text-align: center;
            color: #e9e9e9;
            background: rgba(255,255,255,0.06);
            border-radius: 14px;
        }

        @media (max-width: 992px) {
            .filters { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar { left: -240px; }
            .sidebar.active { left: 0; }
            .main { margin-left: 0; }
            .topbar { flex-wrap: wrap; }
        }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="logo">🎓 ADMIN</div>

    <a href="dashboard.php" class="<?= ($page == 'dashboard.php') ? 'active' : '' ?>">
        <i class="fas fa-home"></i><span>Dashboard</span>
    </a>

    <a href="add_exam.php" class="<?= ($page == 'add_exam.php') ? 'active' : '' ?>">
        <i class="fas fa-plus-circle"></i><span>Add Exam</span>
    </a>

    <a href="add_question.php" class="<?= ($page == 'add_question.php') ? 'active' : '' ?>">
        <i class="fas fa-circle-question"></i><span>Add Questions</span>
    </a>

    <a href="manage_users.php" class="<?= ($page == 'manage_users.php') ? 'active' : '' ?>">
        <i class="fas fa-users"></i><span>Manage Users</span>
    </a>

    <a href="view_results.php" class="<?= ($page == 'view_results.php') ? 'active' : '' ?>">
        <i class="fas fa-chart-line"></i><span>View Results</span>
    </a>

    <a href="../logout.php">
        <i class="fas fa-sign-out-alt"></i><span>Logout</span>
    </a>
</div>

<div class="main" id="main">
    <div class="topbar">
        <div class="top-left">
            <button class="menu-btn" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="page-title">Manage Users</h1>
        </div>

        <div class="top-right">
            <button class="theme-btn" onclick="toggleDark()">
                <i class="fas fa-moon"></i>
            </button>

            <div class="profile">
                <i class="fas fa-user-shield"></i>
                <?= htmlspecialchars($admin_name) ?>
                <i class="fas fa-chevron-down"></i>

                <div class="profile-box">
                    <a href="dashboard.php"><i class="fas fa-gauge"></i> Dashboard</a>
                    <a href="../logout.php"><i class="fas fa-right-from-bracket"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Users</h3>
            <p><?= (int) $total_users ?></p>
        </div>

        <div class="stat-card">
            <h3>Verified Users</h3>
            <p><?= (int) $verified_users ?></p>
        </div>

        <div class="stat-card">
            <h3>Admins</h3>
            <p><?= (int) $admin_users ?></p>
        </div>

        <div class="stat-card">
            <h3>Normal Users</h3>
            <p><?= (int) $normal_users ?></p>
        </div>
    </div>

    <div class="glass-card">
        <h2 class="section-title">User Directory</h2>

        <?php if ($success): ?>
            <div class="alert success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="GET" class="filters">
            <input type="text" name="search" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">

            <select name="role">
                <option value="">All Roles</option>
                <option value="user" <?= ($role === 'user') ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= ($role === 'admin') ? 'selected' : '' ?>>Admin</option>
            </select>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>&nbsp; Search
            </button>
        </form>

        <div class="table-wrap">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= (int) $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <span class="badge <?= ($user['role'] === 'admin') ? 'badge-role-admin' : 'badge-role-user' ?>">
                                        <?= htmlspecialchars(ucfirst($user['role'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= ($user['is_verified'] == 1) ? 'badge-verified' : 'badge-unverified' ?>">
                                        <?= ($user['is_verified'] == 1) ? 'Verified' : 'Unverified' ?>
                                    </span>
                                </td>
                                <td>
                                    <a
                                        href="manage_users.php?delete=<?= (int) $user['id'] ?>"
                                        class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this user?')"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-box">
                    <i class="fas fa-users-slash"></i><br><br>
                    No users found for the selected filter.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleSidebar() {
    let sidebar = document.getElementById("sidebar");
    let main = document.getElementById("main");

    if (window.innerWidth <= 768) {
        sidebar.classList.toggle("active");
    } else {
        sidebar.classList.toggle("collapsed");
        main.classList.toggle("full");
    }
}

function toggleDark() {
    document.body.classList.toggle("dark");
}
</script>

</body>
</html>