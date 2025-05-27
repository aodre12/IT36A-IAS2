<?php
session_start();
@include('user_auth.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Access Denied - Admin Only');
}

require_once 'config.php';

$message = "";

// Resolve incident
if (isset($_GET['resolve'])) {
    $incident_id = (int) $_GET['resolve'];
    $stmt = $pdo->prepare("UPDATE incidents SET status = 'Resolved', resolution_date = NOW() WHERE id = ?");
    if ($stmt->execute([$incident_id])) {
        $message = "Incident ID $incident_id marked as resolved.";
    } else {
        $message = "Error resolving incident ID $incident_id.";
    }
}

// ---- User Management Actions ----

// Add User
if (isset($_POST['add_user'])) {
    $first_name = trim($_POST['add_first_name'] ?? '');
    $last_name = trim($_POST['add_last_name'] ?? '');
    $email = trim($_POST['add_email'] ?? '');
    $password = $_POST['add_password'] ?? '';
    $role = $_POST['add_role'] ?? 'employee'; // Default to employee

    if (!empty($first_name) && !empty($last_name) && !empty($email) && !empty($password) && !empty($role)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Error: Invalid email format for new user.";
        } else {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $message = "Error: Email '$email' already exists.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
                if ($stmt->execute([$first_name, $last_name, $email, $hash, $role])) {
                    $message = "User '$first_name $last_name' added successfully.";
                } else {
                    $message = "Error: Could not add user.";
                }
            }
        }
    } else {
        $message = "Error: All fields are required to add a new user.";
    }
}

// Edit User
if (isset($_POST['edit_user'])) {
    $user_id = (int) ($_POST['edit_user_id'] ?? 0);
    $first_name = trim($_POST['edit_first_name'] ?? '');
    $last_name = trim($_POST['edit_last_name'] ?? '');
    $email = trim($_POST['edit_email'] ?? '');
    $role = $_POST['edit_role'] ?? '';

    if ($user_id > 0 && !empty($first_name) && !empty($last_name) && !empty($email) && !empty($role)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Error: Invalid email format for user ID $user_id.";
        } else {
            // Check if email already exists for another user
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetch()) {
                $message = "Error: Email '$email' already exists for another user.";
            } else {
                $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ? WHERE id = ?");
                if ($stmt->execute([$first_name, $last_name, $email, $role, $user_id])) {
                    $message = "User ID $user_id updated successfully.";
                } else {
                    $message = "Error: Could not update user ID $user_id.";
                }
            }
        }
    } else {
        $message = "Error: All fields are required to edit user ID $user_id, or user ID is invalid.";
    }
}

// Delete User
if (isset($_GET['delete_user_id'])) {
    $user_id_to_delete = (int) $_GET['delete_user_id'];
    if ($user_id_to_delete > 0) {
        // Optional: Prevent deleting the currently logged-in admin or a specific admin ID
        if (isset($_SESSION['user_id']) && $user_id_to_delete == $_SESSION['user_id']) {
            $message = "Error: Cannot delete your own account.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            if ($stmt->execute([$user_id_to_delete])) {
                $message = "User ID $user_id_to_delete deleted successfully.";
            } else {
                $message = "Error: Could not delete user ID $user_id_to_delete.";
            }
        }
    } else {
        $message = "Error: Invalid user ID for deletion.";
    }
}

// ---- End User Management Actions ----

// Fetch data
$attendance_query = $pdo->query("SELECT * FROM attendance ORDER BY date DESC, id DESC");
$incident_query   = $pdo->query("SELECT * FROM incidents ORDER BY incident_date DESC, id DESC"); // Added id DESC for consistent sort
$users_query      = $pdo->query("SELECT id, first_name, last_name, email, role FROM users ORDER BY id ASC");

// BIA metrics
$total    = $pdo->query("SELECT COUNT(*) FROM incidents")->fetchColumn();
$pending  = $pdo->query("SELECT COUNT(*) FROM incidents WHERE status = 'Pending'")->fetchColumn();
$resolved = $pdo->query("SELECT COUNT(*) FROM incidents WHERE status = 'Resolved'")->fetchColumn();

