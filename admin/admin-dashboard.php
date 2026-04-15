<?php
require_once '../common/auth-check.php';
checkRole('admin');
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bike Police System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .admin-card { border-left: 4px solid #dc3545; }
        .stat-card { text-align: center; }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-danger shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">⚙️ Admin Control Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#" onclick="loadDashboard()">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="loadUsers()">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="loadBikes()">Bikes</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="loadReports()">Reports</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                            <?php echo htmlspecialchars($user['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" onclick="showProfile()">Profile</a></li>
                            <li><a class="dropdown-item" href="#" onclick="showSettings()">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../login/logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid mt-4 mb-5">
        <h2 class="mb-4 text-danger fw-bold">📊 System Administration Dashboard</h2>

        <!-- Statistics Row -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm admin-card stat-card">
                    <div class="card-body">
                        <h6 class="text-muted">Total Users</h6>
                        <h3 class="text-primary" id="totalUsers">0</h3>
                        <small class="text-muted">Active accounts</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm admin-card stat-card">
                    <div class="card-body">
                        <h6 class="text-muted">Registered Bikes</h6>
                        <h3 class="text-info" id="totalBikes">0</h3>
                        <small class="text-muted">In system</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm admin-card stat-card">
                    <div class="card-body">
                        <h6 class="text-muted">Open Reports</h6>
                        <h3 class="text-warning" id="openReports">0</h3>
                        <small class="text-muted">Pending investigation</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm admin-card stat-card">
                    <div class="card-body">
                        <h6 class="text-muted">Stolen Bikes</h6>
                        <h3 class="text-danger" id="stolenBikes">0</h3>
                        <small class="text-muted">Active cases</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs for different sections -->
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <ul class="nav nav-tabs card-header-tabs" id="adminTabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#users" data-bs-toggle="tab">👥 Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#bikes" data-bs-toggle="tab">🚲 Bikes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#reports" data-bs-toggle="tab">📋 Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#system" data-bs-toggle="tab">⚙️ System</a>
                    </li>
                </ul>
            </div>

            <div class="tab-content p-4">
                <!-- Users Tab -->
                <div class="tab-pane fade show active" id="users">
                    <h5 class="mb-3">User Management</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTable">
                                <tr><td colspan="6" class="text-center text-muted">Loading users...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Bikes Tab -->
                <div class="tab-pane fade" id="bikes">
                    <h5 class="mb-3">Bicycle Registry</h5>
                    <button class="btn btn-sm btn-danger mb-3" onclick="exportBikes()">📥 Export Bikes</button>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>MPN</th>
                                    <th>Brand/Model</th>
                                    <th>Owner</th>
                                    <th>Status</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="bikesTable">
                                <tr><td colspan="6" class="text-center text-muted">Loading bikes...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Reports Tab -->
                <div class="tab-pane fade" id="reports">
                    <h5 class="mb-3">All Reports</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Report ID</th>
                                    <th>Bike</th>
                                    <th>Status</th>
                                    <th>Investigation</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="reportsTable">
                                <tr><td colspan="6" class="text-center text-muted">Loading reports...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- System Tab -->
                <div class="tab-pane fade" id="system">
                    <h5 class="mb-3">System Management</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-danger w-100 mb-2" onclick="backupDatabase()">💾 Backup Database</button>
                            <button class="btn btn-warning w-100 mb-2" onclick="clearLogs()">🗑️ Clear Logs</button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-info w-100 mb-2" onclick="systemStatus()">📊 System Status</button>
                            <button class="btn btn-secondary w-100 mb-2" onclick="userGuide()">📖 User Guide</button>
                        </div>
                    </div>
                    <hr>
                    <h6 class="mt-4">System Information</h6>
                    <ul class="list-unstyled">
                        <li><strong>Database:</strong> bike_police_system</li>
                        <li><strong>Server:</strong> localhost</li>
                        <li><strong>Admin User:</strong> <?php echo htmlspecialchars($user['username']); ?></li>
                        <li><strong>Last Backup:</strong> <span id="lastBackup">Never</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Administrator Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                    <p><strong>Role:</strong> Administrator</p>
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($user['id']); ?></p>
                    <p><strong>Access Level:</strong> Full System Access</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showProfile() {
            new bootstrap.Modal(document.getElementById('profileModal')).show();
        }

        function showSettings() {
            alert('Settings panel (Backend integration needed)');
        }

        function loadDashboard() {
            location.reload();
        }

        function loadUsers() {
            document.getElementById('usersTable').innerHTML = '<tr><td colspan="6" class="text-center">Loading users...</td></tr>';
            // Backend call here
        }

        function loadBikes() {
            document.getElementById('bikesTable').innerHTML = '<tr><td colspan="6" class="text-center">Loading bikes...</td></tr>';
            // Backend call here
        }

        function loadReports() {
            document.getElementById('reportsTable').innerHTML = '<tr><td colspan="6" class="text-center">Loading reports...</td></tr>';
            // Backend call here
        }

        function exportBikes() {
            alert('Export bikes to CSV (Backend integration needed)');
        }

        function backupDatabase() {
            alert('Database backup started (Backend integration needed)');
        }

        function clearLogs() {
            if (confirm('Are you sure? This cannot be undone.')) {
                alert('Logs cleared (Backend integration needed)');
            }
        }

        function systemStatus() {
            alert('System Status: All systems operational');
        }

        function userGuide() {
            window.open('#', '_blank');
        }
    </script>
</body>
</html>
