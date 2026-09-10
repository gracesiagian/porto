<?php
/**
 * Admin Dashboard Overview
 * Graphic Design Portfolio & CMS
 */

declare(strict_types=1);

$page_title = 'Dashboard';
require_once __DIR__ . '/includes/admin_header.php';

$db = get_db();

// Counts & Metrics
$total_artworks = (int)$db->query("SELECT COUNT(*) FROM portfolio_items")->fetchColumn();
$active_artworks = (int)$db->query("SELECT COUNT(*) FROM portfolio_items WHERE is_active = 1")->fetchColumn();
$total_categories = (int)$db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$recent_items = $db->query("SELECT p.*, c.name as category_name FROM portfolio_items p INNER JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 6")->fetchAll();

$designer_name = get_setting('designer_name', 'yelloplanetman');
$status_badge  = get_setting('status_badge', 'Open for Commission');
$whatsapp_num  = get_setting('whatsapp_number', '6287794297888');
?>

<!-- Welcome Banner -->
<div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-8 sm:p-10 text-white shadow-xl mb-8 border border-slate-800">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-indigo-300 text-xs font-semibold mb-3 border border-white/10">
                <span class="w-2 h-2 rounded-full bg-emerald-400 status-beacon"></span>
                <span><?= e($status_badge) ?></span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                Halo, <?= e($designer_name) ?>! ✨
            </h1>
        </div>

        <!-- Quick Upload Action Button -->
        <div class="flex flex-wrap gap-3">
            <a href="<?= base_url('admin/portfolio.php?action=add') ?>" 
               class="px-5 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-indigo-600/30 transition-all duration-200 flex items-center gap-2 active:scale-95">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Upload Karya Baru</span>
            </a>
            <a href="<?= base_url() ?>" target="_blank"
               class="px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs sm:text-sm backdrop-blur-md border border-white/15 transition-all duration-200 flex items-center gap-2">
                <i data-lucide="external-link" class="w-4 h-4"></i>
                <span>Lihat Portofolio</span>
            </a>
        </div>
    </div>
</div>

<!-- Stats Metric Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    
    <!-- Total Artworks -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Karya Desain</span>
            <p class="text-3xl font-extrabold text-slate-900 mt-1"><?= $total_artworks ?></p>
            <span class="text-xs text-emerald-600 font-semibold mt-1 inline-block"><?= $active_artworks ?> Aktif Tampil</span>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
            <i data-lucide="image" class="w-6 h-6"></i>
        </div>
    </div>

    <!-- Categories -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kategori Desain</span>
            <p class="text-3xl font-extrabold text-slate-900 mt-1"><?= $total_categories ?></p>
            <a href="<?= base_url('admin/categories.php') ?>" class="text-xs text-indigo-600 hover:underline font-semibold mt-1 inline-block">Kelola Kategori →</a>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
            <i data-lucide="tags" class="w-6 h-6"></i>
        </div>
    </div>

    <!-- WhatsApp Channel -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">WhatsApp Client</span>
            <p class="text-sm font-extrabold text-slate-900 mt-1 truncate max-w-[150px]"><?= e($whatsapp_num) ?></p>
            <span class="text-xs text-emerald-600 font-semibold mt-1 inline-block">Direct Inquire Ready</span>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
            <i data-lucide="message-circle" class="w-6 h-6"></i>
        </div>
    </div>

    <!-- Settings Quick Link -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Profil & Bio</span>
            <p class="text-sm font-extrabold text-slate-900 mt-1">Carrd Style Card</p>
            <a href="<?= base_url('admin/settings.php') ?>" class="text-xs text-indigo-600 hover:underline font-semibold mt-1 inline-block">Edit Profil →</a>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
            <i data-lucide="sliders" class="w-6 h-6"></i>
        </div>
    </div>
</div>

<!-- Recent Artworks Grid & Quick Management -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-black text-slate-900 tracking-tight">Karya Terbaru Diupload</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar artwork terkini yang aktif di galeri publik</p>
        </div>
        <a href="<?= base_url('admin/portfolio.php') ?>" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1">
            <span>Lihat Semua Karya</span>
            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
        </a>
    </div>

    <?php if (empty($recent_items)): ?>
    <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
        <i data-lucide="image-off" class="w-8 h-8 text-slate-400 mx-auto mb-2"></i>
        <p class="text-sm font-semibold text-slate-600">Belum ada karya yang diunggah.</p>
        <a href="<?= base_url('admin/portfolio.php?action=add') ?>" class="inline-flex items-center gap-1.5 mt-3 text-xs font-bold text-indigo-600 hover:underline">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i> Upload sekarang
        </a>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($recent_items as $item): ?>
        <div class="rounded-2xl border border-slate-200/80 overflow-hidden bg-slate-50/50 flex flex-col justify-between group hover:shadow-md transition-shadow">
            <div class="relative aspect-video w-full overflow-hidden bg-slate-200">
                <img src="<?= upload_url($item['image_url']) ?>" 
                     alt="<?= e($item['title']) ?>" 
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                <span class="absolute top-2.5 left-2.5 px-2.5 py-0.5 rounded-md bg-slate-900/80 backdrop-blur-sm text-white text-[10px] font-bold">
                    <?= e($item['category_name']) ?>
                </span>
            </div>
            <div class="p-4 flex-1 flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm line-clamp-1 mb-1"><?= e($item['title']) ?></h3>
                    <p class="text-[11px] text-slate-500 line-clamp-1"><?= e($item['tools_used'] ?? 'Adobe Suite') ?></p>
                </div>
                <div class="pt-3 mt-3 border-t border-slate-200/60 flex items-center justify-between text-xs">
                    <span class="inline-flex items-center gap-1.5 <?= $item['is_active'] ? 'text-emerald-600 font-semibold' : 'text-slate-400' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= $item['is_active'] ? 'bg-emerald-500' : 'bg-slate-300' ?>"></span>
                        <?= $item['is_active'] ? 'Aktif' : 'Disembunyikan' ?>
                    </span>
                    <a href="<?= base_url('admin/portfolio.php') ?>" class="text-indigo-600 hover:text-indigo-800 font-semibold text-xs">
                        Kelola →
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
