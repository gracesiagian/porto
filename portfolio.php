<?php
/**
 * Portfolio Management (CRUD) - Root Redirection or Admin View
 * Graphic Design Portfolio & CMS
 */

declare(strict_types=1);

require_once __DIR__ . '/config/helpers.php';
header('Location: ' . base_url('admin/portfolio.php'));
exit;
