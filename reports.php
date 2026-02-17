<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit();
}
$role = $_SESSION['role'];
require 'config.php';

// Handle filters
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

// Build query
$params = [];
$sql = 'SELECT event_id, title, event_type, start_time, chapel_location, pastor_assigned, status FROM events';
if ($from !== '' || $to !== '') {
  $clauses = [];
  if ($from !== '') { $clauses[] = 'start_time >= ?'; $params[] = $from . ' 00:00:00'; }
  if ($to !== '') { $clauses[] = 'start_time <= ?'; $params[] = $to . ' 23:59:59'; }
  $sql .= ' WHERE ' . implode(' AND ', $clauses);
}
 $sql .= ' ORDER BY start_time DESC';

// Execute
$events = [];
if ($stmt = $conn->prepare($sql)) {
  if (count($params) > 0) {
    // build types string
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
  }
  $stmt->execute();
  $res = $stmt->get_result();
  while ($r = $res->fetch_assoc()) { $events[] = $r; }
  $stmt->close();
}

// CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename="events_report.csv"');
  $out = fopen('php://output','w');
  fputcsv($out, ['event_id','title','event_type','start_time','chapel_location','pastor_assigned','status']);
  foreach ($events as $e) {
    fputcsv($out, [$e['event_id'],$e['title'],$e['event_type'],$e['start_time'],$e['chapel_location'],$e['pastor_assigned'],$e['status']]);
  }
  fclose($out);
  exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Reports</title>
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
    <h1>Reports</h1>
    <p>Generate and export reports.</p>

    <form method="GET" class="action-bar" style="display:flex;gap:12px;align-items:center;margin:18px 0;">
      <label>From: <input type="date" name="from" value="<?php echo htmlspecialchars($from); ?>"></label>
      <label>To: <input type="date" name="to" value="<?php echo htmlspecialchars($to); ?>"></label>
      <button type="submit" class="logout-btn">Generate</button>
      <a href="?<?php echo http_build_query(array_merge($_GET,['export'=>'csv'])); ?>" class="logout-btn">Export CSV</a>
    </form>

    <div style="background:#fff;padding:18px;border-radius:10px;box-shadow:0 8px 20px rgba(2,6,23,0.04);">
      <?php if (count($events) === 0): ?>
        <p>No events found for the selected range.</p>
      <?php else: ?>
        <table style="width:100%;border-collapse:collapse;">
          <thead>
            <tr style="text-align:left;border-bottom:1px solid #e5e7eb;">
              <th style="padding:10px">ID</th>
              <th>Title</th>
              <th>Type</th>
              <th>Start Time</th>
              <th>Location</th>
              <th>Pastor</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($events as $ev): ?>
              <tr>
                <td style="padding:10px"><?php echo htmlspecialchars($ev['event_id']); ?></td>
                <td><?php echo htmlspecialchars($ev['title']); ?></td>
                <td><?php echo htmlspecialchars($ev['event_type']); ?></td>
                <td><?php echo htmlspecialchars($ev['start_time']); ?></td>
                <td><?php echo htmlspecialchars($ev['chapel_location']); ?></td>
                <td><?php echo htmlspecialchars($ev['pastor_assigned']); ?></td>
                <td><?php echo htmlspecialchars($ev['status']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

    <p style="margin-top:12px;"><a href="dashboard.php">Back to Dashboard</a></p>
  </div>
</section>
</body>
</html>
