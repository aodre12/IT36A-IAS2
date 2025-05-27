<?php
session_start();
require_once 'config.php';
@include('user_auth.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 2;       
    $_SESSION['role'] = 'employee';
}
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Ensure this page is only accessed by employees.
if ($role !== 'employee') {
    die('Access Denied - Employees Only');
}

$message = "";

// Process Attendance and Incident Reporting actions.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Attendance: Clock In
    if (isset($_POST['clock_in'])) {
        $stmt = $pdo->prepare("SELECT * FROM attendance WHERE user_id = ? AND date = CURDATE()");
        $stmt->execute([$user_id]);
        if ($stmt->rowCount() == 0) {
            $stmt = $pdo->prepare("INSERT INTO attendance (user_id, date, time_in) VALUES (?, CURDATE(), CURTIME())");
            $stmt->execute([$user_id]);
            $message = "Clocked in successfully!";
        } else {
            $message = "You have already clocked in today!";
        }
    }

    // Attendance: Clock Out
    if (isset($_POST['clock_out'])) {
        $stmt = $pdo->prepare("UPDATE attendance SET time_out = CURTIME() WHERE user_id = ? AND date = CURDATE() AND time_out IS NULL");
        $stmt->execute([$user_id]);
        $message = "Clocked out successfully!";
    }

    // Incident Reporting
    if (isset($_POST['submit_incident'])) {
        $incident_type = $_POST['incident_type'];
        $description = $_POST['description'];
        $stmt = $pdo->prepare("INSERT INTO incidents (user_id, incident_type, description) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $incident_type, $description]);
        $message = "Incident reported successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SafeTime - Employee Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .card { border-radius: 1rem; }
    .nav-tabs .nav-link.active { font-weight: bold; background-color: #e9ecef; }
    .logout-btn { position: absolute; top: 1rem; right: 1rem; }
  </style>
</head>
<body>
<div class="container mt-5 position-relative">
  <a href="logout.php" class="btn btn-outline-danger logout-btn">Logout</a>
  <h1 class="mb-4 text-center">SafeTime Employee Dashboard</h1>

  <?php if ($message): ?>
    <div class="alert alert-info"><?php echo $message; ?></div>
  <?php endif; ?>

  <!-- Navigation Tabs -->
  <ul class="nav nav-tabs" id="employeeTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="attendance-action-tab" data-bs-toggle="tab" data-bs-target="#attendance-action" type="button" role="tab">Clock In/Out</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="incident-report-tab" data-bs-toggle="tab" data-bs-target="#incident-report" type="button" role="tab">Report Incident</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="my-attendance-tab" data-bs-toggle="tab" data-bs-target="#my-attendance" type="button" role="tab">My Attendance</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="my-incidents-tab" data-bs-toggle="tab" data-bs-target="#my-incidents" type="button" role="tab">My Incidents</button>
    </li>
  </ul>

  <div class="tab-content mt-4">
    <!-- Clock In/Out Tab -->
    <div class="tab-pane fade show active" id="attendance-action" role="tabpanel">
      <h2>Attendance Actions</h2>
      <?php
      // Ensure $pdo is available, already handled by require_once 'config.php';
      $stmt = $pdo->prepare("SELECT * FROM attendance WHERE user_id = ? AND date = CURDATE() ORDER BY id DESC LIMIT 1");
      $stmt->execute([$user_id]);
      $attendance = $stmt->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="card mb-4 p-3 shadow-sm">
          <?php if (!$attendance): ?>
              <p>You have not clocked in yet today.</p>
              <form method="post">
                  <button type="submit" name="clock_in" class="btn btn-primary">Clock In</button>
              </form>
          <?php elseif ($attendance && empty($attendance['time_out'])): ?>
              <p>Clocked in at: <strong><?php echo htmlspecialchars($attendance['time_in']); ?></strong></p>
              <form method="post">
                  <button type="submit" name="clock_out" class="btn btn-warning">Clock Out</button>
              </form>
          <?php else: ?>
              <p>You clocked in at: <strong><?php echo htmlspecialchars($attendance['time_in']); ?></strong> and clocked out at: <strong><?php echo htmlspecialchars($attendance['time_out']); ?></strong></p>
              <p>Your attendance is complete for today.</p>
          <?php endif; ?>
      </div>
    </div>

    <!-- Incident Reporting Tab -->
    <div class="tab-pane fade" id="incident-report" role="tabpanel">
      <h2>Incident Reporting</h2>
      <div class="card mb-4 p-3 shadow-sm">
        <form method="post">
          <div class="mb-3">
              <label for="incident_type" class="form-label">Incident Type</label>
              <input type="text" class="form-control" id="incident_type" name="incident_type" placeholder="E.g., Power outage, Security breach" required>
          </div>
          <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" id="description" name="description" rows="4" placeholder="Describe the incident…" required></textarea>
          </div>
          <button type="submit" name="submit_incident" class="btn btn-danger">Submit Incident</button>
        </form>
      </div>
    </div>

    <!-- My Attendance History Tab -->
    <div class="tab-pane fade" id="my-attendance" role="tabpanel">
      <h2>My Attendance History</h2>
      <div class="table-responsive">
         <table class="table table-bordered table-striped">
           <thead class="table-light">
             <tr>
                <th>Date</th>
                <th>Time In</th>
                <th>Time Out</th>
             </tr>
           </thead>
           <tbody>
             <?php
             $stmt_att_history = $pdo->prepare("SELECT * FROM attendance WHERE user_id = ? ORDER BY date DESC, id DESC");
             $stmt_att_history->execute([$user_id]);
             while ($row = $stmt_att_history->fetch(PDO::FETCH_ASSOC)):
             ?>
              <tr>
                <td><?php echo htmlspecialchars($row['date']); ?></td>
                <td><?php echo htmlspecialchars($row['time_in']); ?></td>
                <td><?php echo htmlspecialchars($row['time_out'] ? $row['time_out'] : 'N/A'); ?></td>
              </tr>
             <?php endwhile; ?>
             <?php $stmt_att_history = null; ?>
           </tbody>
         </table>
       </div>
    </div>

    <!-- My Incident Reports Tab -->
    <div class="tab-pane fade" id="my-incidents" role="tabpanel">
      <h2>My Incident Reports</h2>
      <div class="table-responsive">
         <table class="table table-bordered table-striped">
           <thead class="table-light">
             <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Description</th>
                <th>Date Reported</th>
                <th>Status</th>
             </tr>
           </thead>
           <tbody>
             <?php
             $stmt_inc_history = $pdo->prepare("SELECT * FROM incidents WHERE user_id = ? ORDER BY incident_date DESC, id DESC");
             $stmt_inc_history->execute([$user_id]);
             while ($row = $stmt_inc_history->fetch(PDO::FETCH_ASSOC)):
             ?>
              <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['incident_type']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars($row['incident_date']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
              </tr>
             <?php endwhile; ?>
             <?php $stmt_inc_history = null; ?>
           </tbody>
         </table>
       </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>