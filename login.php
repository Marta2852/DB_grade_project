<?php
require_once 'config.php';
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $table = $role === 'teacher' ? 'teachers' : 'students';

    $stmt = $pdo->prepare("SELECT * FROM $table WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $role;
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<h2>Login</h2>
<?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>

<form method="post">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <select name="role">
        <option value="teacher">Teacher</option>
        <option value="student">Student</option>
    </select><br>
    <button type="submit">Login</button>
</form>
