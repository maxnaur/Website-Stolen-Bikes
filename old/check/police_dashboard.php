<?php
session_start();
require_once 'db.php';

// =========================================================
// Access Control
// =========================================================
// Restrict access to 'police' and 'admin' roles only
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'police' && $_SESSION['role'] !== 'admin')) {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name'];
$role = $_SESSION['role'];

// =========================================================
// Fetch Stolen Bikes with Filtering
// =========================================================
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Base query to fetch stolen bikes and their owner's details
$sql = "SELECT b.*, u.first_name, u.last_name, u.email, u.phone 
        FROM bicycles b 
        JOIN users u ON b.user_id = u.id 
        WHERE b.status = 'stolen'";

// Apply time-based filters based on 'report_date'
if ($filter == 'week') {
    $sql .= " AND b.report_date >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
} elseif ($filter == 'month') {
    $sql .= " AND b.report_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
} elseif ($filter == 'year') {
    $sql .= " AND b.report_date >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
}
$sql .= " ORDER BY b.report_date DESC";

try {
    $stmt = $pdo->query($sql);
    $stolen_bikes = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}

// =========================================================
// Handle Add Police Officer (Admin Only)
// =========================================================
if ($role == 'admin' && isset($_POST['action']) && $_POST['action'] == 'add_police') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, 'police')");
        $stmt->execute([$first_name, $last_name, $email, $password]);
        $success_msg = "Police officer added successfully.";
    } catch (PDOException $e) {
        $error_msg = "Error adding police: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Police Dashboard - Bike Safety</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="form-base.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
      <a class="navbar-brand" href="#">Police Control Panel</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <span class="nav-link text-white">Officer: <?php echo htmlspecialchars($user_name); ?> (<?php echo ucfirst($role); ?>)</span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="auth.php?action=logout">Logout</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($success_msg)): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($error_msg)): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <!-- Admin Section: Only visible to users with 'admin' role -->
    <?php if ($role == 'admin'): ?>
    <div class="card mb-4">
      <div class="card-header bg-primary text-white">Admin: Enrol New Police Officer</div>
      <div class="card-body">
        <form method="POST">
          <input type="hidden" name="action" value="add_police">
          <div class="row g-2 align-items-center">
            <div class="col-md-3">
              <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
            </div>
            <div class="col-md-3">
              <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
            </div>
            <div class="col-md-3">
              <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="col-md-2">
              <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <div class="col-md-1">
              <button type="submit" class="btn btn-success w-100">Add</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <?php endif; ?>

    <!-- Stolen Bikes List Section -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>Reported Stolen Bicycles</h2>
      <!-- Filter Buttons -->
      <div class="btn-group">
        <a href="?filter=all" class="btn btn-outline-secondary <?php echo $filter=='all'?'active':''; ?>">All Time</a>
        <a href="?filter=year" class="btn btn-outline-secondary <?php echo $filter=='year'?'active':''; ?>">This Year</a>
        <a href="?filter=month" class="btn btn-outline-secondary <?php echo $filter=='month'?'active':''; ?>">This Month</a>
        <a href="?filter=week" class="btn btn-outline-secondary <?php echo $filter=='week'?'active':''; ?>">This Week</a>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-hover border">
        <thead class="table-light">
          <tr>
            <th>Reported</th>
            <th>Bike Details</th>
            <th>Owner Contact</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($stolen_bikes as $bike): ?>
          <tr>
            <td><?php echo date('d M Y', strtotime($bike['report_date'])); ?></td>
            <td>
              <strong><?php echo htmlspecialchars($bike['brand'] . ' ' . $bike['model']); ?></strong><br>
              <small>MPN: <?php echo htmlspecialchars($bike['mpn']); ?></small><br>
              <small>Colour: <?php echo htmlspecialchars($bike['colour']); ?></small>
            </td>
            <td>
              <?php echo htmlspecialchars($bike['first_name'] . ' ' . $bike['last_name']); ?><br>
              <small><?php echo htmlspecialchars($bike['phone']); ?></small><br>
              <small><?php echo htmlspecialchars($bike['email']); ?></small>
            </td>
            <td>
              <span class="badge bg-warning text-dark"><?php echo ucfirst($bike['investigation_status']); ?></span>
            </td>
            <td>
              <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $bike['id']; ?>">
                Update Status
              </button>
            </td>
          </tr>

          <!-- Modal for Updating Investigation Status -->
          <div class="modal fade" id="updateModal<?php echo $bike['id']; ?>" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Update Investigation: <?php echo htmlspecialchars($bike['brand']); ?></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="bikes.php" method="POST">
                  <div class="modal-body">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="bike_id" value="<?php echo $bike['id']; ?>">
                    
                    <div class="mb-3">
                      <label class="form-label">Investigation Status</label>
                      <select name="investigation_status" class="form-select">
                        <option value="open" <?php echo $bike['investigation_status']=='open'?'selected':''; ?>>Open</option>
                        <option value="in_progress" <?php echo $bike['investigation_status']=='in_progress'?'selected':''; ?>>In Progress</option>
                        <option value="closed" <?php echo $bike['investigation_status']=='closed'?'selected':''; ?>>Closed (Found/Unsolved)</option>
                      </select>
                    </div>
                    
                    <div class="mb-3">
                      <label class="form-label">Notes</label>
                      <textarea name="investigation_notes" class="form-control" rows="3"><?php echo htmlspecialchars($bike['investigation_notes']); ?></textarea>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
