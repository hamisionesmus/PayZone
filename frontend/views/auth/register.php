<?php
$pageTitle = 'Register';
require_once '../../includes/header.php';

// Check if user is already logged in
if (isLoggedIn()) {
    header('Location: ../dashboard/index.php');
    exit;
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $role_id = (int)($_POST['role_id'] ?? 3);

    if (empty($username) || empty($password) || empty($email)) {
        showError('Please fill in all required fields');
    } elseif (strlen($password) < 6) {
        showError('Password must be at least 6 characters long');
    } else {
        // Make API call to backend
        $result = apiRequest('api/auth/register', 'POST', [
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'role_id' => $role_id,
            'company_id' => 1
        ]);

        if ($result['success']) {
            showSuccess('Registration successful! Please login.');
            header('Location: login.php');
            exit;
        } else {
            showError($result['data']['error'] ?? 'Registration failed');
        }
    }
}
?>

<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-user-plus text-white fs-4"></i>
                            </div>
                            <h2 class="mt-3 mb-1">Create Account</h2>
                            <p class="text-muted">Join the Payroll Management System</p>
                        </div>

                        <?php
                        $error = getError();
                        if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php
                        $success = getSuccess();
                        if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="registerForm">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-2"></i>Username *
                                </label>
                                <input type="text" class="form-control form-control-lg" id="username" name="username"
                                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email *
                                </label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email"
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password *
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">
                                    <i class="fas fa-user-tag me-2"></i>Role
                                </label>
                                <select class="form-select form-select-lg" id="role" name="role_id">
                                    <option value="3">Employee</option>
                                    <option value="2">HR Manager</option>
                                    <option value="1">Administrator</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3" id="registerBtn">
                                <span class="loading-spinner d-none me-2"></span>
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </button>
                        </form>

                        <div class="text-center">
                            <p class="mb-0">Already have an account?
                                <a href="login.php" class="text-primary text-decoration-none">Sign in</a>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <small class="text-muted">
                        <?php echo APP_NAME; ?> v<?php echo APP_VERSION; ?> | <?php echo COMPANY_NAME; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const registerBtn = document.getElementById('registerBtn');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
    });

    // Form submission
    registerForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const username = document.getElementById('username').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        if (!username || !email || !password) {
            window.payrollApp.showError('Please fill in all required fields');
            return;
        }

        if (password.length < 6) {
            window.payrollApp.showError('Password must be at least 6 characters long');
            return;
        }

        // Show loading state
        const resetLoading = window.payrollApp.showLoading(registerBtn);

        try {
            const response = await window.payrollApp.apiRequest('/api/auth/register', {
                method: 'POST',
                body: JSON.stringify({
                    username,
                    email,
                    password,
                    role_id: parseInt(document.getElementById('role').value),
                    company_id: 1
                })
            });

            if (response.ok) {
                const data = await response.json();
                window.payrollApp.showSuccess('Registration successful! Redirecting to login...');
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 2000);
            } else {
                const error = await response.json();
                window.payrollApp.showError(error.error || 'Registration failed');
            }
        } catch (error) {
            window.payrollApp.showError('Network error. Please try again.');
        } finally {
            resetLoading();
        }
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>