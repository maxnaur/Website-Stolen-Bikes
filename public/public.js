/**
 * public/public.js
 * Logic for the Public Dashboard
 */

document.addEventListener('DOMContentLoaded', async () => {
    const session = await verifySession('public');
    if (session) {
        await loadBikes();
        await loadReports();
        handlePublicQueryParams();
    }
});

async function loadBikes() {
    try {
    const response = await fetch('../api/bicycles.php?action=list');
    const result = await response.json();
    
    const tableBody = document.getElementById('bikesTableBody');
    const bikeSelect = document.getElementById('reportBikeSelect');
    
    if (result.success) {
      tableBody.innerHTML = '';
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

const incidentStatusSelect = document.getElementById('incidentStatusSelect');
if (incidentStatusSelect) {
  incidentStatusSelect.addEventListener('change', function() {
    const recoveredSection = document.getElementById('recoveredExplanationSection');
    const recoveryInput = document.getElementById('recoveryExplanation');
    if (this.value === 'recovered') {
      recoveredSection.style.display = 'block';
      recoveryInput.required = true;
    } else {
      recoveredSection.style.display = 'none';
      recoveryInput.required = false;
    }
  });
}

const registerBikeForm = document.getElementById('registerBikeForm');
if (registerBikeForm) {
  registerBikeForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    try {
      const response = await fetch('../api/bicycles.php?action=add', {
        method: 'POST',
        body: formData
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
}

const reportTheftForm = document.getElementById('reportTheftForm');
if (reportTheftForm) {
  reportTheftForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    if (formData.get('bike_status') === 'recovered') {
      const expl = formData.get('recovery_explanation');
      const notes = formData.get('investigation_notes');
      formData.set('investigation_notes', `RECOVERY REPORT: ${expl}\n\nAdditional Info: ${notes}`);
    }

    try {
      const response = await fetch('../api/reports.php?action=create', {
        method: 'POST',
        body: formData
      });
      const result = await response.json();
      if (result.success) {
        alert('Report submitted successfully!');
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
}

const profileUpdateForm = document.getElementById('profileUpdateForm');
if (profileUpdateForm) {
  profileUpdateForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    try {
      const response = await fetch('../api/users.php?action=update_profile', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      const result = await response.json();
      if (result.success) {
        alert('Profile updated successfully!');
        bootstrap.Modal.getInstance(document.getElementById('profileModal')).hide();
      } else {
        alert('Update failed: ' + result.message);
      }
    } catch (error) {
      console.error('Error:', error);
      alert('An error occurred while updating profile.');
    }
  });
}

function handlePublicQueryParams() {
  const params = new URLSearchParams(window.location.search);
  const viewBikeId = params.get('view_bike');
  if (viewBikeId) {
    viewBike(viewBikeId);
  }
}

async function viewBike(id) {
    const content = document.getElementById('bikeDetailsContent');
    content.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary"></div></div>';
    
    const modal = new bootstrap.Modal(document.getElementById('bikeDetailsModal'));
    modal.show();

    try {
      const response = await fetch(`../api/bicycles.php?action=detail&id=${id}`);
      const result = await response.json();
      
      if (result.success) {
        const bike = result.data;
        const statusClass = bike.status === 'safe' ? 'bg-success' : 'bg-danger';
        const imageHtml = bike.image_path 
          ? `<img src="../${bike.image_path}" class="bike-thumbnail-details shadow-sm mb-3 d-block mx-auto" alt="Bike Image">`
          : `<div class="bg-light text-center p-5 rounded mb-3" style="font-size: 4rem;">🚲</div>`;

        content.innerHTML = `
          ${imageHtml}
          <div class="row g-2">
            <div class="col-6"><strong>Brand:</strong> ${bike.brand}</div>
            <div class="col-6"><strong>Model:</strong> ${bike.model}</div>
            <div class="col-6"><strong>Type:</strong> ${bike.type}</div>
            <div class="col-6"><strong>Colour:</strong> ${bike.colour}</div>
            <div class="col-6"><strong>Wheel Size:</strong> ${bike.wheel_size ? bike.wheel_size + '"' : 'N/A'}</div>
            <div class="col-6"><strong>Gears:</strong> ${bike.gears || 'N/A'}</div>
            <div class="col-6"><strong>Brakes:</strong> ${bike.brake_type || 'N/A'}</div>
            <div class="col-6"><strong>Suspension:</strong> ${bike.suspension || 'None'}</div>
            <div class="col-6"><strong>Gender:</strong> ${bike.gender}</div>
            <div class="col-6"><strong>Age Group:</strong> ${bike.age_group}</div>
            <div class="col-6"><strong>MPN/Serial:</strong> ${bike.mpn || 'N/A'}</div>
            <div class="col-6"><strong>Status:</strong> <span class="badge ${statusClass}">${bike.status.toUpperCase()}</span></div>
            <div class="col-12 mt-2"><hr></div>
            <div class="col-12"><strong>Registered To:</strong> ${bike.first_name} ${bike.last_name}</div>
            <div class="col-12"><strong>Contact:</strong> ${bike.email}</div>
            <div class="col-12 small text-muted"><strong>Registered On:</strong> ${new Date(bike.created_at).toLocaleString()}</div>
          </div>
        `;
      } else {
        content.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
      }
    } catch (error) {
      console.error('Error:', error);
      content.innerHTML = `<div class="alert alert-danger">Failed to load bike details.</div>`;
    }
}
