    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Material Design for Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/mdbootstrap@4.20.0/js/mdb.min.js"></script>

    <!-- jQuery (required for some plugins) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- SweetAlert2 for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart.js for basic charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom JavaScript -->
    <script src="../assets/js/app.js?v=<?php echo time(); ?>"></script>

    <script>
        // Global Payroll App Instance
        window.payrollApp = {
            apiBaseUrl: '<?php echo API_BASE_URL; ?>',
            token: '<?php echo getAuthToken(); ?>',
            theme: localStorage.getItem('theme') || '<?php echo DEFAULT_THEME; ?>',

            // API Request Helper
            async apiRequest(endpoint, options = {}) {
                const url = this.apiBaseUrl + endpoint;
                const defaultOptions = {
                    headers: {
                        'Content-Type': 'application/json',
                        ...options.headers
                    }
                };

                if (this.token) {
                    defaultOptions.headers['Authorization'] = `Bearer ${this.token}`;
                }

                const response = await fetch(url, { ...defaultOptions, ...options });
                return response;
            },

            // Show Success Message
            showSuccess(message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: message,
                    timer: 3000,
                    showConfirmButton: false
                });
            },

            // Show Error Message
            showError(message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: message,
                    confirmButtonColor: '#4f46e5'
                });
            },

            // Show Loading
            showLoading(button) {
                const originalText = button.innerHTML;
                button.innerHTML = '<span class="loading-spinner me-2"></span>Loading...';
                button.disabled = true;

                return () => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                };
            },

            // Toggle Theme
            toggleTheme() {
                this.theme = this.theme === 'light' ? 'dark' : 'light';
                localStorage.setItem('theme', this.theme);
                document.documentElement.setAttribute('data-theme', this.theme);

                const themeIcon = document.querySelector('.theme-toggle i');
                if (themeIcon) {
                    themeIcon.className = this.theme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
                }
            },

            // Initialize Theme
            initTheme() {
                document.documentElement.setAttribute('data-theme', this.theme);
                const themeIcon = document.querySelector('.theme-toggle i');
                if (themeIcon) {
                    themeIcon.className = this.theme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
                }
            },

            // Format Currency
            formatCurrency(amount) {
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD'
                }).format(amount);
            },

            // Format Date
            formatDate(dateString) {
                return new Date(dateString).toLocaleDateString();
            },

            // Format Date and Time
            formatDateTime(dateString) {
                return new Date(dateString).toLocaleString();
            }
        };

        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            window.payrollApp.initTheme();

            // Theme toggle event listener
            const themeToggle = document.querySelector('.theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', () => window.payrollApp.toggleTheme());
            }

            // Sidebar toggle
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    const sidebar = document.querySelector('.sidebar');
                    const mainContent = document.querySelector('.main-content');

                    if (sidebar && mainContent) {
                        sidebar.classList.toggle('collapsed');
                        mainContent.classList.toggle('expanded');
                    }
                });
            }

            // Mobile sidebar toggle
            const mobileSidebarToggle = document.querySelector('.mobile-sidebar-toggle');
            if (mobileSidebarToggle) {
                mobileSidebarToggle.addEventListener('click', function() {
                    const sidebar = document.querySelector('.sidebar');
                    sidebar.classList.toggle('show');
                });
            }
        });

        // Global error handler
        window.addEventListener('unhandledrejection', function(event) {
            console.error('Unhandled promise rejection:', event.reason);
            window.payrollApp.showError('An unexpected error occurred. Please try again.');
        });
    </script>
</body>
</html>