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
  <div class="row">
    <div class="col-md-12">
    <div class="alert alert-info shadow-sm" id="welcomeAlert">
      <h4 class="alert-heading">Welcome, <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>!</h4>
      <p>Register your bicycles here to protect them. If a registered bike is lost or stolen, you can quickly file a report for the police.</p>
    </div>
    </div>
  </div>

  <div class="row">
    <!-- Register Bike Form -->
    <div class="col-md-5 mb-4">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-success text-white">
      <h5 class="card-title mb-0">🚲 Register New Bicycle</h5>
      </div>
      <div class="card-body">
      <form id="registerBikeForm">
        <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold small">Brand *</label>
          <input type="text" class="form-control" name="brand" placeholder="e.g. Trek" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold small">Model *</label>
          <input type="text" class="form-control" name="model" placeholder="e.g. FX 3" required>
        </div>
        </div>
        <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold small">Type *</label>
          <select class="form-select" name="type" required>
          <option value="Road">Road</option>
          <option value="Mountain">Mountain</option>
          <option value="Hybrid">Hybrid</option>
          <option value="Electric">Electric</option>
          <option value="BMX">BMX</option>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold small">Colour *</label>
          <input type="text" class="form-control" name="colour" placeholder="e.g. Blue" required>
        </div>
        </div>
        <div class="mb-3">
        <label class="form-label fw-bold small">Serial Number / MPN</label>
        <input type="text" class="form-control" name="mpn" placeholder="Optional but recommended">
        </div>
        <div class="mb-3">
        <label class="form-label fw-bold small">Bike Image</label>
        <input type="file" class="form-control" name="bike_image" accept="image/*">
        </div>
        <button type="submit" class="btn btn-success w-100">Register Bike</button>
      </form>
      </div>
    </div>

    <div class="card shadow-sm border-0 mt-4">
      <div class="card-header bg-danger text-white">
      <h5 class="card-title mb-0">🚨 Report Theft or Loss</h5>
      </div>
      <div class="card-body">
      <form id="reportTheftForm">
        <div class="mb-3">
        <label class="form-label fw-bold small">Select Your Bike *</label>
        <select class="form-select" id="reportBikeSelect" name="bicycle_id" required>
          <option value="">Choose a registered bike...</option>
        </select>
        </div>
        <div class="mb-3">
        <label class="form-label fw-bold small">Incident Status *</label>
        <select class="form-select" name="bike_status" required>
          <option value="stolen">Stolen</option>
          <option value="lost">Lost</option>
        </select>
        </div>
        <div class="mb-3">
        <label class="form-label fw-bold small">Incident Location *</label>
        <input type="text" class="form-control" name="incident_location" placeholder="e.g. Central London Station" required>
        </div>
        <div class="mb-3">
        <label class="form-label fw-bold small">Additional Notes</label>
        <textarea class="form-control" name="investigation_notes" rows="2" placeholder="Any other details?"></textarea>
        </div>
        <button type="submit" class="btn btn-danger w-100">Submit Police Report</button>
      </form>
      </div>
    </div>
    </div>

    <!-- Your Bikes List -->
    <div class="col-md-7 mb-4">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">📊 Your Registered Bicycles</h5>
      <button class="btn btn-sm btn-light" onclick="loadBikes()">Refresh</button>
      </div>
      <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
        <tr>
          <th>Bike Info</th>
          <th>Status</th>
          <th>Registered</th>
          <th>Action</th>
        </tr>
        </thead>
        <tbody id="bikesTableBody">
        <!-- Loaded via AJAX -->
        </tbody>
      </table>
      </div>
    </div>
    
    <div class="card shadow-sm border-0 mt-4">
      <div class="card-header bg-info text-white">
      <h5 class="card-title mb-0">📜 Recent Activity / Reports</h5>
      </div>
      <div class="card-body p-0">
      <ul class="list-group list-group-flush" id="activityList">
        <!-- Loaded via AJAX -->
      </ul>
      </div>
    </div>
    </div>
  </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    loadBikes();
    loadReports();
  });

  async function loadBikes() {
    try {
    const response = await fetch('../api/bicycles.php?action=list');
    const result = await response.json();
    
    const tableBody = document.getElementById('bikesTableBody');
    const bikeSelect = document.getElementById('reportBikeSelect');
    
    if (result.success) {
      tableBody.innerHTML = '';
      // Keep the first default option
      bikeSelect.innerHTML = '<option value="">Choose a registered bike...</option>';
      
      if (result.data.length === 0) {
      tableBody.innerHTML = '<tr><td colspan="4" class="text-center p-4 text-muted">No bikes registered yet.</td></tr>';
      return;
      }
      
      result.data.forEach(bike => {
      const statusClass = bike.status === 'safe' ? 'bg-success' : 'bg-danger';
      tableBody.innerHTML += `
        <tr>
        <td>
          <strong>${bike.brand} ${bike.model}</strong><br>
          <small class="text-muted">${bike.colour} • ${bike.type}</small>
        </td>
        <td><span class="badge ${statusClass}">${bike.status.toUpperCase()}</span></td>
        <td>${new Date(bike.created_at).toLocaleDateString()}</td>
        <td>
          <button class="btn btn-sm btn-outline-primary" onclick="viewBike(${bike.id})">Details</button>
        </td>
        </tr>
      `;
      
      // Add to report select if not already stolen/lost
      if (bike.status === 'safe') {
        bikeSelect.innerHTML += `<option value="${bike.id}">${bike.brand} ${bike.model} (${bike.colour})</option>`;
      }
      });
    }
    } catch (error) {
    console.error('Error loading bikes:', error);
    }
  }

  async function loadReports() {
    try {
    const response = await fetch('../api/reports.php');
    const result = await response.json();
    const list = document.getElementById('activityList');
    
    if (result.success) {
      list.innerHTML = '';
      if (result.data.length === 0) {
      list.innerHTML = '<li class="list-group-item text-center text-muted p-3">No recent reports.</li>';
      return;
      }
      
      result.data.forEach(report => {
      const statusColor = report.investigation_status === 'open' ? 'text-danger' : 'text-success';
      list.innerHTML += `
        <li class="list-group-item">
        <div class="d-flex w-100 justify-content-between">
          <h6 class="mb-1">${report.brand} ${report.model} reported ${report.bike_status}</h6>
          <small>${new Date(report.report_date).toLocaleDateString()}</small>
        </div>
        <p class="mb-1 text-muted small">
          <strong>Location:</strong> ${report.incident_location || 'Not specified'}<br>
          ${report.investigation_notes || 'No additional notes provided'}
        </p>
        <small class="${statusColor} fw-bold">Status: ${report.investigation_status.replace('_', ' ').toUpperCase()}</small>
        </li>
      `;
      });
    }
    } catch (error) {
    console.error('Error loading reports:', error);
    }
  }

  document.getElementById('registerBikeForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    try {
    const response = await fetch('../api/bicycles.php?action=add', {
      method: 'POST',
      body: formData // Use FormData directly
    });
    const result = await response.json();
    if (result.success) {
      alert('Bike registered successfully!');
      e.target.reset();
      loadBikes();
    } else {
      alert('Error: ' + result.message);
    }
    } catch (error) {
    alert('An error occurred. Please try again.');
    }
  });

  document.getElementById('reportTheftForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    try {
    const response = await fetch('../api/reports.php?action=create', {
      method: 'POST',
      body: formData // Use FormData directly
    });
    const result = await response.json();
    if (result.success) {
      alert('Report submitted to police successfully!');
      e.target.reset();
      loadBikes();
      loadReports();
    } else {
      alert('Error: ' + result.message);
    }
    } catch (error) {
    alert('An error occurred. Please try again.');
    }
  });

  function showProfile() {
    // ... already implemented or can be expanded ...
    alert('Profile view (Mockup)');
  }
  
  function viewBike(id) {
    alert('Viewing bike #' + id + ' details (Mockup)');
  }
  </script>
</body>
</html>
