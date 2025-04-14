<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

function isTeacher() {
    return $_SESSION['role'] === 'teacher';
}

function isStudent() {
    return $_SESSION['role'] === 'student';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}
?>
