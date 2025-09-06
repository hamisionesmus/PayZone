<?php
$pageTitle = 'Employee Management';
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
                        <h1 class="h3 mb-1">Employee Management</h1>
                        <p class="text-muted mb-0">Manage your organization's workforce</p>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                        <i class="fas fa-user-plus me-2"></i>Add Employee
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card chart-container">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search employees...">
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="departmentFilter">
                                    <option value="">All Departments</option>
                                    <option value="IT">IT</option>
                                    <option value="HR">HR</option>
                                    <option value="Finance">Finance</option>
                                    <option value="Marketing">Marketing</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-primary w-100" onclick="applyFilters()">
                                    <i class="fas fa-filter me-2"></i>Filter
                                </button>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                    <i class="fas fa-times me-2"></i>Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employees Table -->
        <div class="row">
            <div class="col-12">
                <div class="card chart-container">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Employees</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="exportEmployees()">
                                <i class="fas fa-download me-1"></i>Export
                            </button>
                            <button class="btn btn-sm btn-outline-success" onclick="importEmployees()">
                                <i class="fas fa-upload me-1"></i>Import
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="employeesTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Department</th>
                                        <th>Position</th>
                                        <th>Salary</th>
                                        <th>Status</th>
                                        <th>Hire Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="employeesTableBody">
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-spinner fa-spin me-2"></i>Loading employees...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Employee pagination" class="mt-4">
                            <ul class="pagination justify-content-center" id="pagination">
                                <!-- Pagination will be generated by JavaScript -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addEmployeeForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">First Name *</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="lastName" class="form-label">Last Name *</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="col-md-6">
                            <label for="department" class="form-label">Department *</label>
                            <select class="form-select" id="department" name="department" required>
                                <option value="">Select Department</option>
                                <option value="IT">IT</option>
                                <option value="HR">HR</option>
                                <option value="Finance">Finance</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Operations">Operations</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="position" class="form-label">Position *</label>
                            <input type="text" class="form-control" id="position" name="position" required>
                        </div>
                        <div class="col-md-6">
                            <label for="salary" class="form-label">Salary *</label>
                            <div class="input-group">
                                <span class="input-group-text">KSH</span>
                                <input type="number" class="form-control" id="salary" name="salary" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="hireDate" class="form-label">Hire Date *</label>
                            <input type="date" class="form-control" id="hireDate" name="hire_date" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveEmployeeBtn">
                        <span class="loading-spinner d-none me-2"></span>
                        <i class="fas fa-save me-2"></i>Save Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Employee Modal -->
<div class="modal fade" id="viewEmployeeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Employee Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                                    <i class="fas fa-user text-primary fs-1"></i>
                                </div>
                                <h5 id="viewEmployeeName">-</h5>
                                <p class="text-muted mb-1">Employee ID: <span id="viewEmployeeId">-</span></p>
                                <span class="badge bg-success" id="viewEmployeeStatus">Active</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Contact Information</h6>
                                <div class="mb-2">
                                    <i class="fas fa-envelope me-2 text-primary"></i>
                                    <span id="viewEmployeeEmail">-</span>
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-phone me-2 text-primary"></i>
                                    <span id="viewEmployeePhone">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Job Information</h6>
                                <div class="mb-2">
                                    <i class="fas fa-building me-2 text-primary"></i>
                                    <strong>Department:</strong> <span id="viewEmployeeDepartment">-</span>
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-briefcase me-2 text-primary"></i>
                                    <strong>Position:</strong> <span id="viewEmployeePosition">-</span>
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-calendar me-2 text-primary"></i>
                                    <strong>Hire Date:</strong> <span id="viewEmployeeHireDate">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Compensation</h6>
                                <div class="mb-2">
                                    <i class="fas fa-dollar-sign me-2 text-success"></i>
                                    <strong>Salary:</strong> <span id="viewEmployeeSalary">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="editEmployeeFromView()">
                    <i class="fas fa-edit me-2"></i>Edit Employee
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Employee Modal -->
<div class="modal fade" id="editEmployeeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editEmployeeForm">
                <input type="hidden" id="editEmployeeId" name="id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="editFirstName" class="form-label">First Name *</label>
                            <input type="text" class="form-control" id="editFirstName" name="first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editLastName" class="form-label">Last Name *</label>
                            <input type="text" class="form-control" id="editLastName" name="last_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editEmail" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editPhone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="editPhone" name="phone">
                        </div>
                        <div class="col-md-6">
                            <label for="editDepartment" class="form-label">Department *</label>
                            <select class="form-select" id="editDepartment" name="department" required>
                                <option value="">Select Department</option>
                                <option value="IT">IT</option>
                                <option value="HR">HR</option>
                                <option value="Finance">Finance</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Operations">Operations</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editPosition" class="form-label">Position *</label>
                            <input type="text" class="form-control" id="editPosition" name="position" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editSalary" class="form-label">Salary *</label>
                            <div class="input-group">
                                <span class="input-group-text">KSH</span>
                                <input type="number" class="form-control" id="editSalary" name="salary" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="editHireDate" class="form-label">Hire Date *</label>
                            <input type="date" class="form-control" id="editHireDate" name="hire_date" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="updateEmployeeBtn">
                        <span class="loading-spinner d-none me-2"></span>
                        <i class="fas fa-save me-2"></i>Update Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let employees = [];
