<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom fixed-top" style="margin-left: 260px;">
    <div class="container-fluid">
        <button class="btn btn-link text-decoration-none me-3 sidebar-toggle d-lg-none" type="button">
            <i class="fas fa-bars fs-5"></i>
        </button>

        <div class="d-flex align-items-center">
            <h5 class="mb-0 me-3 d-none d-lg-block"><?php echo htmlspecialchars($pageTitle); ?></h5>
        </div>

        <div class="ms-auto d-flex align-items-center">
            <!-- Theme Toggle -->
            <button class="btn btn-link text-decoration-none me-3 theme-toggle" type="button" title="Toggle Theme">
                <i class="fas fa-moon"></i>
            </button>

            <!-- Notifications -->
            <div class="dropdown me-3">
                <button class="btn btn-link text-decoration-none position-relative" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="notificationBadge">
                        0
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width: 300px;">
                    <li><h6 class="dropdown-header">Notifications</h6></li>
                    <li><a class="dropdown-item" href="#">No new notifications</a></li>
                </ul>
            </div>

            <!-- User Menu -->
            <div class="dropdown">
                <button class="btn btn-link text-decoration-none d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['user']['username'] ?? 'User'); ?></span>
                    <i class="fas fa-chevron-down ms-2"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header"><?php echo htmlspecialchars($_SESSION['user']['username'] ?? 'User'); ?></h6></li>
                    <li><a class="dropdown-item" href="../settings/index.php"><i class="fas fa-user-cog me-2"></i>Profile Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item logout-btn" href="#" onclick="window.payrollApp.logout()"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
