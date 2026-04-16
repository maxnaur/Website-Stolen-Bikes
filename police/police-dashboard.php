<?php
require_once '../common/auth-check.php';
checkRole('police');
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Police Dashboard - Bike Police System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
  body { background-color: #f8f9fa; }
  .navbar { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
  .alert-status { font-weight: bold; }
  .badge-stolen { background-color: #dc3545; }
  .badge-recovered { background-color: #28a745; }
  .badge-investigating { background-color: #ffc107; }
  </style>
</head>
<body>

  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#">🚔 Police Dashboard</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
    <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a class="nav-link" href="#" onclick="loadReports()">Reports</a></li>
      <li class="nav-item"><a class="nav-link" href="#" onclick="loadBikes()">All Bikes</a></li>
      <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
        <?php echo htmlspecialchars($user['username']); ?>
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
  <h2 class="mb-4 text-success fw-bold">🔍 Stolen Bike Investigation System</h2>

  <!-- Stats Row -->
  <div class="row mb-4">
    <div class="col-md-3">
    <div class="card text-center shadow-sm">
      <div class="card-body">
      <h6 class="text-muted">Active Reports</h6>
      <h3 class="text-danger" id="activeReportCount">0</h3>
      </div>
    </div>
    </div>
    <div class="col-md-3">
    <div class="card text-center shadow-sm">
      <div class="card-body">
      <h6 class="text-muted">Under Investigation</h6>
      <h3 class="text-warning" id="investigatingCount">0</h3>
      </div>
    </div>
    </div>
    <div class="col-md-3">
    <div class="card text-center shadow-sm">
      <div class="card-body">
      <h6 class="text-muted">Recovered</h6>
      <h3 class="text-success" id="recoveredCount">0</h3>
      </div>
    </div>
    </div>
    <div class="col-md-3">
    <div class="card text-center shadow-sm">
      <div class="card-body">
      <h6 class="text-muted">Total Bikes</h6>
      <h3 class="text-primary" id="totalBikeCount">0</h3>
      </div>
    </div>
    </div>
  </div>

  <!-- Reports Table -->
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-success text-white">
    <h5 class="mb-0">📋 Active Reports</h5>
    </div>
    <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle">
      <thead class="table-light">
        <tr>
        <th>Report ID</th>
        <th>Bike Details</th>
        <th>Reporter</th>
        <th>Status</th>
        <th>Investigation</th>
        <th>Actions</th>
        </tr>
      </thead>
      <tbody id="reportsTable">
        <tr><td colspan="6" class="text-center text-muted">Loading reports...</td></tr>
      </tbody>
      </table>
    </div>
    </div>
  </div>

  <!-- Investigation Form -->
  <div class="card shadow-sm">
    <div class="card-header bg-success text-white">
    <h5 class="mb-0">📝 Update Investigation</h5>
    </div>
    <div class="card-body">
    <form id="investigationForm">
      <div class="row mb-3">
      <div class="col-md-6">
        <label for="reportSelect" class="form-label fw-bold">Select Report *</label>
        <select class="form-select" id="reportSelect" required>
        <option value="">Choose a report...</option>
        </select>
      </div>
      <div class="col-md-6">
        <label for="investStatus" class="form-label fw-bold">Investigation Status *</label>
        <select class="form-select" id="investStatus" required>
        <option value="">Select status</option>
        <option value="open">Open</option>
        <option value="in_progress">In Progress</option>
        <option value="closed">Closed</option>
        </select>
      </div>
      </div>
      <div class="mb-3">
      <label for="bikeStatus" class="form-label fw-bold">Bike Status *</label>
      <select class="form-select" id="bikeStatus" required>
        <option value="">Select status</option>
        <option value="stolen">Stolen</option>
        <option value="captured">Captured</option>
        <option value="returned">Returned to Owner</option>
      </select>
      </div>
      <div class="mb-3">
      <label for="notes" class="form-label fw-bold">Investigation Notes *</label>
      <textarea class="form-control" id="notes" rows="4" placeholder="Enter investigation details..." required></textarea>
      </div>
      <button type="submit" class="btn btn-success w-100">Update Investigation</button>
    </form>
    </div>
  </div>
  </div>

  <!-- Profile Modal -->
  <div class="modal fade" id="profileModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">Police Officer Profile</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
      <p><strong>Name:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
      <p><strong>Role:</strong> Police Officer</p>
      <p><strong>ID:</strong> <?php echo htmlspecialchars($user['id']); ?></p>
    </div>
    </div>
  </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    loadReports();
    loadStats();
  });

  async function loadStats() {
    try {
    const [reportsRes, bikesRes] = await Promise.all([
      fetch('../api/reports.php'),
      fetch('../api/bicycles.php?action=list')
    ]);
    
    const reports = await reportsRes.json();
    const bikes = await bikesRes.json();
    
    if (reports.success && bikes.success) {
      const active = reports.data.filter(r => r.investigation_status === 'open').length;
      const inProgress = reports.data.filter(r => r.investigation_status === 'in_progress').length;
      const recovered = reports.data.filter(r => r.bike_status === 'returned' || r.bike_status === 'captured').length;
      
      document.getElementById('activeReportCount').innerText = active;
      document.getElementById('investigatingCount').innerText = inProgress;
      document.getElementById('recoveredCount').innerText = recovered;
      document.getElementById('totalBikeCount').innerText = bikes.data.length;
    }
    } catch (error) {
    console.error('Error loading stats:', error);
    }
  }

  async function loadReports() {
    const tableBody = document.getElementById('reportsTable');
    const reportSelect = document.getElementById('reportSelect');
    
    try {
    const response = await fetch('../api/reports.php');
    const result = await response.json();
    
    if (result.success) {
      tableBody.innerHTML = '';
      reportSelect.innerHTML = '<option value="">Choose a report...</option>';
      
      if (result.data.length === 0) {
      tableBody.innerHTML = '<tr><td colspan="6" class="text-center p-4 text-muted">No reports found in system.</td></tr>';
      return;
      }
      
      result.data.forEach(report => {
      const investBadge = getInvestBadge(report.investigation_status);
      const bikeBadge = getBikeBadge(report.bike_status);
      
      tableBody.innerHTML += `
        <tr>
        <td>#${report.id}</td>
        <td>
          <strong>${report.brand} ${report.model}</strong><br>
          <small class="text-muted">${report.colour}</small>
        </td>
        <td>
          ${report.first_name} ${report.last_name}<br>
          <small class="text-muted">${report.email}</small>
        </td>
        <td>${bikeBadge}</td>
        <td>${investBadge}</td>
        <td>
          <button class="btn btn-sm btn-outline-success" onclick="editInvestigation(${report.id})">Update</button>
        </td>
        </tr>
      `;
      
      reportSelect.innerHTML += `<option value="${report.id}">Report #${report.id} - ${report.brand} ${report.model}</option>`;
      });
    }
    } catch (error) {
    tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading reports.</td></tr>';
    }
  }

  function getInvestBadge(status) {
    switch(status) {
    case 'open': return '<span class="badge bg-danger">OPEN</span>';
    case 'in_progress': return '<span class="badge bg-warning text-dark">IN PROGRESS</span>';
    case 'closed': return '<span class="badge bg-secondary">CLOSED</span>';
    default: return `<span class="badge bg-info">${status.toUpperCase()}</span>`;
    }
  }

  function getBikeBadge(status) {
    switch(status) {
    case 'stolen': return '<span class="badge bg-danger">STOLEN</span>';
    case 'captured': return '<span class="badge bg-primary">CAPTURED</span>';
    case 'returned': return '<span class="badge bg-success">RETURNED</span>';
    default: return `<span class="badge bg-info">${status.toUpperCase()}</span>`;
    }
  }

  function editInvestigation(id) {
    document.getElementById('reportSelect').value = id;
    document.getElementById('reportSelect').scrollIntoView({ behavior: 'smooth' });
  }

  document.getElementById('investigationForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const reportId = document.getElementById('reportSelect').value;
    const data = {
    investigation_status: document.getElementById('investStatus').value,
    bike_status: document.getElementById('bikeStatus').value,
    investigation_notes: document.getElementById('notes').value
    };
    
    try {
    const response = await fetch(`../api/reports.php?action=update&id=${reportId}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    const result = await response.json();
    if (result.success) {
      alert('Investigation updated successfully!');
      e.target.reset();
      loadReports();
      loadStats();
    } else {
      alert('Error: ' + result.message);
    }
    } catch (error) {
    alert('An error occurred. Please try again.');
    }
  });

  async function loadBikes() {
    try {
    const response = await fetch('../api/bicycles.php?action=list');
    const result = await response.json();
    if (result.success) {
      console.log('Bikes:', result.data);
      alert(`System contains ${result.data.length} total registered bicycles.`);
    }
    } catch (error) {
    alert('Error fetching bike list.');
    }
  }
  </script>
</body>
</html>
