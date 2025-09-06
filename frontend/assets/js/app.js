// Payroll Management System - Custom JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the application
    initializeApp();

    // Set up global event listeners
    setupGlobalEvents();

    // Initialize theme
    initializeTheme();

    // Initialize tooltips and popovers
    initializeBootstrapComponents();
});

function initializeApp() {
    // Set up CSRF protection for AJAX requests
    setupCSRFProtection();

    // Initialize loading states
    setupLoadingStates();

    // Set up error handling
    setupErrorHandling();

    console.log('Payroll Management System initialized successfully!');
}

function setupGlobalEvents() {
    // Theme toggle
    const themeToggle = document.querySelector('.theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }

    // Mobile sidebar toggle
    const mobileSidebarToggle = document.querySelector('.mobile-sidebar-toggle');
    if (mobileSidebarToggle) {
        mobileSidebarToggle.addEventListener('click', toggleMobileSidebar);
    }

    // Search functionality
    const searchInputs = document.querySelectorAll('input[type="search"], input[placeholder*="search"], input[placeholder*="Search"]');
    searchInputs.forEach(input => {
        input.addEventListener('input', debounce(handleSearch, 300));
    });

    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
}

function initializeTheme() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);
}

function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';

    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateThemeIcon(newTheme);

    // Show success message
    showToast('Theme switched to ' + newTheme + ' mode', 'success');
}

function updateThemeIcon(theme) {
    const themeIcon = document.querySelector('.theme-toggle i');
    if (themeIcon) {
        themeIcon.className = theme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
    }
}

function toggleMobileSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (sidebar) {
        sidebar.classList.toggle('show');
    }

    if (overlay) {
        overlay.classList.toggle('show');
    }
}


function setupCSRFProtection() {
    // Add CSRF token to all AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
            }
        });
    }
}

function setupLoadingStates() {
    // Add loading class to body during AJAX requests
    $(document).ajaxStart(function() {
        $('body').addClass('loading');
    });

    $(document).ajaxStop(function() {
        $('body').removeClass('loading');
    });
}

function setupErrorHandling() {
    // Global AJAX error handler
    $(document).ajaxError(function(event, xhr, settings, thrownError) {
        console.error('AJAX Error:', thrownError);

        if (xhr.status === 401) {
            // Unauthorized - redirect to login
            window.location.href = '../auth/login.php';
        } else if (xhr.status === 403) {
            // Forbidden
            showToast('You do not have permission to perform this action', 'error');
        } else if (xhr.status >= 500) {
            // Server error
            showToast('Server error occurred. Please try again later.', 'error');
        }
    });

    // Handle unhandled promise rejections
    window.addEventListener('unhandledrejection', function(event) {
        console.error('Unhandled promise rejection:', event.reason);
        showToast('An unexpected error occurred', 'error');
    });
}

function initializeBootstrapComponents() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

// Utility Functions

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function handleSearch(event) {
    const searchTerm = event.target.value.toLowerCase();
    const searchContainer = event.target.closest('.search-container') || event.target.closest('.card') || event.target.closest('.table-responsive');

    if (searchContainer) {
        const searchableItems = searchContainer.querySelectorAll('.searchable-item, tbody tr, .card');

        searchableItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }
}

function showToast(message, type = 'info', duration = 3000) {
    // Use SweetAlert2 for consistent notifications
    const toastType = type === 'error' ? 'error' : type === 'success' ? 'success' : 'info';

    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: duration,
        timerProgressBar: true,
        icon: toastType,
        title: message,
        background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1f2937' : '#ffffff',
        color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#e5e7eb' : '#1f2937'
    });
}

function showLoading(button) {
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="loading-spinner me-2"></span>Loading...';
    button.disabled = true;

    return () => {
        button.innerHTML = originalText;
        button.disabled = false;
    };
}

function formatCurrency(amount) {
    return 'KSH ' + new Intl.NumberFormat('en-KE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function confirmAction(message = 'Are you sure you want to proceed?') {
    return Swal.fire({
        title: 'Are you sure?',
        text: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, proceed',
        cancelButtonText: 'Cancel'
    });
}

function showSuccess(message) {
    showToast(message, 'success');
}

function showError(message) {
    showToast(message, 'error');
}

function showInfo(message) {
    showToast(message, 'info');
}

// Form validation helpers
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^[\+]?[1-9][\d]{0,15}$/;
    return re.test(phone.replace(/[\s\-\(\)]/g, ''));
}

function validateRequired(value) {
    return value && value.trim().length > 0;
}

// Local storage helpers
function setLocalStorage(key, value) {
    try {
        localStorage.setItem(key, JSON.stringify(value));
    } catch (e) {
        console.error('Error saving to localStorage:', e);
    }
}

function getLocalStorage(key, defaultValue = null) {
    try {
        const item = localStorage.getItem(key);
        return item ? JSON.parse(item) : defaultValue;
    } catch (e) {
        console.error('Error reading from localStorage:', e);
        return defaultValue;
    }
}

function removeLocalStorage(key) {
    try {
        localStorage.removeItem(key);
    } catch (e) {
        console.error('Error removing from localStorage:', e);
    }
}

function logout() {
    // Clear localStorage
    localStorage.removeItem('auth_token');

    // Clear cookie
    document.cookie = 'auth_token=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';

    // Redirect to logout URL (which will clear PHP session)
    window.location.href = '../auth/logout.php';
}

// Export functions for global use
window.payrollApp = window.payrollApp || {};
window.payrollApp.showToast = showToast;
window.payrollApp.showLoading = showLoading;
window.payrollApp.formatCurrency = formatCurrency;
window.payrollApp.formatDate = formatDate;
window.payrollApp.formatDateTime = formatDateTime;
window.payrollApp.confirmAction = confirmAction;
window.payrollApp.showSuccess = showSuccess;
window.payrollApp.showError = showError;
window.payrollApp.showInfo = showInfo;
window.payrollApp.validateEmail = validateEmail;
window.payrollApp.validatePhone = validatePhone;
window.payrollApp.validateRequired = validateRequired;
window.payrollApp.setLocalStorage = setLocalStorage;
window.payrollApp.getLocalStorage = getLocalStorage;
window.payrollApp.removeLocalStorage = removeLocalStorage;
window.payrollApp.logout = logout;