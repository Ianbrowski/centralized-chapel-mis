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

    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" type="image/x-icon" href="img/2323.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cathedral Management System - Login</title>

</head>
<body>

<div class="header">

	<img class="pic" src="img/2323.png">
     <h2  href="index.html" class="logo">Cathedral Management System

</div>

<section class="hero">

<div class="login-card">
    <h2>Cathedral Management System</h2>

<?php
if (isset($_GET['error'])) {

    if ($_GET['error'] == "empty_all") {
        echo "<p style='color:red;'>Please enter username and password.</p>";
    }

    if ($_GET['error'] == "empty_user") {
        echo "<p style='color:red;'>Username is required.</p>";
    }

    if ($_GET['error'] == "empty_pass") {
        echo "<p style='color:red;'>Password is required.</p>";
    }

    if ($_GET['error'] == "wrong_pass") {
        echo "<p style='color:red;'>Incorrect password.</p>";
    }

    if ($_GET['error'] == "user_not_found") {
        echo "<p style='color:red;'>User not found.</p>";
    }
}
?>

    <form action="authenticate.php" method="POST">
        <input type="text" name="user" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>

</section>

</body>
</html>
