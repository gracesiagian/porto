<?php
/**
 * Admin Panel Header & Navigation
 * Graphic Design Portfolio & CMS
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../includes/auth.php';

require_auth();

$current_page  = basename($_SERVER['PHP_SELF'] ?? '');
$admin_user    = current_user();
$designer_name = get_setting('designer_name', 'Dimas Arya');
$avatar_url    = get_setting('avatar_url', 'assets/images/avatar.svg');
$favicon_url   = upload_url($avatar_url);
$favicon_ext   = strtolower(pathinfo(parse_url($avatar_url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
$favicon_type  = match($favicon_ext) {
    'svg'   => 'image/svg+xml',
    'png'   => 'image/png',
    'jpg', 'jpeg' => 'image/jpeg',
    'webp'  => 'image/webp',
    default => 'image/x-icon'
};
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title ?? 'Dashboard') ?> — Admin CMS</title>
    
    <!-- Favicon Integration -->
    <link rel="icon" type="image/jpeg" href="/assets/faviconyell.jpg">
    <link rel="icon" type="image/png" href="/assets/faviconyell.png">
    <link rel="icon" type="image/jpeg" href="<?= asset_url('faviconyell.jpg') ?>">
    <link rel="icon" type="image/png" href="<?= asset_url('faviconyell.png') ?>">
    <link rel="shortcut icon" href="/assets/faviconyell.jpg">
    <link rel="apple-touch-icon" href="/assets/faviconyell.jpg">
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800&display=swap" rel="stylesheet">
    
    <!-- Base URL Definition for Client JS -->
    <script>
        window.BASE_URL = '<?= base_path() === '/' ? '/' : rtrim(base_path(), '/') . '/' ?>';
    </script>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
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
<body class="h-full flex flex-col antialiased text-slate-800 bg-[#F4F5F7]">

    <!-- Admin Topbar Navigation -->
    <nav class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Left: Brand Logo & Title -->
                <div class="flex items-center gap-8">
                    <a href="<?= base_url('admin/index.php') ?>" class="flex items-center gap-3 group">
                        <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-base shadow-sm group-hover:bg-indigo-600 transition-colors">
                            <i data-lucide="palette" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="font-extrabold text-slate-900 tracking-tight text-base block leading-none">
                                <?= e($designer_name) ?>
                            </span>
                            <span class="text-[11px] font-semibold text-indigo-600 uppercase tracking-wider block mt-0.5">
                                CMS Panel
                            </span>
                        </div>
                    </a>

                    <!-- Desktop Nav Links -->
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="<?= base_url('admin/index.php') ?>" 
                           class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?= $current_page === 'index.php' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span>Dashboard</span>
                        </a>

                        <a href="<?= base_url('admin/portfolio.php') ?>" 
                           class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?= $current_page === 'portfolio.php' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
                            <i data-lucide="image" class="w-4 h-4"></i>
                            <span>Karya Portofolio</span>
                        </a>

                        <a href="<?= base_url('admin/categories.php') ?>" 
                           class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?= $current_page === 'categories.php' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
                            <i data-lucide="tags" class="w-4 h-4"></i>
                            <span>Kategori</span>
                        </a>

                        <a href="<?= base_url('admin/settings.php') ?>" 
                           class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 <?= $current_page === 'settings.php' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
                            <i data-lucide="sliders" class="w-4 h-4"></i>
                            <span>Profil & Kontak</span>
                        </a>
                    </div>
                </div>

                <!-- Right: Live Site Link & Profile -->
                <div class="flex items-center space-x-3">
                    <a href="<?= base_url() ?>" target="_blank" 
                       class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold border border-slate-200/80 transition-colors">
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        <span>Lihat Website</span>
                    </a>

                    <!-- User Pill & Logout -->
                    <div class="flex items-center pl-2 border-l border-slate-200 space-x-2">
                        <div class="text-right hidden sm:block">
                            <span class="block text-xs font-bold text-slate-800"><?= e($admin_user['username'] ?? 'Admin') ?></span>
                            <span class="block text-[10px] text-emerald-600 font-semibold">● Online</span>
                        </div>
                        <a href="<?= base_url('admin/logout.php') ?>" 
                           onclick="return confirm('Apakah Anda yakin ingin keluar dari Admin Panel?')"
                           class="p-2 rounded-xl text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-100 transition-colors" 
                           title="Keluar / Logout">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Bottom Nav -->
        <div class="md:hidden border-t border-slate-200/80 bg-white px-4 py-2 flex items-center justify-around">
            <a href="<?= base_url('admin/index.php') ?>" class="flex flex-col items-center gap-1 text-[10px] font-bold <?= $current_page === 'index.php' ? 'text-slate-900' : 'text-slate-400' ?>">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                <span>Home</span>
            </a>
            <a href="<?= base_url('admin/portfolio.php') ?>" class="flex flex-col items-center gap-1 text-[10px] font-bold <?= $current_page === 'portfolio.php' ? 'text-slate-900' : 'text-slate-400' ?>">
                <i data-lucide="image" class="w-4 h-4"></i>
                <span>Karya</span>
            </a>
            <a href="<?= base_url('admin/categories.php') ?>" class="flex flex-col items-center gap-1 text-[10px] font-bold <?= $current_page === 'categories.php' ? 'text-slate-900' : 'text-slate-400' ?>">
                <i data-lucide="tags" class="w-4 h-4"></i>
                <span>Kategori</span>
            </a>
            <a href="<?= base_url('admin/settings.php') ?>" class="flex flex-col items-center gap-1 text-[10px] font-bold <?= $current_page === 'settings.php' ? 'text-slate-900' : 'text-slate-400' ?>">
                <i data-lucide="sliders" class="w-4 h-4"></i>
                <span>Setting</span>
            </a>
        </div>
    </nav>

    <!-- Admin Main Content Wrapper -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Alerts Container -->
        <?= render_flashes() ?>
