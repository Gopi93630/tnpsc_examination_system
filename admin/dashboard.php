<?php
session_start();
require '../backend/db.php';

// 🔐 ADMIN CHECK
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_name = $_SESSION['admin_name'] ?? "Admin";
$page = basename($_SERVER['PHP_SELF']);

/* -----------------------------
   Helper Functions
----------------------------- */
function tableExists(mysqli $conn, string $table): bool {
    $table = $conn->real_escape_string($table);
    $res = $conn->query("SHOW TABLES LIKE '{$table}'");
    return $res && $res->num_rows > 0;
}

function columnExists(mysqli $conn, string $table, string $column): bool {
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $res = $conn->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
    return $res && $res->num_rows > 0;
}

function getScalar(mysqli $conn, string $sql, $default = 0) {
    $res = $conn->query($sql);
    if ($res && $row = $res->fetch_assoc()) {
        return array_values($row)[0] ?? $default;
    }
    return $default;
}

/* -----------------------------
   Safe schema detection
----------------------------- */
$hasUsersTable        = tableExists($conn, 'users');
$hasExamsTable        = tableExists($conn, 'exams');
$hasResultsTable      = tableExists($conn, 'results');
$hasQuestionsTable    = tableExists($conn, 'questions');

$hasUsersRole         = $hasUsersTable && columnExists($conn, 'users', 'role');
$hasUsersVerified     = $hasUsersTable && columnExists($conn, 'users', 'is_verified');
$hasUsersName         = $hasUsersTable && columnExists($conn, 'users', 'name');
$hasUsersEmail        = $hasUsersTable && columnExists($conn, 'users', 'email');

$hasExamsTitle        = $hasExamsTable && columnExists($conn, 'exams', 'title');
$hasExamsSubject      = $hasExamsTable && columnExists($conn, 'exams', 'subject');
$hasExamsStatus       = $hasExamsTable && columnExists($conn, 'exams', 'status');
$hasExamsDuration     = $hasExamsTable && columnExists($conn, 'exams', 'duration');
$hasExamsQuestions    = $hasExamsTable && columnExists($conn, 'exams', 'total_questions');

$hasResultsUserId     = $hasResultsTable && columnExists($conn, 'results', 'user_id');
$hasResultsScore      = $hasResultsTable && columnExists($conn, 'results', 'score');
$hasResultsTotal      = $hasResultsTable && columnExists($conn, 'results', 'total');
$hasResultsCreatedAt  = $hasResultsTable && columnExists($conn, 'results', 'created_at');

/* -----------------------------
   Stats
----------------------------- */
$total_users = $hasUsersTable ? (int)getScalar($conn, "SELECT COUNT(*) FROM users", 0) : 0;
$total_exams = $hasExamsTable ? (int)getScalar($conn, "SELECT COUNT(*) FROM exams", 0) : 0;
$total_results = $hasResultsTable ? (int)getScalar($conn, "SELECT COUNT(*) FROM results", 0) : 0;
$total_questions = $hasQuestionsTable ? (int)getScalar($conn, "SELECT COUNT(*) FROM questions", 0) : 0;

$verified_users = ($hasUsersTable && $hasUsersVerified)
    ? (int)getScalar($conn, "SELECT COUNT(*) FROM users WHERE is_verified = 1", 0)
    : 0;

$admin_users = ($hasUsersTable && $hasUsersRole)
    ? (int)getScalar($conn, "SELECT COUNT(*) FROM users WHERE role = 'admin'", 0)
    : 0;

$user_users = ($hasUsersTable && $hasUsersRole)
    ? (int)getScalar($conn, "SELECT COUNT(*) FROM users WHERE role = 'user'", 0)
    : $total_users;

$avg_score = ($hasResultsTable && $hasResultsScore)
    ? round((float)getScalar($conn, "SELECT AVG(score) FROM results", 0), 2)
    : 0;

/* -----------------------------
   Chart Data
   Chart 1: User Role Overview
----------------------------- */
$user_chart_labels = [];
$user_chart_data = [];

if ($hasUsersTable && $hasUsersRole) {
    $user_chart_labels = ['Admins', 'Users'];
    $user_chart_data = [$admin_users, $user_users];
} else {
    $user_chart_labels = ['Total Users'];
    $user_chart_data = [$total_users];
}

