<?php
/**
 * Admin Login Page
 * Graphic Design Portfolio & CMS
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

// If already logged in, redirect to dashboard
if (is_logged_in()) {
    header('Location: ' . base_url('admin/index.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf($token)) {
        $error = 'Invalid security session. Please refresh and try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password.';
        } elseif (attempt_login($username, $password)) {
            flash('success', 'Welcome back, ' . e($_SESSION['admin_username']) . '!');
            header('Location: ' . base_url('admin/index.php'));
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin CMS Login — <?= e($designer_name) ?></title>
    
    <!-- Favicon Integration -->
    <link rel="icon" type="image/png" href="/assets/favicon_porto.png">
    <link rel="icon" type="image/png" href="<?= asset_url('favicon_porto.png') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= asset_url('favicon_porto.svg') ?>">
    <link rel="shortcut icon" href="/assets/favicon_porto.png">
    <link rel="apple-touch-icon" href="/assets/favicon_porto.png">
    
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
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 antialiased relative overflow-hidden">
    
    <!-- Ambient Background Glow -->
    <div class="absolute top-1/4 left-1/3 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/3 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Login Box Card -->
    <div class="w-full max-w-md bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl relative z-10">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-indigo-600/20 border border-indigo-500/40 text-indigo-400 flex items-center justify-center mx-auto mb-4 shadow-inner">
                <i data-lucide="shield-check" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">CMS Admin Panel</h1>
            <p class="text-xs text-slate-400 mt-1">Kelola portofolio, kategori & profil desain</p>
        </div>

        <!-- Flash & Error Alert -->
        <?= render_flashes() ?>

        <?php if (!empty($error)): ?>
        <div class="flex items-center space-x-3 p-4 mb-6 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
            <span><?= e($error) ?></span>
        </div>
        <?php endif; ?>

        <!-- Form -->
        <form action="<?= base_url('admin/login.php') ?>" method="POST" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <!-- Username Field -->
            <div>
                <label for="username" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                    Username
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </div>
                    <input type="text" id="username" name="username" required autofocus
                           value="admin"
                           placeholder="Masukkan username"
                           class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-800/80 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
            </div>

            <!-- Password Field -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">
                        Password
                    </label>
                    <span class="text-[11px] text-slate-400">Default: <code class="text-indigo-400">admin123</code></span>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </div>
                    <input type="password" id="password" name="password" required
                           value="admin123"
                           placeholder="Masukkan password"
                           class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-800/80 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-indigo-500 to-blue-600 hover:from-indigo-600 hover:to-blue-700 text-white font-bold text-sm shadow-lg shadow-indigo-500/25 transition-all duration-200 active:scale-98 flex items-center justify-center gap-2 mt-6">
                <i data-lucide="log-in" class="w-4 h-4"></i>
                <span>Masuk ke Dashboard</span>
            </button>
        </form>

        <!-- Back to Public Site Link -->
        <div class="mt-8 pt-6 border-t border-slate-800/80 text-center">
            <a href="<?= base_url() ?>" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali ke Halaman Portofolio</span>
            </a>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
