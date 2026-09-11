<?php
/**
 * Automated Verification Suite for Portfolio & Admin CMS
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

echo "=== STARTING AUTOMATED TEST SUITE ===\n\n";

$tests_passed = 0;
$tests_total = 0;

function assert_test(string $name, bool $condition, string $details = '') {
    global $tests_passed, $tests_total;
    $tests_total++;
    if ($condition) {
        $tests_passed++;
        echo " [PASS] $name\n";
    } else {
        echo " [FAIL] $name" . ($details ? " - $details" : "") . "\n";
    }
}

// 1. Test Database Connection
try {
    $db = get_db();
    assert_test("Database PDO Connection", $db instanceof PDO);
} catch (Exception $e) {
    assert_test("Database PDO Connection", false, $e->getMessage());
}

// 2. Test Tables Existence
$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
assert_test("Database Tables Existence", in_array('users', $tables) && in_array('site_settings', $tables) && in_array('categories', $tables) && in_array('portfolio_items', $tables));

// 3. Test Site Settings Retrieval
$site_title = get_setting('site_title');
$designer_name = get_setting('designer_name');
assert_test("Site Settings Retrieval", !empty($site_title) && !empty($designer_name), "Title: $site_title, Designer: $designer_name");

// 4. Test Categories Retrieval
$categories = get_categories();
assert_test("Categories Count (>0)", count($categories) >= 5, "Found " . count($categories) . " categories");

// 5. Test Portfolio Items Retrieval
$items = get_portfolio_items();
assert_test("Public Active Portfolio Items (>0)", count($items) >= 10, "Found " . count($items) . " active items");

// 6. Test Admin Authentication Verification
$db->prepare("UPDATE users SET password = :p WHERE username = 'admin'")->execute([':p' => password_hash('admin123', PASSWORD_BCRYPT)]);

$login_success = attempt_login('admin', 'admin123');
assert_test("Admin Login Authentication (admin/admin123)", $login_success === true);

$user = current_user();
assert_test("Current User Session Retrieval", $user !== null && $user['username'] === 'admin');

// 7. Test Portfolio Item & Multi-Image CRUD Operations
// Create Item
$test_title = "Automated Test Design Multi-Slide " . time();
$stmt = $db->prepare("INSERT INTO portfolio_items (category_id, title, image_url, description, client_name, tools_used, display_order, is_active) 
    VALUES (:cat, :title, :img, :desc, :client, :tools, :order, 1)");
$stmt->execute([
    ':cat' => $categories[0]['id'],
    ':title' => $test_title,
    ':img' => 'assets/images/sample_thumb_1.svg',
    ':desc' => 'Test item description for automated verification',
    ':client' => 'Automated Test Client',
    ':tools' => 'PHPUnit, PDO',
    ':order' => 99
]);
$test_id = (int)$db->lastInsertId();
assert_test("Create Portfolio Item via DB", $test_id > 0, "Created ID: $test_id");

// Insert multiple images for this item
$stmt_img = $db->prepare("INSERT INTO portfolio_images (portfolio_id, image_url, display_order) VALUES (:pid, :img, :order)");
$stmt_img->execute([':pid' => $test_id, ':img' => 'assets/images/sample_thumb_1.svg', ':order' => 1]);
$stmt_img->execute([':pid' => $test_id, ':img' => 'assets/images/sample_thumb_2.svg', ':order' => 2]);
$stmt_img->execute([':pid' => $test_id, ':img' => 'assets/images/sample_thumb_3.svg', ':order' => 3]);

// Read with gallery images
$inserted_item = get_portfolio_item($test_id);
assert_test("Read Created Portfolio Item with Multi-Images", $inserted_item !== null && $inserted_item['title'] === $test_title && $inserted_item['image_count'] === 3, "Image count: " . ($inserted_item['image_count'] ?? 0));

// Check get_portfolio_items batch retrieval has multi-images attached
$all_retrieved = get_portfolio_items(null, false);
$found_test_item = null;
foreach ($all_retrieved as $it) {
    if ($it['id'] === $test_id) {
        $found_test_item = $it;
        break;
    }
}
assert_test("Batch Retrieval has Multi-Images Attached", $found_test_item !== null && $found_test_item['image_count'] === 3);

// Delete single image
$test_images = get_portfolio_images($test_id);
$img_to_del = $test_images[1]['id'];
$db->prepare("DELETE FROM portfolio_images WHERE id = :id")->execute([':id' => $img_to_del]);
$updated_images = get_portfolio_images($test_id);
assert_test("Delete Single Image from Gallery", count($updated_images) === 2);

// Delete parent item and verify cascading delete of remaining images
$stmt = $db->prepare("DELETE FROM portfolio_items WHERE id = :id");
$stmt->execute([':id' => $test_id]);
$deleted_item = get_portfolio_item($test_id);
$orphan_images = $db->prepare("SELECT COUNT(*) FROM portfolio_images WHERE portfolio_id = :id");
$orphan_images->execute([':id' => $test_id]);
$orphan_count = (int)$orphan_images->fetchColumn();
assert_test("Cascading Delete of Portfolio Images", $deleted_item === null && $orphan_count === 0, "Remaining images: $orphan_count");

// 8. Test HTTP Endpoints
$test_urls = ["http://localhost/porto/", "http://127.0.0.1:8000/"];
$base_http = "http://localhost/porto";

$ch = curl_init("http://localhost/porto/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$html_home = curl_exec($ch);
$http_code_home = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code_home !== 200) {
    $ch = curl_init("http://127.0.0.1:8000/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $html_home = curl_exec($ch);
    $http_code_home = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $base_http = "http://127.0.0.1:8000";
}

assert_test("HTTP Home Page 200 OK", $http_code_home === 200);
if ($html_home) {
    assert_test("Home Page Carrd Profile Card Rendered", str_contains((string)$html_home, 'carrd-container'));
}

// Test Dedicated Portfolio Page (portfolio.php)
$ch = curl_init($base_http . "/portfolio.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$html_portfolio = curl_exec($ch);
$http_code_portfolio = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

assert_test("HTTP Portfolio Page 200 OK", $http_code_portfolio === 200);
if ($html_portfolio) {
    assert_test("Portfolio Page Gallery Grid Rendered", str_contains((string)$html_portfolio, 'portfolio-gallery') || str_contains((string)$html_portfolio, 'portfolio-grid'));
    assert_test("Portfolio Page Modal / Lightbox Rendered", str_contains((string)$html_portfolio, 'portfolio-modal') || str_contains((string)$html_portfolio, 'artwork-lightbox'));
    assert_test("Portfolio Page Filter Tabs Rendered", str_contains((string)$html_portfolio, 'filter-btn'));
    assert_test("Portfolio Page Onclick Handler Attached", str_contains((string)$html_portfolio, 'openPortfolioModal'));
}

$ch = curl_init($base_http . "/admin/login.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$html_login = curl_exec($ch);
$http_code_login = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

assert_test("HTTP Admin Login Page 200 OK", $http_code_login === 200);
if ($html_login) {
    assert_test("Admin Login Form & CSRF Rendered", str_contains((string)$html_login, 'csrf_token') && str_contains((string)$html_login, 'username') && str_contains((string)$html_login, 'password'));
}

echo "\n=== TEST RESULTS: $tests_passed / $tests_total PASSED ===\n";
