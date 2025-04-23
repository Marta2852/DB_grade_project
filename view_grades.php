<?php
require_once 'config.php';
require_once 'auth.php';

requireLogin();

$isTeacher = $_SESSION['role'] === 'teacher';
$userId = $_SESSION['user_id'];

// Initialize base query
$query = "
    SELECT grades.id, students.first_name, students.last_name, subjects.subject_name, grades.grade, grades.created_at
    FROM grades
    JOIN students ON grades.student_id = students.id
    JOIN subjects ON grades.subject_id = subjects.id
";

// Filters and params
$filters = [];
$params = [];

if ($isTeacher) {
    // Get students and subjects for dropdown
    $students = $pdo->query("SELECT id, first_name, last_name FROM students")->fetchAll(PDO::FETCH_ASSOC);
    $subjects = $pdo->query("SELECT id, subject_name FROM subjects")->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($_GET['student_id'])) {
        $filters[] = "grades.student_id = ?";
        $params[] = $_GET['student_id'];
    }

    if (!empty($_GET['subject_id'])) {
        $filters[] = "grades.subject_id = ?";
        $params[] = $_GET['subject_id'];
    }
} else {
    // Student view: get only their grades
    $filters[] = "grades.student_id = ?";
    $params[] = $userId;

    // Get subjects for the student filter
    $subjects = $pdo->query("SELECT id, subject_name FROM subjects")->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($_GET['subject_id'])) {
        $filters[] = "grades.subject_id = ?";
        $params[] = $_GET['subject_id'];
    }
}

// Apply filters
if (!empty($filters)) {
    $query .= " WHERE " . implode(" AND ", $filters);
}

// Sorting
$sortOrder = (isset($_GET['sort_order']) && strtolower($_GET['sort_order']) === 'asc') ? 'ASC' : 'DESC';

// Teacher sorts by date, or by grade if subject is selected
if ($isTeacher) {
    if (!empty($_GET['subject_id'])) {
        $query .= " ORDER BY grades.grade $sortOrder";
    } else {
        $query .= " ORDER BY grades.created_at $sortOrder";
    }
} else {
    // Students always sort by grade
    $query .= " ORDER BY grades.grade $sortOrder";
}

// Execute
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Grades</h2>

<?php if (isset($_GET['action']) && $_GET['action'] === 'deleted'): ?>
    <p style="color: green;">Grade successfully deleted.</p>
<?php endif; ?>

<?php if ($isTeacher): ?>
<!-- Teacher filter form -->
<form method="get" style="margin-bottom: 20px;">
    <label for="student_id">Filter by Student:</label>
    <select name="student_id" id="student_id">
        <option value="">All Students</option>
        <?php foreach ($students as $student): ?>
            <option value="<?= $student['id'] ?>" <?= isset($_GET['student_id']) && $_GET['student_id'] == $student['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="subject_id">Filter by Subject:</label>
    <select name="subject_id" id="subject_id">
        <option value="">All Subjects</option>
        <?php foreach ($subjects as $subject): ?>
            <option value="<?= $subject['id'] ?>" <?= isset($_GET['subject_id']) && $_GET['subject_id'] == $subject['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($subject['subject_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="sort_order">Sort by:</label>
    <select name="sort_order" id="sort_order">
        <option value="asc" <?= isset($_GET['sort_order']) && $_GET['sort_order'] == 'asc' ? 'selected' : '' ?>>Ascending</option>
        <option value="desc" <?= isset($_GET['sort_order']) && $_GET['sort_order'] == 'desc' ? 'selected' : '' ?>>Descending</option>
    </select>

    <button type="submit">Filter</button>
</form>
<?php endif; ?>

<?php if (!$isTeacher): ?>
<!-- Student filter form -->
<form method="get" style="margin-bottom: 20px;">
    <label for="subject_id">Filter by Subject:</label>
    <select name="subject_id" id="subject_id">
        <option value="">All Subjects</option>
        <?php foreach ($subjects as $subject): ?>
            <option value="<?= $subject['id'] ?>" <?= isset($_GET['subject_id']) && $_GET['subject_id'] == $subject['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($subject['subject_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="sort_order">Sort by Grade:</label>
    <select name="sort_order" id="sort_order">
        <option value="asc" <?= isset($_GET['sort_order']) && $_GET['sort_order'] == 'asc' ? 'selected' : '' ?>>Ascending</option>
        <option value="desc" <?= isset($_GET['sort_order']) && $_GET['sort_order'] == 'desc' ? 'selected' : '' ?>>Descending</option>
    </select>

    <button type="submit">Filter</button>
</form>
<?php endif; ?>

<?php if ($grades): ?>
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

<a href="index.php">Back to Home</a>
