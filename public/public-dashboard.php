<?php
require_once '../common/auth-check.php';
checkRole('public');
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bike Police Report System - Public Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public.css"> 
    <style>
        body { background-color: #f8f9fa; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">🚲 Bike Police System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                            <?php echo htmlspecialchars($user['username']); ?> (Public)
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" onclick="showProfile()">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../login/logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5 mb-5">
        <h2 class="mb-4 text-primary fw-bold">📋 Report Lost or Stolen Bicycle</h2>
        <p class="lead mb-4">Use the form below to report your bike or track reports.</p>

        <div class="row">
            <!-- Report Form -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Submit New Report</h5>
                        <form id="reportForm">
                            <div class="mb-3">
                                <label for="bikeStatus" class="form-label fw-bold">Bike Status *</label>
                                <select class="form-select" id="bikeStatus" required>
                                    <option value="">Select status</option>
                                    <option value="stolen">Stolen</option>
                                    <option value="lost">Lost</option>
                                    <option value="found">Found (Report Found Bike)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label fw-bold">Incident Location *</label>
                                <input type="text" class="form-control" id="location" placeholder="Where did this happen?" required>
                            </div>
                            <div class="mb-3">
                                <label for="bikeBrand" class="form-label fw-bold">Bike Brand/Model *</label>
                                <input type="text" class="form-control" id="bikeBrand" placeholder="e.g., Trek FX 3" required>
                            </div>
                            <div class="mb-3">
                                <label for="bikeColor" class="form-label fw-bold">Bike Color *</label>
                                <input type="text" class="form-control" id="bikeColor" placeholder="e.g., Blue" required>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label fw-bold">Additional Details</label>
                                <textarea class="form-control" id="notes" rows="3" placeholder="Any other details..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Submit Report</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Your Bikes Section -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Your Registered Bikes</h5>
                        <div id="bikesList">
                            <p class="text-muted">Loading your bikes...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports Table -->
        <div class="mt-5 pt-4 border-top">
            <h2 class="mb-3 text-primary fw-bold">📊 Bike Inventory Overview</h2>
            <div class="table-responsive card p-3 shadow-sm">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>Brand & Model</th>
                            <th>Color</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="bikeTable">
                        <tr><td colspan="5" class="text-center text-muted">Loading bikes...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="profileContent">
                    <p class="text-muted">Loading...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showProfile() {
            document.getElementById('profileContent').innerHTML = '<p>User: <?php echo htmlspecialchars($user['username']); ?><br>Role: Public User</p>';
            new bootstrap.Modal(document.getElementById('profileModal')).show();
        }

        document.getElementById('reportForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            // Submit report to backend
            alert('Report submitted! (Backend integration needed)');
        });
    </script>
</body>
</html>