// Average resolution time (in hours)
$avg_resolution = $pdo->query("
    SELECT AVG(TIMESTAMPDIFF(HOUR, incident_date, resolution_date)) 
    FROM incidents 
    WHERE status = 'Resolved'
")->fetchColumn();
$avg_resolution = $avg_resolution ? round($avg_resolution, 2) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SafeTime - Admin Dashboard</title>
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
  <h1 class="mb-4 text-center">SafeTime Admin Dashboard</h1>

  <?php if ($message): ?>
    <div class="alert alert-info"><?php echo $message; ?></div>
  <?php endif; ?>

  <ul class="nav nav-tabs" id="adminTabs" role="tablist">
    <li class="nav-item"><button class="nav-link active" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance">Attendance</button></li>
    <li class="nav-item"><button class="nav-link" id="incidents-tab" data-bs-toggle="tab" data-bs-target="#incidents">Incidents</button></li>
    <li class="nav-item"><button class="nav-link" id="bia-tab" data-bs-toggle="tab" data-bs-target="#bia">Business Impact Analysis</button></li>
    <li class="nav-item"><button class="nav-link" id="user-management-tab" data-bs-toggle="tab" data-bs-target="#user-management">User Management</button></li>
  </ul>

  <div class="tab-content mt-4">
    <!-- Attendance Tab -->
    <div class="tab-pane fade show active" id="attendance">
      <h2>Attendance Records</h2>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-light">
            <tr><th>ID</th><th>User ID</th><th>Date</th><th>Time In</th><th>Time Out</th></tr>
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
    <div class="tab-pane fade" id="incidents">
      <h2>Incident Reports</h2>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-light">
            <tr>
              <th>ID</th><th>User ID</th><th>Type</th><th>Description</th><th>Date Reported</th><th>Status</th><th>Action</th>
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
    <div class="tab-pane fade" id="bia">
      <h2 class="mb-4">Business Impact Analysis (BIA)</h2>
      <div class="row">
        <div class="col-md-3 mb-3">
          <div class="card border-primary shadow-sm">
            <div class="card-body">
              <h5 class="card-title text-primary"><i class="bi bi-exclamation-circle"></i> Total Incidents</h5>
              <p class="card-text display-6"><?php echo $total; ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-3 mb-3">
          <div class="card border-warning shadow-sm">
            <div class="card-body">
              <h5 class="card-title text-warning"><i class="bi bi-hourglass-split"></i> Pending Incidents</h5>
              <p class="card-text display-6"><?php echo $pending; ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-3 mb-3">
          <div class="card border-success shadow-sm">
            <div class="card-body">
              <h5 class="card-title text-success"><i class="bi bi-check-circle"></i> Resolved Incidents</h5>
              <p class="card-text display-6"><?php echo $resolved; ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-3 mb-3">
          <div class="card border-info shadow-sm">
            <div class="card-body">
              <h5 class="card-title text-info"><i class="bi bi-clock-history"></i> Avg. Resolution Time (hrs)</h5>
              <p class="card-text display-6"><?php echo $avg_resolution; ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- User Management Tab -->
    <div class="tab-pane fade" id="user-management">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>User Management</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
          <i class="bi bi-plus-circle"></i> Add New User
        </button>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($user = $users_query->fetch(PDO::FETCH_ASSOC)): ?>
              <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($user['role'])); ?></td>
                <td>
                  <button type="button" class="btn btn-sm btn-warning edit-user-btn" 
                          data-bs-toggle="modal" data-bs-target="#editUserModal"
                          data-user-id="<?php echo $user['id']; ?>"
                          data-first-name="<?php echo htmlspecialchars($user['first_name']); ?>"
                          data-last-name="<?php echo htmlspecialchars($user['last_name']); ?>"
                          data-email="<?php echo htmlspecialchars($user['email']); ?>"
                          data-role="<?php echo $user['role']; ?>">
                    <i class="bi bi-pencil-square"></i> Edit
                  </button>
                  <button type="button" class="btn btn-sm btn-danger delete-user-btn" 
                          data-bs-toggle="modal" data-bs-target="#deleteUserModal"
                          data-user-id="<?php echo $user['id']; ?>"
                          data-user-name="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>">
                    <i class="bi bi-trash"></i> Delete
                  </button>
                </td>
              </tr>
            <?php endwhile; ?>
            <?php $users_query = null; // Close the user query statement ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modals -->
