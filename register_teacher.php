<?php
require_once 'config.php';

function isStrongPassword($password) {
    return preg_match('/^(?=.*[A-Z])(?=.*[\d\W]).{8,}$/', $password);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password_raw = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    if (!isStrongPassword($password_raw)) {
        die("Password must be at least 8 characters, include one uppercase letter, and one number or special character.");
    }

    // Check for duplicate email
    $check = $pdo->prepare("SELECT * FROM teachers WHERE email = ?");
    $check->execute([$email]);
    if ($check->rowCount() > 0) {
        die("Email already exists.");
    }

    $password = password_hash($password_raw, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO teachers (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $password]);

    echo "Teacher registered. <a href='login.php'>Login</a>";
}

?>

<h2>Register Teacher</h2>
<form method="post">
<input name="name" pattern="[A-Za-zĀāĒēĪīŪūĶķĻļŅņŠšĢģŽžČč\s]+" maxlength="50" required><br>
<input name="email" type="email" required><br>
<input type="password" name="password" required minlength="8"
       title="Password must be at least 8 characters, include one uppercase letter, and one number or special character."> <br>

    <button type="submit">Register</button>
</form>

<script src="validation.js"></script>