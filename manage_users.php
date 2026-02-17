<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit();
}
$role = $_SESSION['role'];
require 'config.php';

// CSRF token
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf_token'];

// Flash messages
if (!empty($_SESSION['flash'])) {
  $flash = $_SESSION['flash'];
  unset($_SESSION['flash']);
} else {
  $flash = '';
}

// Handle POST actions early (before any output) so header() redirects work
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Basic CSRF check
  if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
    $_SESSION['flash'] = 'Invalid CSRF token.';
    header('Location: manage_users.php');
    exit();
  }

  // Only Super Admin can modify users
  if ($role !== 'Super Admin') {
    $_SESSION['flash'] = 'Permission denied.';
    header('Location: manage_users.php');
    exit();
  }

  $action = $_POST['action'] ?? '';
  if ($action === 'create') {
    $user = trim($_POST['user'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role_new = trim($_POST['role'] ?? '');
    $errors = [];
    if ($user === '') $errors[] = 'Username required.';
    if ($password === '') $errors[] = 'Password required.';
    $allowed_roles = ['Super Admin','Cathedral Admin','Chapel Admin','Pastor','Staff'];
    if (!in_array($role_new, $allowed_roles, true)) $errors[] = 'Invalid role.';

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

    if (empty($errors)) {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $ins = $conn->prepare('INSERT INTO users (`user`, `password`, `role`) VALUES (?, ?, ?)');
      if ($ins) {
        $ins->bind_param('sss', $user, $hash, $role_new);
        if ($ins->execute()) {
          $_SESSION['flash'] = 'User created.';
          $ins->close();
          header('Location: manage_users.php');
          exit();
        } else {
          $_SESSION['flash'] = 'DB error creating user.';
        }
      } else {
        $_SESSION['flash'] = 'DB error preparing statement.';
      }
    } else {
      $_SESSION['flash'] = implode(' ', $errors);
    }

    header('Location: manage_users.php');
    exit();
  }

  if ($action === 'update') {
    $id = intval($_POST['id'] ?? 0);
    $user = trim($_POST['user'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role_new = trim($_POST['role'] ?? '');
    $errors = [];
    if ($id <= 0) $errors[] = 'Invalid user id.';
    if ($user === '') $errors[] = 'Username required.';
    $allowed_roles = ['Super Admin','Cathedral Admin','Chapel Admin','Pastor','Staff'];
    if (!in_array($role_new, $allowed_roles, true)) $errors[] = 'Invalid role.';

    if (empty($errors)) {
      $check = $conn->prepare('SELECT id FROM users WHERE user = ? AND id != ?');
      $check->bind_param('si', $user, $id);
      $check->execute();
      $res = $check->get_result();
      if ($res && $res->num_rows > 0) {
        $errors[] = 'Username already exists.';
      }
      $check->close();
    }

    if (empty($errors)) {
      if ($password !== '') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $upd = $conn->prepare('UPDATE users SET `user` = ?, `password` = ?, `role` = ? WHERE id = ?');
        $upd->bind_param('sssi', $user, $hash, $role_new, $id);
      } else {
        $upd = $conn->prepare('UPDATE users SET `user` = ?, `role` = ? WHERE id = ?');
        $upd->bind_param('ssi', $user, $role_new, $id);
      }

      if ($upd) {
        if ($upd->execute()) {
          $_SESSION['flash'] = 'User updated.';
          $upd->close();
        } else {
          $_SESSION['flash'] = 'DB error updating user.';
        }
      } else {
        $_SESSION['flash'] = 'DB error preparing update.';
      }
    } else {
      $_SESSION['flash'] = implode(' ', $errors);
    }

    header('Location: manage_users.php');
    exit();
  }

  if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
      $_SESSION['flash'] = 'Invalid id.';
      header('Location: manage_users.php');
      exit();
    }
    $del = $conn->prepare('DELETE FROM users WHERE id = ?');
    if ($del) {
      $del->bind_param('i', $id);
      if ($del->execute()) {
        $_SESSION['flash'] = 'User deleted.';
      } else {
        $_SESSION['flash'] = 'DB error deleting user.';
      }
      $del->close();
    } else {
      $_SESSION['flash'] = 'DB error preparing delete.';
    }
    header('Location: manage_users.php');
    exit();
  }
}

// Search/filter and fetch users
$q = trim($_GET['q'] ?? '');
$params = [];
$sql = 'SELECT id, user, role FROM users';
if ($q !== '') {
  $sql .= ' WHERE user LIKE ? OR role LIKE ?';
  $like = "%" . $q . "%";
  $params[] = $like; $params[] = $like;
}
$sql .= ' ORDER BY id DESC';

