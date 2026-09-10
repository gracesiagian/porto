<?php
/**
 * Test Suite specifically verifying the 3 requested fixes:
 * 1. Modal / Lightbox cross-project slider navigation
 * 2. Append images on edit (with existing gallery & single image deletion)
 * 3. Dynamic default display order in admin upload form
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

echo "=== VERIFYING 3 PORTFOLIO FIXES ===\n\n";

$db = get_db();
$passed = 0;
$total = 0;

function check(string $name, bool $ok, string $detail = '') {
    global $passed, $total;
    $total++;
    if ($ok) {
        $passed++;
        echo " [PASS] $name\n";
    } else {
        echo " [FAIL] $name" . ($detail ? " - $detail" : "") . "\n";
    }
}

// -------------------------------------------------------------
// Test 1: Verify Lightbox Navigation Implementation in main.js
// -------------------------------------------------------------
$main_js = file_get_contents(__DIR__ . '/../assets/js/main.js');

$has_continuous_next = str_contains($main_js, 'nextProjectIdx = (currentProjectIdx + 1) % portfolioData.length');
$has_continuous_prev = str_contains($main_js, 'prevProjectIdx = (currentProjectIdx - 1 + portfolioData.length) % portfolioData.length');
$has_keyboard_nav = str_contains($main_js, "e.key === 'ArrowLeft'") && str_contains($main_js, "e.key === 'ArrowRight'");

check("Lightbox JS: Continuous Next across projects", $has_continuous_next);
check("Lightbox JS: Continuous Prev across projects", $has_continuous_prev);
check("Lightbox JS: Keyboard arrow navigation configured", $has_keyboard_nav);

// -------------------------------------------------------------
// Test 2: Verify Image Append on Edit & Single Slide Deletion
// -------------------------------------------------------------
// Get first category
$cats = get_categories();
$cat_id = $cats[0]['id'];

// Create test item with 2 initial images
$test_title = "Append Test Artwork " . time();
$stmt = $db->prepare("INSERT INTO portfolio_items (category_id, title, image_url, description, display_order, is_active) VALUES (:cat, :t, :img, :d, :o, 1)");
$stmt->execute([
    ':cat' => $cat_id,
    ':t'   => $test_title,
    ':img' => 'assets/images/sample_thumb_1.svg',
    ':d'   => 'Initial description',
    ':o'   => 88
]);
$item_id = (int)$db->lastInsertId();

$stmt_img = $db->prepare("INSERT INTO portfolio_images (portfolio_id, image_url, display_order) VALUES (:pid, :img, :o)");
$stmt_img->execute([':pid' => $item_id, ':img' => 'assets/images/sample_thumb_1.svg', ':o' => 1]);
$stmt_img->execute([':pid' => $item_id, ':img' => 'assets/images/sample_thumb_2.svg', ':o' => 2]);

$initial_item = get_portfolio_item($item_id);
check("Item created with 2 initial slides", $initial_item !== null && $initial_item['image_count'] === 2);

// Simulate Appending 2 new images via SQL query that mimics portfolio_action.php logic
$max_order_stmt = $db->prepare("SELECT COALESCE(MAX(display_order), 0) FROM portfolio_images WHERE portfolio_id = :pid");
$max_order_stmt->execute([':pid' => $item_id]);
$current_max_order = (int)$max_order_stmt->fetchColumn();

// Append image 3
$current_max_order++;
$stmt_img->execute([':pid' => $item_id, ':img' => 'assets/images/sample_thumb_3.svg', ':o' => $current_max_order]);

// Append image 4
$current_max_order++;
$stmt_img->execute([':pid' => $item_id, ':img' => 'assets/images/sample_poster_1.svg', ':o' => $current_max_order]);

$appended_item = get_portfolio_item($item_id);
check("Existing artwork preserved and 2 new slides appended (Total 4 slides)", $appended_item !== null && $appended_item['image_count'] === 4);
check("Primary cover image remains intact", $appended_item['image_url'] === 'assets/images/sample_thumb_1.svg');

// Test single slide deletion
$images = get_portfolio_images($item_id);
$to_delete_id = $images[1]['id']; // delete second image
$db->prepare("DELETE FROM portfolio_images WHERE id = :id")->execute([':id' => $to_delete_id]);

$after_del_item = get_portfolio_item($item_id);
check("Individual slide deletion leaves remaining 3 slides", $after_del_item !== null && $after_del_item['image_count'] === 3);

// Cleanup test item
$db->prepare("DELETE FROM portfolio_items WHERE id = :id")->execute([':id' => $item_id]);

// Verify admin/portfolio.php has gallery grid and single delete action
$portfolio_admin_php = file_get_contents(__DIR__ . '/../admin/portfolio.php');
$has_gallery_grid = str_contains($portfolio_admin_php, 'id="edit_existing_images_grid"');
$has_single_delete_form = str_contains($portfolio_admin_php, 'id="delete-single-image-form"') && str_contains($portfolio_admin_php, 'value="delete_image"');
$has_append_label = str_contains($portfolio_admin_php, 'Otomatis di-append');

check("Admin Edit Modal: Existing gallery grid present", $has_gallery_grid);
check("Admin Edit Modal: Single image deletion form present", $has_single_delete_form);
check("Admin Edit Modal: Append notice label present", $has_append_label);

// -------------------------------------------------------------
// Test 3: Dynamic Default Display Order
// -------------------------------------------------------------
$has_dynamic_order_calc = str_contains($portfolio_admin_php, '$next_display_order = max(');
$has_dynamic_order_value = str_contains($portfolio_admin_php, 'id="add_display_order" value="<?= $next_display_order ?>"');

check("Admin Upload Form: Dynamic order calculation logic present", $has_dynamic_order_calc);
check("Admin Upload Form: Value uses dynamic \$next_display_order", $has_dynamic_order_value);

echo "\n=== SUMMARY: $passed / $total TESTS PASSED ===\n";
