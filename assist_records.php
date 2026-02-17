<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit();
}
$role = $_SESSION['role'];
require 'config.php';

// Allow Staff and above
$allowed = ['Staff','Pastor','Chapel Admin','Cathedral Admin','Super Admin'];
if (!in_array($role, $allowed, true)) {
  header('Location: dashboard.php');
  exit();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $event_type = trim($_POST['event_type'] ?? '');
  $start_time = trim($_POST['start_time'] ?? '');
  $chapel_location = trim($_POST['chapel_location'] ?? '');
  $pastor_assigned = trim($_POST['pastor_assigned'] ?? '');
  $status = trim($_POST['status'] ?? 'Pending');

  if ($title === '') $errors[] = 'Title is required.';
  if ($event_type === '') $errors[] = 'Event type is required.';
  if ($start_time === '') $errors[] = 'Start time is required.';
  if ($chapel_location === '') $errors[] = 'Chapel location is required.';

  // normalize datetime-local to MySQL DATETIME
  if (empty($errors)) {
    // If input uses datetime-local (e.g. 2026-02-12T10:00), convert
    $dt = str_replace('T', ' ', $start_time);
    // Optionally validate
    $d = date_create($dt);
    if ($d === false) {
      $errors[] = 'Invalid start time format.';
    } else {
      $start_time_db = $d->format('Y-m-d H:i:s');
    }
  }

  if (empty($errors)) {
    $stmt = $conn->prepare('INSERT INTO events (title, event_type, start_time, chapel_location, pastor_assigned, status) VALUES (?, ?, ?, ?, ?, ?)');
    if ($stmt) {
      $stmt->bind_param('ssssss', $title, $event_type, $start_time_db, $chapel_location, $pastor_assigned, $status);
      if ($stmt->execute()) {
        $stmt->close();
        header('Location: view_schedule.php?created=1');
        exit();
      } else {
        $errors[] = 'Database error: failed to insert event.';
      }
    } else {
      $errors[] = 'Database error: failed to prepare statement.';
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Assist Records</title>
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
    <h1>Assist Records</h1>
    <p>Input new baptism/wedding records into the events database.</p>

    <?php if (!empty($errors)): ?>
      <div style="background:#fee2e2;color:#821717;padding:10px;border-radius:6px;margin-bottom:12px;">
        <?php foreach ($errors as $e) echo htmlspecialchars($e) . '<br>'; ?>
      </div>
    <?php endif; ?>

    <form method="POST" style="max-width:640px;">
      <div style="margin-bottom:10px;">
        <label>Title</label>
        <input type="text" name="title" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;">
      </div>
      <div style="margin-bottom:10px;display:flex;gap:8px;">
        <div style="flex:1;">
          <label>Event Type</label>
          <select name="event_type" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;">
            <option value="">Select type</option>
            <option value="Baptism" <?php echo (isset($event_type) && $event_type==='Baptism') ? 'selected' : ''; ?>>Baptism</option>
            <option value="Wedding" <?php echo (isset($event_type) && $event_type==='Wedding') ? 'selected' : ''; ?>>Wedding</option>
            <option value="Other" <?php echo (isset($event_type) && $event_type==='Other') ? 'selected' : ''; ?>>Other</option>
          </select>
        </div>
        <div style="flex:1;">
          <label>Start Time</label>
          <input type="datetime-local" name="start_time" value="<?php echo isset($start_time) ? htmlspecialchars($start_time) : ''; ?>" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;">
        </div>
      </div>
      <div style="margin-bottom:10px;">
        <label>Chapel Location</label>
        <input type="text" name="chapel_location" value="<?php echo isset($chapel_location) ? htmlspecialchars($chapel_location) : ''; ?>" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;">
      </div>
      <div style="margin-bottom:10px;display:flex;gap:8px;">
        <div style="flex:1;">
          <label>Pastor Assigned</label>
          <input type="text" name="pastor_assigned" value="<?php echo isset($pastor_assigned) ? htmlspecialchars($pastor_assigned) : ''; ?>" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;">
        </div>
        <div style="width:160px;">
          <label>Status</label>
          <select name="status" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;">
            <option value="Pending" <?php echo (isset($status) && $status==='Pending') ? 'selected' : ''; ?>>Pending</option>
            <option value="Confirmed" <?php echo (isset($status) && $status==='Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
            <option value="Completed" <?php echo (isset($status) && $status==='Completed') ? 'selected' : ''; ?>>Completed</option>
          </select>
        </div>
      </div>
      <div style="display:flex;gap:8px;">
        <button type="submit" class="logout-btn">Save Event</button>
        <a href="view_schedule.php" class="logout-btn" style="background:#6b7280;">Cancel</a>
      </div>
    </form>

    <p style="margin-top:12px;"><a href="dashboard.php">Back to Dashboard</a></p>
  </div>
</section>
</body>
</html>
