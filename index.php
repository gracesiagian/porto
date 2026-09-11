<?php
/**
 * Public Landing Page - Minimalist & Sleek Profile Bio Card
 * Graphic Design Portfolio & CMS
 */

declare(strict_types=1);

require_once __DIR__ . '/config/helpers.php';

// Fetch Site Profile Settings
$designer_name    = get_setting('designer_name', 'Dimas Arya');
$designer_role    = get_setting('designer_role', 'Visual & Graphic Designer');
$bio_summary      = get_setting('bio_summary', 'Graphic Designer specializing in YouTube Thumbnails, Marketing Posters, Service Pricelists, and Corporate Training Reports (Laporan Diklat). Delivering impactful visuals that drive engagement.');
$status_badge     = get_setting('status_badge', 'Open for Commissions & Freelance');
$status_available = get_setting('status_available', '1') === '1';
$avatar_url       = get_setting('avatar_url', 'assets/images/avatar.svg');

$whatsapp_number  = get_setting('whatsapp_number', '6281234567890');
$whatsapp_message = get_setting('whatsapp_message', 'Halo Dimas, saya tertarik dengan portofolio desain grafis Anda. Ingin konsultasi project design:');
$twitter_url      = get_setting('twitter_url', 'https://twitter.com/');
$twitter_handle   = get_setting('twitter_handle', '@dimasdesign_');
$instagram_url    = get_setting('instagram_url', 'https://instagram.com/');
$email_address    = get_setting('email_address', 'dimas.design@example.com');
$footer_credit    = get_setting('footer_credit', '© ' . date('Y') . ' ' . $designer_name . '. All rights reserved.');

require_once __DIR__ . '/includes/header.php';
?>

<!-- ==========================================================
     CARRD-INSPIRED FLOATING PROFILE CARD (COMPACT & CENTERED)
     ========================================================== -->
