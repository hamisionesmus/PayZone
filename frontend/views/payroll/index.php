<?php
$pageTitle = 'Payroll Management';
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
                        <h1 class="h3 mb-1">Payroll Management</h1>
                        <p class="text-muted mb-0">Process payroll runs and generate payslips</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-success" onclick="runPayroll()">
                            <i class="fas fa-play me-2"></i>Run Payroll
                        </button>
                        <button class="btn btn-primary" onclick="generatePayslips()">
                            <i class="fas fa-file-invoice me-2"></i>Generate Payslips
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payroll Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card chart-container">
                    <div class="card-body text-center">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check text-success"></i>
                        </div>
                        <h4 class="stat-value" id="totalRuns">0</h4>
                        <p class="stat-label">Payroll Runs</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card chart-container">
                    <div class="card-body text-center">
                        <div class="stat-icon">
                            <i class="fas fa-money-bill-wave text-primary"></i>
                        </div>
                        <h4 class="stat-value" id="totalAmount">KSH 0</h4>
                        <p class="stat-label">Total Paid</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card chart-container">
                    <div class="card-body text-center">
                        <div class="stat-icon">
                            <i class="fas fa-users text-info"></i>
                        </div>
                        <h4 class="stat-value" id="employeesPaid">0</h4>
                        <p class="stat-label">Employees Paid</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card chart-container">
                    <div class="card-body text-center">
                        <div class="stat-icon">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                        <h4 class="stat-value" id="pendingRuns">0</h4>
                        <p class="stat-label">Pending Runs</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payroll Runs Table -->
        <div class="row">
            <div class="col-12">
                <div class="card chart-container">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Payroll Runs</h5>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" id="statusFilter" style="width: auto;">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="completed">Completed</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="payrollRunsTable">
                                <thead>
                                    <tr>
                                        <th>Run Date</th>
                                        <th>Status</th>
                                        <th>Total Amount</th>
                                        <th>Employees</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                        <th>Individual</th>
                                    </tr>
                                </thead>
                                <tbody id="payrollRunsTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-spinner fa-spin me-2"></i>Loading payroll runs...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Payroll pagination" class="mt-4">
                            <ul class="pagination justify-content-center" id="payrollPagination">
                                <!-- Pagination will be generated by JavaScript -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Run Payroll Modal -->
<div class="modal fade" id="runPayrollModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Run Payroll</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="runPayrollForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="payrollDate" class="form-label">Payroll Date *</label>
                            <input type="date" class="form-control" id="payrollDate" name="run_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="payrollPeriod" class="form-label">Payroll Period</label>
                            <select class="form-select" id="payrollPeriod" name="period">
                                <option value="monthly">Monthly</option>
                                <option value="weekly">Weekly</option>
                                <option value="bi-weekly">Bi-weekly</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                This will process payroll for all active employees based on their current salary and deductions.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="runPayrollBtn">
                        <span class="loading-spinner d-none me-2"></span>
                        <i class="fas fa-play me-2"></i>Run Payroll
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentPayrollPage = 1;
let payrollRuns = [];

document.addEventListener('DOMContentLoaded', function() {
    loadPayrollRuns();
    loadPayrollStats();
    setupPayrollEventListeners();
});

function setupPayrollEventListeners() {
    document.getElementById('statusFilter').addEventListener('change', function() {
        filterPayrollRuns();
    });

    document.getElementById('runPayrollForm').addEventListener('submit', handleRunPayroll);
}

