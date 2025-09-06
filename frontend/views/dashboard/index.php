<?php
$pageTitle = 'Dashboard';
require_once '../../config/config.php';
requireAuth();
require_once '../../includes/header.php';
?>

<?php include '../../includes/sidebar.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="main-content" style="margin-top: 76px;">
    <div class="container-fluid p-4">
        <!-- Creative Hero Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="hero-section">
                    <div class="hero-content">
                        <div class="hero-badge">
                            <i class="fas fa-crown text-warning me-2"></i>
                            <span>Premium Dashboard</span>
                        </div>

                        <h1 class="hero-title">
                            <span class="hero-greeting">Welcome back,</span>
                            <br>
                            <span class="hero-name"><?php echo htmlspecialchars($_SESSION['user']['username'] ?? 'User'); ?>!</span>
                        </h1>

                        <p class="hero-subtitle">
                            <i class="fas fa-magic me-2"></i>
                            Manage your payroll system efficiently and effectively.
                        </p>


                    </div>

                </div>
            </div>
        </div>

        <!-- Creative KPI Cards -->
        <div class="row mb-5" id="kpiCards">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card-creative h-100">
                    <div class="card-body text-center">
                        <div class="kpi-icon-creative mx-auto">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5 class="kpi-title-creative">Total Employees</h5>
                        <div class="kpi-value-creative" id="totalEmployees">-</div>
                        <div class="kpi-change-creative">
                            <i class="fas fa-arrow-up text-success"></i>
                            <span>+12% this month</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card-creative h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <div class="card-body text-center">
                        <div class="kpi-icon-creative mx-auto">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <h5 class="kpi-title-creative">Total Payroll</h5>
                        <div class="kpi-value-creative" id="totalPayroll">$0</div>
                        <div class="kpi-change-creative">
                            <i class="fas fa-arrow-up text-success"></i>
                            <span>+8% from last month</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card-creative h-100" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <div class="card-body text-center">
                        <div class="kpi-icon-creative mx-auto">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h5 class="kpi-title-creative">Pending Leaves</h5>
                        <div class="kpi-value-creative" id="pendingLeaves">-</div>
                        <div class="kpi-change-creative">
                            <i class="fas fa-clock text-warning"></i>
                            <span>3 need approval</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card-creative h-100" style="background: linear-gradient(135deg, #ec4899 0%, #be185d 100%);">
                    <div class="card-body text-center">
                        <div class="kpi-icon-creative mx-auto">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h5 class="kpi-title-creative">Performance</h5>
                        <div class="kpi-value-creative">98%</div>
                        <div class="kpi-change-creative">
                            <i class="fas fa-star text-warning"></i>
                            <span>Excellent rating</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Creative Charts Section -->
        <div class="row mb-5">
            <div class="col-xl-8 mb-4">
                <div class="card chart-container-creative h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">
                                <i class="fas fa-chart-line me-2 text-primary"></i>
                                Payroll Analytics
                            </h5>
                            <p class="text-muted mb-0 small">Monthly trends and insights</p>
                        </div>
                        <div class="chart-badge">
                            <i class="fas fa-rocket text-success"></i>
                            <span>Growing</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="payrollChart" style="height: 350px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 mb-4">
                <div class="card chart-container-creative h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-1">
                            <i class="fas fa-users me-2 text-info"></i>
                            Team Composition
                        </h5>
                        <p class="text-muted mb-0 small">Employee distribution</p>
                    </div>
                    <div class="card-body">
                        <div id="employeeChart" style="height: 350px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity & Quick Actions -->
        <div class="row">
            <div class="col-xl-6 mb-4">
                <div class="card chart-container h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Recent Activity</h5>
                        <a href="../employees/index.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div id="recentActivity" class="list-group list-group-flush">
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-spinner fa-spin me-2"></i>Loading recent activity...
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 mb-4">
                <div class="card chart-container h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <button class="btn btn-primary w-100" onclick="window.location.href='../employees/index.php'">
                                    <i class="fas fa-user-plus me-2"></i>Add Employee
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-success w-100" onclick="window.location.href='../payroll/index.php'">
                                    <i class="fas fa-money-bill-wave me-2"></i>Run Payroll
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-warning w-100" onclick="window.location.href='../leaves/index.php'">
                                    <i class="fas fa-calendar-plus me-2"></i>Request Leave
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-info w-100" onclick="window.location.href='../reports/index.php'">
                                    <i class="fas fa-chart-line me-2"></i>View Reports
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
    initializeCharts();
});

async function loadDashboardData() {
    try {
        // Load KPI data
        const kpiResponse = await window.payrollApp.apiRequest('/api/dashboard/kpi');
        if (kpiResponse.ok) {
            const kpiData = await kpiResponse.json();
            updateKPICards(kpiData);
        }

        // Load recent activity
        const activityResponse = await window.payrollApp.apiRequest('/api/dashboard/activity');
        if (activityResponse.ok) {
            const activityData = await activityResponse.json();
            updateRecentActivity(activityData);
        }
    } catch (error) {
        console.error('Failed to load dashboard data:', error);
    }
}

function updateKPICards(data) {
    document.getElementById('totalEmployees').textContent = data.totalEmployees || 0;
    document.getElementById('totalPayroll').textContent = window.payrollApp.formatCurrency(data.totalPayroll || 0);
    document.getElementById('pendingLeaves').textContent = data.pendingLeaves || 0;
    document.getElementById('activeProjects').textContent = data.activeProjects || 0;
}

function updateRecentActivity(activities) {
    const container = document.getElementById('recentActivity');
    if (!activities || activities.length === 0) {
        container.innerHTML = '<div class="text-center text-muted py-4">No recent activity</div>';
        return;
    }

    container.innerHTML = activities.map(activity => `
        <div class="list-group-item px-0">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                    <i class="fas fa-${activity.icon} text-primary"></i>
                </div>
                <div class="flex-grow-1">
                    <p class="mb-0 small">${activity.description}</p>
                    <small class="text-muted">${window.payrollApp.formatDate(activity.date)}</small>
                </div>
            </div>
        </div>
    `).join('');
}

function initializeCharts() {
    // Payroll Trends Chart
    const payrollOptions = {
        series: [{
            name: 'Payroll Amount',
            data: [12000, 15000, 18000, 14000, 20000, 17000, 22000]
        }],
        chart: {
            type: 'area',
            height: 300,
            toolbar: { show: false }
        },
        colors: ['#4f46e5'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth' },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul']
        },
        yaxis: {
            labels: {
                formatter: function(value) {
                    return 'KSH ' + (value / 1000) + 'k';
                }
            }
        }
    };

    const payrollChart = new ApexCharts(document.querySelector("#payrollChart"), payrollOptions);
    payrollChart.render();

    // Employee Distribution Chart
    const employeeOptions = {
        series: [44, 55, 13, 43],
        chart: {
            type: 'donut',
            height: 300
        },
        labels: ['Full-time', 'Part-time', 'Contract', 'Intern'],
        colors: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444'],
        legend: {
            position: 'bottom'
        }
    };

    const employeeChart = new ApexCharts(document.querySelector("#employeeChart"), employeeOptions);
    employeeChart.render();
}

function refreshDashboard() {
    const button = event.target.closest('button');
    const resetLoading = window.payrollApp.showLoading(button);

    loadDashboardData().finally(() => {
        resetLoading();
        window.payrollApp.showSuccess('Dashboard refreshed successfully!');
    });
}

function exportReport() {
    window.payrollApp.showSuccess('Report export feature coming soon!');
}
</script>

<?php require_once '../../includes/footer.php'; ?>