<main class="min-h-screen flex flex-col justify-between items-center px-4 py-8 sm:py-12 relative overflow-hidden select-none">
    
    <!-- Background Ambient Glow Accents -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-96 h-96 bg-indigo-200/40 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="absolute bottom-1/4 left-1/3 -translate-x-1/2 w-80 h-80 bg-sky-200/30 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <!-- Top Spacer for True Vertical Centering -->
    <div class="w-full max-w-md hidden sm:block"></div>

    <!-- Sleek & Compact Floating Central Card (Max-W 440px - 460px) -->
    <div class="carrd-container w-full max-w-[440px] sm:max-w-[460px] mx-auto rounded-3xl p-7 sm:p-9 border border-white/80 shadow-2xl transition-all duration-300 relative text-center my-auto">
        
        <!-- Status Pill Badge -->
        <?php if ($status_available && !empty($status_badge)): ?>
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs font-semibold tracking-wide mb-6 shadow-2xs">
            <span class="w-2 h-2 rounded-full bg-emerald-500 status-beacon flex-shrink-0"></span>
            <span class="truncate"><?= e($status_badge) ?></span>
        </div>
        <?php endif; ?>

        <!-- Avatar / Illustration with Glowing Gradient Ring -->
        <div class="relative w-24 h-24 sm:w-28 sm:h-28 mx-auto mb-5 group">
            <div class="absolute -inset-1 bg-gradient-to-tr from-indigo-500 via-sky-400 to-emerald-400 rounded-full blur-sm opacity-70 group-hover:opacity-100 transition duration-500"></div>
            <img src="<?= upload_url($avatar_url) ?>" 
                 alt="<?= e($designer_name) ?>" 
                 class="relative w-full h-full object-cover rounded-full bg-white p-1 border border-white shadow-inner">
        </div>

        <!-- Designer Name & Role -->
        <h1 class="text-2xl sm:text-[26px] font-extrabold text-slate-900 tracking-tight mb-1">
            <?= e($designer_name) ?>
        </h1>
        <p class="text-xs sm:text-sm font-semibold text-indigo-600 mb-4 tracking-wide">
            <?= e($designer_role) ?>
        </p>

        <!-- Bio Description -->
        <?php if (!empty($bio_summary)): ?>
        <p class="text-slate-600 text-xs sm:text-sm leading-relaxed mb-7 font-normal px-1">
            <?= nl2br(e($bio_summary)) ?>
        </p>
        <?php endif; ?>

        <!-- Action & Navigation Pill Buttons -->
        <div class="flex flex-col gap-3 w-full">
            
            <!-- Primary Portfolio Button: Direct Multi-page Link to portfolio.php -->
            <a href="<?= base_url('portfolio.php') ?>" 
               class="carrd-pill w-full flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-2xl bg-slate-900 text-white font-bold text-sm shadow-md hover:bg-slate-800 hover:shadow-lg transition-all active:scale-98">
                <i data-lucide="grid" class="w-4 h-4 text-indigo-300"></i>
                <span>Lihat Portofolio Desain</span>
                <i data-lucide="arrow-right" class="w-4 h-4 ml-0.5 opacity-80"></i>
            </a>

            <!-- Social Action Buttons (Twitter/X & WhatsApp) - Evenly Distributed & Centered -->
            <?php 
            $has_twitter  = !empty($twitter_url);
            $has_whatsapp = !empty($whatsapp_number);
            ?>
            <?php if ($has_twitter || $has_whatsapp): ?>
            <div class="grid <?= ($has_twitter && $has_whatsapp) ? 'grid-cols-2' : 'grid-cols-1' ?> gap-3 w-full">
                <!-- Twitter / X -->
                <?php if ($has_twitter): ?>
                <a href="<?= e($twitter_url) ?>" target="_blank" rel="noopener noreferrer" 
                   class="carrd-pill flex items-center justify-center gap-2 px-3 py-3 rounded-2xl bg-white border border-slate-200 text-slate-700 font-bold text-xs sm:text-sm hover:bg-slate-50 hover:border-slate-300 transition-all shadow-xs text-center w-full active:scale-98">
                    <i data-lucide="twitter" class="w-4 h-4 text-slate-800 flex-shrink-0"></i>
                    <span class="truncate">Twitter / X</span>
                </a>
                <?php endif; ?>

                <!-- WhatsApp Direct Chat with prefilled message -->
                <?php if ($has_whatsapp): ?>
                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $whatsapp_number) ?>?text=<?= urlencode($whatsapp_message) ?>" 
                   target="_blank" rel="noopener noreferrer" 
                   class="carrd-pill flex items-center justify-center gap-2 px-3 py-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold text-xs sm:text-sm hover:bg-emerald-100 hover:border-emerald-300 transition-all shadow-xs text-center w-full active:scale-98">
                    <i data-lucide="message-circle" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                    <span class="truncate">WhatsApp</span>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Additional Contact Links (Instagram / Email) if configured -->
            <?php if (!empty($instagram_url) || !empty($email_address)): ?>
            <div class="flex items-center justify-center gap-2 pt-2 text-xs text-slate-400">
                <?php if (!empty($instagram_url)): ?>
                <a href="<?= e($instagram_url) ?>" target="_blank" rel="noopener noreferrer" 
                   class="p-2 rounded-xl text-slate-400 hover:text-pink-600 hover:bg-pink-50 transition-all" title="Instagram">
                    <i data-lucide="instagram" class="w-4 h-4"></i>
                </a>
                <?php endif; ?>

                <?php if (!empty($email_address)): ?>
                <a href="mailto:<?= e($email_address) ?>" 
                   class="p-2 rounded-xl text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all" title="Email Contact">
                    <i data-lucide="mail" class="w-4 h-4"></i>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Minimalist Bottom Footer & Concealed Admin Lock Icon -->
    <div class="w-full max-w-md mx-auto pt-6 pb-2 text-center text-xs text-slate-400 flex items-center justify-center gap-2 relative">
        <span><?= e($footer_credit) ?></span>
        
        <!-- Subtle, low-opacity key/lock icon in corner -->
        <a href="<?= base_url('admin/login.php') ?>" 
           class="text-slate-400 opacity-25 hover:opacity-80 transition-opacity p-1 rounded" 
           title="Admin Panel" 
           aria-label="Admin Login">
            <i data-lucide="lock" class="w-3 h-3"></i>
        </a>
    </div>
</main>

<script src="<?= asset_url('js/main.js') ?>"></script>
</body>
</html>