async function loadPayrollRuns() {
    try {
        console.log('Loading payroll runs...');
        console.log('API Base URL:', window.payrollApp.apiBaseUrl);
        console.log('Auth Token:', window.payrollApp.token ? 'Present' : 'Missing');

        const response = await window.payrollApp.apiRequest('/payroll/runs');
        console.log('API Response:', response);
        console.log('Response status:', response.status);

        if (response.ok) {
            payrollRuns = await response.json();
            console.log('Payroll runs loaded:', payrollRuns);
            renderPayrollRuns();
            renderPayrollPagination();
        } else {
            const errorText = await response.text();
            console.error('API Error response:', errorText);
            throw new Error(`Failed to load payroll runs: ${response.status}`);
        }
    } catch (error) {
        console.error('Error loading payroll runs:', error);
        document.getElementById('payrollRunsTableBody').innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Failed to load payroll runs. Please check console for details.
                </td>
            </tr>
        `;
    }
}

async function loadPayrollStats() {
    try {
        const response = await window.payrollApp.apiRequest('/payroll/stats');
        if (response.ok) {
            const stats = await response.json();
            document.getElementById('totalRuns').textContent = stats.total_runs || 0;
            document.getElementById('totalAmount').textContent = window.payrollApp.formatCurrency(stats.total_amount || 0);
            document.getElementById('employeesPaid').textContent = stats.employees_paid || 0;
            document.getElementById('pendingRuns').textContent = stats.pending_runs || 0;
        }
    } catch (error) {
        console.error('Error loading payroll stats:', error);
    }
}

function filterPayrollRuns() {
    const statusFilter = document.getElementById('statusFilter').value;
    let filteredRuns = payrollRuns;

    if (statusFilter) {
        filteredRuns = payrollRuns.filter(run => run.status === statusFilter);
    }

    currentPayrollPage = 1;
    renderFilteredPayrollRuns(filteredRuns);
    renderPayrollPagination(filteredRuns.length);
}

function renderPayrollRuns() {
    renderFilteredPayrollRuns(payrollRuns);
}

function renderFilteredPayrollRuns(runs) {
    const tbody = document.getElementById('payrollRunsTableBody');
    const startIndex = (currentPayrollPage - 1) * 10;
    const endIndex = startIndex + 10;
    const pageRuns = runs.slice(startIndex, endIndex);

    if (pageRuns.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4">
                    <i class="fas fa-calendar-alt me-2"></i>No payroll runs found
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = pageRuns.map(run => `
        <tr>
            <td>${window.payrollApp.formatDate(run.run_date)}</td>
            <td>
                <span class="badge bg-${getStatusColor(run.status)}">${run.status}</span>
            </td>
            <td>${window.payrollApp.formatCurrency(run.total_amount)}</td>
            <td>${run.employee_count || 0}</td>
            <td>${window.payrollApp.formatDateTime(run.created_at)}</td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="viewPayrollRun(${run.id})" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="downloadPayslips(${run.id})" title="Download All Payslips">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                    ${run.status === 'completed' ? `
                        <button class="btn btn-sm btn-outline-danger" onclick="deletePayrollRun(${run.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    ` : ''}
                </div>
            </td>
            <td>
                ${run.status === 'completed' ? `
                    <button class="btn btn-sm btn-outline-info" onclick="downloadIndividualPayslips(${run.id})" title="Download Individual Payslips">
                        <i class="fas fa-users"></i>
                    </button>
                ` : '<span class="text-muted">N/A</span>'}
            </td>
        </tr>
    `).join('');
}

function renderPayrollPagination(totalItems = payrollRuns.length) {
    const totalPages = Math.ceil(totalItems / 10);
    const pagination = document.getElementById('payrollPagination');

    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let paginationHtml = '';

    // Previous button
    paginationHtml += `
        <li class="page-item ${currentPayrollPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePayrollPage(${currentPayrollPage - 1})">Previous</a>
        </li>
    `;

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === currentPayrollPage || i === 1 || i === totalPages || (i >= currentPayrollPage - 1 && i <= currentPayrollPage + 1)) {
            paginationHtml += `
                <li class="page-item ${i === currentPayrollPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePayrollPage(${i})">${i}</a>
                </li>
            `;
        } else if (i === currentPayrollPage - 2 || i === currentPayrollPage + 2) {
            paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    // Next button
    paginationHtml += `
        <li class="page-item ${currentPayrollPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePayrollPage(${currentPayrollPage + 1})">Next</a>
        </li>
    `;

    pagination.innerHTML = paginationHtml;
}

function changePayrollPage(page) {
    if (page >= 1 && page <= Math.ceil(payrollRuns.length / 10)) {
        currentPayrollPage = page;
        renderPayrollRuns();
        renderPayrollPagination();
    }
}

function getStatusColor(status) {
    switch (status) {
        case 'completed': return 'success';
        case 'processing': return 'warning';
        case 'pending': return 'secondary';
        case 'failed': return 'danger';
        default: return 'secondary';
    }
}

function runPayroll() {
    // Set default date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('payrollDate').value = today;

    const modal = new bootstrap.Modal(document.getElementById('runPayrollModal'));
    modal.show();
}

async function handleRunPayroll(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const payrollData = Object.fromEntries(formData.entries());

    console.log('Payroll data to send:', payrollData);

    const runBtn = document.getElementById('runPayrollBtn');
    const resetLoading = window.payrollApp.showLoading(runBtn);

    try {
        console.log('Sending payroll run request...');
        const response = await window.payrollApp.apiRequest('/payroll/run', {
            method: 'POST',
            body: JSON.stringify(payrollData)
        });

        console.log('Payroll run response:', response);
        console.log('Response status:', response.status);

        if (response.ok) {
            const result = await response.json();
            console.log('Payroll run result:', result);
            window.payrollApp.showSuccess('Payroll run started successfully!');

            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('runPayrollModal'));
            modal.hide();

            // Refresh data
            loadPayrollRuns();
            loadPayrollStats();
        } else {
            const errorText = await response.text();
            console.error('Payroll run error response:', errorText);
            try {
                const error = JSON.parse(errorText);
                window.payrollApp.showError(error.error || 'Failed to run payroll');
            } catch (parseError) {
                window.payrollApp.showError('Failed to run payroll: ' + errorText);
            }
        }
    } catch (error) {
        console.error('Payroll run network error:', error);
        if (error.message && error.message.includes('Payroll has already been run')) {
            window.payrollApp.showError('Payroll has already been processed for this date');
        } else {
            window.payrollApp.showError('Network error. Please try again.');
        }
    } finally {
        resetLoading();
    }
}

function generatePayslips() {
    console.log('Generate payslips clicked');
    console.log('Available payroll runs:', payrollRuns);

    // Find the most recent completed payroll run
    const completedRuns = payrollRuns.filter(run => run.status === 'completed');
    console.log('Completed runs found:', completedRuns);

    if (completedRuns.length === 0) {
        window.payrollApp.showError('No completed payroll runs found');
        return;
    }

    // Get the most recent completed run
    const latestRun = completedRuns.sort((a, b) => new Date(b.run_date) - new Date(a.run_date))[0];
    console.log('Latest completed run:', latestRun);

    // Download payslips for this run
    downloadPayslips(latestRun.id);
}

async function viewPayrollRun(id) {
    try {
        const response = await window.payrollApp.apiRequest(`/payroll/runs/${id}/payslips`);

        if (response.ok) {
            const payslips = await response.json();
            const payrollRun = payrollRuns.find(run => run.id === id);

            if (!payrollRun) {
                window.payrollApp.showError('Payroll run not found');
                return;
            }

            // Create view modal
            const modalHtml = `
                <div class="modal fade" id="viewPayrollRunModal" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Payroll Run Details - ${window.payrollApp.formatDate(payrollRun.run_date)}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <div class="card text-center">
                                            <div class="card-body">
                                                <h6 class="card-title">Status</h6>
                                                <span class="badge bg-${getStatusColor(payrollRun.status)} fs-6">${payrollRun.status}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card text-center">
                                            <div class="card-body">
                                                <h6 class="card-title">Total Amount</h6>
                                                <h5 class="text-primary">${window.payrollApp.formatCurrency(payrollRun.total_amount)}</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card text-center">
                                            <div class="card-body">
                                                <h6 class="card-title">Employees Paid</h6>
                                                <h5 class="text-success">${payslips.length}</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card text-center">
                                            <div class="card-body">
                                                <h6 class="card-title">Created</h6>
                                                <small class="text-muted">${window.payrollApp.formatDateTime(payrollRun.created_at)}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h6>Payslip Details</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Employee</th>
                                                <th>Email</th>
                                                <th>Gross Pay</th>
                                                <th>Deductions</th>
                                                <th>Allowances</th>
                                                <th>Net Pay</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${payslips.map(payslip => `
                                                <tr>
                                                    <td>${payslip.employee_name}</td>
                                                    <td>${payslip.employee_email}</td>
                                                    <td>${window.payrollApp.formatCurrency(payslip.gross_pay)}</td>
                                                    <td>${window.payrollApp.formatCurrency(payslip.deductions_total)}</td>
                                                    <td>${window.payrollApp.formatCurrency(payslip.allowances_total)}</td>
                                                    <td><strong>${window.payrollApp.formatCurrency(payslip.net_pay)}</strong></td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-success" onclick="downloadPayslips(${id})">
                                    <i class="fas fa-download me-2"></i>Download Payslips
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal if present
            const existingModal = document.getElementById('viewPayrollRunModal');
            if (existingModal) {
                existingModal.remove();
            }

            // Add new modal
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('viewPayrollRunModal'));
            modal.show();
        } else {
            const error = await response.json();
            window.payrollApp.showError(error.error || 'Failed to load payroll run details');
        }
    } catch (error) {
        console.error('Error viewing payroll run:', error);
        window.payrollApp.showError('Network error. Please try again.');
    }
}

async function downloadPayslips(id, format = 'csv') {
    try {
        if (format === 'pdf') {
            // Open bulk payslips in new tab for printing/saving as PDF
            const payslipWindow = window.open(`${window.payrollApp.apiBaseUrl}/payroll/runs/${id}?format=pdf`, '_blank');

            if (payslipWindow) {
                window.payrollApp.showSuccess('Bulk payslips opened in new tab. Use Ctrl+P (or Cmd+P on Mac) to print/save as PDF!');
            } else {
                window.payrollApp.showError('Please allow popups for this site to view payslips');
            }
        } else {
            // Download CSV
            const response = await window.payrollApp.apiRequest(`/payroll/runs/${id}/payslips`);

            if (response.ok) {
                const payslips = await response.json();

                if (payslips.length === 0) {
                    window.payrollApp.showError('No payslips found for this payroll run');
                    return;
                }

                // Create CSV content
                const headers = ['Employee Name', 'Email', 'Gross Pay', 'Deductions', 'Allowances', 'Net Pay'];
                const csvContent = [
                    headers.join(','),
                    ...payslips.map(payslip => [
                        `"${payslip.employee_name}"`,
                        `"${payslip.employee_email}"`,
                        payslip.gross_pay,
                        payslip.deductions_total,
                        payslip.allowances_total,
                        payslip.net_pay
                    ].join(','))
                ].join('\n');

                // Create and download file
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', `payslips_${new Date().toISOString().split('T')[0]}.csv`);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                window.payrollApp.showSuccess(`Downloaded ${payslips.length} payslips successfully!`);
            } else {
                const error = await response.json();
                window.payrollApp.showError(error.error || 'Failed to download payslips');
            }
        }
    } catch (error) {
        console.error('Error downloading payslips:', error);
        window.payrollApp.showError('Network error. Please try again.');
    }
}

async function downloadIndividualPayslips(payrollRunId) {
    try {
        const response = await window.payrollApp.apiRequest(`/payroll/runs/${payrollRunId}/payslips`);

        if (response.ok) {
            const payslips = await response.json();

            if (payslips.length === 0) {
                window.payrollApp.showError('No payslips found for this payroll run');
                return;
            }

            // Create modal with individual download options
            const modalHtml = `
                <div class="modal fade" id="individualPayslipsModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Download Individual Payslips</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Click on any employee's name to download their individual payslip as a PDF document.
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Employee Name</th>
                                                <th>Email</th>
                                                <th>Gross Pay</th>
                                                <th>Net Pay</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${payslips.map(payslip => `
                                                <tr>
                                                    <td>
                                                        <button class="btn btn-link p-0 text-decoration-none" onclick="downloadSinglePayslip(${payslip.id})">
                                                            ${payslip.employee_name}
                                                        </button>
                                                    </td>
                                                    <td>${payslip.employee_email}</td>
                                                    <td>${window.payrollApp.formatCurrency(payslip.gross_pay)}</td>
                                                    <td><strong>${window.payrollApp.formatCurrency(payslip.net_pay)}</strong></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary" onclick="downloadSinglePayslip(${payslip.id})">
                                                            <i class="fas fa-file-pdf me-1"></i>Download PDF
                                                        </button>
                                                    </td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-success" onclick="downloadPayslips(${payrollRunId}, 'pdf')">
                                    <i class="fas fa-file-pdf me-2"></i>Download All as PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal if present
            const existingModal = document.getElementById('individualPayslipsModal');
            if (existingModal) {
                existingModal.remove();
            }

            // Add new modal
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('individualPayslipsModal'));
            modal.show();
        } else {
            const error = await response.json();
            window.payrollApp.showError(error.error || 'Failed to load payslips');
        }
    } catch (error) {
        console.error('Error loading individual payslips:', error);
        window.payrollApp.showError('Network error. Please try again.');
    }
}

async function downloadSinglePayslip(payslipId) {
    try {
        // Download PDF directly
        const response = await window.payrollApp.apiRequest(`/payslips/${payslipId}/download`);

        if (response.ok) {
            const blob = await response.blob();
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `payslip_${payslipId}_${new Date().toISOString().split('T')[0]}.pdf`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);

            window.payrollApp.showSuccess('Payslip downloaded successfully!');
        } else {
            const error = await response.json();
            window.payrollApp.showError(error.error || 'Failed to download payslip');
        }
    } catch (error) {
        console.error('Error downloading payslip:', error);
        window.payrollApp.showError('Network error. Please try again.');
    }
}

async function deletePayrollRun(id) {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: 'This will permanently delete this payroll run and all associated payslips.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    });

    if (result.isConfirmed) {
        try {
            const response = await window.payrollApp.apiRequest(`/payroll/runs/${id}`, {
                method: 'DELETE'
            });

            if (response.ok) {
                window.payrollApp.showSuccess('Payroll run deleted successfully!');
                loadPayrollRuns();
                loadPayrollStats();
            } else {
                const error = await response.json();
                window.payrollApp.showError(error.error || 'Failed to delete payroll run');
            }
        } catch (error) {
            window.payrollApp.showError('Network error. Please try again.');
        }
    }
}
</script>

<?php require_once '../../includes/footer.php'; ?>