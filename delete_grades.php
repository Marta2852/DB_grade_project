<?php
require_once 'config.php';
require_once 'auth.php';

requireLogin();

// Check if the grade ID is provided
if (!isset($_GET['id'])) {
    die("Grade ID is required.");
}

$gradeId = $_GET['id'];

// Delete the grade from the grades table
$stmt = $pdo->prepare("DELETE FROM grades WHERE id = ?");
$stmt->execute([$gradeId]);

// Redirect to the view grades page
header("Location: view_grades.php");
exit;
