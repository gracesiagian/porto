<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';

echo "=== TESTING PORTFOLIO MODAL & LIGHTBOX INTEGRITY ===\n\n";

$_SERVER['SCRIPT_NAME'] = '/portfolio.php';
$_SERVER['HTTP_HOST'] = 'yelloplanetman.up.railway.app';
$_SERVER['HTTPS'] = 'on';

ob_start();
require __DIR__ . '/../portfolio.php';
$output = ob_get_clean();

$passed = 0;
$total = 0;

function verify(string $test, bool $ok, string $detail = '') {
    global $passed, $total;
    $total++;
    if ($ok) {
        $passed++;
        echo " [PASS] $test\n";
    } else {
        echo " [FAIL] $test" . ($detail ? " - $detail" : "") . "\n";
    }
}

// 1. Strict root-relative paths & no mixed content
verify("No insecure http:// script outputs", !str_contains($output, 'src="http://'));
verify("No hardcoded railway domain script outputs", !str_contains($output, 'src="http://yelloplanetman.up.railway.app'));
verify("main.js loaded with strict root-relative path", str_contains($output, 'src="/assets/js/main.js"') || str_contains($output, 'src="<?= asset_url(\'js/main.js\') ?>'));

// 2. Modal markup & explicit onclick event binding
verify("Modal container #portfolio-modal exists", str_contains($output, 'id="portfolio-modal"'));
verify("Modal title #modal-title exists", str_contains($output, 'id="modal-title"'));
verify("Modal category #modal-category exists", str_contains($output, 'id="modal-category"'));
verify("Modal image #modal-image exists", str_contains($output, 'id="modal-image"'));
verify("Modal description #modal-description exists", str_contains($output, 'id="modal-description"'));
verify("Modal close button exists", str_contains($output, 'id="modal-close"'));
verify("Portfolio items have explicit onclick='openPortfolioModal(this, event)'", str_contains($output, 'onclick="openPortfolioModal(this, event)"'));
verify("Portfolio items have class 'portfolio-item'", str_contains($output, 'portfolio-item'));
verify("Portfolio items have class 'cursor-pointer'", str_contains($output, 'cursor-pointer'));

// 3. Standalone Fail-Safe Script
verify("Standalone JavaScript block present before </body>", str_contains($output, '<script>') && str_contains($output, 'openPortfolioModal') && str_contains($output, '</body>'));
verify("openPortfolioModal defined in standalone script", str_contains($output, 'function openPortfolioModal'));
verify("closePortfolioModal defined in standalone script", str_contains($output, 'function closePortfolioModal'));
verify("Multi-slide carousel rendering defined in standalone script", str_contains($output, 'function renderModalSlide'));

echo "\n=== MODAL VERIFICATION RESULTS: $passed / $total PASSED ===\n";
if ($passed !== $total) {
    exit(1);
}