/* -----------------------------
   Chart 2: Result Score Buckets
----------------------------- */
$exam_chart_labels = ['0-25%', '26-50%', '51-75%', '76-100%'];
$exam_chart_data = [0, 0, 0, 0];

if ($hasResultsTable && $hasResultsScore && $hasResultsTotal) {
    $bucketQuery = "
        SELECT
            SUM(CASE WHEN (score / NULLIF(total,0)) * 100 <= 25 THEN 1 ELSE 0 END) AS b1,
            SUM(CASE WHEN (score / NULLIF(total,0)) * 100 > 25 AND (score / NULLIF(total,0)) * 100 <= 50 THEN 1 ELSE 0 END) AS b2,
            SUM(CASE WHEN (score / NULLIF(total,0)) * 100 > 50 AND (score / NULLIF(total,0)) * 100 <= 75 THEN 1 ELSE 0 END) AS b3,
            SUM(CASE WHEN (score / NULLIF(total,0)) * 100 > 75 THEN 1 ELSE 0 END) AS b4
        FROM results
    ";
    $bucketRes = $conn->query($bucketQuery);
    if ($bucketRes && $row = $bucketRes->fetch_assoc()) {
        $exam_chart_data = [
            (int)($row['b1'] ?? 0),
            (int)($row['b2'] ?? 0),
            (int)($row['b3'] ?? 0),
            (int)($row['b4'] ?? 0),
        ];
    }
}

/* -----------------------------
   Recent Exams
----------------------------- */
$recent_exams = [];
if ($hasExamsTable) {
    $fields = ["id"];
    if ($hasExamsTitle) $fields[] = "title";
    if ($hasExamsSubject) $fields[] = "subject";
    if ($hasExamsStatus) $fields[] = "status";
    if ($hasExamsDuration) $fields[] = "duration";
    if ($hasExamsQuestions) $fields[] = "total_questions";

    $examSql = "SELECT " . implode(", ", $fields) . " FROM exams ORDER BY id DESC LIMIT 5";
    $examRes = $conn->query($examSql);
    if ($examRes) {
        while ($row = $examRes->fetch_assoc()) {
            $recent_exams[] = $row;
        }
    }
}

