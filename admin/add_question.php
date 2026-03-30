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

// 📘 FETCH EXAMS FOR DROPDOWN
$exam_list = [];
$examQuery = $conn->query("SELECT id, title, subject, status FROM exams ORDER BY id DESC");
if ($examQuery) {
    while ($row = $examQuery->fetch_assoc()) {
        $exam_list[] = $row;
    }
}

// ➕ ADD QUESTION
if (isset($_POST['add_question'])) {
    $exam_id = (int) $_POST['exam_id'];
    $question = trim($_POST['question']);
    $option1 = trim($_POST['option1']);
    $option2 = trim($_POST['option2']);
    $option3 = trim($_POST['option3']);
    $option4 = trim($_POST['option4']);
    $correct_option = (int) $_POST['correct_option'];

    if (
        $exam_id <= 0 ||
        $question === "" ||
        $option1 === "" ||
        $option2 === "" ||
        $option3 === "" ||
        $option4 === "" ||
        !in_array($correct_option, [1, 2, 3, 4])
    ) {
        $error = "Please fill all fields correctly.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO questions 
            (exam_id, question, option1, option2, option3, option4, correct_option)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "isssssi",
            $exam_id,
            $question,
            $option1,
            $option2,
            $option3,
            $option4,
            $correct_option
        );

        if ($stmt->execute()) {
            $success = "Question added successfully.";
        } else {
            $error = "Failed to add question: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Question | Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

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

        body.dark {
            background: #121212;
        }

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

        .sidebar.collapsed {
            width: 80px;
        }

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

        .sidebar.collapsed a span {
            display: none;
        }

        .sidebar.collapsed a {
            justify-content: center;
        }

        .main {
            margin-left: 240px;
            padding: 20px;
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

        .profile:hover .profile-box {
            display: block;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 20px;
        }

        .glass-card {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.18);
        }

        .section-title {
            margin: 0 0 18px;
            font-size: 20px;
            font-weight: 600;
        }

        .helper-text {
            color: #e5e5e5;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 18px;
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

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #fff;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
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

        .form-group select option {
            color: #000;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #d9d9d9;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-actions {
            margin-top: 20px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-primary {
            background: linear-gradient(90deg, #00c6ff, #0072ff);
            color: #fff;
        }

        .btn-secondary {
            background: rgba(255,255,255,0.14);
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .info-list {
            display: grid;
            gap: 14px;
        }

        .info-item {
            background: rgba(255,255,255,0.08);
            border-radius: 14px;
            padding: 16px;
        }

        .info-item h4 {
            margin: 0 0 6px;
            font-size: 15px;
        }

        .info-item p {
            margin: 0;
            color: #e5e5e5;
            font-size: 13px;
            line-height: 1.6;
        }

        .exam-meta {
            margin-top: 10px;
            padding: 12px 14px;
            border-radius: 12px;
            background: rgba(255,255,255,0.08);
            font-size: 13px;
            color: #f1f1f1;
            line-height: 1.6;
        }

        @media (max-width: 992px) {
            .content-grid {
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

            .form-grid {
                grid-template-columns: 1fr;
            }

            .topbar {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="logo">🎓 ADMIN</div>

    <a href="dashboard.php" class="<?= ($page == 'dashboard.php') ? 'active' : '' ?>">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
    </a>

    <a href="add_exam.php" class="<?= ($page == 'add_exam.php') ? 'active' : '' ?>">
        <i class="fas fa-plus-circle"></i>
        <span>Add Exam</span>
    </a>

    <a href="add_question.php" class="<?= ($page == 'add_question.php') ? 'active' : '' ?>">
        <i class="fas fa-circle-question"></i>
        <span>Add Questions</span>
    </a>

    <a href="manage_users.php" class="<?= ($page == 'manage_users.php') ? 'active' : '' ?>">
        <i class="fas fa-users"></i>
        <span>Manage Users</span>
    </a>

    <a href="view_results.php" class="<?= ($page == 'view_results.php') ? 'active' : '' ?>">
        <i class="fas fa-chart-line"></i>
        <span>View Results</span>
    </a>

    <a href="../logout.php">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
    </a>
</div>

<div class="main" id="main">
    <div class="topbar">
        <div class="top-left">
            <button class="menu-btn" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="page-title">Add New Question</h1>
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

    <div class="content-grid">
        <div class="glass-card">
            <h2 class="section-title">Question Details</h2>
            <p class="helper-text">
                Select the exam, enter the question text, provide four options, and choose the correct answer.
                This page is used to build the exam content for your TNPSC examination system.
            </p>

            <?php if ($success): ?>
                <div class="alert success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-grid">
                    <div class="form-group full">
                        <label for="exam_id">Select Exam</label>
                        <select id="exam_id" name="exam_id" required onchange="updateExamMeta(this)">
                            <option value="">Choose Exam</option>
                            <?php foreach ($exam_list as $exam): ?>
                                <option 
                                    value="<?= (int) $exam['id'] ?>"
                                    data-title="<?= htmlspecialchars($exam['title']) ?>"
                                    data-subject="<?= htmlspecialchars($exam['subject'] ?? 'N/A') ?>"
                                    data-status="<?= htmlspecialchars($exam['status'] ?? 'N/A') ?>"
                                >
                                    <?= htmlspecialchars($exam['title']) ?> (ID: <?= (int) $exam['id'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <div class="exam-meta" id="examMeta">
                            Selected exam details will appear here.
                        </div>
                    </div>

                    <div class="form-group full">
                        <label for="question">Question</label>
                        <textarea id="question" name="question" placeholder="Enter the full question text" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="option1">Option 1</label>
                        <input type="text" id="option1" name="option1" placeholder="Enter option 1" required>
                    </div>

                    <div class="form-group">
                        <label for="option2">Option 2</label>
                        <input type="text" id="option2" name="option2" placeholder="Enter option 2" required>
                    </div>

                    <div class="form-group">
                        <label for="option3">Option 3</label>
                        <input type="text" id="option3" name="option3" placeholder="Enter option 3" required>
                    </div>

                    <div class="form-group">
                        <label for="option4">Option 4</label>
                        <input type="text" id="option4" name="option4" placeholder="Enter option 4" required>
                    </div>

                    <div class="form-group">
                        <label for="correct_option">Correct Option</label>
                        <select id="correct_option" name="correct_option" required>
                            <option value="">Select Correct Option</option>
                            <option value="1">Option 1</option>
                            <option value="2">Option 2</option>
                            <option value="3">Option 3</option>
                            <option value="4">Option 4</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="add_question" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Question
                    </button>

                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>&nbsp; Back to Dashboard
                    </a>
                </div>
            </form>
        </div>

        <div class="glass-card">
            <h2 class="section-title">Quick Guidelines</h2>

            <div class="info-list">
                <div class="info-item">
                    <h4><i class="fas fa-pen"></i> Question Writing</h4>
                    <p>Write the question clearly and avoid ambiguous wording for better student understanding.</p>
                </div>

                <div class="info-item">
                    <h4><i class="fas fa-list"></i> Options</h4>
                    <p>Make sure all four options are relevant and only one option is clearly correct.</p>
                </div>

                <div class="info-item">
                    <h4><i class="fas fa-check-circle"></i> Correct Answer</h4>
                    <p>Select the right option carefully, since it will be used for score calculation.</p>
                </div>

                <div class="info-item">
                    <h4><i class="fas fa-book-open"></i> Exam Mapping</h4>
                    <p>Always add the question under the correct exam so results and subject flow work properly.</p>
                </div>
            </div>
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

    function updateExamMeta(select) {
        const selected = select.options[select.selectedIndex];
        const title = selected.getAttribute("data-title");
        const subject = selected.getAttribute("data-subject");
        const status = selected.getAttribute("data-status");

        const box = document.getElementById("examMeta");

        if (!select.value) {
            box.innerHTML = "Selected exam details will appear here.";
            return;
        }

        box.innerHTML = `
            <strong>Exam Title:</strong> ${title}<br>
            <strong>Subject:</strong> ${subject}<br>
            <strong>Status:</strong> ${status}
        `;
    }
</script>

</body>
</html>