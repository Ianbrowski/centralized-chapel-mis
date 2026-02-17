<?php
session_start();
if (!isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit();
}

require 'config.php';

$role = $_SESSION['role'];
$username_session = $_SESSION['username'];

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = trim($_POST['user'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role_new = trim($_POST['role'] ?? '');

        // Basic validation
        if ($user === '') {
                $errors[] = 'Username is required.';
        }
        if ($password === '') {
                $errors[] = 'Password is required.';
        }
        $allowed_roles = ['Super Admin','Cathedral Admin','Chapel Admin','Pastor','Staff'];
        if (!in_array($role_new, $allowed_roles, true)) {
                $errors[] = 'Please select a valid role.';
        }

        // Check username uniqueness
        if (empty($errors)) {
                $check = $conn->prepare('SELECT id FROM users WHERE user = ?');
                $check->bind_param('s', $user);
                $check->execute();
                $res = $check->get_result();
                if ($res && $res->num_rows > 0) {
                        $errors[] = 'Username already exists.';
                }
                $check->close();
        }

        // Insert user
        if (empty($errors)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $insert = $conn->prepare('INSERT INTO users (`user`, `password`, `role`) VALUES (?, ?, ?)');
                if ($insert) {
                        $insert->bind_param('sss', $user, $hash, $role_new);
                        if ($insert->execute()) {
                                $insert->close();
                                header('Location: manage_users.php?created=1');
                                exit();
                        } else {
                                $errors[] = 'Failed to create user (database error).';
                        }
                } else {
                        $errors[] = 'Failed to prepare database statement.';
                }
        }
}
?>
<!DOCTYPE html>
<html>
<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Create User</title>
        <link rel="stylesheet" href="style.css">
        <link rel="icon" href="img/2323.png">
        <style>
            .form-row { margin-bottom:12px; }
            .form-row input, .form-row select { padding:8px;border:1px solid #ddd;border-radius:6px;width:100%; }
            .error { color:#b91c1c;margin-bottom:12px; }
        </style>
</head>
<body class="dashboard-body">
<div class="header2">
    <div class="header-left">
        <img class="pic" src="img/2323.png" alt="logo">
        <h2 class="logo">Cathedral Management System</h2>
    </div>
    <div class="header-right">
        <span>Role: <strong><?php echo htmlspecialchars($role); ?></strong></span>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>
<section class="hero-dashboard">
    <div class="container-centered">
        <h1>Create User</h1>
        <p>Create a new user account. Passwords are stored securely.</p>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $e) echo htmlspecialchars($e) . '<br>'; ?>
            </div>
        <?php endif; ?>

        <form method="POST" style="max-width:480px;">
            <div class="form-row">
                <label>Username</label>
                <input type="text" name="user" value="<?php echo isset($user) ? htmlspecialchars($user) : ''; ?>" required>
            </div>
            <div class="form-row">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-row">
                <label>Role</label>
                <select name="role" required>
                    <option value="">Select role</option>
                    <option>Super Admin</option>
                    <option>Cathedral Admin</option>
                    <option>Chapel Admin</option>
                    <option>Pastor</option>
                    <option>Staff</option>
                </select>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="logout-btn">Create User</button>
                <a href="manage_users.php" class="logout-btn" style="background:#6b7280;">Cancel</a>
            </div>
        </form>

        <p style="margin-top:12px;"><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
</section>
</body>
</html>