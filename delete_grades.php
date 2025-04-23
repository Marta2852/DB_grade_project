<?php
require_once 'config.php';
require_once 'auth.php';

requireLogin();

if (!isset($_GET['id'])) {
    die("Grade ID is required.");
}

$gradeId = $_GET['id'];

// First, delete the grade
$stmt = $pdo->prepare("DELETE FROM grades WHERE id = ?");
$stmt->execute([$gradeId]);

// Redirect back to the grades page with a success message
header("Location: view_grades.php?action=deleted");
exit;
