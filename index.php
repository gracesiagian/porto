<?php
/**
 * Public Landing Page & Fluid Multi-Column Portfolio Gallery
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

// Fetch Categories & Portfolio Works
$categories = get_categories();
$portfolio_items = get_portfolio_items(null, true);
$total_items = count($portfolio_items);

require_once __DIR__ . '/includes/header.php';
?>

<!-- ==========================================================
     SECTION 1: CARRD-INSPIRED FLOATING PROFILE CARD
     ========================================================== -->
<section class="min-h-[85vh] flex items-center justify-center px-4 py-12 relative overflow-hidden">
    <!-- Subtle Background Ambient Glow Elements -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-96 h-96 bg-indigo-200/40 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="absolute top-1/3 left-1/4 w-72 h-72 bg-blue-200/40 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <!-- Floating Central Card -->
    <div class="carrd-container w-full max-w-md mx-auto rounded-3xl p-8 sm:p-10 border border-white/80 shadow-2xl transition-all duration-300 relative text-center">
        
        <!-- Status Pill Badge -->
        <?php if ($status_available && $status_badge): ?>
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs font-semibold tracking-wide mb-6 shadow-2xs">
            <span class="w-2 h-2 rounded-full bg-emerald-500 status-beacon"></span>
            <span><?= e($status_badge) ?></span>
        </div>
        <?php endif; ?>

        <!-- Avatar / Illustration -->
        <div class="relative w-28 h-28 sm:w-32 sm:h-32 mx-auto mb-6 group">
            <div class="absolute -inset-1.5 bg-gradient-to-tr from-indigo-500 via-sky-400 to-emerald-400 rounded-full blur-sm opacity-60 group-hover:opacity-100 transition duration-500"></div>
            <img src="<?= upload_url($avatar_url) ?>" 
                 alt="<?= e($designer_name) ?>" 
                 class="relative w-full h-full object-cover rounded-full bg-white p-1 border border-white shadow-inner">
        </div>

        <!-- Designer Name & Role -->
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mb-1">
            <?= e($designer_name) ?>
        </h1>
        <p class="text-sm font-medium text-slate-500 mb-5">
            <?= e($designer_role) ?>
        </p>

        <!-- Bio Description -->
        <p class="text-slate-600 text-sm sm:text-base leading-relaxed mb-8 font-normal">
            <?= nl2br(e($bio_summary)) ?>
        </p>

        <!-- Quick Action Pill Buttons -->
        <div class="flex flex-col gap-3">
            <!-- Primary Portfolio Button (Smooth Scroll to Fluid Gallery) -->
            <a href="#portfolio-gallery" 
               class="carrd-pill w-full flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-2xl bg-slate-900 text-white font-semibold text-sm shadow-md hover:bg-slate-800 hover:shadow-lg transition-all">
                <i data-lucide="grid" class="w-4 h-4"></i>
                <span>Lihat Portofolio Desain</span>
                <i data-lucide="arrow-down" class="w-4 h-4 ml-0.5 opacity-70"></i>
            </a>

            <!-- Social Action Buttons (Twitter/X & WhatsApp) -->
            <div class="grid grid-cols-2 gap-3">
                <!-- Twitter / X -->
                <?php if ($twitter_url): ?>
                <a href="<?= e($twitter_url) ?>" target="_blank" rel="noopener noreferrer" 
                   class="carrd-pill flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-white border border-slate-200 text-slate-700 font-semibold text-xs sm:text-sm hover:bg-slate-50 hover:border-slate-300 transition-all shadow-xs">
                    <i data-lucide="twitter" class="w-4 h-4 text-slate-800"></i>
                    <span>Twitter / X</span>
                </a>
                <?php endif; ?>

                <!-- WhatsApp Direct Chat with prefilled message -->
                <?php if ($whatsapp_number): ?>
                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $whatsapp_number) ?>?text=<?= urlencode($whatsapp_message) ?>" 
                   target="_blank" rel="noopener noreferrer" 
                   class="carrd-pill flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold text-xs sm:text-sm hover:bg-emerald-100 hover:border-emerald-300 transition-all shadow-xs">
                    <i data-lucide="message-circle" class="w-4 h-4 text-emerald-600"></i>
                    <span>WhatsApp</span>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Subtle quick link to admin -->
        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-center text-xs text-slate-400">
            <a href="<?= base_url('admin/login.php') ?>" class="hover:text-slate-600 inline-flex items-center gap-1.5 transition-colors">
                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                <span>Admin CMS Panel</span>
            </a>
        </div>
    </div>
</section>

<!-- ==========================================================
     SECTION 2: FLUID MULTI-COLUMN PORTFOLIO SHOWCASE (NO NARROW MODALS!)
     ========================================================== -->
<section id="portfolio-gallery" class="py-16 sm:py-24 bg-white/60 border-t border-slate-200/80 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Showcase Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold mb-3">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-indigo-500"></i>
                    <span>Selected Works</span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Galeri Portofolio Desain
                </h2>
                <p class="text-slate-500 text-sm sm:text-base mt-2 max-w-2xl">
                    Koleksi karya visual kreatif berkualitas tinggi: YouTube Thumbnail, Poster / Infografis, Pricelist, dan Cover Laporan Diklat.
                </p>
            </div>

            <!-- Total Counter Badge -->
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 bg-slate-100/80 px-4 py-2 rounded-xl border border-slate-200 self-start md:self-auto">
                <i data-lucide="image" class="w-4 h-4 text-slate-700"></i>
                <span>Menampilkan <strong id="visible-count" class="text-slate-900"><?= $total_items ?></strong> karya</span>
            </div>
        </div>

        <!-- Category Filter Tabs (Pill Buttons) -->
        <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-10 no-scrollbar">
            <button type="button" 
                    data-filter="all" 
                    class="filter-btn active flex-shrink-0 px-5 py-2.5 rounded-full text-xs sm:text-sm font-semibold border border-slate-200/80 shadow-xs cursor-pointer">
                Semua Karya
            </button>
            <?php foreach ($categories as $cat): ?>
            <button type="button" 
                    data-filter="<?= e($cat['slug']) ?>" 
                    class="filter-btn flex-shrink-0 px-5 py-2.5 rounded-full bg-white text-slate-600 hover:bg-slate-100 text-xs sm:text-sm font-semibold border border-slate-200/80 shadow-xs cursor-pointer">
                <?= e($cat['name']) ?>
                <span class="ml-1.5 px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[11px] font-medium"><?= $cat['item_count'] ?></span>
            </button>
            <?php endforeach; ?>
        </div>

        <!-- Fluid Multi-Column Grid (1 col mobile, 2-3 col tablet, 3-4 col desktop) -->
        <?php if (empty($portfolio_items)): ?>
        <div class="text-center py-20 bg-slate-50 rounded-3xl border border-dashed border-slate-300">
            <i data-lucide="folder-open" class="w-12 h-12 mx-auto text-slate-400 mb-3"></i>
            <h3 class="text-lg font-bold text-slate-800">Belum Ada Karya Portofolio</h3>
            <p class="text-sm text-slate-500 mt-1">Silakan tambahkan portofolio baru melalui Admin CMS Panel.</p>
            <a href="<?= base_url('admin/portfolio.php') ?>" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Karya
            </a>
        </div>
        <?php else: ?>
        <div id="portfolio-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 sm:gap-8">
            <?php foreach ($portfolio_items as $index => $item): ?>
            <div class="portfolio-card group bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs cursor-pointer flex flex-col justify-between"
                 data-id="<?= $item['id'] ?>"
                 data-category="<?= e($item['category_slug']) ?>"
                 data-category-name="<?= e($item['category_name']) ?>"
                 data-title="<?= e($item['title']) ?>"
                 data-image="<?= upload_url($item['image_url']) ?>"
                 data-images='<?= htmlspecialchars(json_encode($item['images'] ?? []), ENT_QUOTES, 'UTF-8') ?>'
                 data-image-count="<?= $item['image_count'] ?? 1 ?>"
                 data-desc="<?= e($item['description'] ?? '') ?>">  
                
                <!-- Artwork Image & Category Pill -->
                <div class="relative aspect-video w-full overflow-hidden bg-slate-100">
                    <img src="<?= upload_url($item['image_url']) ?>" 
                         alt="<?= e($item['title']) ?>" 
                         loading="lazy" 
                         class="artwork-img w-full h-full object-cover">
                    
                    <!-- Top Category Badge -->
                    <div class="absolute top-3 left-3 z-10">
                        <span class="px-3 py-1 rounded-lg bg-slate-900/80 backdrop-blur-md text-white text-[11px] font-semibold tracking-wide border border-white/20 shadow-xs">
                            <?= e($item['category_name']) ?>
                        </span>
                    </div>

                    <!-- Multi-Slide Carousel Pill Badge (Top Right) -->
                    <?php if (!empty($item['image_count']) && $item['image_count'] > 1): ?>
                    <div class="absolute top-3 right-3 z-10">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-950/85 backdrop-blur-md text-white text-[11px] font-bold border border-white/20 shadow-xs">
                            <i data-lucide="layers" class="w-3.5 h-3.5 text-amber-400"></i>
                            <span><?= $item['image_count'] ?> Slides</span>
                        </span>
                    </div>
                    <?php endif; ?>

                    <!-- Overlay Inspect Indicator on Hover -->
                    <div class="overlay-actions absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all duration-300">
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/90 backdrop-blur-md text-slate-900 text-xs font-bold shadow-lg transform translate-y-2 group-hover:translate-y-0 transition-transform">
                            <i data-lucide="maximize-2" class="w-4 h-4"></i>
                            <span><?= (!empty($item['image_count']) && $item['image_count'] > 1) ? 'Buka Carousel Desain' : 'Lihat Detail Desain' ?></span>
                        </span>
                    </div>
                </div>

                <!-- Artwork Info Card Footer -->
                <div class="p-5 flex-1 flex flex-col justify-between bg-white">
                    <div>
                        <div class="flex items-start justify-between gap-2 mb-1.5">
                            <h3 class="font-bold text-slate-900 text-base leading-snug group-hover:text-indigo-600 transition-colors line-clamp-2">
                                <?= e($item['title']) ?>
                            </h3>
                        </div>
                        <?php if (!empty($item['description'])): ?>
                        <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                            <?= e($item['description']) ?>
                        </p>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($item['image_count']) && $item['image_count'] > 1): ?>
                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center gap-1.5 text-[11px] font-bold text-indigo-600">
                        <i data-lucide="images" class="w-3.5 h-3.5"></i>
                        <span>Carousel Multi-Slide (<?= $item['image_count'] ?> gambar)</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ==========================================================
     SECTION 3: INTERACTIVE ARTWORK LIGHTBOX MODAL (MULTI-SLIDE CAROUSEL)
     ========================================================== -->
<div id="artwork-lightbox" 
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-950/85 backdrop-blur-lg opacity-0 transition-opacity duration-300"
     role="dialog" aria-modal="true" aria-labelledby="lightbox-title">
    
    <!-- Lightbox Modal Box -->
    <div class="lightbox-content relative w-full max-w-5xl bg-white rounded-3xl overflow-hidden shadow-2xl border border-white/20 flex flex-col lg:flex-row max-h-[92vh]">
        
        <!-- Left / Top: High-Res Artwork Stage & Carousel Slider -->
        <div class="relative flex-1 bg-slate-950 flex flex-col items-center justify-center p-4 sm:p-6 min-h-[320px] sm:min-h-[440px] lg:min-h-[520px] overflow-hidden select-none">
            
            <!-- Slide Counter Pill Badge -->
            <div id="lightbox-slide-counter-wrap" class="hidden absolute top-4 left-4 z-20 px-3.5 py-1.5 rounded-full bg-slate-900/80 backdrop-blur-md text-white text-xs font-bold border border-white/20 shadow-md items-center gap-2">
                <i data-lucide="layers" class="w-3.5 h-3.5 text-amber-400"></i>
                <span id="lightbox-slide-counter">Slide 1 / 1</span>
            </div>

            <!-- Main Slide Image Preview with Smooth Transition -->
            <div class="relative w-full h-full flex items-center justify-center">
                <img id="lightbox-img" 
                     src="" 
                     alt="Artwork Preview" 
                     class="max-w-full max-h-[68vh] object-contain rounded-xl shadow-2xl select-none transition-all duration-300">
            </div>

            <!-- Previous / Next Controls Over Image -->
            <button id="lightbox-prev" type="button" 
                    class="absolute left-4 top-1/2 -translate-y-1/2 p-3 rounded-full bg-slate-900/75 hover:bg-slate-900 text-white backdrop-blur-md border border-white/20 shadow-lg transition-all hover:scale-110 active:scale-95 z-20 cursor-pointer" 
                    title="Slide Sebelumnya (Panah Kiri)">
                <i data-lucide="chevron-left" class="w-6 h-6"></i>
            </button>

            <button id="lightbox-next" type="button" 
                    class="absolute right-4 top-1/2 -translate-y-1/2 p-3 rounded-full bg-slate-900/75 hover:bg-slate-900 text-white backdrop-blur-md border border-white/20 shadow-lg transition-all hover:scale-110 active:scale-95 z-20 cursor-pointer" 
                    title="Slide Berikutnya (Panah Kanan)">
                <i data-lucide="chevron-right" class="w-6 h-6"></i>
            </button>

            <!-- Bottom Slide Dots Indicator -->
            <div id="lightbox-dots-wrap" class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-950/70 backdrop-blur-md border border-white/10 max-w-[90%] overflow-x-auto no-scrollbar">
                <!-- Injected via JavaScript -->
            </div>
        </div>

        <!-- Right / Bottom: Artwork Details, Slide Gallery Thumbnails & WhatsApp Inquire -->
        <div class="w-full lg:w-96 p-6 sm:p-8 flex flex-col justify-between bg-white overflow-y-auto border-t lg:border-t-0 lg:border-l border-slate-100">
            <div>
                <!-- Top Row: Category Badge & Close Button -->
                <div class="flex items-center justify-between mb-4">
                    <span id="lightbox-category" 
                          class="px-3.5 py-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-800 text-xs font-bold tracking-wide">
                        Category
                    </span>
                    <button id="lightbox-close" type="button" 
                            class="p-2 rounded-full text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-colors cursor-pointer"
                            title="Tutup Lightbox (Esc)">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Artwork Title -->
                <h3 id="lightbox-title" class="text-xl sm:text-2xl font-black text-slate-900 leading-tight mb-3">
                    Artwork Title
                </h3>

                <!-- Description -->
                <p id="lightbox-desc" class="text-sm text-slate-600 leading-relaxed mb-6">
                    Artwork detailed description.
                </p>

                <!-- Mini Thumbnail Grid for Multi-Slide Carousels -->
                <div id="lightbox-thumbs-section" class="hidden mb-6">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                        Pilih Slide Desain
                    </label>
                    <div id="lightbox-thumbnails-grid" class="grid grid-cols-4 gap-2">
                        <!-- Populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Call to Action: WhatsApp Order / Inquire for Similar Work -->
            <div class="pt-6 mt-6 border-t border-slate-100">
                <a id="lightbox-inquire" 
                   href="#" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   data-phone="<?= preg_replace('/[^0-9]/', '', $whatsapp_number) ?>"
                   class="w-full flex items-center justify-center gap-2.5 px-5 py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md transition-all active:scale-95">
                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                    <span>Pesan Desain Serupa</span>
                </a>
                <p class="text-[11px] text-center text-slate-400 mt-2">
                    Konsultasi gratis via WhatsApp • Fast Response
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