$users = [];
if ($stmt = $conn->prepare($sql)) {
  if (count($params) > 0) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
  }
  $stmt->execute();
  $res = $stmt->get_result();
  while ($r = $res->fetch_assoc()) { $users[] = $r; }
  $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Manage Users</title>
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
    <h1>Manage Users</h1>
    <p>Manage site users: create, edit, and remove accounts.</p>

    <?php if (isset($_GET['created']) && $_GET['created'] == '1'): ?>
      <div style="background:#ecfdf5;color:#065f46;padding:10px;border-radius:6px;margin-bottom:12px;">User created successfully.</div>
    <?php endif; ?>

    

    <div class="action-bar" style="display:flex;justify-content:space-between;align-items:center;margin:18px 0;">
      <?php if ($role === 'Super Admin'): ?>
        <a href="manage_users.php?action=add" class="logout-btn">+ Add User</a>
      <?php else: ?>
        <span style="color:#6b7280;">User management view</span>
      <?php endif; ?>
      <form method="GET" style="display:flex;gap:8px;">
        <input type="search" name="q" placeholder="Search users..." value="<?php echo htmlspecialchars($q); ?>" style="padding:8px;border-radius:6px;border:1px solid #ddd;">
        <button type="submit" class="logout-btn">Search</button>
      </form>
    </div>

    <?php
    // Add / Edit form UI
    $showForm = false;
    $formMode = '';
    $formData = ['id'=>0,'user'=>'','role'=>'','password'=>''];
    if (isset($_GET['action']) && $_GET['action'] === 'add' && $role === 'Super Admin') {
        $showForm = true; $formMode = 'create';
    }
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && $role === 'Super Admin' && isset($_GET['id'])) {
        $idEdit = intval($_GET['id']);
        if ($idEdit > 0) {
            $stmt = $conn->prepare('SELECT id, user, role FROM users WHERE id = ?');
            if ($stmt) {
                $stmt->bind_param('i',$idEdit);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($row = $res->fetch_assoc()) {
                    $formData['id'] = $row['id'];
                    $formData['user'] = $row['user'];
                    $formData['role'] = $row['role'];
                    $showForm = true; $formMode = 'update';
                }
                $stmt->close();
            }
        }
    }

    if ($showForm): ?>
      <div style="background:#fff;padding:14px;border-radius:8px;margin-bottom:14px;">
        <h3><?php echo $formMode === 'create' ? 'Add New User' : 'Edit User'; ?></h3>
        <form method="POST" style="max-width:520px;">
          <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
          <?php if ($formMode === 'update'): ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?php echo (int)$formData['id']; ?>">
          <?php else: ?>
            <input type="hidden" name="action" value="create">
          <?php endif; ?>

          <div style="margin-bottom:8px;"><label>Username</label>
            <input name="user" value="<?php echo htmlspecialchars($formData['user']); ?>" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;">
          </div>

          <div style="margin-bottom:8px;"><label>Password</label>
            <input type="password" name="password" <?php echo $formMode === 'create' ? 'required' : ''; ?> placeholder="Leave blank to keep existing" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;">
          </div>

          <div style="margin-bottom:8px;"><label>Role</label>
            <select name="role" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;">
              <?php
                $rolesList = ['Super Admin','Cathedral Admin','Chapel Admin','Pastor','Staff'];
                foreach ($rolesList as $rname) {
                    $sel = ($formData['role'] === $rname) ? 'selected' : '';
                    echo "<option value=\"$rname\" $sel>$rname</option>";
                }
              ?>
            </select>
          </div>

          <div style="display:flex;gap:8px;">
            <button type="submit" class="logout-btn"><?php echo $formMode === 'create' ? 'Create' : 'Save'; ?></button>
            <a href="manage_users.php" class="logout-btn" style="background:#6b7280;">Cancel</a>
          </div>
        </form>
      </div>
    <?php endif; ?>

    <table class="data-table" style="width:100%;border-collapse:collapse;">
      <thead>
        <tr style="text-align:left;border-bottom:1px solid #e5e7eb;">
          <th style="padding:12px">ID</th>
          <th>Username</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($users) === 0): ?>
          <tr><td colspan="4" style="padding:12px;color:#6b7280;">No users found.</td></tr>
        <?php else: ?>
          <?php foreach ($users as $u): ?>
            <tr>
              <td style="padding:12px"><?php echo htmlspecialchars($u['id']); ?></td>
              <td><?php echo htmlspecialchars($u['user']); ?></td>
              <td><?php echo htmlspecialchars($u['role']); ?></td>
              <td>
                <?php if ($role === 'Super Admin'): ?>
                  <a href="manage_users.php?action=edit&id=<?php echo $u['id']; ?>">Edit</a> |
                  <form method="POST" action="manage_users.php" style="display:inline;margin:0;padding:0;">
                    <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                    <button type="submit" style="background:none;border:none;color:#ef4444;cursor:pointer;padding:0;margin:0;">Delete</button>
                  </form>
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <p style="margin-top:12px;"><a href="dashboard.php">Back to Dashboard</a></p>
  </div>
</section>
</body>
</html>