let filteredEmployees = [];

document.addEventListener('DOMContentLoaded', function() {
    loadEmployees();
    setupEventListeners();
});

function setupEventListeners() {
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        filterEmployees();
    });

    // Filter functionality
    document.getElementById('departmentFilter').addEventListener('change', filterEmployees);
    document.getElementById('statusFilter').addEventListener('change', filterEmployees);

    // Form submission
    document.getElementById('addEmployeeForm').addEventListener('submit', handleEmployeeSubmit);
    document.getElementById('editEmployeeForm').addEventListener('submit', handleEditEmployeeSubmit);
}

async function loadEmployees() {
    try {
        const response = await window.payrollApp.apiRequest('/api/employees');
        if (response.ok) {
            employees = await response.json();
            filteredEmployees = [...employees];
            renderEmployees();
            renderPagination();
        } else {
            throw new Error('Failed to load employees');
        }
    } catch (error) {
        console.error('Error loading employees:', error);
        document.getElementById('employeesTableBody').innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Failed to load employees. Please try again.
                </td>
            </tr>
        `;
    }
}

function filterEmployees() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const departmentFilter = document.getElementById('departmentFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;

    filteredEmployees = employees.filter(employee => {
        const matchesSearch = !searchTerm ||
            employee.first_name.toLowerCase().includes(searchTerm) ||
            employee.last_name.toLowerCase().includes(searchTerm) ||
            employee.email.toLowerCase().includes(searchTerm);

        const matchesDepartment = !departmentFilter || employee.department === departmentFilter;
        const matchesStatus = !statusFilter || employee.status === statusFilter;

        return matchesSearch && matchesDepartment && matchesStatus;
    });

    currentPage = 1;
    renderEmployees();
    renderPagination();
}

function renderEmployees() {
    const tbody = document.getElementById('employeesTableBody');
    const startIndex = (currentPage - 1) * 10;
    const endIndex = startIndex + 10;
    const pageEmployees = filteredEmployees.slice(startIndex, endIndex);

    if (pageEmployees.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <i class="fas fa-users me-2"></i>No employees found
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = pageEmployees.map(employee => `
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="fas fa-user text-primary"></i>
                    </div>
                    <div>
                        <div class="fw-bold">${employee.first_name} ${employee.last_name}</div>
                        <small class="text-muted">ID: ${employee.id}</small>
                    </div>
                </div>
            </td>
            <td>${employee.email}</td>
            <td><span class="badge bg-secondary">${employee.department || 'N/A'}</span></td>
            <td>${employee.position || 'N/A'}</td>
            <td>${window.payrollApp.formatCurrency(employee.salary)}</td>
            <td>
                <span class="badge bg-success">Active</span>
            </td>
            <td>${window.payrollApp.formatDate(employee.hire_date)}</td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="viewEmployee(${employee.id})" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="editEmployee(${employee.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteEmployee(${employee.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function renderPagination() {
    const totalPages = Math.ceil(filteredEmployees.length / 10);
    const pagination = document.getElementById('pagination');

    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let paginationHtml = '';

    // Previous button
    paginationHtml += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Previous</a>
        </li>
    `;

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === currentPage || i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            paginationHtml += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                </li>
            `;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    // Next button
    paginationHtml += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Next</a>
        </li>
    `;

    pagination.innerHTML = paginationHtml;
}

function changePage(page) {
    if (page >= 1 && page <= Math.ceil(filteredEmployees.length / 10)) {
        currentPage = page;
        renderEmployees();
        renderPagination();
    }
}

async function handleEmployeeSubmit(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const employeeData = Object.fromEntries(formData.entries());

    const saveBtn = document.getElementById('saveEmployeeBtn');
    const resetLoading = window.payrollApp.showLoading(saveBtn);

    try {
        const response = await window.payrollApp.apiRequest('/api/employees', {
            method: 'POST',
            body: JSON.stringify(employeeData)
        });

        if (response.ok) {
            const result = await response.json();
            window.payrollApp.showSuccess('Employee added successfully!');

            // Close modal and reset form
            const modal = bootstrap.Modal.getInstance(document.getElementById('addEmployeeModal'));
            modal.hide();
            e.target.reset();

            // Reload employees
            loadEmployees();
        } else {
            const error = await response.json();
            window.payrollApp.showError(error.error || 'Failed to add employee');
        }
    } catch (error) {
        window.payrollApp.showError('Network error. Please try again.');
    } finally {
        resetLoading();
    }
}

function viewEmployee(id) {
    const employee = employees.find(emp => emp.id === id);
    if (!employee) {
        window.payrollApp.showError('Employee not found');
        return;
    }

    // Populate view modal
    document.getElementById('viewEmployeeId').textContent = employee.id;
    document.getElementById('viewEmployeeName').textContent = `${employee.first_name} ${employee.last_name}`;
    document.getElementById('viewEmployeeEmail').textContent = employee.email;
    document.getElementById('viewEmployeePhone').textContent = employee.phone || 'N/A';
    document.getElementById('viewEmployeeDepartment').textContent = employee.department || 'N/A';
    document.getElementById('viewEmployeePosition').textContent = employee.position || 'N/A';
    document.getElementById('viewEmployeeSalary').textContent = window.payrollApp.formatCurrency(employee.salary);
    document.getElementById('viewEmployeeHireDate').textContent = window.payrollApp.formatDate(employee.hire_date);
    document.getElementById('viewEmployeeStatus').textContent = 'Active';

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('viewEmployeeModal'));
    modal.show();
}

function editEmployee(id) {
    const employee = employees.find(emp => emp.id === id);
    if (!employee) {
        window.payrollApp.showError('Employee not found');
        return;
    }

    // Populate edit form
    document.getElementById('editEmployeeId').value = employee.id;
    document.getElementById('editFirstName').value = employee.first_name;
    document.getElementById('editLastName').value = employee.last_name;
    document.getElementById('editEmail').value = employee.email;
    document.getElementById('editPhone').value = employee.phone || '';
    document.getElementById('editDepartment').value = employee.department || '';
    document.getElementById('editPosition').value = employee.position || '';
    document.getElementById('editSalary').value = employee.salary;
    document.getElementById('editHireDate').value = employee.hire_date;

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('editEmployeeModal'));
    modal.show();
}

function editEmployeeFromView() {
    const employeeId = document.getElementById('viewEmployeeId').textContent;
    // Close view modal
    const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewEmployeeModal'));
    viewModal.hide();

    // Open edit modal
    setTimeout(() => {
        editEmployee(parseInt(employeeId));
    }, 300);
}

async function handleEditEmployeeSubmit(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const employeeData = Object.fromEntries(formData.entries());
    const employeeId = employeeData.id;

    const updateBtn = document.getElementById('updateEmployeeBtn');
    const resetLoading = window.payrollApp.showLoading(updateBtn);

    try {
        const response = await window.payrollApp.apiRequest(`/api/employees/${employeeId}`, {
            method: 'PUT',
            body: JSON.stringify(employeeData)
        });

        if (response.ok) {
            const result = await response.json();
            window.payrollApp.showSuccess('Employee updated successfully!');

            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editEmployeeModal'));
            modal.hide();

            // Reload employees
            loadEmployees();
        } else {
            const error = await response.json();
            window.payrollApp.showError(error.error || 'Failed to update employee');
        }
    } catch (error) {
        window.payrollApp.showError('Network error. Please try again.');
    } finally {
        resetLoading();
    }
}

async function deleteEmployee(id) {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    });

    if (result.isConfirmed) {
        try {
            const response = await window.payrollApp.apiRequest(`/api/employees/${id}`, {
                method: 'DELETE'
            });

            if (response.ok) {
                window.payrollApp.showSuccess('Employee deleted successfully!');
                loadEmployees();
            } else {
                const error = await response.json();
                window.payrollApp.showError(error.error || 'Failed to delete employee');
            }
        } catch (error) {
            window.payrollApp.showError('Network error. Please try again.');
        }
    }
}

function applyFilters() {
    filterEmployees();
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('departmentFilter').value = '';
    document.getElementById('statusFilter').value = '';
    filterEmployees();
}

function exportEmployees() {
    if (filteredEmployees.length === 0) {
        window.payrollApp.showError('No employees to export');
        return;
    }

    // Create CSV content
    const headers = ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Department', 'Position', 'Salary', 'Hire Date', 'Status'];
    const csvContent = [
        headers.join(','),
        ...filteredEmployees.map(emp => [
            emp.id,
            `"${emp.first_name}"`,
            `"${emp.last_name}"`,
            `"${emp.email}"`,
            `"${emp.phone || ''}"`,
            `"${emp.department || ''}"`,
            `"${emp.position || ''}"`,
            emp.salary,
            emp.hire_date,
            'Active'
        ].join(','))
    ].join('\n');

    // Create and download file
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `employees_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    window.payrollApp.showSuccess('Employee data exported successfully!');
}

function importEmployees() {
    // Create file input
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.csv';
    input.onchange = handleFileSelect;
    input.click();

    // Add some debugging
    console.log('Import button clicked, file input created');
}

function handleFileSelect(event) {
    const file = event.target.files[0];
    if (!file) {
        console.log('No file selected');
        return;
    }

    console.log('File selected:', file.name);

    if (!file.name.toLowerCase().endsWith('.csv')) {
        window.payrollApp.showError('Please select a CSV file');
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        console.log('File loaded, parsing CSV');
        const csv = e.target.result;
        parseCSVAndImport(csv);
    };
    reader.onerror = function(e) {
        console.error('File reading error:', e);
        window.payrollApp.showError('Error reading file');
    };
    reader.readAsText(file);
}

function parseCSVAndImport(csvText) {
    console.log('Parsing CSV text:', csvText.substring(0, 200) + '...');

    const lines = csvText.split('\n').filter(line => line.trim());
    console.log('Parsed lines:', lines.length);

    if (lines.length < 2) {
        window.payrollApp.showError('CSV file must contain at least a header row and one data row');
        return;
    }

    const headers = lines[0].split(',').map(h => h.replace(/"/g, '').trim().toLowerCase());
    console.log('Headers found:', headers);

    const requiredHeaders = ['first name', 'last name', 'email', 'department', 'position', 'salary', 'hire date'];

    // Validate headers
    const missingHeaders = requiredHeaders.filter(h => !headers.includes(h));
    if (missingHeaders.length > 0) {
        window.payrollApp.showError(`Missing required columns: ${missingHeaders.join(', ')}`);
        return;
    }

    // Parse data rows
    const employees = [];
    for (let i = 1; i < lines.length; i++) {
        const values = parseCSVLine(lines[i]);
        console.log(`Row ${i} parsed values:`, values);

        if (values.length !== headers.length) {
            console.log(`Skipping row ${i}: value count mismatch`);
            continue;
        }

        const employee = {};
        headers.forEach((header, index) => {
            let value = values[index].replace(/"/g, '').trim();

            // Map headers to API field names
            switch (header) {
                case 'first name':
                    employee.first_name = value;
                    break;
                case 'last name':
                    employee.last_name = value;
                    break;
                case 'email':
                    employee.email = value;
                    break;
                case 'phone':
                    employee.phone = value;
                    break;
                case 'department':
                    employee.department = value;
                    break;
                case 'position':
                    employee.position = value;
                    break;
                case 'salary':
                    employee.salary = parseFloat(value) || 0;
                    break;
                case 'hire date':
                    employee.hire_date = value;
                    break;
            }
        });

        console.log(`Parsed employee ${i}:`, employee);

        // Validate required fields
        if (!employee.first_name || !employee.last_name || !employee.email || !employee.department || !employee.position || !employee.salary || !employee.hire_date) {
            console.log(`Skipping invalid employee ${i}`);
            continue; // Skip invalid rows
        }

        employees.push(employee);
    }

    console.log('Valid employees parsed:', employees.length);

    if (employees.length === 0) {
        window.payrollApp.showError('No valid employee data found in CSV');
        return;
    }

    // Show import preview
    showImportPreview(employees);
}

function parseCSVLine(line) {
    const result = [];
    let current = '';
    let inQuotes = false;

    for (let i = 0; i < line.length; i++) {
        const char = line[i];

        if (char === '"') {
            inQuotes = !inQuotes;
        } else if (char === ',' && !inQuotes) {
            result.push(current);
            current = '';
        } else {
            current += char;
        }
    }

    result.push(current);
    return result;
}

function showImportPreview(employees) {
    const modalHtml = `
        <div class="modal fade" id="importPreviewModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Preview</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Found ${employees.length} employee(s) to import. Please review the data below.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Department</th>
                                        <th>Position</th>
                                        <th>Salary</th>
                                        <th>Hire Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${employees.slice(0, 10).map(emp => `
                                        <tr>
                                            <td>${emp.first_name} ${emp.last_name}</td>
                                            <td>${emp.email}</td>
                                            <td><span class="badge bg-secondary">${emp.department}</span></td>
                                            <td>${emp.position}</td>
                                            <td>${window.payrollApp.formatCurrency(emp.salary)}</td>
                                            <td>${window.payrollApp.formatDate(emp.hire_date)}</td>
                                        </tr>
                                    `).join('')}
                                    ${employees.length > 10 ? `<tr><td colspan="6" class="text-center text-muted">... and ${employees.length - 10} more employees</td></tr>` : ''}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="proceedWithImport(${JSON.stringify(employees).replace(/"/g, '"')})" id="importBtn">
                            <i class="fas fa-upload me-2"></i>Import ${employees.length} Employee(s)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove existing modal if present
    const existingModal = document.getElementById('importPreviewModal');
    if (existingModal) {
        existingModal.remove();
    }

    // Add new modal
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('importPreviewModal'));
    modal.show();
}

async function proceedWithImport(employees) {
    const importBtn = document.getElementById('importBtn');
    const resetLoading = window.payrollApp.showLoading(importBtn);

    let successCount = 0;
    let errorCount = 0;
    let errors = [];

    for (const employee of employees) {
        try {
            // Add company_id from current user session
            const employeeData = {
                ...employee,
                company_id: 1 // Default company ID, can be made dynamic later
            };

            const response = await window.payrollApp.apiRequest('/api/employees', {
                method: 'POST',
                body: JSON.stringify(employeeData)
            });

            if (response.ok) {
                successCount++;
            } else {
                const errorData = await response.json();
                errorCount++;
                errors.push(`${employee.first_name} ${employee.last_name}: ${errorData.error || 'Unknown error'}`);
            }
        } catch (error) {
            errorCount++;
            errors.push(`${employee.first_name} ${employee.last_name}: Network error`);
        }
    }

    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('importPreviewModal'));
    modal.hide();

    // Show results
    if (successCount > 0) {
        window.payrollApp.showSuccess(`Successfully imported ${successCount} employee(s)`);
        loadEmployees(); // Refresh the table
    }

    if (errorCount > 0) {
        window.payrollApp.showError(`Failed to import ${errorCount} employee(s)`);
        if (errors.length <= 3) {
            // Show first few errors
            errors.slice(0, 3).forEach(error => {
                setTimeout(() => window.payrollApp.showError(error), 100);
            });
        }
    }
}
</script>

<?php require_once '../../includes/footer.php'; ?>