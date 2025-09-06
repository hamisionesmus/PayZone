<?php
// Check if user is already logged in BEFORE any output
require_once '../../config/config.php';
if (isLoggedIn()) {
    header('Location: ../dashboard/index.php');
    exit;
}

$pageTitle = 'Login';
require_once '../../includes/header.php';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        showError('Please enter both username and password');
    } else {
        // Make API call to backend
        $result = apiRequest('api/auth/login', 'POST', [
            'username' => $username,
            'password' => $password
        ]);

        if ($result['success']) {
            setAuthToken($result['data']['token']);
            $_SESSION['user'] = $result['data']['user'] ?? null;
            showSuccess('Login successful!');
            header('Location: ../dashboard/index.php');
            exit;
        } else {
            showError($result['data']['error'] ?? 'Login failed');
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
                                <i class="fas fa-building text-white fs-4"></i>
                            </div>
                            <h2 class="mt-3 mb-1"><?php echo APP_NAME; ?></h2>
                            <p class="text-muted">Sign in to your account</p>
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

                        <form method="POST" id="loginForm">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-2"></i>Username
                                </label>
                                <input type="text" class="form-control form-control-lg" id="username" name="username"
                                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                                <label class="form-check-label" for="rememberMe">
                                    Remember me
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3" id="loginBtn">
                                <span class="loading-spinner d-none me-2"></span>
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </button>
                        </form>

                        <div class="text-center">
                            <p class="mb-0">Don't have an account?
                                <a href="register.php" class="text-primary text-decoration-none">Sign up</a>
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
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
    });

    // Form submission
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        if (!username || !password) {
            window.payrollApp.showError('Please enter both username and password');
            return;
        }

        // Show loading state
        const resetLoading = window.payrollApp.showLoading(loginBtn);

        try {
            const response = await window.payrollApp.apiRequest('/api/auth/login', {
                method: 'POST',
                body: JSON.stringify({ username, password })
            });

            if (response.ok) {
                const data = await response.json();
                window.payrollApp.token = data.token;
                localStorage.setItem('auth_token', data.token);

                // Set cookie for PHP to read
                document.cookie = `auth_token=${data.token}; path=/; max-age=3600`;

                // Also set PHP session by making a server-side request
                fetch('../auth/set_session.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        token: data.token,
                        user: data.user
                    })
                }).then(() => {
                    window.payrollApp.showSuccess('Login successful! Redirecting...');
                    setTimeout(() => {
                        window.location.href = '../dashboard/index.php';
                    }, 1500);
                });
            } else {
                const error = await response.json();
                window.payrollApp.showError(error.error || 'Login failed');
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