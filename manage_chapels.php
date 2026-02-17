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
    <title>Manage Chapels</title>
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
    <h1>Manage Chapels</h1>
    <p>Create and update chapel records.</p>

    <div class="action-bar" style="display:flex;justify-content:space-between;align-items:center;margin:18px 0;">
      <a href="#" class="logout-btn">+ Add Chapel</a>
      <form method="GET" style="display:flex;gap:8px;">
        <input type="search" name="q" placeholder="Search chapels..." style="padding:8px;border-radius:6px;border:1px solid #ddd;">
        <button type="submit" class="logout-btn">Search</button>
      </form>
    </div>

    <table class="data-table" style="width:100%;border-collapse:collapse;">
      <thead>
        <tr style="text-align:left;border-bottom:1px solid #e5e7eb;">
          <th style="padding:12px">ID</th>
          <th>Name</th>
          <th>Location</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="padding:12px">1</td>
          <td>Main Cathedral Chapel</td>
          <td>Downtown</td>
          <td><a href="#">Edit</a> | <a href="#">Delete</a></td>
        </tr>
      </tbody>
    </table>

    <p style="margin-top:12px;"><a href="dashboard.php">Back to Dashboard</a></p>
  </div>
</section>
</body>
</html>
