<?php
require_once 'config.php';
require_once 'auth.php';

requireLogin();
if (!isTeacher()) {
    echo "Access Denied";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $subject_id = $_POST['subject_id'];
    $grade = $_POST['grade'];

    // Insert the grade into the database
    $stmt = $pdo->prepare("INSERT INTO grades (student_id, subject_id, grade) VALUES (?, ?, ?)");
    $stmt->execute([$student_id, $subject_id, $grade]);

    echo "Grade added successfully. <a href='index.php'>Back to Dashboard</a>";
}

// Fetch students and subjects for dropdown
$students = $pdo->query("SELECT id, CONCAT(first_name, ' ', last_name) AS full_name FROM students");
$subjects = $pdo->query("SELECT id, subject_name FROM subjects");
?>

<h2>Add Grade</h2>
<form method="post">
    <select name="student_id" required>
        <option value="">Select Student</option>
        <?php while ($student = $students->fetch(PDO::FETCH_ASSOC)): ?>
            <option value="<?= $student['id'] ?>"><?= $student['full_name'] ?></option>
        <?php endwhile; ?>
    </select><br>

    <select name="subject_id" required>
        <option value="">Select Subject</option>
        <?php while ($subject = $subjects->fetch(PDO::FETCH_ASSOC)): ?>
            <option value="<?= $subject['id'] ?>"><?= $subject['subject_name'] ?></option>
        <?php endwhile; ?>
    </select><br>

    <input name="grade" type="number" placeholder="Grade" required><br>

    <button type="submit">Add Grade</button>
</form>
