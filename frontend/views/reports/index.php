<?php
$pageTitle = 'Reports & Analytics';
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
                        <h1 class="h3 mb-1">Reports & Analytics</h1>
                        <p class="text-muted mb-0">Comprehensive business insights and analytics</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary" onclick="exportReport()">
                            <i class="fas fa-download me-2"></i>Export Report
                        </button>
                        <button class="btn btn-primary" onclick="refreshAllReports()">
                            <i class="fas fa-sync me-2"></i>Refresh All
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Type Selector -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card chart-container">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Report Type</label>
                                <select class="form-select" id="reportType" onchange="loadReport()">
                                    <option value="compliance">Compliance Overview</option>
                                    <option value="payroll">Payroll Summary</option>
                                    <option value="employees">Employee Salaries</option>
                                    <option value="leaves">Leave Analytics</option>
                                    <option value="departments">Department Analysis</option>
                                    <option value="payroll-trend">Payroll Trends</option>
                                    <option value="attendance">Attendance Report</option>
                                </select>
                            </div>
                            <div class="col-md-3" id="dateRangeSection" style="display: none;">
                                <label class="form-label fw-bold">Start Date</label>
                                <input type="date" class="form-control" id="startDate" onchange="loadReport()">
                            </div>
                            <div class="col-md-3" id="endDateSection" style="display: none;">
                                <label class="form-label fw-bold">End Date</label>
                                <input type="date" class="form-control" id="endDate" onchange="loadReport()">
                            </div>
                            <div class="col-md-3" id="yearSection" style="display: none;">
                                <label class="form-label fw-bold">Year</label>
                                <select class="form-select" id="reportYear" onchange="loadReport()">
                                    <option value="2025">2025</option>
                                    <option value="2024">2024</option>
                                    <option value="2023">2023</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Content -->
        <div id="reportContent">
            <!-- Report will be loaded here -->
        </div>
    </div>
</div>

<script>
let currentReportData = null;

document.addEventListener('DOMContentLoaded', function() {
    loadReport();
    setupReportControls();
});

function setupReportControls() {
    document.getElementById('reportType').addEventListener('change', function() {
        const reportType = this.value;
        toggleDateControls(reportType);
        loadReport();
    });
}

function toggleDateControls(reportType) {
    const dateRangeSection = document.getElementById('dateRangeSection');
    const endDateSection = document.getElementById('endDateSection');
    const yearSection = document.getElementById('yearSection');

    // Hide all controls first
    dateRangeSection.style.display = 'none';
    endDateSection.style.display = 'none';
    yearSection.style.display = 'none';

    // Show relevant controls based on report type
    if (['payroll', 'leaves'].includes(reportType)) {
        dateRangeSection.style.display = 'block';
        endDateSection.style.display = 'block';
    } else if (reportType === 'attendance') {
        yearSection.style.display = 'block';
    }
}

async function loadReport() {
    const reportType = document.getElementById('reportType').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const year = document.getElementById('reportYear').value;

    try {
        let url = `/reports/${reportType}`;
        const params = new URLSearchParams();

        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (year) params.append('year', year);

        if (params.toString()) {
            url += '?' + params.toString();
        }

        const response = await window.payrollApp.apiRequest(url);
        if (response.ok) {
            currentReportData = await response.json();
            renderReport(reportType, currentReportData);
        } else {
            throw new Error('Failed to load report');
        }
    } catch (error) {
        console.error('Error loading report:', error);
        document.getElementById('reportContent').innerHTML = `
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Failed to load report. Please try again.
                    </div>
                </div>
            </div>
        `;
    }
}

function renderReport(reportType, data) {
    let html = '';

    switch (reportType) {
        case 'compliance':
            html = renderComplianceReport(data);
            break;
        case 'payroll':
            html = renderPayrollReport(data);
            break;
        case 'employees':
            html = renderEmployeeReport(data);
            break;
        case 'leaves':
            html = renderLeaveReport(data);
            break;
        case 'departments':
            html = renderDepartmentReport(data);
            break;
        case 'payroll-trend':
            html = renderPayrollTrendReport(data);
            break;
        case 'attendance':
            html = renderAttendanceReport(data);
            break;
        default:
            html = '<div class="alert alert-info">Report type not implemented yet.</div>';
    }

    document.getElementById('reportContent').innerHTML = html;
}

