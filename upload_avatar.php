<?php
require_once 'config.php';
require_once 'auth.php';

requireLogin();

$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];
    $uploadDir = 'uploads/avatars/';
    
    // Make sure the folder exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // true means it creates nested folders
    }

    $fileName = basename($file['name']);
    $targetFile = $uploadDir . time() . '_' . $fileName;

    // Check file type
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            // Update avatar path in the right table
            if ($role === 'teacher') {
                $stmt = $pdo->prepare("UPDATE teachers SET avatar = ? WHERE id = ?");
            } else {
                $stmt = $pdo->prepare("UPDATE students SET avatar = ? WHERE id = ?");
            }
            $stmt->execute([$targetFile, $userId]);
            $message = "Avatar uploaded successfully!";
        } else {
            $message = "Error uploading the file.";
        }
    } else {
        $message = "Only JPG, JPEG, PNG & GIF files are allowed.";
    }
}
?>

<h2>Upload Avatar</h2>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="avatar" required>
    <button type="submit">Upload</button>
</form>

<p><?= htmlspecialchars($message) ?></p>

<a href="index.php">Back to Home</a>
