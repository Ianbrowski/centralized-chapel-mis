<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cathedral Management System - Login</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f4f6f9;
            font-family: Arial, sans-serif;
        }

        .login-card {
            width: 350px;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            text-align: center;
        }

        .login-card h2 {
            margin-bottom: 10px;
        }

        .login-card input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .login-card button {
            width: 100%;
            padding: 12px;
            background-color: #2c3e50;
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }

        .login-card button:hover {
            background-color: #34495e;
        }

        .error {
            color: red;
            font-size: 13px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Cathedral Management System</h2>

    <?php
    if (isset($_GET['error'])) {
        echo "<p class='error'>Invalid username or password.</p>";
    }
    ?>

    <form action="authenticate.php" method="POST">
        <input type="text" name="user" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
