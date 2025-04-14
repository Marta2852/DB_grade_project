<?php
require_once 'config.php';
require_once 'auth.php';
?>

<h1>Welcome to School Web App</h1>

<?php if (isLoggedIn()): ?>
    <p>Logged in as <?= $_SESSION['role'] ?>.</p>
    <nav>
        <?php if (isTeacher()): ?>
            <a href="add_student.php">Add Student</a> |
            <a href="add_subject.php">Add Subject</a> |
            <a href="add_grade.php">Add Grade</a> |
        <?php endif; ?>
        <a href="view_grades.php">View Grades</a> |
        <a href="upload_avatar.php">Upload Avatar</a> |
        <a href="logout.php">Logout</a>
    </nav>
<?php else: ?>
    <a href="login.php">Login</a> |
    <a href="register_teacher.php">Register Teacher</a> |
    <a href="register_student.php">Register Student</a>
<?php endif; ?>
