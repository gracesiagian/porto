<?php
/**
 * Global Helpers, Security & Utilities
 * Graphic Design Portfolio & CMS
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax'
    ]);
}

require_once __DIR__ . '/database.php';

/**
 * Detect if the request is running over HTTPS (including behind Reverse Proxies like Railway, Cloudflare, Heroku, Nginx)
 */
function is_https(): bool {
    if (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off') {
        return true;
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
        return true;
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_SSL']) === 'on') {
        return true;
    }
    if (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower((string)$_SERVER['HTTP_FRONT_END_HTTPS']) === 'on') {
        return true;
    }
    if (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) {
        return true;
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_PORT']) && (int)$_SERVER['HTTP_X_FORWARDED_PORT'] === 443) {
        return true;
    }
    if (!empty($_SERVER['REQUEST_SCHEME']) && strtolower((string)$_SERVER['REQUEST_SCHEME']) === 'https') {
        return true;
    }
    if (!empty($_SERVER['HTTP_CF_VISITOR'])) {
        $cf = json_decode((string)$_SERVER['HTTP_CF_VISITOR'], true);
        if (isset($cf['scheme']) && strtolower((string)$cf['scheme']) === 'https') {
            return true;
        }
    }
    return false;
}

/**
 * Returns the root-relative base path of the application (e.g. '' or '/porto').
 * Useful for building root-relative asset and page URLs that never suffer from mixed content.
 */
function base_path(string $path = ''): string {
    $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $base_dir = preg_replace('#/(admin|includes|config).*$#', '', $script_dir);
    $base_dir = trim((string)$base_dir, '/');
    
    $prefix = $base_dir !== '' ? '/' . $base_dir : '';
    
    if ($path === '') {
        return $prefix !== '' ? $prefix : '/';
    }
    return $prefix . '/' . ltrim($path, '/');
}

/**
 * Returns Full Base URL of the application with proper HTTPS detection
 */
function base_url(string $path = ''): string {
    $protocol = is_https() ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $base_dir = preg_replace('#/(admin|includes|config).*$#', '', $script_dir);
    $base_dir = trim((string)$base_dir, '/');
    
    $url = $protocol . $host . ($base_dir !== '' ? '/' . $base_dir : '');
    if ($path !== '') {
        $url .= '/' . ltrim($path, '/');
    }
    return $url;
}

/**
 * Asset URL helper - returns root-relative path (e.g. /assets/css/custom.css or /porto/assets/css/custom.css)
 */
function asset_url(string $path = ''): string {
    return base_path('assets/' . ltrim($path, '/'));
}

/**
 * Upload URL helper - returns root-relative path (e.g. /uploads/portfolio/... or /assets/images/avatar.svg)
 */
function upload_url(string $path = ''): string {
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        if (is_https() && str_starts_with($path, 'http://')) {
            $path = 'https://' . substr($path, 7);
        }
        return $path;
    }
    return base_path(ltrim($path, '/'));
}

/**
 * Sanitize string for HTML output
 */
