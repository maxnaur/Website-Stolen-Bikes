<?php
session_start();
require_once 'db.php';

// =========================================================
// Authentication Check
// =========================================================
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// =========================================================
// Fetch User's Bicycles
// =========================================================
try {
    // Select all bicycles associated with the current user ID
    $stmt = $pdo->prepare("SELECT * FROM bicycles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $bikes = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching bikes: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - Bike Safety</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="form-base.css">
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
      <a class="navbar-brand" href="#">Bike Safety</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <span class="nav-link text-white">Welcome, <?php echo htmlspecialchars($user_name); ?></span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="auth.php?action=logout">Logout</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <!-- Display Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>My Bicycles</h2>
      <a href="add_bike.php" class="btn btn-primary">Register New Bike</a>
    </div>

    <!-- Check if the user has any registered bikes -->
    <?php if (empty($bikes)): ?>
      <div class="alert alert-info">You haven't registered any bikes yet.</div>
    <?php else: ?>
      <div class="row">
        <!-- Loop through each bike and display it in a card -->
        <?php foreach ($bikes as $bike): ?>
          <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm <?php echo $bike['status'] == 'stolen' ? 'border-danger' : ''; ?>">
              <!-- Bike Image Display -->
              <?php if ($bike['image_path']): ?>
                <img src="<?php echo htmlspecialchars($bike['image_path']); ?>" class="card-img-top" alt="Bike Image" style="height: 200px; object-fit: cover;">
              <?php else: ?>
                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                  No Image
                </div>
              <?php endif; ?>
              
              <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($bike['brand'] . ' ' . $bike['model']); ?></h5>
                <p class="card-text">
                  <strong>Type:</strong> <?php echo htmlspecialchars($bike['type']); ?><br>
                  <strong>Colour:</strong> <?php echo htmlspecialchars($bike['colour']); ?><br>
                  <strong>Status:</strong> 
                  <span class="badge <?php echo $bike['status'] == 'safe' ? 'bg-success' : 'bg-danger'; ?>">
                    <?php echo ucfirst($bike['status']); ?>
                  </span>
                </p>
                <!-- Show investigation status if reported stolen -->
                <?php if ($bike['status'] == 'stolen'): ?>
                  <div class="alert alert-warning small p-2">
                    <strong>Investigation Status:</strong> <?php echo ucfirst($bike['investigation_status']); ?><br>
                    <?php if ($bike['investigation_notes']): ?>
                      <small>Note: <?php echo htmlspecialchars($bike['investigation_notes']); ?></small>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
              </div>
              
              <div class="card-footer bg-white border-top-0">
                <!-- Action Buttons -->
                <?php if ($bike['status'] == 'safe'): ?>
                  <form action="bikes.php" method="POST" onsubmit="return confirm('Are you sure you want to report this bike as stolen?');">
                    <input type="hidden" name="action" value="report_stolen">
                    <input type="hidden" name="bike_id" value="<?php echo $bike['id']; ?>">
                    <button type="submit" class="btn btn-danger w-100">Report Stolen</button>
                  </form>
                <?php else: ?>
                  <button class="btn btn-secondary w-100" disabled>Reported Stolen</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