function renderComplianceReport(data) {
    return `
        <div class="row">
            <div class="col-12">
                <div class="card chart-container">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Compliance Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            ${data.map(item => `
                                <div class="col-md-3">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <div class="display-4 text-primary mb-2">${item.value}</div>
                                            <h6 class="card-title">${item.metric}</h6>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderPayrollReport(data) {
    return `
        <div class="row">
            <div class="col-12">
                <div class="card chart-container">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Payroll Summary Report</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Payroll Date</th>
                                        <th>Status</th>
                                        <th>Total Amount</th>
                                        <th>Employee Count</th>
                                        <th>Avg Gross Pay</th>
                                        <th>Avg Net Pay</th>
                                        <th>Total Deductions</th>
                                        <th>Total Allowances</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.map(item => `
                                        <tr>
                                            <td>${window.payrollApp.formatDate(item.run_date)}</td>
                                            <td><span class="badge bg-${getStatusColor(item.status)}">${item.status}</span></td>
                                            <td>${window.payrollApp.formatCurrency(item.total_amount)}</td>
                                            <td>${item.employee_count}</td>
                                            <td>${window.payrollApp.formatCurrency(item.avg_gross_pay)}</td>
                                            <td>${window.payrollApp.formatCurrency(item.avg_net_pay)}</td>
                                            <td>${window.payrollApp.formatCurrency(item.total_deductions)}</td>
                                            <td>${window.payrollApp.formatCurrency(item.total_allowances)}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderEmployeeReport(data) {
    return `
        <div class="row">
            <div class="col-12">
                <div class="card chart-container">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Employee Salary Report</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th>Salary</th>
                                        <th>Hire Date</th>
                                        <th>Avg Monthly Pay</th>
                                        <th>Payslips</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.map(employee => `
                                        <tr>
                                            <td>
                                                <strong>${employee.first_name} ${employee.last_name}</strong><br>
                                                <small class="text-muted">${employee.email}</small>
                                            </td>
                                            <td>${employee.position || 'N/A'}</td>
                                            <td>${employee.department || 'N/A'}</td>
                                            <td>${window.payrollApp.formatCurrency(employee.salary)}</td>
                                            <td>${window.payrollApp.formatDate(employee.hire_date)}</td>
                                            <td>${window.payrollApp.formatCurrency(employee.avg_monthly_pay)}</td>
                                            <td>${employee.payslips_count}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderLeaveReport(data) {
    return `
        <div class="row">
            <div class="col-12">
                <div class="card chart-container">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Leave Analytics Report</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th>Total Leaves</th>
                                        <th>Approved</th>
                                        <th>Pending</th>
                                        <th>Rejected</th>
                                        <th>Total Days</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.map(employee => `
                                        <tr>
                                            <td>
                                                <strong>${employee.first_name} ${employee.last_name}</strong><br>
                                                <small class="text-muted">${employee.email}</small>
                                            </td>
                                            <td>${employee.position || 'N/A'}</td>
                                            <td>${employee.department || 'N/A'}</td>
                                            <td>${employee.total_leaves}</td>
                                            <td><span class="badge bg-success">${employee.approved_leaves}</span></td>
                                            <td><span class="badge bg-warning">${employee.pending_leaves}</span></td>
                                            <td><span class="badge bg-danger">${employee.rejected_leaves}</span></td>
                                            <td>${employee.total_leave_days}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderDepartmentReport(data) {
    return `
        <div class="row">
            <div class="col-12">
                <div class="card chart-container">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Department Salary Analysis</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Department</th>
                                        <th>Employee Count</th>
                                        <th>Average Salary</th>
                                        <th>Min Salary</th>
                                        <th>Max Salary</th>
                                        <th>Total Salary</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.map(dept => `
                                        <tr>
                                            <td><strong>${dept.department}</strong></td>
                                            <td>${dept.employee_count}</td>
                                            <td>${window.payrollApp.formatCurrency(dept.avg_salary)}</td>
                                            <td>${window.payrollApp.formatCurrency(dept.min_salary)}</td>
                                            <td>${window.payrollApp.formatCurrency(dept.max_salary)}</td>
                                            <td>${window.payrollApp.formatCurrency(dept.total_salary)}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderPayrollTrendReport(data) {
    return `
        <div class="row">
            <div class="col-12">
                <div class="card chart-container">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Payroll Trends (Last 12 Months)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Payroll Runs</th>
                                        <th>Total Payroll</th>
                                        <th>Avg per Run</th>
                                        <th>Total Payslips</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.map(trend => `
                                        <tr>
                                            <td><strong>${trend.month}</strong></td>
                                            <td>${trend.payroll_runs}</td>
                                            <td>${window.payrollApp.formatCurrency(trend.total_payroll)}</td>
                                            <td>${window.payrollApp.formatCurrency(trend.avg_payroll_per_run)}</td>
                                            <td>${trend.total_payslips}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderAttendanceReport(data) {
    return `
        <div class="row">
            <div class="col-12">
                <div class="card chart-container">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Employee Attendance Report</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th>Approved Leaves</th>
                                        <th>Total Leave Days</th>
                                        <th>Pending Leaves</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.map(employee => `
                                        <tr>
                                            <td>
                                                <strong>${employee.first_name} ${employee.last_name}</strong><br>
                                                <small class="text-muted">${employee.email}</small>
                                            </td>
                                            <td>${employee.position || 'N/A'}</td>
                                            <td>${employee.department || 'N/A'}</td>
                                            <td><span class="badge bg-success">${employee.approved_leaves}</span></td>
                                            <td>${employee.total_leave_days}</td>
                                            <td><span class="badge bg-warning">${employee.pending_leaves}</span></td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
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

function refreshAllReports() {
    loadReport();
    window.payrollApp.showSuccess('All reports refreshed successfully!');
}

function exportReport() {
    if (!currentReportData) {
        window.payrollApp.showError('No report data to export');
        return;
    }

    // Create CSV content
    const reportType = document.getElementById('reportType').value;
    let csvContent = '';

    // Add headers based on report type
    switch (reportType) {
        case 'compliance':
            csvContent = 'Metric,Value\n';
            currentReportData.forEach(item => {
                csvContent += `"${item.metric}","${item.value}"\n`;
            });
            break;
        case 'employees':
            csvContent = 'First Name,Last Name,Email,Position,Department,Salary,Hire Date,Avg Monthly Pay,Payslips\n';
            currentReportData.forEach(emp => {
                csvContent += `"${emp.first_name}","${emp.last_name}","${emp.email}","${emp.position || ''}","${emp.department || ''}","${emp.salary}","${emp.hire_date}","${emp.avg_monthly_pay}","${emp.payslips_count}"\n`;
            });
            break;
        default:
            window.payrollApp.showError('Export not implemented for this report type');
            return;
    }

    // Download CSV
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${reportType}_report_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);

    window.payrollApp.showSuccess('Report exported successfully!');
}
</script>

<?php require_once '../../includes/footer.php'; ?>