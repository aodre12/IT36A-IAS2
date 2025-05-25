<?php
session_start();
include('db.php');

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
</head>
<body>
<div class="container mt-4">
  <h1 class="mb-4">SafeTime - Employee Dashboard</h1>
  
  <?php if ($message): ?>
    <div class="alert alert-info"><?php echo $message; ?></div>
  <?php endif; ?>

  <!-- Attendance Module -->
  <h2>Attendance</h2>
  <?php
  $stmt = $pdo->prepare("SELECT * FROM attendance WHERE user_id = ? AND date = CURDATE() ORDER BY id DESC LIMIT 1");
  $stmt->execute([$user_id]);
  $attendance = $stmt->fetch(PDO::FETCH_ASSOC);
  ?>
  <div class="card mb-4 p-3">
      <?php if (!$attendance): ?>
          <p>You have not clocked in yet today.</p>
          <form method="post">
              <button type="submit" name="clock_in" class="btn btn-primary">Clock In</button>
          </form>
      <?php elseif ($attendance && empty($attendance['time_out'])): ?>
          <p>Clocked in at: <strong><?php echo $attendance['time_in']; ?></strong></p>
          <form method="post">
              <button type="submit" name="clock_out" class="btn btn-warning">Clock Out</button>
          </form>
      <?php else: ?>
          <p>You clocked in at: <strong><?php echo $attendance['time_in']; ?></strong> and clocked out at: <strong><?php echo $attendance['time_out']; ?></strong></p>
          <p>Your attendance is complete for today.</p>
      <?php endif; ?>
  </div>

  <!-- Incident Reporting Module -->
  <h2>Incident Reporting</h2>
  <div class="card mb-4 p-3">
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

  <!-- Employee History Section -->
  <h2>My History</h2>
  <div class="row">
    <div class="col-md-6">
         <h3>Attendance History</h3>
         <table class="table table-bordered">
           <thead class="table-light">
             <tr>
                <th>Date</th>
                <th>Time In</th>
                <th>Time Out</th>
             </tr>
           </thead>
           <tbody>
             <?php
             $stmt = $pdo->prepare("SELECT * FROM attendance WHERE user_id = ? ORDER BY date DESC");
             $stmt->execute([$user_id]);
             while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
             ?>
              <tr>
                <td><?php echo $row['date']; ?></td>
                <td><?php echo $row['time_in']; ?></td>
                <td><?php echo $row['time_out']; ?></td>
              </tr>
             <?php endwhile; ?>
           </tbody>
         </table>
    </div>
    <div class="col-md-6">
         <h3>Incident Reports</h3>
         <table class="table table-bordered">
           <thead class="table-light">
             <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Status</th>
             </tr>
           </thead>
           <tbody>
             <?php
             $stmt = $pdo->prepare("SELECT * FROM incidents WHERE user_id = ? ORDER BY incident_date DESC");
             $stmt->execute([$user_id]);
             while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
             ?>
              <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['incident_type']; ?></td>
                <td><?php echo $row['status']; ?></td>
              </tr>
             <?php endwhile; ?>
           </tbody>
         </table>
    </div>
  </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>