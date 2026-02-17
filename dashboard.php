<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" type="image/x-icon" href="img/2323.png">
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
    <div class="dashboard-container container-centered">
        <div class="welcome-block" style="width:100%; text-align:center; margin-bottom:18px;">
            <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
            <p>Your Role: <?php echo htmlspecialchars($role); ?></p>
        </div>

        <?php if($role == 'Super Admin'): ?>
            <div class="module-box">
                <h2>Manage Users</h2>
                <button onclick="window.location.href='manage_users.php'">Go</button>
            </div>
            <div class="module-box">
                <h2>Manage Chapels</h2>
                <button onclick="window.location.href='manage_chapels.php'">Go</button>
            </div>
            <div class="module-box">
                <h2>Reports</h2>
                <button onclick="window.location.href='reports.php'">Go</button>
            </div>
        <?php endif; ?>

        <?php if($role == 'Cathedral Admin'): ?>
            <div class="module-box">
                <h2>Manage Scheduling</h2>
                <button onclick="window.location.href='schedule_cathedral.php'">Go</button>
            </div>
            <div class="module-box">
                <h2>Sacramental Records</h2>
                <button onclick="window.location.href='records_cathedral.php'">Go</button>
            </div>
            <div class="module-box">
                <h2>Reports</h2>
                <button onclick="window.location.href='reports.php'">Go</button>
            </div>
        <?php endif; ?>

        <?php if($role == 'Chapel Admin'): ?>
            <div class="module-box">
                <h2>Manage Chapel Schedule</h2>
                <button onclick="window.location.href='schedule_chapel.php'">Go</button>
            </div>
            <div class="module-box">
                <h2>Records</h2>
                <button onclick="window.location.href='records_chapel.php'">Go</button>
            </div>
        <?php endif; ?>

        <?php if($role == 'Pastor'): ?>
            <div class="module-box">
                <h2>View Schedule</h2>
                <button onclick="window.location.href='view_schedule.php'">Go</button>
            </div>
            <div class="module-box">
                <h2>Update Events</h2>
                <button onclick="window.location.href='update_events.php'">Go</button>
            </div>
        <?php endif; ?>

        <?php if($role == 'Staff'): ?>
            <div class="module-box">
                <h2>Assist Records</h2>
                <button onclick="window.location.href='assist_records.php'">Go</button>
            </div>
            <div class="module-box">
                <h2>View Schedule</h2>
                <button onclick="window.location.href='view_schedule.php'">Go</button>
            </div>
        <?php endif; ?>

    </div>
</section>

</body>
</html>