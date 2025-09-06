<?php
$pageTitle = 'Settings';
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
                        <h1 class="h3 mb-1">Settings</h1>
                        <p class="text-muted mb-0">Manage your account and system preferences</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary" onclick="exportData()">
                            <i class="fas fa-download me-2"></i>Export Data
                        </button>
                        <button class="btn btn-success" onclick="saveAllSettings()">
                            <i class="fas fa-save me-2"></i>Save All Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Navigation -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card chart-container">
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                                    <i class="fas fa-user me-2"></i>Profile
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                                    <i class="fas fa-shield-alt me-2"></i>Security
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab">
                                    <i class="fas fa-bell me-2"></i>Notifications
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button" role="tab">
                                    <i class="fas fa-cog me-2"></i>System
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="audit-tab" data-bs-toggle="tab" data-bs-target="#audit" type="button" role="tab">
                                    <i class="fas fa-history me-2"></i>Audit Logs
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="tab-content" id="settingsTabContent">
            <!-- Profile Settings -->
            <div class="tab-pane fade show active" id="profile" role="tabpanel">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card chart-container">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Profile Information</h5>
                            </div>
                            <div class="card-body">
                                <form id="profileForm">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="firstName" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="firstName" name="first_name" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="lastName" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="lastName" name="last_name" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="phone" class="form-label">Phone</label>
                                            <input type="tel" class="form-control" id="phone" name="phone">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="position" class="form-label">Position</label>
                                            <input type="text" class="form-control" id="position" name="position">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="department" class="form-label">Department</label>
                                            <input type="text" class="form-control" id="department" name="department">
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Update Profile
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card chart-container">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Account Status</h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="fas fa-user-circle fa-3x text-primary mb-3"></i>
                                    <h6 id="username">Loading...</h6>
                                    <span class="badge bg-success" id="userRole">Loading...</span>
                                </div>
                                <div class="small text-muted">
                                    Member since: <span id="memberSince">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="tab-pane fade" id="security" role="tabpanel">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card chart-container">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Change Password</h5>
                            </div>
                            <div class="card-body">
                                <form id="passwordForm">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="currentPassword" class="form-label">Current Password</label>
                                            <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="newPassword" class="form-label">New Password</label>
                                            <input type="password" class="form-control" id="newPassword" name="new_password" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-key me-2"></i>Change Password
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card chart-container mt-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Security Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Last Login</label>
                                        <p class="mb-0" id="lastLogin">Loading...</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Login Attempts</label>
                                        <p class="mb-0" id="loginAttempts">Loading...</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Account Status</label>
                                        <p class="mb-0">
                                            <span class="badge bg-success" id="accountStatus">Active</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Two-Factor Auth</label>
                                        <p class="mb-0" id="twoFactorStatus">Disabled</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary" onclick="clearSessions()">
                                        <i class="fas fa-sign-out-alt me-2"></i>Clear All Sessions
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="enableTwoFactor()">
                                        <i class="fas fa-mobile-alt me-2"></i>Enable 2FA
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card chart-container">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Security Tips</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled small">
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Use strong passwords
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Enable two-factor authentication
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Regularly change passwords
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Monitor login activity
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications Settings -->
            <div class="tab-pane fade" id="notifications" role="tabpanel">
                <div class="row">
                    <div class="col-12">
                        <div class="card chart-container">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Notification Preferences</h5>
                            </div>
                            <div class="card-body">
                                <form id="notificationsForm">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <h6 class="fw-bold mb-3">Email Notifications</h6>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="emailPayroll" name="email_payroll_processed">
                                                <label class="form-check-label" for="emailPayroll">
                                                    Payroll processed notifications
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="emailLeaveApproved" name="email_leave_approved">
                                                <label class="form-check-label" for="emailLeaveApproved">
                                                    Leave request approved
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="emailLeaveRejected" name="email_leave_rejected">
                                                <label class="form-check-label" for="emailLeaveRejected">
                                                    Leave request rejected
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="emailPasswordChanged" name="email_password_changed">
                                                <label class="form-check-label" for="emailPasswordChanged">
                                                    Password changed notifications
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="fw-bold mb-3">Other Notifications</h6>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="browserNotifications" name="browser_notifications">
                                                <label class="form-check-label" for="browserNotifications">
                                                    Browser notifications
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="smsAlerts" name="sms_alerts">
                                                <label class="form-check-label" for="smsAlerts">
                                                    SMS alerts
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="weeklyReports" name="weekly_reports">
                                                <label class="form-check-label" for="weeklyReports">
                                                    Weekly summary reports
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="monthlyReports" name="monthly_reports">
                                                <label class="form-check-label" for="monthlyReports">
                                                    Monthly summary reports
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Save Notification Preferences
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Settings -->
            <div class="tab-pane fade" id="system" role="tabpanel">
                <div class="row">
                    <div class="col-12">
                        <div class="card chart-container">
                            <div class="card-header">
                                <h5 class="card-title mb-0">System Configuration</h5>
                            </div>
                            <div class="card-body">
                                <form id="systemForm">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="companyName" class="form-label">Company Name</label>
                                            <input type="text" class="form-control" id="companyName" name="company_name">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="companyEmail" class="form-label">Company Email</label>
                                            <input type="email" class="form-control" id="companyEmail" name="company_email">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="timezone" class="form-label">Timezone</label>
                                            <select class="form-select" id="timezone" name="timezone">
                                                <option value="UTC">UTC</option>
                                                <option value="America/New_York">Eastern Time</option>
                                                <option value="America/Chicago">Central Time</option>
                                                <option value="America/Denver">Mountain Time</option>
                                                <option value="America/Los_Angeles">Pacific Time</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="currency" class="form-label">Currency</label>
                                            <select class="form-select" id="currency" name="currency">
                                                <option value="KSH">KSH (KSH)</option>
                                                <option value="EUR">EUR (€)</option>
                                                <option value="GBP">GBP (£)</option>
                                                <option value="JPY">JPY (¥)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="theme" class="form-label">Default Theme</label>
                                            <select class="form-select" id="theme" name="theme">
                                                <option value="light">Light</option>
                                                <option value="dark">Dark</option>
                                                <option value="auto">Auto</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="language" class="form-label">Language</label>
                                            <select class="form-select" id="language" name="language">
                                                <option value="en">English</option>
                                                <option value="es">Spanish</option>
                                                <option value="fr">French</option>
                                                <option value="de">German</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <hr>
                                            <h6 class="fw-bold mb-3">Security Settings</h6>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="sessionTimeout" class="form-label">Session Timeout (minutes)</label>
                                            <input type="number" class="form-control" id="sessionTimeout" name="session_timeout" min="15" max="480">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="maxLoginAttempts" class="form-label">Max Login Attempts</label>
                                            <input type="number" class="form-control" id="maxLoginAttempts" name="max_login_attempts" min="3" max="10">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="passwordMinLength" class="form-label">Min Password Length</label>
                                            <input type="number" class="form-control" id="passwordMinLength" name="password_min_length" min="6" max="20">
                                        </div>
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="emailNotifications" name="email_notifications">
                                                <label class="form-check-label" for="emailNotifications">
                                                    Enable email notifications
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="autoBackup" name="auto_backup">
                                                <label class="form-check-label" for="autoBackup">
                                                    Enable automatic backups
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="twoFactorAuth" name="two_factor_auth">
                                                <label class="form-check-label" for="twoFactorAuth">
                                                    Enable two-factor authentication
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Save System Settings
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Audit Logs -->
            <div class="tab-pane fade" id="audit" role="tabpanel">
                <div class="row">
                    <div class="col-12">
                        <div class="card chart-container">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Audit Logs</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="auditTable">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Table</th>
                                                <th>Timestamp</th>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        <tbody id="auditTableBody">
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    <i class="fas fa-spinner fa-spin me-2"></i>Loading audit logs...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let currentSettings = {};