function e(?string $value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Generate or get CSRF token
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verify_csrf(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Set flash message
 */
function flash(string $type, string $message): void {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

/**
 * Get and clear flash messages
 */
function get_flashes(): array {
    $flashes = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $flashes;
}

/**
 * Render Flash Messages UI
 */
function render_flashes(): string {
    $flashes = get_flashes();
    if (empty($flashes)) {
        return '';
    }

    $html = '<div class="space-y-3 mb-6" id="flash-container">';
    foreach ($flashes as $f) {
        $bg = match($f['type']) {
            'success' => 'bg-emerald-50 text-emerald-900 border-emerald-200',
            'error'   => 'bg-rose-50 text-rose-900 border-rose-200',
            'warning' => 'bg-amber-50 text-amber-900 border-amber-200',
            default   => 'bg-blue-50 text-blue-900 border-blue-200'
        };
        $icon = match($f['type']) {
            'success' => '<i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>',
            'error'   => '<i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600 flex-shrink-0"></i>',
            'warning' => '<i data-lucide="alert-circle" class="w-5 h-5 text-amber-600 flex-shrink-0"></i>',
            default   => '<i data-lucide="info" class="w-5 h-5 text-blue-600 flex-shrink-0"></i>'
        };

        $html .= '<div class="flex items-center justify-between p-4 rounded-xl border ' . $bg . ' shadow-xs transition-all duration-300">
            <div class="flex items-center space-x-3">
                ' . $icon . '
                <p class="text-sm font-medium">' . e($f['message']) . '</p>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>';
    }
    $html .= '</div>';
    return $html;
}

/**
 * Get all site settings from database with caching
 */
function get_all_settings(): array {
    static $settings = null;
    if ($settings === null) {
        $db = get_db();
        $stmt = $db->query("SELECT setting_key, setting_value FROM site_settings");
        $rows = $stmt->fetchAll();
        $settings = [];
        foreach ($rows as $r) {
            $settings[$r['setting_key']] = $r['setting_value'];
        }
    }
    return $settings;
}

/**
 * Get single site setting
 */
function get_setting(string $key, string $default = ''): string {
    $settings = get_all_settings();
    return $settings[$key] ?? $default;
}

/**
 * Update single site setting
 */
function update_setting(string $key, string $value): bool {
    $db = get_db();
    $stmt = $db->prepare("INSERT INTO site_settings (setting_key, setting_value) 
        VALUES (:k, :v) 
        ON DUPLICATE KEY UPDATE setting_value = :v2");
    return $stmt->execute([':k' => $key, ':v' => $value, ':v2' => $value]);
}

/**
 * Get all categories
 */
function get_categories(): array {
    $db = get_db();
    $stmt = $db->query("SELECT c.*, COUNT(p.id) as item_count 
        FROM categories c 
        LEFT JOIN portfolio_items p ON c.id = p.category_id 
        GROUP BY c.id 
        ORDER BY c.display_order ASC, c.name ASC");
    return $stmt->fetchAll();
}

/**
 * Get all images for a specific portfolio item
 */
function get_portfolio_images(int $portfolio_id): array {
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM portfolio_images WHERE portfolio_id = :pid ORDER BY display_order ASC, id ASC");
    $stmt->execute([':pid' => $portfolio_id]);
    return $stmt->fetchAll();
}

/**
 * Get portfolio items with all gallery images attached
 */
function get_portfolio_items(?int $category_id = null, bool $only_active = true): array {
    $db = get_db();
    $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM portfolio_items p 
            INNER JOIN categories c ON p.category_id = c.id";
    
    $conditions = [];
    $params = [];

    if ($only_active) {
        $conditions[] = "p.is_active = 1";
    }

    if ($category_id !== null && $category_id > 0) {
        $conditions[] = "p.category_id = :cat_id";
        $params[':cat_id'] = $category_id;
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY p.display_order ASC, p.created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $items = $stmt->fetchAll();

    if (empty($items)) {
        return [];
    }

    // Collect item IDs to fetch all associated gallery images in one batch
    $item_ids = array_column($items, 'id');
    $placeholders = implode(',', array_fill(0, count($item_ids), '?'));
    
    $img_stmt = $db->prepare("SELECT * FROM portfolio_images WHERE portfolio_id IN ($placeholders) ORDER BY display_order ASC, id ASC");
    $img_stmt->execute($item_ids);
    $all_images = $img_stmt->fetchAll();

    $images_by_item = [];
    foreach ($all_images as $img) {
        $images_by_item[$img['portfolio_id']][] = $img;
    }

    // Attach images to each portfolio item
    foreach ($items as &$item) {
        $pid = $item['id'];
        if (!empty($images_by_item[$pid])) {
            $item['images'] = $images_by_item[$pid];
            $item['image_urls'] = array_column($images_by_item[$pid], 'image_url');
        } elseif (!empty($item['image_url'])) {
            $item['images'] = [
                ['id' => 0, 'portfolio_id' => $pid, 'image_url' => $item['image_url'], 'display_order' => 1]
            ];
            $item['image_urls'] = [$item['image_url']];
        } else {
            $item['images'] = [];
            $item['image_urls'] = [];
        }
        $item['image_count'] = count($item['images']);
    }
    unset($item);

    return $items;
}

/**
 * Get single portfolio item by ID with its gallery images
 */
function get_portfolio_item(int $id): ?array {
    $db = get_db();
    $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM portfolio_items p 
        INNER JOIN categories c ON p.category_id = c.id 
        WHERE p.id = :id");
    $stmt->execute([':id' => $id]);
    $item = $stmt->fetch();
    
    if (!$item) {
        return null;
    }

    $item['images'] = get_portfolio_images($id);
    if (empty($item['images']) && !empty($item['image_url'])) {
        $item['images'] = [
            ['id' => 0, 'portfolio_id' => $id, 'image_url' => $item['image_url'], 'display_order' => 1]
        ];
    }
    $item['image_urls'] = !empty($item['images']) ? array_column($item['images'], 'image_url') : [];
    $item['image_count'] = count($item['images']);

    return $item;
}

/**
 * Secure file upload handler
 * @return array ['success' => bool, 'path' => string, 'error' => string]
 */
function handle_file_upload(array $file, string $subfolder = 'portfolio', int $max_mb = 10): array {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'error' => 'Invalid file upload parameters.'];
    }

    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'error' => 'No file uploaded.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload error code: ' . $file['error']];
    }

    if ($file['size'] > ($max_mb * 1024 * 1024)) {
        return ['success' => false, 'error' => "File size exceeds limit of {$max_mb}MB."];
    }

    $allowed_mime = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg'
    ];

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    if (!array_key_exists($mime, $allowed_mime)) {
        return ['success' => false, 'error' => 'Invalid file format. Only JPG, PNG, WEBP, and SVG are accepted.'];
    }

    $ext = $allowed_mime[$mime];
    $filename = sprintf('%s_%s.%s', uniqid('art_', true), bin2hex(random_bytes(4)), $ext);

    // Root uploads folder
    $root_dir = realpath(__DIR__ . '/..') ?: dirname(__DIR__);
    $target_dir = $root_dir . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $subfolder;

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $target_file = $target_dir . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['success' => false, 'error' => 'Failed to save uploaded file to destination.'];
    }

    // Relative web path
    $relative_path = 'uploads/' . trim($subfolder, '/') . '/' . $filename;
    return ['success' => true, 'path' => $relative_path];
}
