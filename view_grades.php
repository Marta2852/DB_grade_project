<?php
require_once 'config.php';
require_once 'auth.php';

requireLogin();

$isTeacher = $_SESSION['role'] === 'teacher';
$userId = $_SESSION['user_id'];

// Initialize the base query
$query = "
    SELECT grades.id, students.first_name, students.last_name, subjects.subject_name, grades.grade, grades.created_at
    FROM grades
    JOIN students ON grades.student_id = students.id
    JOIN subjects ON grades.subject_id = subjects.id
";

// Initialize filter array and parameters
$filters = [];
$params = [];

if ($isTeacher) {
    // Get all students for the dropdown
    $studentsStmt = $pdo->query("SELECT id, first_name, last_name FROM students");
    $students = $studentsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get all subjects for the dropdown
    $subjectsStmt = $pdo->query("SELECT id, subject_name FROM subjects");
    $subjects = $subjectsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Apply student filter if set
    if (!empty($_GET['student_id'])) {
        $filters[] = "grades.student_id = ?";
        $params[] = $_GET['student_id'];
    }

    // Apply subject filter if set
    if (!empty($_GET['subject_id'])) {
        $filters[] = "grades.subject_id = ?";
        $params[] = $_GET['subject_id'];
    }
} else {
    // If not a teacher, only show the logged-in student's grades
    $filters[] = "grades.student_id = ?";
    $params[] = $userId;
}

// Add filters to the query
if (!empty($filters)) {
    $query .= " WHERE " . implode(" AND ", $filters);
}

// Order the grades by created_at
$query .= " ORDER BY grades.created_at DESC";

// Prepare and execute the query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<h2>Grades</h2>

<!-- Display feedback messages -->
<?php if (isset($_GET['action']) && $_GET['action'] === 'deleted'): ?>
    <p style="color: green;">Grade successfully deleted.</p>
<?php endif; ?>

<!-- Filter Section -->
<?php if ($isTeacher): ?>
<form method="get" style="margin-bottom: 20px;">
    <label for="student_id">Filter by Student:</label>
    <select name="student_id" id="student_id">
        <option value="">Select Student</option>
        <?php foreach ($students as $student): ?>
            <option value="<?= $student['id'] ?>" <?= isset($_GET['student_id']) && $_GET['student_id'] == $student['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="subject_id">Filter by Subject:</label>
    <select name="subject_id" id="subject_id">
        <option value="">Select Subject</option>
        <?php foreach ($subjects as $subject): ?>
            <option value="<?= $subject['id'] ?>" <?= isset($_GET['subject_id']) && $_GET['subject_id'] == $subject['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($subject['subject_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Filter</button>
</form>
<?php endif; ?>

<!-- Check if grades exist -->
<?php if ($grades): ?>
<!-- Grades Table -->
<table border="1">
    <thead>
        <tr>
            <?php if ($isTeacher): ?>
                <th>Student Name</th>
            <?php endif; ?>
            <th>Subject</th>
            <th>Grade</th>
            <th>Created At</th>
            <?php if ($isTeacher): ?>
                <th>Actions</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($grades as $g): ?>
            <tr>
                <?php if ($isTeacher): ?>
                    <td><?= htmlspecialchars($g['first_name'] . ' ' . $g['last_name']) ?></td>
                <?php endif; ?>
                <td><?= htmlspecialchars($g['subject_name']) ?></td>
                <td><?= htmlspecialchars($g['grade']) ?></td>
                <td><?= htmlspecialchars($g['created_at']) ?></td>
                <?php if ($isTeacher): ?>
                    <td>
                        <a href="edit_grade.php?id=<?= $g['id'] ?>">Edit</a> |
                        <a href="delete_grades.php?id=<?= $g['id'] ?>" onclick="return confirm('Delete this grade?')">Delete</a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
    <p>No grades available.</p>
<?php endif; ?>

<!-- Home Page Link -->
<a href="index.php">Back to Home</a>