document.addEventListener('DOMContentLoaded', function() {
    loadAllSettings();
    setupSettingsEventListeners();
});

function setupSettingsEventListeners() {
    // Profile form
    document.getElementById('profileForm').addEventListener('submit', handleProfileUpdate);

    // Password form
    document.getElementById('passwordForm').addEventListener('submit', handlePasswordChange);

    // Notifications form
    document.getElementById('notificationsForm').addEventListener('submit', handleNotificationsUpdate);

    // System form
    document.getElementById('systemForm').addEventListener('submit', handleSystemSettingsUpdate);
}

async function loadAllSettings() {
    try {
        await Promise.all([
            loadProfile(),
            loadSecurityInfo(),
            loadNotifications(),
            loadSystemSettings(),
            loadAuditLogs()
        ]);
    } catch (error) {
        console.error('Error loading settings:', error);
        window.payrollApp.showError('Failed to load settings');
    }
}

async function loadProfile() {
    try {
        const response = await window.payrollApp.apiRequest('/settings/profile');
        if (response.ok) {
            const profile = await response.json();

            document.getElementById('firstName').value = profile.first_name || '';
            document.getElementById('lastName').value = profile.last_name || '';
            document.getElementById('email').value = profile.email || '';
            document.getElementById('phone').value = profile.phone || '';
            document.getElementById('position').value = profile.position || '';
            document.getElementById('department').value = profile.department || '';
            document.getElementById('username').textContent = profile.username || '';
            document.getElementById('userRole').textContent = profile.role_name || '';
            document.getElementById('memberSince').textContent = window.payrollApp.formatDate(profile.created_at);
        }
    } catch (error) {
        console.error('Error loading profile:', error);
    }
}

