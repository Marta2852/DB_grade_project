<?php
require_once 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$first_name, $last_name, $email, $password]);

    echo "Student registered successfully. <a href='login.php'>Login</a>";
}
?>

<h2>Register Student</h2>
<form method="post">
    <input name="first_name" placeholder="First Name" required><br>
    <input name="last_name" placeholder="Last Name" required><br>
    <input name="email" type="email" placeholder="Email" required><br>
    <input name="password" type="password" placeholder="Password" required><br>
    <button type="submit">Register</button>
</form>
