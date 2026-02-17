<?php
session_start();
require 'config.php';

$email = $_POST['user'];
$password = $_POST['password'];

/* Prepare statement (SECURE) */
$stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $user = $result->fetch_assoc();

    /* Verify password */
    if (password_verify($password, $user['password'])) {

        // Store session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email']   = $user['email'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']    = $user['role'];

        header("Location: dashboard.php");
        exit();

    } else {
        header("Location: login.php?error=1");
        exit();
    }

} else {
    header("Location: login.php?error=1");
    exit();
}
?>