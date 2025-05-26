<?php
session_start();
@include('user_auth.php');

// Ensure that only users with an admin role can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Access Denied - Admin Only');
}

require_once 'config.php';

$message = "";

// Resolve incident if requested
if (isset($_GET['resolve'])) {
    $incident_id = (int) $_GET['resolve'];
    $stmt = $pdo->prepare("UPDATE incidents SET status = 'Resolved', resolution_date = NOW() WHERE id = ?");
    $stmt->execute([$incident_id]);
    $message = "Incident ID $incident_id marked as resolved.";
}

// Fetch attendance & incident data
$attendance_query = $pdo->query("SELECT * FROM attendance ORDER BY date DESC, id DESC");
$incident_query   = $pdo->query("SELECT * FROM incidents ORDER BY incident_date DESC");

// BIA metrics
$total    = $pdo->query("SELECT COUNT(*) FROM incidents")->fetchColumn();
$pending  = $pdo->query("SELECT COUNT(*) FROM incidents WHERE status = 'Pending'")->fetchColumn();
$resolved = $pdo->query("SELECT COUNT(*) FROM incidents WHERE status = 'Resolved'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SafeTime - Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .card {
      border-radius: 1rem;
    }
    .nav-tabs .nav-link.active {
      font-weight: bold;
      background-color: #e9ecef;
    }
    .logout-btn {
      position: absolute;
      top: 1rem;
      right: 1rem;
    }
  </style>
</head>
<body>
<div class="container mt-5 position-relative">
  <a href="logout.php" class="btn btn-outline-danger logout-btn">Logout</a>
  <h1 class="mb-4 text-center">SafeTime Admin Dashboard</h1>

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
    <!-- Attendance Tab -->
    <div class="tab-pane fade show active" id="attendance" role="tabpanel">
      <h2>Attendance Records</h2>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
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
    </div>

    <!-- Incident Tab -->
    <div class="tab-pane fade" id="incidents" role="tabpanel">
      <h2>Incident Reports</h2>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
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
                    <span class="badge bg-success">Resolved</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- BIA Tab -->
    <div class="tab-pane fade" id="bia" role="tabpanel">
      <h2 class="mb-4">Business Impact Analysis (BIA)</h2>
      <div class="row">
        <div class="col-md-4 mb-3">
          <div class="card border-primary shadow-sm">
            <div class="card-body">
              <h5 class="card-title text-primary"><i class="bi bi-exclamation-circle"></i> Total Incidents</h5>
              <p class="card-text display-6"><?php echo $total; ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="card border-warning shadow-sm">
            <div class="card-body">
              <h5 class="card-title text-warning"><i class="bi bi-hourglass-split"></i> Pending Incidents</h5>
              <p class="card-text display-6"><?php echo $pending; ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="card border-success shadow-sm">
            <div class="card-body">
              <h5 class="card-title text-success"><i class="bi bi-check-circle"></i> Resolved Incidents</h5>
              <p class="card-text display-6"><?php echo $resolved; ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>