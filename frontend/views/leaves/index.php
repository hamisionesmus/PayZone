<?php
$pageTitle = 'Leave Management';
require_once '../../config/config.php';
requireAuth();
require_once '../../includes/header.php';
?>

<?php include '../../includes/sidebar.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="main-content" style="margin-top: 76px;">
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1">Leave Management</h1>
                        <p class="text-muted mb-0">Manage employee leave requests and approvals</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-success" onclick="requestLeave()">
                            <i class="fas fa-plus me-2"></i>Request Leave
                        </button>
                        <button class="btn btn-primary" onclick="loadLeaves()">
                            <i class="fas fa-sync me-2"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card chart-container">
                    <div class="card-body text-center">
                        <div class="stat-icon">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                        <h4 class="stat-value" id="pendingLeaves">0</h4>
                        <p class="stat-label">Pending Requests</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card chart-container">
                    <div class="card-body text-center">
                        <div class="stat-icon">
                            <i class="fas fa-check text-success"></i>
                        </div>
                        <h4 class="stat-value" id="approvedLeaves">0</h4>
                        <p class="stat-label">Approved</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card chart-container">
                    <div class="card-body text-center">
                        <div class="stat-icon">
                            <i class="fas fa-times text-danger"></i>
                        </div>
                        <h4 class="stat-value" id="rejectedLeaves">0</h4>
                        <p class="stat-label">Rejected</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card chart-container">
                    <div class="card-body text-center">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-alt text-info"></i>
                        </div>
                        <h4 class="stat-value" id="totalLeaves">0</h4>
                        <p class="stat-label">Total Requests</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave Requests Table -->
        <div class="row">
            <div class="col-12">
                <div class="card chart-container">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Leave Requests</h5>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" id="statusFilter" style="width: auto;">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                            <select class="form-select form-select-sm" id="typeFilter" style="width: auto;">
                                <option value="">All Types</option>
                                <option value="annual">Annual Leave</option>
                                <option value="sick">Sick Leave</option>
                                <option value="maternity">Maternity Leave</option>
                                <option value="paternity">Paternity Leave</option>
                                <option value="emergency">Emergency Leave</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="leavesTable">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Type</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Days</th>
                                        <th>Status</th>
                                        <th>Reason</th>
                                        <th>Requested</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="leavesTableBody">
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="fas fa-spinner fa-spin me-2"></i>Loading leave requests...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Leave pagination" class="mt-4">
                            <ul class="pagination justify-content-center" id="leavePagination">
                                <!-- Pagination will be generated by JavaScript -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leave Review Modal -->
<div class="modal fade" id="leaveReviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Leave Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="leaveReviewContent">
                    <!-- Leave details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="approveBtn" onclick="approveLeaveFromModal()">
                    <i class="fas fa-check me-2"></i>Approve Leave
                </button>
                <button type="button" class="btn btn-danger" id="rejectBtn" onclick="rejectLeaveFromModal()">
                    <i class="fas fa-times me-2"></i>Reject Leave
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Request Leave Modal -->
<div class="modal fade" id="requestLeaveModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Leave</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="requestLeaveForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="leaveType" class="form-label">Leave Type *</label>
                            <select class="form-select" id="leaveType" name="type" required>
                                <option value="">Select leave type</option>
                                <option value="annual">Annual Leave</option>
                                <option value="sick">Sick Leave</option>
                                <option value="maternity">Maternity Leave</option>
                                <option value="paternity">Paternity Leave</option>
                                <option value="emergency">Emergency Leave</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="employeeSelect" class="form-label">Employee *</label>
                            <select class="form-select" id="employeeSelect" name="employee_id" required>
                                <option value="">Select employee</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="startDate" class="form-label">Start Date *</label>
                            <input type="date" class="form-control" id="startDate" name="start_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="endDate" class="form-label">End Date *</label>
                            <input type="date" class="form-control" id="endDate" name="end_date" required>
                        </div>
                        <div class="col-12">
                            <label for="leaveReason" class="form-label">Reason</label>
                            <textarea class="form-control" id="leaveReason" name="reason" rows="3" placeholder="Please provide a reason for your leave request"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Your leave request will be submitted for approval. You will be notified once it's reviewed.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="submitLeaveBtn">
                        <span class="loading-spinner d-none me-2"></span>
                        <i class="fas fa-paper-plane me-2"></i>Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentLeavePage = 1;
let leaves = [];

