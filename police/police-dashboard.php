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
  .clickable-row { cursor: pointer; }
  .clickable-row:hover { background-color: rgba(0,0,0,0.05) !important; }
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
      <?php if ($user['role'] === 'admin'): ?>
        <li class="nav-item"><a class="nav-link fw-bold text-warning" href="../admin/admin-dashboard.php">⚙️ Admin View</a></li>
        <li class="nav-item me-3"><a class="nav-link fw-bold text-info" href="../public/public-dashboard.php">👤 Public View</a></li>
      <?php endif; ?>
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

  <div class="row">
    <!-- Reports Table -->
    <div class="col-lg-8">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">📋 Active Reports</h5>
        <div class="form-check form-switch small">
          <input class="form-check-input" type="checkbox" id="showClosedReports">
          <label class="form-check-label text-white" for="showClosedReports">Show Closed</label>
        </div>
        </div>
        <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
            <th>Report ID</th>
            <th>Bike Details</th>
            <th>Reporter</th>
            <th>Status</th>
            <th>Actions</th>
            </tr>
          </thead>
          <tbody id="reportsTable">
            <tr><td colspan="5" class="text-center text-muted p-4">Loading reports...</td></tr>
          </tbody>
          </table>
        </div>
        </div>
      </div>
    </div>

    <!-- Bike/Report Detail Section -->
    <div class="col-lg-4">
      <div class="card shadow-sm mb-4 sticky-top" style="top: 20px;">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">ℹ️ Detailed View</h5>
        </div>
        <div class="card-body" id="detailViewContent">
          <p class="text-muted text-center mt-3">Click on a report row to view full details and history.</p>
        </div>
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
  <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
      <h5 class="modal-title" id="profileModalLabel">👤 Police Officer Profile Information</h5>
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
        <div class="alert alert-info py-2 small mb-0">
          <i class="bi bi-info-circle me-2"></i> Police profiles are managed by department administration and cannot be edited here.
        </div>
      </div>
    </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY_HERE&libraries=places"></script>
  <script>
  let allReports = [];

  document.addEventListener('DOMContentLoaded', () => {
    loadReports();
    loadStats();
    
    document.getElementById('showClosedReports').addEventListener('change', renderReports);
  });

  async function loadReports() {
    try {
    const response = await fetch('../api/reports.php');
    const result = await response.json();
    if (result.success) {
      allReports = result.data;
      renderReports();
    }
    } catch (error) {
      console.error('Error loading reports:', error);
    }
  }

  function renderReports() {
    const tableBody = document.getElementById('reportsTable');
    const reportSelect = document.getElementById('reportSelect');
    const showClosed = document.getElementById('showClosedReports').checked;
    
    tableBody.innerHTML = '';
    reportSelect.innerHTML = '<option value="">Choose a report...</option>';
    
    const filtered = allReports.filter(r => showClosed || r.investigation_status !== 'closed');
    
    if (filtered.length === 0) {
      tableBody.innerHTML = `<tr><td colspan="5" class="text-center p-4 text-muted">No ${showClosed ? '' : 'active'} reports found.</td></tr>`;
      return;
    }
    
    filtered.forEach(report => {
      const bikeBadge = getBikeBadge(report.bike_status);
      const investBadge = getInvestBadge(report.investigation_status);
      
      const row = document.createElement('tr');
      row.className = 'clickable-row';
      row.onclick = () => showDetail(report.id);
      row.innerHTML = `
        <td>#${report.id}</td>
        <td>
          <strong class="bike-details-text">${report.brand} ${report.model}</strong><br>
          <small class="text-muted">${report.colour}</small>
        </td>
        <td>
          ${report.first_name} ${report.last_name}
        </td>
        <td>${bikeBadge} ${investBadge}</td>
        <td>
          <button class="btn btn-sm btn-outline-success" onclick="event.stopPropagation(); editInvestigation(${report.id})">Update</button>
        </td>
      `;
      tableBody.appendChild(row);
      
      reportSelect.innerHTML += `<option value="${report.id}">Report #${report.id} - ${report.brand} ${report.model}</option>`;
    });
  }

  function showDetail(reportId) {
    const report = allReports.find(r => r.id == reportId);
    if (!report) return;
    
    const content = document.getElementById('detailViewContent');
    content.innerHTML = `
      <div class="mb-4">
        <h6>🚲 Bicycle Information</h6>
        <p class="small mb-1"><strong>Brand/Model:</strong> ${report.brand} ${report.model}</p>
        <p class="small mb-1"><strong>Colour:</strong> ${report.colour}</p>
        <p class="small mb-1"><strong>Status:</strong> ${getBikeBadge(report.bike_status)}</p>
      </div>
      <div class="mb-4">
        <h6>👤 Reporter Details</h6>
        <p class="small mb-1"><strong>Name:</strong> ${report.first_name} ${report.last_name}</p>
        <p class="small mb-1"><strong>Email:</strong> ${report.email}</p>
      </div>
      <div class="mb-4">
        <h6>📍 Incident Details</h6>
        <p class="small mb-1"><strong>Date:</strong> ${new Date(report.report_date).toLocaleString()}</p>
        <p class="small mb-1"><strong>Location:</strong> ${report.incident_location || 'N/A'}</p>
        <div id="miniMap" style="height: 150px; background: #eee;" class="rounded mt-2"></div>
      </div>
      <div class="mb-0">
        <h6>📝 Investigation Log</h6>
        <div class="p-2 bg-light border rounded small" style="max-height: 150px; overflow-y: auto;">
          ${report.investigation_notes || 'No notes yet.'}
        </div>
        <p class="small mt-2"><strong>Status:</strong> ${getInvestBadge(report.investigation_status)}</p>
        ${report.closed_at ? `<p class="small text-muted"><strong>Closed on:</strong> ${new Date(report.closed_at).toLocaleString()}</p>` : ''}
      </div>
    `;

    // Attempt to show on map if location exists
    if (report.incident_location && typeof google !== 'undefined') {
      const geocoder = new google.maps.Geocoder();
      geocoder.geocode({ address: report.incident_location }, (results, status) => {
        if (status === 'OK') {
          const map = new google.maps.Map(document.getElementById('miniMap'), {
            zoom: 15,
            center: results[0].geometry.location,
            disableDefaultUI: true
          });
          new google.maps.Marker({
            map: map,
            position: results[0].geometry.location
          });
        }
      });
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
    const report = allReports.find(r => r.id == id);
    if (report) {
      document.getElementById('investStatus').value = report.investigation_status;
      document.getElementById('bikeStatus').value = report.bike_status;
      document.getElementById('notes').value = report.investigation_notes;
    }
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
  </script>
</body>
</html>