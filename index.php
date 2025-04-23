<?php
require_once 'config.php';
require_once 'auth.php';

requireLogin(); // Make sure the user is logged in

// Fetch the user's details
$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch the user's name and avatar based on their role
if ($role === 'teacher') {
    $stmt = $pdo->prepare("SELECT name, avatar FROM teachers WHERE id = ?");
} else {
    $stmt = $pdo->prepare("SELECT first_name, last_name, avatar FROM students WHERE id = ?");
}
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Set full name and avatar based on role
if ($role === 'teacher') {
    $fullName = htmlspecialchars($user['name']);
} else {
    $fullName = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
}

$avatarPath = $user['avatar'] ? $user['avatar'] : 'uploads/avatars/default-avatar.png'; // Default avatar if not set
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Web App</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Web App</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>

    <!-- Navigation Bar at the top -->
    <nav class="navbar">
        <div class="nav-links">
            <?php if (isLoggedIn()): ?>
                <a href="index.php">Home</a>
                <?php if (isTeacher()): ?>
                    <a href="add_student.php">Add Student</a>
                    <a href="add_subject.php">Add Subject</a>
                    <a href="add_grade.php">Add Grade</a>
                <?php endif; ?>
                <a href="view_grades.php">View Grades</a>
                <a href="upload_avatar.php">Upload Avatar</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register_teacher.php">Register Teacher</a>
                <a href="register_student.php">Register Student</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="main-content">
        <h1>Welcome to School Web App</h1>

        <?php if (isLoggedIn()): ?>
            <div class="user-info">
                <div class="user-avatar">
                <img src="<?= htmlspecialchars($avatarPath) ?>" alt="Avatar" class="avatar">
                </div>
                <div class="user-welcome">
                <p>Welcome, <?= htmlspecialchars($fullName) ?>!</p>
                <a href="logout.php">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <p>Please log in to access the system.</p>
        <?php endif; ?>

    </div>

</body>
</html>


</body>
</html>