document.addEventListener('DOMContentLoaded', function() {
    loadLeaves();
    loadLeaveStats();
    setupLeaveEventListeners();
    loadEmployees();
});

function setupLeaveEventListeners() {
    document.getElementById('statusFilter').addEventListener('change', function() {
        filterLeaves();
    });

    document.getElementById('typeFilter').addEventListener('change', function() {
        filterLeaves();
    });

    document.getElementById('requestLeaveForm').addEventListener('submit', handleRequestLeave);
}

async function loadEmployees() {
    try {
        const response = await window.payrollApp.apiRequest('/employees');
        if (response.ok) {
            const employees = await response.json();
            const select = document.getElementById('employeeSelect');
            select.innerHTML = '<option value="">Select employee</option>';

            employees.forEach(employee => {
                const option = document.createElement('option');
                option.value = employee.id;
                option.textContent = `${employee.first_name} ${employee.last_name}`;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading employees:', error);
    }
}

async function loadLeaves() {
    try {
        console.log('Loading leave requests...');
        const response = await window.payrollApp.apiRequest('/leaves');

        if (response.ok) {
            leaves = await response.json();
            console.log('Leave requests loaded:', leaves);
            renderLeaves();
            renderLeavePagination();
        } else {
            const errorText = await response.text();
            console.error('API Error response:', errorText);
            throw new Error(`Failed to load leave requests: ${response.status}`);
        }
    } catch (error) {
        console.error('Error loading leave requests:', error);
        document.getElementById('leavesTableBody').innerHTML = `
            <tr>
                <td colspan="9" class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Failed to load leave requests. Please check console for details.
                </td>
            </tr>
        `;
    }
}

async function loadLeaveStats() {
    try {
        const response = await window.payrollApp.apiRequest('/leaves');
        if (response.ok) {
            const allLeaves = await response.json();

            const stats = {
                pending: allLeaves.filter(leave => leave.status === 'pending').length,
                approved: allLeaves.filter(leave => leave.status === 'approved').length,
                rejected: allLeaves.filter(leave => leave.status === 'rejected').length,
                total: allLeaves.length
            };

            document.getElementById('pendingLeaves').textContent = stats.pending;
            document.getElementById('approvedLeaves').textContent = stats.approved;
            document.getElementById('rejectedLeaves').textContent = stats.rejected;
            document.getElementById('totalLeaves').textContent = stats.total;
        }
    } catch (error) {
        console.error('Error loading leave stats:', error);
    }
}

function filterLeaves() {
    const statusFilter = document.getElementById('statusFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;

    let filteredLeaves = leaves;

    if (statusFilter) {
        filteredLeaves = filteredLeaves.filter(leave => leave.status === statusFilter);
    }

    if (typeFilter) {
        filteredLeaves = filteredLeaves.filter(leave => leave.type === typeFilter);
    }

    currentLeavePage = 1;
    renderFilteredLeaves(filteredLeaves);
    renderLeavePagination(filteredLeaves.length);
}

function renderLeaves() {
    renderFilteredLeaves(leaves);
}

function renderFilteredLeaves(leavesList) {
    const tbody = document.getElementById('leavesTableBody');
    const startIndex = (currentLeavePage - 1) * 10;
    const endIndex = startIndex + 10;
    const pageLeaves = leavesList.slice(startIndex, endIndex);

    if (pageLeaves.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-4">
                    <i class="fas fa-calendar-alt me-2"></i>No leave requests found
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = pageLeaves.map(leave => `
        <tr>
            <td>
                <div>
                    <strong>${leave.employee_name}</strong><br>
                    <small class="text-muted">${leave.employee_email}</small>
                </div>
            </td>
            <td>
                <span class="badge bg-secondary">${leave.type}</span>
            </td>
            <td>${window.payrollApp.formatDate(leave.start_date)}</td>
            <td>${window.payrollApp.formatDate(leave.end_date)}</td>
            <td>${calculateLeaveDays(leave.start_date, leave.end_date)}</td>
            <td>
                <span class="badge bg-${getLeaveStatusColor(leave.status)}">${leave.status}</span>
            </td>
            <td>
                <span title="${leave.reason || 'No reason provided'}">
                    ${leave.reason ? leave.reason.substring(0, 30) + (leave.reason.length > 30 ? '...' : '') : 'N/A'}
                </span>
            </td>
            <td>${window.payrollApp.formatDateTime(leave.created_at)}</td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="viewLeaveDetails(${leave.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${leave.status === 'pending' ? `
                        <button class="btn btn-sm btn-outline-warning" onclick="openLeaveReviewModal(${leave.id})" title="Review Leave">
                            <i class="fas fa-tasks"></i> Review
                        </button>
                    ` : ''}
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteLeave(${leave.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function renderLeavePagination(totalItems = leaves.length) {
    const totalPages = Math.ceil(totalItems / 10);
    const pagination = document.getElementById('leavePagination');

    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let paginationHtml = '';

    // Previous button
    paginationHtml += `
        <li class="page-item ${currentLeavePage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changeLeavePage(${currentLeavePage - 1})">Previous</a>
        </li>
    `;

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === currentLeavePage || i === 1 || i === totalPages || (i >= currentLeavePage - 1 && i <= currentLeavePage + 1)) {
            paginationHtml += `
                <li class="page-item ${i === currentLeavePage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changeLeavePage(${i})">${i}</a>
                </li>
            `;
        } else if (i === currentLeavePage - 2 || i === currentLeavePage + 2) {
            paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    // Next button
    paginationHtml += `
        <li class="page-item ${currentLeavePage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changeLeavePage(${currentLeavePage + 1})">Next</a>
        </li>
    `;

    pagination.innerHTML = paginationHtml;
}

function changeLeavePage(page) {
    if (page >= 1 && page <= Math.ceil(leaves.length / 10)) {
        currentLeavePage = page;
        renderLeaves();
        renderLeavePagination();
    }
}

function getLeaveStatusColor(status) {
    switch (status) {
        case 'approved': return 'success';
        case 'pending': return 'warning';
        case 'rejected': return 'danger';
        default: return 'secondary';
    }
}

function calculateLeaveDays(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    const diffTime = Math.abs(end - start);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
    return diffDays;
}

function requestLeave() {
    // Set default dates
    const today = new Date().toISOString().split('T')[0];
    const tomorrow = new Date(Date.now() + 24 * 60 * 60 * 1000).toISOString().split('T')[0];

    document.getElementById('startDate').value = today;
    document.getElementById('endDate').value = tomorrow;

    const modal = new bootstrap.Modal(document.getElementById('requestLeaveModal'));
    modal.show();
}

async function handleRequestLeave(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const leaveData = Object.fromEntries(formData.entries());

    console.log('Leave data to send:', leaveData);

    const submitBtn = document.getElementById('submitLeaveBtn');
    const resetLoading = window.payrollApp.showLoading(submitBtn);

    try {
        console.log('Sending leave request...');
        const response = await window.payrollApp.apiRequest('/leaves', {
            method: 'POST',
            body: JSON.stringify(leaveData)
        });

        console.log('Leave request response:', response);
        console.log('Response status:', response.status);

        if (response.ok) {
            const result = await response.json();
            console.log('Leave request result:', result);
            window.payrollApp.showSuccess('Leave request submitted successfully!');

            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('requestLeaveModal'));
            modal.hide();

            // Reset form
            e.target.reset();

            // Refresh data
            loadLeaves();
            loadLeaveStats();
        } else {
            const errorText = await response.text();
            console.error('Leave request error response:', errorText);
            try {
                const error = JSON.parse(errorText);
                window.payrollApp.showError(error.error || 'Failed to submit leave request');
            } catch (parseError) {
                window.payrollApp.showError('Failed to submit leave request: ' + errorText);
            }
        }
    } catch (error) {
        console.error('Leave request network error:', error);
        window.payrollApp.showError('Network error. Please try again.');
    } finally {
        resetLoading();
    }
}

async function approveLeave(id) {
    try {
        const response = await window.payrollApp.apiRequest(`/leaves/${id}/approve`, {
            method: 'PUT'
        });

        if (response.ok) {
            const result = await response.json();
            window.payrollApp.showSuccess(result.message || 'Leave request approved successfully!');
            loadLeaves();
            loadLeaveStats();
        } else {
            const error = await response.json();
            window.payrollApp.showError(error.error || 'Failed to approve leave request');
        }
    } catch (error) {
        window.payrollApp.showError('Network error. Please try again.');
    }
}

async function rejectLeave(id) {
    try {
        const response = await window.payrollApp.apiRequest(`/leaves/${id}/reject`, {
            method: 'PUT'
        });

        if (response.ok) {
            const result = await response.json();
            window.payrollApp.showSuccess(result.message || 'Leave request rejected!');
            loadLeaves();
            loadLeaveStats();
        } else {
            const error = await response.json();
            window.payrollApp.showError(error.error || 'Failed to reject leave request');
        }
    } catch (error) {
        window.payrollApp.showError('Network error. Please try again.');
    }
}

async function deleteLeave(id) {
    const result = await Swal.fire({
        title: 'Delete Leave Request?',
        text: 'This will permanently delete the leave request.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    });

    if (result.isConfirmed) {
        try {
            const response = await window.payrollApp.apiRequest(`/leaves/${id}`, {
                method: 'DELETE'
            });

            if (response.ok) {
                window.payrollApp.showSuccess('Leave request deleted successfully!');
                loadLeaves();
                loadLeaveStats();
            } else {
                const error = await response.json();
                window.payrollApp.showError(error.error || 'Failed to delete leave request');
            }
        } catch (error) {
            window.payrollApp.showError('Network error. Please try again.');
        }
    }
}

let currentReviewLeaveId = null;

function openLeaveReviewModal(id) {
    const leave = leaves.find(l => l.id === id);
    if (!leave) {
        window.payrollApp.showError('Leave request not found');
        return;
    }

    currentReviewLeaveId = id;

    const modalContent = `
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Employee</label>
                <p class="mb-1">${leave.employee_name}</p>
                <small class="text-muted">${leave.employee_email}</small>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Leave Type</label>
                <p class="mb-0"><span class="badge bg-secondary">${leave.type}</span></p>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Start Date</label>
                <p class="mb-0">${window.payrollApp.formatDate(leave.start_date)}</p>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">End Date</label>
                <p class="mb-0">${window.payrollApp.formatDate(leave.end_date)}</p>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Total Days</label>
                <p class="mb-0">${calculateLeaveDays(leave.start_date, leave.end_date)}</p>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Status</label>
                <p class="mb-0"><span class="badge bg-${getLeaveStatusColor(leave.status)}">${leave.status}</span></p>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Requested On</label>
                <p class="mb-0">${window.payrollApp.formatDateTime(leave.created_at)}</p>
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Reason</label>
                <p class="mb-0">${leave.reason || 'No reason provided'}</p>
            </div>
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Please review this leave request carefully before making a decision.
                </div>
            </div>
        </div>
    `;

    document.getElementById('leaveReviewContent').innerHTML = modalContent;

    const modal = new bootstrap.Modal(document.getElementById('leaveReviewModal'));
    modal.show();
}

function approveLeaveFromModal() {
    if (!currentReviewLeaveId) return;

    const result = confirm('Are you sure you want to approve this leave request?');
    if (result) {
        approveLeave(currentReviewLeaveId);
        bootstrap.Modal.getInstance(document.getElementById('leaveReviewModal')).hide();
    }
}

function rejectLeaveFromModal() {
    if (!currentReviewLeaveId) return;

    const result = confirm('Are you sure you want to reject this leave request?');
    if (result) {
        rejectLeave(currentReviewLeaveId);
        bootstrap.Modal.getInstance(document.getElementById('leaveReviewModal')).hide();
    }
}

function viewLeaveDetails(id) {
    const leave = leaves.find(l => l.id === id);
    if (!leave) {
        window.payrollApp.showError('Leave request not found');
        return;
    }

    // Create view modal
    const modalHtml = `
        <div class="modal fade" id="viewLeaveModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Leave Request Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Employee</label>
                                <p class="mb-1">${leave.employee_name}</p>
                                <small class="text-muted">${leave.employee_email}</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Leave Type</label>
                                <p class="mb-0"><span class="badge bg-secondary">${leave.type}</span></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Start Date</label>
                                <p class="mb-0">${window.payrollApp.formatDate(leave.start_date)}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">End Date</label>
                                <p class="mb-0">${window.payrollApp.formatDate(leave.end_date)}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Total Days</label>
                                <p class="mb-0">${calculateLeaveDays(leave.start_date, leave.end_date)}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Status</label>
                                <p class="mb-0"><span class="badge bg-${getLeaveStatusColor(leave.status)}">${leave.status}</span></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Requested On</label>
                                <p class="mb-0">${window.payrollApp.formatDateTime(leave.created_at)}</p>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Reason</label>
                                <p class="mb-0">${leave.reason || 'No reason provided'}</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove existing modal if present
    const existingModal = document.getElementById('viewLeaveModal');
    if (existingModal) {
        existingModal.remove();
    }

    // Add new modal
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('viewLeaveModal'));
    modal.show();
}
</script>

<?php require_once '../../includes/footer.php'; ?>