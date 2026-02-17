<?php
session_start();
require 'config.php';

// Only handle POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

// Get input safely
$email = trim($_POST['user'] ?? '');
$password = trim($_POST['password'] ?? '');

// 1️⃣ Check for empty fields
if (empty($email) && empty($password)) {
    header("Location: index.php?error=empty_all");
    exit();
} elseif (empty($email)) {
    header("Location: index.php?error=empty_user");
    exit();
} elseif (empty($password)) {
    header("Location: index.php?error=empty_pass");
    exit();
}

// 2️⃣ Prepare statement securely
$stmt = $conn->prepare("SELECT id, user, password, role FROM users WHERE user = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// 3️⃣ Check if user exists
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // 4️⃣ Verify password (hashed). If DB still has plain-text passwords,
    // allow login once and migrate that password to a hashed value.
    if (password_verify($password, $user['password'])) {

        // Store session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['user'];
        $_SESSION['role'] = $user['role'];

        // 5️⃣ Redirect to role-specific dashboards (centralized)
        switch($user['role']) {
            case 'Super Admin':
            case 'Cathedral Admin':
            case 'Chapel Admin':
            case 'Pastor':
            case 'Staff':
                header("Location: dashboard.php");
                break;
            default:
                header("Location: index.php?error=role");
                break;
        }
        exit();

    } elseif ($password === $user['password']) {
        // Legacy plain-text password match: accept login but migrate to hashed

        // Store session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['user'];
        $_SESSION['role'] = $user['role'];

        // Re-hash the plain-text password and update the database
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        if ($update) {
            $update->bind_param("si", $newHash, $user['id']);
            $update->execute();
            $update->close();
        }

        // Redirect to centralized dashboard
        header("Location: dashboard.php");
        exit();

    } else {
        // Wrong password
        header("Location: index.php?error=wrong_pass");
        exit();
    }

} else {
    // User not found
    header("Location: index.php?error=user_not_found");
    exit();
}