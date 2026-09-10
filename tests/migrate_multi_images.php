<?php
/**
 * Database Migration: Portfolio Multiple Images Support
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

$db = get_db();

// 1. Create table if not exists
$sql_table = "CREATE TABLE IF NOT EXISTS portfolio_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    portfolio_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (portfolio_id) REFERENCES portfolio_items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$db->exec($sql_table);
echo "[OK] Table portfolio_images verified / created.\n";

// 2. Backfill existing items that have an image_url but no entries in portfolio_images
$stmt = $db->query("SELECT id, image_url FROM portfolio_items WHERE id NOT IN (SELECT DISTINCT portfolio_id FROM portfolio_images) AND image_url != ''");
$items_to_migrate = $stmt->fetchAll();

$inserted = 0;
$insert_stmt = $db->prepare("INSERT INTO portfolio_images (portfolio_id, image_url, display_order) VALUES (:pid, :img, 1)");

foreach ($items_to_migrate as $item) {
    $insert_stmt->execute([
        ':pid' => $item['id'],
        ':img' => $item['image_url']
    ]);
    $inserted++;
}

echo "[OK] Backfilled {$inserted} portfolio items into portfolio_images table.\n";

// 3. Verify total images
$total_images = $db->query("SELECT COUNT(*) FROM portfolio_images")->fetchColumn();
echo "[OK] Total records in portfolio_images: {$total_images}\n";