<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="admin.php">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="add_first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="add_first_name" name="add_first_name" required>
          </div>
          <div class="mb-3">
            <label for="add_last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="add_last_name" name="add_last_name" required>
          </div>
          <div class="mb-3">
            <label for="add_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="add_email" name="add_email" required>
          </div>
          <div class="mb-3">
            <label for="add_password" class="form-label">Password</label>
            <input type="password" class="form-control" id="add_password" name="add_password" required>
          </div>
          <div class="mb-3">
            <label for="add_role" class="form-label">Role</label>
            <select class="form-select" id="add_role" name="add_role" required>
              <option value="employee" selected>Employee</option>
              <option value="admin">Admin</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="admin.php">
        <input type="hidden" id="edit_user_id" name="edit_user_id">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="edit_first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="edit_first_name" name="edit_first_name" required>
          </div>
          <div class="mb-3">
            <label for="edit_last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="edit_last_name" name="edit_last_name" required>
          </div>
          <div class="mb-3">
            <label for="edit_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="edit_email" name="edit_email" required>
          </div>
          <div class="mb-3">
            <label for="edit_role" class="form-label">Role</label>
            <select class="form-select" id="edit_role" name="edit_role" required>
              <option value="employee">Employee</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <p class="small text-muted">Note: Password cannot be changed from this form for security reasons. If a password reset is needed, it should be handled via a separate secure process.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" name="edit_user" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteUserModalLabel">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete user <strong id="delete_user_name_display"></strong> (ID: <span id="delete_user_id_display"></span>)?</p>
        <p class="text-danger">This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <a id="confirmDeleteUserLink" href="#" class="btn btn-danger">Delete User</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Edit User Modal: Populate data
    const editUserModal = document.getElementById('editUserModal');
    if (editUserModal) {
        editUserModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const firstName = button.getAttribute('data-first-name');
            const lastName = button.getAttribute('data-last-name');
            const email = button.getAttribute('data-email');
            const role = button.getAttribute('data-role');

            const modalTitle = editUserModal.querySelector('.modal-title');
            const userIdInput = editUserModal.querySelector('#edit_user_id');
            const firstNameInput = editUserModal.querySelector('#edit_first_name');
            const lastNameInput = editUserModal.querySelector('#edit_last_name');
            const emailInput = editUserModal.querySelector('#edit_email');
            const roleSelect = editUserModal.querySelector('#edit_role');

            modalTitle.textContent = 'Edit User: ' + firstName + ' ' + lastName + ' (ID: ' + userId + ')';
            userIdInput.value = userId;
            firstNameInput.value = firstName;
            lastNameInput.value = lastName;
            emailInput.value = email;
            roleSelect.value = role;
        });
    }

    // Delete User Modal: Populate data and set delete link
    const deleteUserModal = document.getElementById('deleteUserModal');
    if (deleteUserModal) {
        deleteUserModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');

            const userNameDisplay = deleteUserModal.querySelector('#delete_user_name_display');
            const userIdDisplay = deleteUserModal.querySelector('#delete_user_id_display');
            const confirmDeleteLink = deleteUserModal.querySelector('#confirmDeleteUserLink');

            userNameDisplay.textContent = userName;
            userIdDisplay.textContent = userId;
            confirmDeleteLink.href = 'admin.php?delete_user_id=' + userId;
        });
    }

    // Logic to switch to the User Management tab if a user management action message exists
    <?php if (!empty($message) && (isset($_POST['add_user']) || isset($_POST['edit_user']) || isset($_GET['delete_user_id']))): ?>
        const userManagementTab = document.getElementById('user-management-tab');
        if (userManagementTab) {
            new bootstrap.Tab(userManagementTab).show();
        }
    <?php endif; ?>
});
</script>
</body>
</html>