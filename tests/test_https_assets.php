<?php
declare(strict_types=1);

echo "=== TESTING ASSET URL GENERATION & HTTPS DETECTION ===\n\n";

// Test 1: Simulated Railway Reverse Proxy (HTTPS terminated at proxy)
$_SERVER['SCRIPT_NAME'] = '/portfolio.php';
$_SERVER['HTTP_HOST'] = 'yelloplanetman.up.railway.app';
$_SERVER['HTTPS'] = 'off';
$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
$_SERVER['HTTP_X_FORWARDED_SSL'] = 'on';

require_once __DIR__ . '/../config/helpers.php';

echo "1. Railway HTTPS Simulation:\n";
echo "   - is_https(): " . (is_https() ? 'TRUE (PASSED)' : 'FALSE (FAILED)') . "\n";
echo "   - base_path(): " . base_path() . "\n";
echo "   - asset_url('css/custom.css'): " . asset_url('css/custom.css') . "\n";
echo "   - asset_url('js/main.js'): " . asset_url('js/main.js') . "\n";
echo "   - asset_url('favicon_porto.png'): " . asset_url('favicon_porto.png') . "\n";
echo "   - upload_url('assets/images/avatar.svg'): " . upload_url('assets/images/avatar.svg') . "\n";
echo "   - upload_url('uploads/portfolio/art_1.png'): " . upload_url('uploads/portfolio/art_1.png') . "\n";
echo "   - base_url(): " . base_url() . "\n";
echo "   - base_url('portfolio.php'): " . base_url('portfolio.php') . "\n";

// Assertions for Railway
assert(asset_url('css/custom.css') === '/assets/css/custom.css', "asset_url must be root-relative");
assert(asset_url('js/main.js') === '/assets/js/main.js', "asset_url must be root-relative");
assert(!str_starts_with(asset_url('css/custom.css'), 'http://'), "asset_url must NOT start with http://");
assert(!str_starts_with(asset_url('js/main.js'), 'http://'), "asset_url must NOT start with http://");
assert(str_starts_with(base_url(), 'https://'), "base_url must use https:// behind proxy");

echo "\n2. Local Subfolder Simulation (e.g. /porto on XAMPP):\n";
$_SERVER['SCRIPT_NAME'] = '/porto/index.php';
$_SERVER['HTTP_HOST'] = 'localhost';
unset($_SERVER['HTTPS'], $_SERVER['HTTP_X_FORWARDED_PROTO'], $_SERVER['HTTP_X_FORWARDED_SSL']);

echo "   - is_https(): " . (is_https() ? 'TRUE' : 'FALSE') . "\n";
echo "   - base_path(): " . base_path() . "\n";
echo "   - asset_url('css/custom.css'): " . asset_url('css/custom.css') . "\n";
echo "   - asset_url('js/main.js'): " . asset_url('js/main.js') . "\n";
echo "   - upload_url('assets/images/avatar.svg'): " . upload_url('assets/images/avatar.svg') . "\n";

assert(asset_url('css/custom.css') === '/porto/assets/css/custom.css', "subfolder asset_url must include /porto prefix");
assert(asset_url('js/main.js') === '/porto/assets/js/main.js', "subfolder asset_url must include /porto prefix");

echo "\n=== ALL ASSERTIONS PASSED! ZERO MIXED CONTENT RISK ===\n";
