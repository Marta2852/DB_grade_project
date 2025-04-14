<?php
require_once 'config.php';
require_once 'auth.php';

requireLogin();  
if (!isTeacher()) {
    echo "Access Denied";  // Teachers only
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert the student into the database
    $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$first_name, $last_name, $email, $password]);

    echo "Student added successfully. <a href='index.php'>Back to Dashboard</a>";
}
?>

<h2>Add Student</h2>
<form method="post">
    <input name="first_name" placeholder="First Name" required><br>
    <input name="last_name" placeholder="Last Name" required><br>
    <input name="email" type="email" placeholder="Email" required><br>
    <input name="password" type="password" placeholder="Password" required><br>
    <button type="submit">Add Student</button>
</form>
