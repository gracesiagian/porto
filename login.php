<?php
/**
 * Root Login Redirect
 * Redirects to the canonical admin login page in /admin/login.php
 */

declare(strict_types=1);

require_once __DIR__ . '/config/helpers.php';

header('Location: ' . base_url('admin/login.php'));
exit;
