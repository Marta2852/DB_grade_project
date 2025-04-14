<?php
require_once 'config.php';
require_once 'auth.php';

requireLogin();
if (!isTeacher()) {
    echo "Access Denied";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_name = $_POST['subject_name'];

    // Insert the subject into the database
    $stmt = $pdo->prepare("INSERT INTO subjects (subject_name) VALUES (?)");
    $stmt->execute([$subject_name]);

    echo "Subject added successfully. <a href='index.php'>Back to Dashboard</a>";
}
?>

<h2>Add Subject</h2>
<form method="post">
    <input name="subject_name" placeholder="Subject Name" required><br>
    <button type="submit">Add Subject</button>
</form>
