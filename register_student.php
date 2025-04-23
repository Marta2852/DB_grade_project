<?php
require_once 'config.php'; 

function isValidLatvianName($name) {
    return preg_match('/^[A-Za-zĀāĒēĪīŪūĶķĻļŅņŠšĢģŽžČč]+$/u', $name);
}

function isStrongPassword($password) {
    return preg_match('/^(?=.*[A-Z])(?=.*[\d\W]).{8,}$/', $password);
}




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password_raw = $_POST['password'];

    if (!isValidLatvianName($first_name) || !isValidLatvianName($last_name)) {
        die("First and last name must contain only Latvian letters.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    if (!isStrongPassword($password_raw)) {
        die("Password must be at least 6 characters.");
    }

    // Check for duplicate email
    $check = $pdo->prepare("SELECT * FROM students WHERE email = ?");
    $check->execute([$email]);
    if ($check->rowCount() > 0) {
        die("Email already registered.");
    }

    $password = password_hash($password_raw, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$first_name, $last_name, $email, $password]);

    echo "Student registered successfully. <a href='login.php'>Login</a>";
}

?>

<h2>Register Student</h2>
<form method="post">
<input name="first_name" pattern="[A-Za-zĀāĒēĪīŪūĶķĻļŅņŠšĢģŽžČč]+" maxlength="30" required> <br>
<input name="last_name" pattern="[A-Za-zĀāĒēĪīŪūĶķĻļŅņŠšĢģŽžČč]+" maxlength="30" required><br>
<input name="email" type="email" required><br>
<input type="password" name="password" required minlength="8"
       title="Password must be at least 8 characters, include one uppercase letter, and one number or special character."> <br>
    <button type="submit">Register</button>
</form>

<script src="validation.js"></script>