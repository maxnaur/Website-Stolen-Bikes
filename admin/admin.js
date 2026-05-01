/**
 * admin/admin.js
 * Logic for the Admin Dashboard
 */

document.addEventListener('DOMContentLoaded', async () => {
    const session = await verifySession('admin');
    if (session) {
        loadStats();
        loadUsers();
        loadBikes();
        loadReports();
        loadLogs();
        loadProblems();
    }
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
    window.location.href = `../public/public.html?view_bike=${id}`;
}

function viewReport(id) {
    window.location.href = '../police/police.html?report_id=' + id;
}
lems() {
    const tableBody = document.getElementById('problemsTable');
    try {
        const response = await fetch('../api/problems.php');
        const result = await response.json();
        if (result.success) {
            tableBody.innerHTML = '';
            if (result.data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center p-3">No problems reported.</td></tr>';
                return;
            }
            result.data.forEach(p => {
                const statusClass = p.status === 'resolved' ? 'bg-success' : (p.status === 'in_review' ? 'bg-info' : 'bg-warning');
                tableBody.innerHTML += `
                    <tr>
                        <td>${new Date(p.created_at).toLocaleString()}</td>
                        <td>${p.user_email}</td>
                        <td><span class="badge bg-secondary">${p.problem_type}</span></td>
                        <td><div class="small">${p.description}</div><div class="text-muted" style="font-size: 0.7rem;">URL: ${p.page_url}</div></td>
                        <td><span class="badge ${statusClass}">${p.status.toUpperCase()}</span></td>
                        <td>
                            <select class="form-select form-select-sm" onchange="updateProblemStatus(${p.id}, this.value)">
                                <option value="new" ${p.status === 'new' ? 'selected' : ''}>New</option>
                                <option value="in_review" ${p.status === 'in_review' ? 'selected' : ''}>In Review</option>
                                <option value="resolved" ${p.status === 'resolved' ? 'selected' : ''}>Resolved</option>
                            </select>
                        </td>
                    </tr>
                `;
            });
        }
    } catch (error) {
        tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading problems.</td></tr>';
    }
}

async function updateProblemStatus(id, newStatus) {
    try {
        const response = await fetch(`../api/problems.php?id=${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: newStatus })
        });
        const result = await response.json();
        if (result.success) {
            loadProblems();
        }
    } catch (error) {
        alert('Failed to update status.');
    }
}