/* -----------------------------
   Recent Results
----------------------------- */
$recent_results = [];
if ($hasResultsTable) {
    $select = ["results.id"];
    if ($hasResultsScore) $select[] = "results.score";
    if ($hasResultsTotal) $select[] = "results.total";
    if ($hasResultsCreatedAt) $select[] = "results.created_at";
    if ($hasUsersTable && $hasResultsUserId && $hasUsersName) $select[] = "users.name";

    $recentSql = "SELECT " . implode(", ", $select) . " FROM results";
    if ($hasUsersTable && $hasResultsUserId && $hasUsersName) {
        $recentSql .= " LEFT JOIN users ON results.user_id = users.id";
    }
    $recentSql .= " ORDER BY results.id DESC LIMIT 5";

    $recentRes = $conn->query($recentSql);
    if ($recentRes) {
        while ($row = $recentRes->fetch_assoc()) {
            $recent_results[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | TNPSC Examination System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: url('../assets/bg1.jpg') no-repeat center center fixed;
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

        body.dark {
            background: #121212;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 240px;
            height: 100vh;
            background: rgba(0, 0, 0, 0.90);
            backdrop-filter: blur(10px);
            padding-top: 20px;
            transition: 0.3s;
            overflow: hidden;
            z-index: 1000;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar .logo {
            text-align: center;
            color: white;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 28px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 20px;
            margin: 4px 10px;
            color: #d8d8d8;
            text-decoration: none;
            border-radius: 10px;
            transition: 0.3s;
            white-space: nowrap;
        }

        .sidebar a i {
            min-width: 22px;
            text-align: center;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: linear-gradient(90deg, #00c6ff, #0072ff);
            color: white;
        }

        .sidebar.collapsed a span {
            display: none;
        }

        .sidebar.collapsed a {
            justify-content: center;
        }

        .main {
            margin-left: 240px;
            padding: 22px;
            transition: 0.3s;
            min-height: 100vh;
        }

        .main.full {
            margin-left: 80px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            margin-bottom: 22px;
            flex-wrap: wrap;
        }

        .top-left,
        .top-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .menu-btn,
        .theme-btn {
            border: none;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            cursor: pointer;
            background: rgba(255,255,255,0.12);
            color: white;
            font-size: 17px;
            backdrop-filter: blur(8px);
        }

        .page-title {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .profile {
            position: relative;
            color: white;
            padding: 10px 14px;
            background: rgba(255,255,255,0.12);
            border-radius: 10px;
            backdrop-filter: blur(8px);
            cursor: pointer;
        }

        .profile-box {
            position: absolute;
            right: 0;
            top: 52px;
            min-width: 170px;
            background: rgba(255,255,255,0.16);
            backdrop-filter: blur(12px);
            border-radius: 12px;
            padding: 10px;
            display: none;
            z-index: 999;
        }

        .profile-box a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 8px 10px;
            border-radius: 8px;
        }

        .profile-box a:hover {
            background: rgba(255,255,255,0.18);
        }

        .profile:hover .profile-box {
            display: block;
        }

        .welcome-card,
        .stat-card,
        .chart-card,
        .list-card {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.18);
        }

        .welcome-card {
            padding: 24px;
            margin-bottom: 20px;
        }

        .welcome-card h2 {
            margin: 0 0 8px;
            font-size: 26px;
        }

        .welcome-card p {
            margin: 0;
            color: #ececec;
            line-height: 1.7;
            font-size: 14px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 18px;
            margin-bottom: 20px;
        }

        .stat-card {
            padding: 22px;
        }

        .stat-card .icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.14);
            margin-bottom: 14px;
            font-size: 18px;
        }

        .stat-card h3 {
            margin: 0 0 8px;
            font-size: 15px;
            font-weight: 500;
        }

        .stat-card p {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }

        .stat-card small {
            display: block;
            margin-top: 6px;
            color: #e5e5e5;
            font-size: 12px;
        }

        .charts-grid,
        .lists-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .chart-card,
        .list-card {
            padding: 22px;
        }

        .section-title {
            margin: 0 0 16px;
            font-size: 18px;
            font-weight: 600;
        }

        .chart-wrap {
            height: 320px;
        }

        .list-item {
            padding: 14px 0;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .list-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .list-item strong {
            display: block;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .list-item span {
            color: #e6e6e6;
            font-size: 13px;
            line-height: 1.6;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 999px;
            font-weight: 600;
            margin-top: 6px;
        }

        .badge-active {
            background: rgba(40, 167, 69, 0.22);
            color: #d4ffd9;
            border: 1px solid rgba(40, 167, 69, 0.35);
        }

        .badge-draft {
            background: rgba(255, 193, 7, 0.22);
            color: #ffe8a3;
            border: 1px solid rgba(255, 193, 7, 0.35);
        }

        .badge-inactive {
            background: rgba(220, 53, 69, 0.22);
            color: #ffd7dc;
            border: 1px solid rgba(220, 53, 69, 0.35);
        }

        .badge-pass {
            background: rgba(40, 167, 69, 0.22);
            color: #d4ffd9;
            border: 1px solid rgba(40, 167, 69, 0.35);
        }

        .badge-fail {
            background: rgba(220, 53, 69, 0.22);
            color: #ffd7dc;
            border: 1px solid rgba(220, 53, 69, 0.35);
        }

        .empty-state {
            color: #ececec;
            font-size: 14px;
            padding: 8px 0;
        }

        @media (max-width: 992px) {
            .charts-grid,
            .lists-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                left: -240px;
            }

            .sidebar.active {
                left: 0;
            }

            .main {
                margin-left: 0;
            }

            .page-title {
                font-size: 20px;
            }
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
        <i class="fas fa-chart-line"></i><span>Results</span>
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
            <h1 class="page-title">Admin Dashboard</h1>
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

    <div class="welcome-card">
        <h2>Welcome, <?= htmlspecialchars($admin_name) ?> 👋</h2>
        <p>
            This dashboard gives you a complete overview of your TNPSC Examination System.
            You can monitor users, exams, questions, and results from one place with a cleaner
            admin experience.
        </p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="icon"><i class="fas fa-users"></i></div>
            <h3>Total Users</h3>
            <p><?= $total_users ?></p>
            <small><?= $verified_users ?> verified users</small>
        </div>

        <div class="stat-card">
            <div class="icon"><i class="fas fa-file-circle-plus"></i></div>
            <h3>Total Exams</h3>
            <p><?= $total_exams ?></p>
            <small>All exams created by admin</small>
        </div>

        <div class="stat-card">
            <div class="icon"><i class="fas fa-list-check"></i></div>
            <h3>Total Questions</h3>
            <p><?= $total_questions ?></p>
            <small>Questions available in system</small>
        </div>

        <div class="stat-card">
            <div class="icon"><i class="fas fa-square-poll-vertical"></i></div>
            <h3>Total Results</h3>
            <p><?= $total_results ?></p>
            <small>Average score: <?= $avg_score ?></small>
        </div>
    </div>

    <div class="charts-grid">
        <div class="chart-card">
            <h3 class="section-title">User Overview</h3>
            <div class="chart-wrap">
                <canvas id="userChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <h3 class="section-title">Result Performance</h3>
            <div class="chart-wrap">
                <canvas id="examChart"></canvas>
            </div>
        </div>
    </div>

    <div class="lists-grid">
        <div class="list-card">
            <h3 class="section-title">Recent Exams</h3>

            <?php if (!empty($recent_exams)): ?>
                <?php foreach ($recent_exams as $exam): ?>
                    <div class="list-item">
                        <strong>
                            <?= htmlspecialchars($exam['title'] ?? ('Exam #' . $exam['id'])) ?>
                        </strong>
                        <span>
                            ID: <?= (int)$exam['id'] ?>
                            <?php if (!empty($exam['subject'])): ?> | Subject: <?= htmlspecialchars($exam['subject']) ?><?php endif; ?>
                            <?php if (!empty($exam['duration'])): ?> | Duration: <?= (int)$exam['duration'] ?> mins<?php endif; ?>
                            <?php if (!empty($exam['total_questions'])): ?> | Questions: <?= (int)$exam['total_questions'] ?><?php endif; ?>
                        </span>
                        <?php
                            $status = strtolower($exam['status'] ?? '');
                            $statusClass = 'badge-draft';
                            if ($status === 'active') $statusClass = 'badge-active';
                            elseif ($status === 'inactive') $statusClass = 'badge-inactive';
                        ?>
                        <?php if (!empty($exam['status'])): ?>
                            <div class="badge <?= $statusClass ?>">
                                <?= htmlspecialchars($exam['status']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">No exams found.</div>
            <?php endif; ?>
        </div>

        <div class="list-card">
            <h3 class="section-title">Recent Results</h3>

            <?php if (!empty($recent_results)): ?>
                <?php foreach ($recent_results as $row): ?>
                    <?php
                        $score = (int)($row['score'] ?? 0);
                        $total = (int)($row['total'] ?? 0);
                        $passed = ($total > 0 && $score >= ($total / 2));
                    ?>
                    <div class="list-item">
                        <strong>
                            <?= htmlspecialchars($row['name'] ?? ('User #' . ($row['user_id'] ?? ''))) ?>
                        </strong>
                        <span>
                            Score: <?= $score ?>/<?= $total ?>
                            <?php if (!empty($row['created_at'])): ?> | <?= htmlspecialchars($row['created_at']) ?><?php endif; ?>
                        </span>
                        <div class="badge <?= $passed ? 'badge-pass' : 'badge-fail' ?>">
                            <?= $passed ? 'Pass' : 'Fail' ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">No results found.</div>
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

    const userCtx = document.getElementById('userChart');
    new Chart(userCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($user_chart_labels) ?>,
            datasets: [{
                data: <?= json_encode($user_chart_data) ?>,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: '#ffffff'
                    }
                }
            }
        }
    });

    const examCtx = document.getElementById('examChart');
    new Chart(examCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($exam_chart_labels) ?>,
            datasets: [{
                label: 'Students',
                data: <?= json_encode($exam_chart_data) ?>,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: '#ffffff'
                    }
                }
            },
            scales: {
                x: {
                    ticks: { color: '#ffffff' },
                    grid: { color: 'rgba(255,255,255,0.08)' }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: '#ffffff' },
                    grid: { color: 'rgba(255,255,255,0.08)' }
                }
            }
        }
    });
</script>

</body>
</html>