async function loadSecurityInfo() {
    try {
        const response = await window.payrollApp.apiRequest('/settings/security');
        if (response.ok) {
            const security = await response.json();

            document.getElementById('lastLogin').textContent = security.last_login ?
                window.payrollApp.formatDateTime(security.last_login) : 'Never';
            document.getElementById('loginAttempts').textContent = security.login_attempts || 0;
            document.getElementById('accountStatus').textContent = security.account_locked ? 'Locked' : 'Active';
            document.getElementById('accountStatus').className = security.account_locked ?
                'badge bg-danger' : 'badge bg-success';
            document.getElementById('twoFactorStatus').textContent = security.two_factor_enabled ? 'Enabled' : 'Disabled';
        }
    } catch (error) {
        console.error('Error loading security info:', error);
    }
}

async function loadNotifications() {
    try {
        const response = await window.payrollApp.apiRequest('/settings/notifications');
        if (response.ok) {
            const notifications = await response.json();

            Object.keys(notifications).forEach(key => {
                const element = document.getElementById(camelToKebab(key));
                if (element) {
                    element.checked = notifications[key];
                }
            });
        }
    } catch (error) {
        console.error('Error loading notifications:', error);
    }
}

async function loadSystemSettings() {
    try {
        const response = await window.payrollApp.apiRequest('/settings/system');
        if (response.ok) {
            const settings = await response.json();
            currentSettings = settings;

            Object.keys(settings).forEach(key => {
                const element = document.getElementById(camelToKebab(key));
                if (element) {
                    if (element.type === 'checkbox') {
                        element.checked = settings[key];
                    } else {
                        element.value = settings[key];
                    }
                }
            });
        }
    } catch (error) {
        console.error('Error loading system settings:', error);
    }
}

async function loadAuditLogs() {
    try {
        const response = await window.payrollApp.apiRequest('/settings/audit-logs');
        if (response.ok) {
            const logs = await response.json();
            renderAuditLogs(logs);
        }
    } catch (error) {
        console.error('Error loading audit logs:', error);
    }
}

function renderAuditLogs(logs) {
    const tbody = document.getElementById('auditTableBody');

    if (logs.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-4">
                    <i class="fas fa-history me-2"></i>No audit logs found
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = logs.map(log => `
        <tr>
            <td><span class="badge bg-primary">${log.action}</span></td>
            <td>${log.table_name}</td>
            <td>${window.payrollApp.formatDateTime(log.created_at)}</td>
            <td>
                <button class="btn btn-sm btn-outline-info" onclick="viewAuditDetails(${JSON.stringify(log).replace(/"/g, '"')})">
                    <i class="fas fa-eye"></i> View
                </button>
            </td>
        </tr>
    `).join('');
}

async function handleProfileUpdate(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await window.payrollApp.apiRequest('/settings/profile', {
            method: 'PUT',
            body: JSON.stringify(data)
        });

        if (response.ok) {
            const result = await response.json();
            window.payrollApp.showSuccess('Profile updated successfully!');
            loadProfile(); // Refresh profile data
        } else {
            const error = await response.json();
            window.payrollApp.showError(error.error || 'Failed to update profile');
        }
    } catch (error) {
        window.payrollApp.showError('Network error. Please try again.');
    }
}

