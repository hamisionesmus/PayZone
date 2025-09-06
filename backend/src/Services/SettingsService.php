<?php

namespace App\Services;

use App\Config\Database;
use PDO;

class SettingsService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Get user profile settings
     */
    public function getUserProfile(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                u.id,
                u.username,
                u.email,
                u.role_id,
                u.is_active,
                u.created_at,
                r.name as role_name,
                e.first_name,
                e.last_name,
                e.phone,
                e.position,
                e.department
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            LEFT JOIN employees e ON u.id = e.user_id
            WHERE u.id = ?
        ");

        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user) {
            throw new \Exception('User not found');
        }

        return $user;
    }

    /**
     * Update user profile
     */
    public function updateUserProfile(int $userId, array $data): array
    {
        $this->pdo->beginTransaction();

        try {
            // Update user table
            if (isset($data['email'])) {
                $stmt = $this->pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
                $stmt->execute([$data['email'], $userId]);
            }

            // Update employee table if employee exists
            $stmt = $this->pdo->prepare("SELECT id FROM employees WHERE user_id = ?");
            $stmt->execute([$userId]);
            $employee = $stmt->fetch();

            if ($employee && isset($data['first_name'], $data['last_name'])) {
                $stmt = $this->pdo->prepare("
                    UPDATE employees
                    SET first_name = ?, last_name = ?, phone = ?, position = ?, department = ?
                    WHERE user_id = ?
                ");
                $stmt->execute([
                    $data['first_name'],
                    $data['last_name'],
                    $data['phone'] ?? null,
                    $data['position'] ?? null,
                    $data['department'] ?? null,
                    $userId
                ]);
            }

            $this->pdo->commit();
            return $this->getUserProfile($userId);

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Change user password
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        // Get current user
        $stmt = $this->pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user) {
            throw new \Exception('User not found');
        }

        // Verify current password
        if (!password_verify($currentPassword, $user['password_hash'])) {
            throw new \Exception('Current password is incorrect');
        }

        // Hash new password
        $newHash = password_hash($newPassword, PASSWORD_ARGON2I);

        // Update password
        $stmt = $this->pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        return $stmt->execute([$newHash, $userId]);
    }

    /**
     * Get system settings
     */
    public function getSystemSettings(): array
    {
        // For now, return default settings
        // In a real application, these would be stored in a settings table
        return [
            'company_name' => 'TechCorp',
            'company_email' => 'admin@techcorp.com',
            'company_phone' => '+1-234-567-8900',
            'timezone' => 'UTC',
            'date_format' => 'Y-m-d',
            'currency' => 'USD',
            'language' => 'en',
            'theme' => 'light',
            'email_notifications' => true,
            'auto_backup' => true,
            'session_timeout' => 3600,
            'max_login_attempts' => 5,
            'password_min_length' => 8,
            'two_factor_auth' => false
        ];
    }

    /**
     * Update system settings
     */
    public function updateSystemSettings(array $settings): array
    {
        // In a real application, save to database
        // For now, just return the updated settings
        $currentSettings = $this->getSystemSettings();

        foreach ($settings as $key => $value) {
            if (array_key_exists($key, $currentSettings)) {
                $currentSettings[$key] = $value;
            }
        }

        return $currentSettings;
    }

    /**
     * Get notification preferences
     */
    public function getNotificationPreferences(int $userId): array
    {
        // Default notification preferences
        return [
            'email_payroll_processed' => true,
            'email_leave_approved' => true,
            'email_leave_rejected' => true,
            'email_password_changed' => true,
            'browser_notifications' => true,
            'sms_alerts' => false,
            'weekly_reports' => true,
            'monthly_reports' => true
        ];
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(int $userId, array $preferences): array
    {
        // In a real application, save to database
        $currentPrefs = $this->getNotificationPreferences($userId);

        foreach ($preferences as $key => $value) {
            if (array_key_exists($key, $currentPrefs)) {
                $currentPrefs[$key] = (bool) $value;
            }
        }

        return $currentPrefs;
    }

    /**
     * Get security settings
     */
    public function getSecuritySettings(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                last_login,
                login_attempts,
                account_locked,
                two_factor_enabled,
                password_changed_at
            FROM users
            WHERE id = ?
        ");

        $stmt->execute([$userId]);
        $security = $stmt->fetch();

        return $security ?: [
            'last_login' => null,
            'login_attempts' => 0,
            'account_locked' => false,
            'two_factor_enabled' => false,
            'password_changed_at' => null
        ];
    }

    /**
     * Get audit logs for user
     */
    public function getUserAuditLogs(int $userId, int $limit = 50): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                action,
                table_name,
                created_at,
                old_values,
                new_values
            FROM audit_logs
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT ?
        ");

        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Clear user session
     */
    public function clearUserSessions(int $userId): bool
    {
        // In a real application, clear all user sessions
        // For now, just return true
        return true;
    }

    /**
     * Export user data
     */
    public function exportUserData(int $userId): array
    {
        $profile = $this->getUserProfile($userId);
        $auditLogs = $this->getUserAuditLogs($userId, 100);

        return [
            'profile' => $profile,
            'audit_logs' => $auditLogs,
            'export_date' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Delete user account
     */
    public function deleteUserAccount(int $userId): bool
    {
        $this->pdo->beginTransaction();

        try {
            // Delete associated employee record
            $stmt = $this->pdo->prepare("DELETE FROM employees WHERE user_id = ?");
            $stmt->execute([$userId]);

            // Delete user
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);

            $this->pdo->commit();
            return true;

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}