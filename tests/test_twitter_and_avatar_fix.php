<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';

echo "=== TESTING TWITTER/X LOGO & PERSISTENT AVATAR FIXES ===\n\n";

$passed = 0;
$total = 0;

function verify(string $name, bool $ok, string $detail = '') {
    global $passed, $total;
    $total++;
    if ($ok) {
        $passed++;
        echo " [PASS] $name\n";
    } else {
        echo " [FAIL] $name" . ($detail ? " - $detail" : "") . "\n";
    }
}

// 1. Verify Twitter / X icon asset existence
$logo_twt_exists = file_exists(__DIR__ . '/../assets/logo_twt.png');
verify("assets/logo_twt.png exists in repository", $logo_twt_exists);

$faviconyell_exists = file_exists(__DIR__ . '/../assets/faviconyell.jpg');
verify("assets/faviconyell.jpg exists as committed avatar", $faviconyell_exists);

// 2. Test get_profile_avatar_url helper with various states
$default_avatar = get_profile_avatar_url('');
verify("Default fallback avatar resolves to faviconyell.jpg", str_contains($default_avatar, 'assets/faviconyell.jpg'));

$nonexistent_avatar = get_profile_avatar_url('uploads/portfolio/ephemeral_nonexistent.png');
verify("Non-existent ephemeral upload falls back to faviconyell.jpg", str_contains($nonexistent_avatar, 'assets/faviconyell.jpg'));

$placeholder_avatar = get_profile_avatar_url('assets/images/avatar.svg');
verify("Generic SVG placeholder falls back to real photo faviconyell.jpg", str_contains($placeholder_avatar, 'assets/faviconyell.jpg'));

// 3. Render index.php and inspect output
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['HTTP_HOST'] = 'yelloplanetman.up.railway.app';
$_SERVER['HTTPS'] = 'on';

ob_start();
require __DIR__ . '/../index.php';
$html_index = ob_get_clean();

verify("index.php renders Twitter / X button with logo_twt.png", str_contains($html_index, 'logo_twt.png'));
verify("index.php avatar rendered with rounded-full & object-cover", str_contains($html_index, 'rounded-full') && str_contains($html_index, 'object-cover'));

// 4. Render portfolio.php and inspect topbar avatar
$_SERVER['SCRIPT_NAME'] = '/portfolio.php';
ob_start();
require __DIR__ . '/../portfolio.php';
$html_portfolio = ob_get_clean();

verify("portfolio.php topbar avatar rendered with rounded-full & object-cover", str_contains($html_portfolio, 'rounded-full') && str_contains($html_portfolio, 'object-cover'));

// 5. Explicit simulation of Railway Redeploy (ephemeral file missing from disk)
$ephemeral_sim = get_profile_avatar_url('uploads/avatar/missing_after_redeploy.jpg');
verify("Railway redeploy simulation: missing upload correctly resolves to /assets/faviconyell.jpg", str_contains($ephemeral_sim, 'faviconyell.jpg'));

echo "\n=== SUMMARY: $passed / $total TESTS PASSED ===\n";
if ($passed !== $total) {
    exit(1);
}
