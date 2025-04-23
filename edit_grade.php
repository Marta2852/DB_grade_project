<?php
require_once 'config.php';
require_once 'auth.php';

requireLogin();

$isTeacher = $_SESSION['role'] === 'teacher';
$gradeId = $_GET['id']; // Get grade ID from the URL

// Fetch grade details
$stmt = $pdo->prepare("
    SELECT grades.id, grades.grade, students.first_name, students.last_name, subjects.subject_name, students.id as student_id, subjects.id as subject_id
    FROM grades
    JOIN students ON grades.student_id = students.id
    JOIN subjects ON grades.subject_id = subjects.id
    WHERE grades.id = ?
");
$stmt->execute([$gradeId]);
$grade = $stmt->fetch();

// If the grade doesn't exist, redirect to view grades page
if (!$grade) {
    header("Location: view_grades.php");
    exit;
}

// Fetch all subjects for the dropdown (if needed for options)
$subjectsStmt = $pdo->query("SELECT * FROM subjects");
$subjects = $subjectsStmt->fetchAll(PDO::FETCH_ASSOC);

// Update grade information if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newGrade = $_POST['grade'];
    $newFirstName = $_POST['first_name'];
    $newLastName = $_POST['last_name'];
    $newSubjectName = $_POST['subject_name']; // Get the new subject name

    // Update the grade, student name, and subject
    $pdo->beginTransaction();
    try {
        // Update student name (if changed)
        $stmt = $pdo->prepare("UPDATE students SET first_name = ?, last_name = ? WHERE id = ?");
        $stmt->execute([$newFirstName, $newLastName, $grade['student_id']]);

        // Update or add the new subject
        // First, check if the new subject name already exists, if not, add it
        $stmt = $pdo->prepare("SELECT * FROM subjects WHERE subject_name = ?");
        $stmt->execute([$newSubjectName]);
        $existingSubject = $stmt->fetch();

        if (!$existingSubject) {
            // Insert new subject if it doesn't exist
            $stmt = $pdo->prepare("INSERT INTO subjects (subject_name) VALUES (?)");
            $stmt->execute([$newSubjectName]);
            $newSubjectId = $pdo->lastInsertId(); // Get the new subject's ID
        } else {
            $newSubjectId = $existingSubject['id']; // Use the existing subject ID
        }

        // Update grade's subject with the new subject ID
        $stmt = $pdo->prepare("UPDATE grades SET grade = ?, subject_id = ? WHERE id = ?");
        $stmt->execute([$newGrade, $newSubjectId, $gradeId]);

        $pdo->commit();
        header("Location: view_grades.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>

<h2>Edit Grade</h2>

<form method="post">
    <label for="first_name">First Name:</label>
    <input type="text" name="first_name" value="<?= htmlspecialchars($grade['first_name']) ?>" required><br>

    <label for="last_name">Last Name:</label>
    <input type="text" name="last_name" value="<?= htmlspecialchars($grade['last_name']) ?>" required><br>

    <label for="subject_name">Subject Name:</label>
    <input type="text" name="subject_name" value="<?= htmlspecialchars($grade['subject_name']) ?>" required><br>

    <label for="grade">Grade:</label>
    <input type="number" name="grade" value="<?= htmlspecialchars($grade['grade']) ?>" step="1" required><br>

    <button type="submit">Update Grade</button>
</form>