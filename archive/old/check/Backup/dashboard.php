<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
  header('Location: index.html');
  exit();
}

// You can pull additional user data from database if needed
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - Bike's Safety</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="form-base.css">
</head>
<body>
  <div class="container py-4">
  <div class="row justify-content-center">
  <div class="col-md-8">
  <div class="card shadow-sm">
    <div class="card-body">
    <h1 class="text-center mb-4">Welcome to your Dashboard</h1>
    <p class="lead text-center">You are logged in as <strong><?php echo htmlspecialchars($_SESSION['email']); ?></strong></p>
    <div class="text-center">
    <a href="logout.php" class="btn btn-secondary">Log Out</a>
    </div>
    </div>
  </div>
  </div>
  </div>
  </div>
</body>
</html>