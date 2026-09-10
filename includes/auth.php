<?php
/**
 * Authentication & Session Management
 * Graphic Design Portfolio & CMS
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';

/**
 * Check if current user is logged in
 */
function is_logged_in(): bool {
    return !empty($_SESSION['admin_user_id']) && !empty($_SESSION['admin_logged_in']);
}

/**
 * Get current logged in user details
 */
function current_user(): ?array {
    if (!is_logged_in()) {
        return null;
    }

    static $user = null;
    if ($user === null) {
        $db = get_db();
        $stmt = $db->prepare("SELECT id, username, email, created_at FROM users WHERE id = :id");
        $stmt->execute([':id' => $_SESSION['admin_user_id']]);
        $user = $stmt->fetch() ?: null;
    }
    return $user;
}

/**
 * Require authentication or redirect to login page
 */
function require_auth(): void {
    if (!is_logged_in()) {
        flash('error', 'Please log in to access the admin dashboard.');
        header('Location: ' . base_url('admin/login.php'));
        exit;
    }
}

/**
 * Attempt login with username/password
 */
function attempt_login(string $username, string $password): bool {
    $db = get_db();
    $stmt = $db->prepare("SELECT id, username, password, email FROM users WHERE username = :u LIMIT 1");
    $stmt->execute([':u' => trim($username)]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Prevent session fixation
        if (!headers_sent()) {
            session_regenerate_id(true);
        }

        $_SESSION['admin_user_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_login_time'] = time();

        return true;
    }

    return false;
}

/**
 * Log out current session
 */
function logout_user(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    session_destroy();
}
