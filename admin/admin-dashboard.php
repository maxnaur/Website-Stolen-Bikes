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
      <li class="nav-item"><a class="nav-link fw-bold text-warning" href="../police/police-dashboard.php">🚔 Police View</a></li>
      <li class="nav-item me-3"><a class="nav-link fw-bold text-info" href="../public/public-dashboard.php">👤 Public View</a></li>
      <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
        <?php echo htmlspecialchars($user['first_name']); ?>
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
      <a class="nav-link" href="#logs" data-bs-toggle="tab">📜 Activity Logs</a>
      </li>
    </ul>
    </div>

    <div class="tab-content p-4">
    <!-- Users Tab -->
    <div class="tab-pane fade show active" id="users">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">User Management</h5>
        <div>
          <button class="btn btn-sm btn-success me-2" data-bs-toggle="modal" data-bs-target="#addUserModal">➕ Add New User</button>
          <button class="btn btn-sm btn-danger" onclick="exportUsers()">📥 Download All Users</button>
        </div>
      </div>
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
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">All Reports</h5>
        <button class="btn btn-sm btn-danger" onclick="exportReports()">📥 Export Reports</button>
      </div>
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

    <!-- Logs Tab -->
    <div class="tab-pane fade" id="logs">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">System Activity Logs</h5>
        <button class="btn btn-sm btn-outline-danger" onclick="loadLogs()">🔄 Refresh Logs</button>
      </div>
      <div class="table-responsive">
      <table class="table table-sm table-striped table-hover small">
        <thead class="table-light">
        <tr>
          <th>Timestamp</th>
          <th>User</th>
          <th>Action</th>
          <th>Target</th>
          <th>Details</th>
          <th>IP Address</th>
        </tr>
        </thead>
        <tbody id="logsTable">
        <tr><td colspan="6" class="text-center text-muted">Loading logs...</td></tr>
        </tbody>
      </table>
      </div>
    </div>
    </div>
  </div>
  </div>

  <!-- Add User Modal -->
  <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
      <h5 class="modal-title" id="addUserModalLabel">➕ Create New User / Officer</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <form id="addUserForm">
        <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold small">First Name *</label>
          <input type="text" class="form-control" name="first_name" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold small">Last Name *</label>
          <input type="text" class="form-control" name="last_name" required>
        </div>
        </div>
        <div class="mb-3">
        <label class="form-label fw-bold small">Email Address *</label>
        <input type="email" class="form-control" name="email" required>
        </div>
        <div class="mb-3">
        <label class="form-label fw-bold small">Temporary Password *</label>
        <input type="password" class="form-control" name="password" required>
        </div>
        <div class="mb-3">
        <label class="form-label fw-bold small">User Role *</label>
        <select class="form-select" name="role" required>
          <option value="police" selected>Police Officer</option>
          <option value="public">Public User</option>
          <option value="admin">Administrator</option>
        </select>
        </div>
        <hr>
        <div class="mb-3">
        <label class="form-label fw-bold small">Address Line 1</label>
        <input type="text" class="form-control" name="address_line1">
        </div>
        <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold small">City</label>
          <input type="text" class="form-control" name="city">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold small">Postcode</label>
          <input type="text" class="form-control" name="postcode">
        </div>
        </div>
        <button type="submit" class="btn btn-success w-100">Create User Account</button>
      </form>
      </div>
    </div>
    </div>
  </div>

  <!-- Profile Modal -->
  <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
      <h5 class="modal-title" id="profileModalLabel">👤 Administrator Profile Information</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold small">First Name</label>
          <input type="text" class="form-control bg-light" id="profile_first_name" readonly>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold small">Last Name</label>
          <input type="text" class="form-control bg-light" id="profile_last_name" readonly>
        </div>
        </div>
        <div class="mb-3">
        <label class="form-label fw-bold small">Email Address</label>
        <input type="email" class="form-control bg-light" id="profile_email" readonly>
        </div>
        <hr>
        <div class="mb-3">
        <label class="form-label fw-bold small">Phone Number</label>
        <input type="text" class="form-control bg-light" id="profile_phone" readonly>
        </div>
        <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold small">Address Line 1</label>
          <input type="text" class="form-control bg-light" id="profile_address_line1" readonly>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold small">Address Line 2</label>
          <input type="text" class="form-control bg-light" id="profile_address_line2" readonly>
        </div>
        </div>
        <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label fw-bold small">City</label>
          <input type="text" class="form-control bg-light" id="profile_city" readonly>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label fw-bold small">County</label>
          <input type="text" class="form-control bg-light" id="profile_county" readonly>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label fw-bold small">Postcode</label>
          <input type="text" class="form-control bg-light" id="profile_postcode" readonly>
        </div>
        </div>
        <div class="alert alert-warning py-2 small mb-0">
          <i class="bi bi-info-circle me-2"></i> Administrator profiles are managed by system configuration and cannot be edited here.
        </div>
      </div>
    </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    loadStats();
    loadUsers();
    loadBikes();
    loadReports();
    loadLogs();
  });

  document.getElementById('addUserForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    
    try {
      const response = await fetch('../api/users.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      const result = await response.json();
      
      if (result.success) {
        alert('User created successfully!');
        e.target.reset();
        bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
        loadUsers();
        loadStats();
      } else {
        alert('Error: ' + result.message);
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Failed to create user.');
    }
  });

  async function loadStats() {
    try {
    const [usersRes, bikesRes, reportsRes] = await Promise.all([
      fetch('../api/users.php?action=list'),
      fetch('../api/bicycles.php?action=list'),
      fetch('../api/reports.php')
    ]);
    
    const users = await usersRes.json();
    const bikes = await bikesRes.json();
    const reports = await reportsRes.json();
    
    if (users.success && bikes.success && reports.success) {
      document.getElementById('totalUsers').innerText = users.data.length;
      document.getElementById('totalBikes').innerText = bikes.data.length;
      document.getElementById('openReports').innerText = reports.data.filter(r => r.investigation_status === 'open').length;
      document.getElementById('stolenBikes').innerText = bikes.data.filter(b => b.status === 'stolen').length;
    }
    } catch (error) {
    console.error('Error loading stats:', error);
    }
  }

  async function loadUsers() {
    const tableBody = document.getElementById('usersTable');
    try {
    const response = await fetch('../api/users.php?action=list');
    const result = await response.json();
    if (result.success) {
      tableBody.innerHTML = '';
      result.data.forEach(user => {
      tableBody.innerHTML += `
        <tr>
        <td>#${user.id}</td>
        <td>${user.first_name} ${user.last_name}</td>
        <td>${user.email}</td>
        <td><span class="badge bg-secondary">${user.role.toUpperCase()}</span></td>
        <td>${new Date(user.created_at).toLocaleDateString()}</td>
        <td>
          <button class="btn btn-sm btn-outline-primary" onclick="editUser(${user.id})">Edit</button>
        </td>
        </tr>
      `;
      });
    }
    } catch (error) {
    tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading users.</td></tr>';
    }
  }

  async function loadBikes() {
    const tableBody = document.getElementById('bikesTable');
    try {
    const response = await fetch('../api/bicycles.php?action=list');
    const result = await response.json();
    if (result.success) {
      tableBody.innerHTML = '';
      result.data.forEach(bike => {
      const statusClass = bike.status === 'safe' ? 'bg-success' : 'bg-danger';
      tableBody.innerHTML += `
        <tr>
        <td>${bike.mpn || 'N/A'}</td>
        <td>${bike.brand} ${bike.model}</td>
        <td>${bike.first_name} ${bike.last_name}</td>
        <td><span class="badge ${statusClass}">${bike.status.toUpperCase()}</span></td>
        <td>${new Date(bike.created_at).toLocaleDateString()}</td>
        <td>
          <button class="btn btn-sm btn-outline-primary" onclick="viewBike(${bike.id})">Details</button>
        </td>
        </tr>
      `;
      });
    }
    } catch (error) {
    tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading bikes.</td></tr>';
    }
  }

  async function loadReports() {
    const tableBody = document.getElementById('reportsTable');
    try {
    const response = await fetch('../api/reports.php');
    const result = await response.json();
    if (result.success) {
      tableBody.innerHTML = '';
      result.data.forEach(report => {
      tableBody.innerHTML += `
        <tr>
        <td>#${report.id}</td>
        <td>${report.brand} ${report.model}</td>
        <td><span class="badge bg-danger">${report.bike_status.toUpperCase()}</span></td>
        <td><span class="badge bg-warning text-dark">${report.investigation_status.toUpperCase()}</span></td>
        <td>${new Date(report.report_date).toLocaleDateString()}</td>
        <td>
          <button class="btn btn-sm btn-outline-primary" onclick="viewReport(${report.id})">Details</button>
        </td>
        </tr>
      `;
      });
    }
    } catch (error) {
    tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading reports.</td></tr>';
    }
  }

  async function loadLogs() {
    const tableBody = document.getElementById('logsTable');
    try {
      const response = await fetch('../api/logs.php?action=list');
      const result = await response.json();
      if (result.success) {
        tableBody.innerHTML = '';
        if (result.data.length === 0) {
          tableBody.innerHTML = '<tr><td colspan="6" class="text-center p-3">No activity logs found.</td></tr>';
          return;
        }
        result.data.forEach(log => {
          tableBody.innerHTML += `
            <tr>
              <td>${new Date(log.created_at).toLocaleString()}</td>
              <td>${log.user_email || 'Anonymous'}</td>
              <td><strong>${log.action}</strong></td>
              <td>${log.table_name || '-'}${log.record_id ? ' (#' + log.record_id + ')' : ''}</td>
              <td class="text-wrap">${log.details || ''}</td>
              <td>${log.ip_address || ''}</td>
            </tr>
          `;
        });
      }
    } catch (error) {
      tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading logs.</td></tr>';
    }
  }

  function exportBikes() {
    window.location.href = '../api/bicycles.php?action=export';
  }

  async function showProfile() {
    try {
      const response = await fetch('../api/users.php?action=profile');
      const result = await response.json();
      
      if (result.success) {
        const user = result.data;
        document.getElementById('profile_first_name').value = user.first_name;
        document.getElementById('profile_last_name').value = user.last_name;
        document.getElementById('profile_email').value = user.email;
        document.getElementById('profile_phone').value = user.phone || 'Not set';
        document.getElementById('profile_address_line1').value = user.address_line1 || 'Not set';
        document.getElementById('profile_address_line2').value = user.address_line2 || '';
        document.getElementById('profile_city').value = user.city || 'Not set';
        document.getElementById('profile_county').value = user.county || 'Not set';
        document.getElementById('profile_postcode').value = user.postcode || 'Not set';
        
        const modal = new bootstrap.Modal(document.getElementById('profileModal'));
        modal.show();
      } else {
        alert('Error loading profile: ' + result.message);
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Failed to load profile details.');
    }
  }

  function exportUsers() {
    window.location.href = '../api/users.php?action=export';
  }

  function exportReports() {
    window.location.href = '../api/reports.php?action=export';
  }

  function editUser(id) {
    fetch(`../api/users.php?action=detail&id=${id}`)
      .then(res => res.json())
      .then(result => {
        if (result.success) {
          alert(`Editing User: ${result.data.first_name} ${result.data.last_name}\nRole: ${result.data.role}\n(Edit Modal would go here)`);
        }
      });
  }

  function viewBike(id) {
    window.location.href = `../public/public-dashboard.php?view_bike=${id}`;
  }

  function viewReport(id) {
    window.location.href = '../police/police-dashboard.php?report_id=' + id;
  }
  </script>
</body>
</html>