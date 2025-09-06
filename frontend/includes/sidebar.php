<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="p-3">
        <div class="d-flex align-items-center mb-4">
            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="fas fa-building text-primary fs-5"></i>
            </div>
            <div class="sidebar-brand">
                <h5 class="mb-0 text-white"><?php echo APP_NAME; ?></h5>
                <small class="text-white-50">v<?php echo APP_VERSION; ?></small>
            </div>
        </div>

        <nav class="nav flex-column">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' && strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false ? 'active' : ''; ?>"
               href="../dashboard/index.php">
                <i class="fas fa-tachometer-alt me-3"></i>
                <span>Dashboard</span>
            </a>

            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'employees') !== false ? 'active' : ''; ?>"
               href="../employees/index.php">
                <i class="fas fa-users me-3"></i>
                <span>Employees</span>
            </a>

            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'payroll') !== false ? 'active' : ''; ?>"
               href="../payroll/index.php">
                <i class="fas fa-money-bill-wave me-3"></i>
                <span>Payroll</span>
            </a>

            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'leaves') !== false ? 'active' : ''; ?>"
               href="../leaves/index.php">
                <i class="fas fa-calendar-alt me-3"></i>
                <span>Leave</span>
            </a>

            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'reports') !== false ? 'active' : ''; ?>"
               href="../reports/index.php">
                <i class="fas fa-chart-bar me-3"></i>
                <span>Reports</span>
            </a>

            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'settings') !== false ? 'active' : ''; ?>"
               href="../settings/index.php">
                <i class="fas fa-cog me-3"></i>
                <span>Settings</span>
            </a>
        </nav>

        <div class="mt-auto">
            <div class="user-info p-3 bg-white bg-opacity-10 rounded">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div class="flex-grow-1">
                        <small class="text-white-50 d-block">Welcome back</small>
                        <strong class="text-white"><?php echo htmlspecialchars($_SESSION['user']['username'] ?? 'User'); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>