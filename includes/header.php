<?php
/**
 * Public Site Header
 * Graphic Design Portfolio & CMS
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';

$site_title   = get_setting('site_title', 'Dimas — Graphic Designer & Visual Creator');
$site_tagline = get_setting('site_tagline', 'Crafting eye-catching visuals, brand identities & engaging social graphics.');
$bio_summary  = get_setting('bio_summary', '');
$avatar_url   = get_setting('avatar_url', 'assets/images/avatar.svg');
$favicon_url  = upload_url($avatar_url);
$favicon_ext  = strtolower(pathinfo(parse_url($avatar_url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
$favicon_type = match($favicon_ext) {
    'svg'   => 'image/svg+xml',
    'png'   => 'image/png',
    'jpg', 'jpeg' => 'image/jpeg',
    'webp'  => 'image/webp',
    default => 'image/x-icon'
};
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($site_title) ?></title>
    <meta name="description" content="<?= e($site_tagline) ?>">
    <meta name="author" content="<?= e(get_setting('designer_name', 'Dimas Arya')) ?>">
    
    <!-- Favicon Integration -->
    <link rel="icon" type="image/png" href="/assets/favicon_porto.png">
    <link rel="icon" type="image/png" href="<?= asset_url('favicon_porto.png') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= asset_url('favicon_porto.svg') ?>">
    <link rel="shortcut icon" href="/assets/favicon_porto.png">
    <link rel="apple-touch-icon" href="/assets/favicon_porto.png">
    
    <!-- OpenGraph / Social Meta -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($site_title) ?>">
    <meta property="og:description" content="<?= e($site_tagline) ?>">
    <meta property="og:url" content="<?= e(base_url()) ?>">
    <meta property="og:image" content="<?= asset_url('favicon_porto.png') ?>">
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
    
    <!-- Base URL Definition for Client JS -->
    <script>
        window.BASE_URL = '<?= rtrim(base_url(), '/') ?>/';
    </script>
    
    <!-- Tailwind CSS CDN with Custom Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#F8FAFC',
                            100: '#F1F5F9',
                            200: '#E2E8F0',
                            300: '#CBD5E1',
                            400: '#94A3B8',
                            500: '#64748B',
                            600: '#475569',
                            700: '#334155',
                            800: '#1E293B',
                            900: '#0F172A',
                            950: '#020617',
                        },
                        accent: {
                            DEFAULT: '#3B82F6',
                            emerald: '#10B981',
                            indigo: '#6366F1',
                            rose: '#F43F5E',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset_url('css/custom.css') ?>">
</head>
<body class="bg-[#F8F9FB] text-slate-800 antialiased min-h-screen flex flex-col selection:bg-slate-900 selection:text-white">
