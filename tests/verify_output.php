<?php
/**
 * Detailed Verification of Multi-Image System
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';

echo "=== VERIFYING MULTI-IMAGE DATA & ATTRIBUTES ===\n\n";

$items = get_portfolio_items(null, false);
echo "Total portfolio items: " . count($items) . "\n\n";

foreach ($items as $item) {
    $img_count = $item['image_count'];
    $is_multi = $img_count > 1 ? " [MULTI-SLIDE]" : "";
    echo sprintf("ID %2d | %-45s | %d gambar%s\n", $item['id'], substr($item['title'], 0, 45), $img_count, $is_multi);
    if ($img_count > 1) {
        foreach ($item['images'] as $idx => $img) {
            echo "       └─ Slide #" . ($idx + 1) . ": " . $img['image_url'] . "\n";
        }
    }
}

echo "\nChecking Public Homepage HTTP & Rendered JSON attributes...\n";
$ch = curl_init("http://localhost/porto/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$html = curl_exec($ch);
curl_close($ch);

if ($html) {
    if (str_contains($html, 'data-images=') && str_contains($html, 'data-image-count=')) {
        echo "[PASS] data-images and data-image-count attributes present on public cards.\n";
    } else {
        echo "[FAIL] data-images or data-image-count missing in HTML output.\n";
    }

    if (str_contains($html, 'id="lightbox-slide-counter"')) {
        echo "[PASS] Lightbox slide counter markup present.\n";
    } else {
        echo "[FAIL] Lightbox slide counter markup missing.\n";
    }

    if (str_contains($html, 'id="lightbox-dots-wrap"')) {
        echo "[PASS] Lightbox dots wrapper present.\n";
    } else {
        echo "[FAIL] Lightbox dots wrapper missing.\n";
    }

    if (str_contains($html, 'id="lightbox-thumbnails-grid"')) {
        echo "[PASS] Lightbox sidebar thumbnails grid present.\n";
    } else {
        echo "[FAIL] Lightbox sidebar thumbnails grid missing.\n";
    }
} else {
    echo "[WARN] Could not connect to localhost/porto/ via curl.\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";
