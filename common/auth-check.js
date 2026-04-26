/**
 * common/auth-check.js
 * Included in dashboard HTML files to verify login status via API
 */

async function verifySession(requiredRole = null) {
    try {
        const response = await fetch('../api/session.php');
        const session = await response.json();

        if (!session.loggedin) {
            window.location.href = '../login/Login.html';
            return null;
        }

        if (requiredRole && session.role !== 'admin' && session.role !== requiredRole) {
            alert('Access Denied: You do not have permission to view this page.');
            window.location.href = '../login/Login.html';
            return null;
        }

        // Apply username to UI elements if they exist
        const nameElements = document.querySelectorAll('.user-first-name');
        nameElements.forEach(el => {
            el.textContent = session.first_name || session.username.split(' ')[0];
        });

        // Show/hide role-based elements
        if (session.role === 'admin') {
            document.querySelectorAll('.admin-only').forEach(el => el.style.display = 'block');
        }

        injectProblemModal();

        return session;
    } catch (error) {
        console.error('Session verification failed:', error);
        window.location.href = '../login/Login.html';
        return null;
    }
}

/**
 * Injects a "Report Problem" modal into the current page
 */
function injectProblemModal() {
    if (document.getElementById('problemModal')) return;

    const modalHtml = `
    <div class="modal fade" id="problemModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-warning text-dark">
            <h5 class="modal-title">⚠️ Report a System Problem</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="problemForm">
              <div class="mb-3">
                <label class="form-label fw-bold small">What kind of problem? *</label>
                <select class="form-select" id="problem_type" required>
                  <option value="Bug">Bug / Error</option>
                  <option value="UI">Display / UI Issue</option>
                  <option value="Feature">Feature Request</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold small">Description *</label>
                <textarea class="form-control" id="problem_description" rows="4" placeholder="Please describe exactly what happened..." required></textarea>
              </div>
              <button type="submit" class="btn btn-warning w-100">Submit Report to Administrator</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
        <button class="btn btn-warning shadow-lg btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#problemModal">
            ⚠️ Report Problem
        </button>
    </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);

    document.getElementById('problemForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = {
            problem_type: document.getElementById('problem_type').value,
            description: document.getElementById('problem_description').value,
            page_url: window.location.href
        };

        try {
            const response = await fetch('../api/problems.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                alert(result.message);
                bootstrap.Modal.getInstance(document.getElementById('problemModal')).hide();
                e.target.reset();
            }
        } catch (error) {
            alert('Failed to send report. Please try again.');
        }
    });
}
