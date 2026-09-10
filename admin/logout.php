<?php
/**
 * Admin Logout Handler
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

logout_user();
flash('info', 'You have been successfully logged out.');
header('Location: ' . base_url('admin/login.php'));
exit;
