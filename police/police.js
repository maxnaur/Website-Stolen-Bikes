/**
 * police/police.js
 * Logic for the Police Dashboard
 */

let allReports = [];
let overviewMapInstance = null;
let overviewMarkers = [];
let csrfToken = '';

async function fetchCsrfToken() {
    try {
        const response = await fetch('../api/auth.php?action=csrf');
        const result = await response.json();
        if (result.success) {
            csrfToken = result.csrf_token;
        }
    } catch (error) {
        console.error('Failed to fetch CSRF token:', error);
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    const session = await verifySession('police');
    if (session) {
        await fetchCsrfToken();
        await loadReports();
        await loadStats();
        document.getElementById('showClosedReports')?.addEventListener('change', renderReports);

        const investigationForm = document.getElementById('investigationForm');
        if (investigationForm) {
            investigationForm.addEventListener('submit', handleInvestigationSubmit);
        }

        handlePoliceQueryParams();
    }
});

async function refreshOverviewMap() {
    const containerId = 'overviewMap';
    const container = document.getElementById(containerId);
    if (!container || !window.L || !window.leafletUtils) return;

    if (!overviewMapInstance) {
        // Center of UK default
        overviewMapInstance = L.map(containerId, { attributionControl: false }).setView([54.5, -2.5], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(overviewMapInstance);
        
        // Wait a tick for CSS to apply then invalidate size
        setTimeout(() => overviewMapInstance.invalidateSize(), 200);
    }

    // Clear existing markers
    overviewMarkers.forEach(m => overviewMapInstance.removeLayer(m));
    overviewMarkers = [];

    const showClosed = document.getElementById('showClosedReports')?.checked;
    const reportsToPlot = allReports.filter(r => (showClosed || r.investigation_status !== 'closed') && r.incident_location);

    if (reportsToPlot.length === 0) return;

    const bounds = L.latLngBounds();

    for (const report of reportsToPlot) {
        try {
            // Add a small delay to avoid hitting Nominatim rate limits (1 req/sec)
            await new Promise(r => setTimeout(r, 1000));
            
            const coords = await window.leafletUtils.geocodeAddress(report.incident_location);
            if (coords) {
                const marker = L.marker([coords.lat, coords.lng]).addTo(overviewMapInstance);
                
                const popupContent = `
                    <strong>Report #${report.id}</strong><br>
                    ${report.brand} ${report.model}<br>
                    Status: ${report.bike_status.toUpperCase()}<br>
                    <a href="#" onclick="showDetail(${report.id}); return false;">View Details</a>
                `;
                marker.bindPopup(popupContent);
                overviewMarkers.push(marker);
                bounds.extend([coords.lat, coords.lng]);
            }
        } catch (error) {
            console.error("Error geocoding for overview map:", error);
        }
    }

    if (overviewMarkers.length > 0) {
        overviewMapInstance.fitBounds(bounds, { padding: [50, 50] });
    }
}

async function loadReports() {
    try {
    const response = await fetch('../api/reports.php');
    const result = await response.json();
    if (result.success) {
      allReports = result.data;
      renderReports();
      // Initially load the map
      setTimeout(refreshOverviewMap, 500);
    }
    } catch (error) {
      console.error('Error loading reports:', error);
    }
}

window.refreshOverviewMap = refreshOverviewMap;

async function handleInvestigationSubmit(e) {
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
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
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
    if (report.incident_location && window.leafletUtils) {
      initMiniMap(report.incident_location).catch(console.error);
    }
}

async function initMiniMap(address) {
    const location = await window.leafletUtils.geocodeAddress(address);
    if (!location) return;

    const miniMap = document.getElementById('miniMap');
    if (!miniMap) return;

    miniMap.style.background = '#fff';
    window.leafletUtils.createOrUpdateMap('miniMap', location.lat, location.lng, 15);
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

function handlePoliceQueryParams() {
    const params = new URLSearchParams(window.location.search);
    const reportId = params.get('report_id');
    if (reportId) {
      showDetail(reportId);
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
