<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; 
    $_SESSION['role'] = 'admin';
}
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Ensure only admins can access this page.
if ($role !== 'admin') {
    die('Access Denied - Admin Only');
}

$message = "";

// Process Admin Actions
if (isset($_GET['resolve'])) {
    $incident_id = (int)$_GET['resolve'];
    $stmt = $pdo->prepare("UPDATE incidents SET status = 'Resolved', resolution_date = NOW() WHERE id = ?");
    $stmt->execute([$incident_id]);
    $message = "Incident ID $incident_id marked as resolved.";
}

// Retrieve all attendance records and incident reports.
$attendance_query = $pdo->query("SELECT * FROM attendance ORDER BY date DESC, id DESC");
$incident_query = $pdo->query("SELECT * FROM incidents ORDER BY incident_date DESC");

// Business Impact Analysis (BIA) metrics.
$total = $pdo->query("SELECT COUNT(*) FROM incidents")->fetchColumn();
$pending = $pdo->query("SELECT COUNT(*) FROM incidents WHERE status = 'Pending'")->fetchColumn();
$resolved = $pdo->query("SELECT COUNT(*) FROM incidents WHERE status = 'Resolved'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SafeTime - Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h1 class="mb-4">SafeTime - Admin Dashboard</h1>
  
  <?php if ($message): ?>
    <div class="alert alert-info"><?php echo $message; ?></div>
  <?php endif; ?>

  <!-- Navigation Tabs -->
  <ul class="nav nav-tabs" id="adminTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab">Attendance</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="incidents-tab" data-bs-toggle="tab" data-bs-target="#incidents" type="button" role="tab">Incidents</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="bia-tab" data-bs-toggle="tab" data-bs-target="#bia" type="button" role="tab">Business Impact Analysis</button>
    </li>
  </ul>

  <div class="tab-content mt-4">
    <!-- Attendance Records Section -->
    <div class="tab-pane fade show active" id="attendance" role="tabpanel">
      <h2>Attendance Records</h2>
      <table class="table table-bordered">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>User ID</th>
            <th>Date</th>
            <th>Time In</th>
            <th>Time Out</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $attendance_query->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
              <td><?php echo $row['id']; ?></td>
              <td><?php echo $row['user_id']; ?></td>
              <td><?php echo $row['date']; ?></td>
              <td><?php echo $row['time_in']; ?></td>
              <td><?php echo $row['time_out']; ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <!-- Incident Reports Section -->
    <div class="tab-pane fade" id="incidents" role="tabpanel">
      <h2>Incident Reports</h2>
      <table class="table table-bordered">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>User ID</th>
            <th>Type</th>
            <th>Description</th>
            <th>Date Reported</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $incident_query->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
              <td><?php echo $row['id']; ?></td>
              <td><?php echo $row['user_id']; ?></td>
              <td><?php echo $row['incident_type']; ?></td>
              <td><?php echo $row['description']; ?></td>
              <td><?php echo $row['incident_date']; ?></td>
              <td><?php echo $row['status']; ?></td>
              <td>
                <?php if ($row['status'] === 'Pending'): ?>
                  <a href="admin.php?resolve=<?php echo $row['id']; ?>" class="btn btn-sm btn-success">Mark Resolved</a>
                <?php else: ?>
                  Resolved
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <!-- Business Impact Analysis (BIA) Section -->
    <div class="tab-pane fade" id="bia" role="tabpanel">
      <h2>Business Impact Analysis (BIA)</h2>
      <ul class="list-group">
        <li class="list-group-item">Total Incidents Reported: <strong><?php echo $total; ?></strong></li>
        <li class="list-group-item">Pending Incidents: <strong><?php echo $pending; ?></strong></li>
        <li class="list-group-item">Resolved Incidents: <strong><?php echo $resolved; ?></strong></li>
      </ul>
    </div>
  </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
