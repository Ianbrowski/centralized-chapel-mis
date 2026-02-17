<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>View Schedule</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="img/2323.png">
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
    <h1>View Schedule</h1>
    <p>Upcoming events and schedule overview.</p>

    <div class="action-bar" style="display:flex;justify-content:space-between;align-items:center;margin:18px 0;">
      <div>
        <label>Show: 
          <select>
            <option>All</option>
            <option>Today</option>
            <option>This Week</option>
          </select>
        </label>
      </div>
      <a href="update_events.php" class="logout-btn">Manage Events</a>
    </div>

    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr style="text-align:left;border-bottom:1px solid #e5e7eb;"><th style="padding:12px">Date</th><th>Time</th><th>Event</th><th>Location</th></tr>
      </thead>
      <tbody>
        <tr>
          <td style="padding:12px">2026-02-14</td>
          <td>10:00</td>
          <td>Sunday Service</td>
          <td>Main Cathedral</td>
        </tr>
      </tbody>
    </table>

    <p style="margin-top:12px;"><a href="dashboard.php">Back to Dashboard</a></p>
  </div>
</section>
</body>
</html>