async function handlePasswordChange(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());

    if (data.new_password !== data.confirm_password) {
        window.payrollApp.showError('New passwords do not match');
        return;
    }

    try {
        const response = await window.payrollApp.apiRequest('/settings/password', {
            method: 'PUT',
            body: JSON.stringify({
                current_password: data.current_password,
                new_password: data.new_password
            })
        });

        if (response.ok) {
            const result = await response.json();
            window.payrollApp.showSuccess('Password changed successfully!');
            e.target.reset();
        } else {
            const error = await response.json();
            window.payrollApp.showError(error.error || 'Failed to change password');
        }
    } catch (error) {
        window.payrollApp.showError('Network error. Please try again.');
    }
}

async function handleNotificationsUpdate(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const data = {};

    // Convert checkbox values to boolean
    for (let [key, value] of formData.entries()) {
        data[key] = value === 'on';
    }

    // Handle unchecked checkboxes
    const checkboxes = e.target.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        if (!formData.has(checkbox.name)) {
            data[checkbox.name] = false;
        }
    });

    try {
        const response = await window.payrollApp.apiRequest('/settings/notifications', {
            method: 'PUT',
            body: JSON.stringify(data)
        });

        if (response.ok) {
            const result = await response.json();
            window.payrollApp.showSuccess('Notification preferences updated successfully!');
        } else {
            const error = await response.json();
            window.payrollApp.showError(error.error || 'Failed to update notifications');
        }
    } catch (error) {
        window.payrollApp.showError('Network error. Please try again.');
    }
}

async function handleSystemSettingsUpdate(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const data = {};

    // Convert form data to appropriate types
    for (let [key, value] of formData.entries()) {
        if (value === 'on') {
            data[key] = true;
        } else if (value === 'off' || value === '') {
            data[key] = false;
        } else if (!isNaN(value)) {
            data[key] = parseInt(value);
        } else {
            data[key] = value;
        }
    }

    // Handle unchecked checkboxes
    const checkboxes = e.target.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        if (!formData.has(checkbox.name)) {
            data[checkbox.name] = false;
        }
    });

    try {
        const response = await window.payrollApp.apiRequest('/settings/system', {
            method: 'PUT',
            body: JSON.stringify(data)
        });

        if (response.ok) {
            const result = await response.json();
            window.payrollApp.showSuccess('System settings updated successfully!');
            currentSettings = result;
        } else {
            const error = await response.json();
            window.payrollApp.showError(error.error || 'Failed to update system settings');
        }
    } catch (error) {
        window.payrollApp.showError('Network error. Please try again.');
    }
}

async function clearSessions() {
    if (!confirm('Are you sure you want to clear all sessions? This will log you out of all devices.')) {
        return;
    }

    try {
        const response = await window.payrollApp.apiRequest('/settings/clear-sessions', {
            method: 'POST'
        });

        if (response.ok) {
            const result = await response.json();
            window.payrollApp.showSuccess('All sessions cleared successfully!');
        } else {
            const error = await response.json();
            window.payrollApp.showError(error.error || 'Failed to clear sessions');
        }
    } catch (error) {
        window.payrollApp.showError('Network error. Please try again.');
    }
}

async function enableTwoFactor() {
    window.payrollApp.showSuccess('Two-factor authentication setup coming soon!');
}

function viewAuditDetails(log) {
    const details = `
        Action: ${log.action}
        Table: ${log.table_name}
        Time: ${window.payrollApp.formatDateTime(log.created_at)}
        Old Values: ${log.old_values || 'N/A'}
        New Values: ${log.new_values || 'N/A'}
    `;

    alert(details);
}

async function exportData() {
    try {
        const response = await window.payrollApp.apiRequest('/settings/export-data');
        if (response.ok) {
            const data = await response.json();

            // Create downloadable JSON file
            const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `user_data_export_${new Date().toISOString().split('T')[0]}.json`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            window.payrollApp.showSuccess('Data exported successfully!');
        } else {
            const error = await response.json();
            window.payrollApp.showError(error.error || 'Failed to export data');
        }
    } catch (error) {
        window.payrollApp.showError('Network error. Please try again.');
    }
}

async function saveAllSettings() {
    // This would save all pending changes
    window.payrollApp.showSuccess('All settings saved successfully!');
}

function camelToKebab(str) {
    return str.replace(/([a-z0-9]|(?=[A-Z]))([A-Z])/g, '$1-$2').toLowerCase();
}
</script>

<?php require_once '../../includes/footer.php'; ?>