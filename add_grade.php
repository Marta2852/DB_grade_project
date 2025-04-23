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

    // Validate the grade to be between 0 and 10
    if ($grade < 0 || $grade > 10) {
        $error_message = "Grade must be between 0 and 10.";
    }

    // Check if the student already has a grade in the selected subject
    $checkGradeStmt = $pdo->prepare("SELECT * FROM grades WHERE student_id = ? AND subject_id = ?");
    $checkGradeStmt->execute([$student_id, $subject_id]);
    if ($checkGradeStmt->rowCount() > 0) {
        $error_message = "This student already has a grade for this subject.";
    }

    // If no errors, insert the grade into the database
    if (!isset($error_message)) {
        $stmt = $pdo->prepare("INSERT INTO grades (student_id, subject_id, grade) VALUES (?, ?, ?)");
        $stmt->execute([$student_id, $subject_id, $grade]);

        // Redirect back with success message
        header("Location: view_grades.php?action=grade_added");
        exit;
    }
}

// Fetch students and subjects for dropdown
$students = $pdo->query("SELECT id, CONCAT(first_name, ' ', last_name) AS full_name FROM students");
$subjects = $pdo->query("SELECT id, subject_name FROM subjects");
?>

<h2>Add Grade</h2>

<!-- Display error message if grade already exists or invalid grade -->
<?php if (isset($error_message)): ?>
    <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
<?php endif; ?>

<form method="post">
    <select name="student_id" required>
        <option value="">Select Student</option>
        <?php while ($student = $students->fetch(PDO::FETCH_ASSOC)): ?>
            <option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['full_name']) ?></option>
        <?php endwhile; ?>
    </select><br>

    <select name="subject_id" required>
        <option value="">Select Subject</option>
        <?php while ($subject = $subjects->fetch(PDO::FETCH_ASSOC)): ?>
            <option value="<?= $subject['id'] ?>"><?= htmlspecialchars($subject['subject_name']) ?></option>
        <?php endwhile; ?>
    </select><br>

    <input name="grade" type="number" placeholder="Grade" min="0" max="10" step="1" required><br>

    <button type="submit">Add Grade</button>
</form>

<!-- Back to Grades link -->
<a href="view_grades.php">Back to Grades</a>