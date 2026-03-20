<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register Bike - Bike Safety</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="form-base.css">
</head>
<body>
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow-sm">
          <div class="card-body">
            <h1 class="text-center mb-4">Register New Bike</h1>
            
            <?php if (isset($_GET['error'])): ?>
              <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <form action="bikes.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="action" value="add_bike">
              
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Brand*</label>
                  <input type="text" name="brand" class="form-control" required placeholder="e.g. Raleigh">
                </div>
                
                <div class="col-md-6">
                  <label class="form-label">Model*</label>
                  <input type="text" name="model" class="form-control" required placeholder="e.g. Motus">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Type*</label>
                  <select name="type" class="form-select" required>
                    <option value="">Select Type</option>
                    <option value="Mountain Bike">Mountain Bike</option>
                    <option value="Road Bike">Road Bike</option>
                    <option value="Hybrid">Hybrid</option>
                    <option value="BMX">BMX</option>
                    <option value="Electric Bike">Electric Bike</option>
                    <option value="Other">Other</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Colour*</label>
                  <input type="text" name="colour" class="form-control" required placeholder="e.g. Red">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Manufacturer Part Number (MPN)</label>
                  <input type="text" name="mpn" class="form-control" placeholder="Optional">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Wheel Size</label>
                  <select name="wheel_size" class="form-select">
                    <option value="">Select Size</option>
                    <option value='14"'>14"</option>
                    <option value='20"'>20"</option>
                    <option value='24"'>24"</option>
                    <option value='26"'>26"</option>
                    <option value='27.5"'>27.5"</option>
                    <option value='29"'>29"</option>
                    <option value='700c'>700c</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Number of Gears</label>
                  <input type="number" name="gears" class="form-control" min="1" max="30">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Brake Type</label>
                  <select name="brake_type" class="form-select">
                    <option value="">Select Type</option>
                    <option value="Rim Brakes">Rim Brakes (V-Brakes)</option>
                    <option value="Disc Brakes">Disc Brakes</option>
                    <option value="Drum Brakes">Drum Brakes</option>
                    <option value="Coaster Brakes">Coaster Brakes</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Suspension</label>
                  <select name="suspension" class="form-select">
                    <option value="">Select Type</option>
                    <option value="None">None (Rigid)</option>
                    <option value="Front">Front (Hardtail)</option>
                    <option value="Full">Full Suspension</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Gender</label>
                  <select name="gender" class="form-select">
                    <option value="Unisex">Unisex</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Age Group</label>
                  <select name="age_group" class="form-select">
                    <option value="Adult">Adult</option>
                    <option value="Child">Child</option>
                    <option value="8-10">8-10</option>
                    <option value="10-12">10-12</option>
                  </select>
                </div>

                <div class="col-md-12">
                  <label class="form-label">Bike Image</label>
                  <input type="file" name="image" class="form-control" accept="image/*">
                </div>

              </div>

              <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary px-5">Register Bike</button>
                <a href="dashboard.php" class="btn btn-link">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
