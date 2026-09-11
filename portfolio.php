<?php
/**
 * Dedicated Public Portfolio Showcase & Multi-Column Gallery
 * Graphic Design Portfolio & CMS
 */

declare(strict_types=1);

require_once __DIR__ . '/config/helpers.php';

// Fetch Site Profile Settings
$designer_name    = get_setting('designer_name', 'yelloplanetman');
$designer_role    = get_setting('designer_role', 'Visual & Graphic Designer');
$status_badge     = get_setting('status_badge', 'Open for Commissions & Freelance');
$status_available = get_setting('status_available', '1') === '1';
$avatar_url       = get_setting('avatar_url', '');
$avatar_rel       = ltrim($avatar_url, '/');
$profile_img      = !empty($avatar_rel) && file_exists(__DIR__ . '/' . $avatar_rel) && !is_dir(__DIR__ . '/' . $avatar_rel) && $avatar_rel !== 'assets/images/avatar.svg'
    ? upload_url($avatar_url) 
    : asset_url('faviconyell.jpg');

$whatsapp_number  = get_setting('whatsapp_number', '6287794297888');
$whatsapp_message = get_setting('whatsapp_message', 'Halo yelloplanetman, saya tertarik dengan karya portofolio desain Anda. Ingin konsultasi project:');
$twitter_url      = get_setting('twitter_url', 'https://twitter.com/');

// Fetch Categories
$categories = get_categories();

// Category filter support via GET query parameter
$selected_category = trim($_GET['category'] ?? 'all');
$cat_id = null;
if ($selected_category !== 'all' && $selected_category !== '') {
    foreach ($categories as $c) {
        if ($c['slug'] === $selected_category) {
            $cat_id = (int)$c['id'];
            break;
        }
    }
}

// Fetch active portfolio items (filtered by category if selected, otherwise all)
$all_items_count = count(get_portfolio_items(null, true));
$portfolio_items = get_portfolio_items($cat_id, true);
$total_items     = count($portfolio_items);

require_once __DIR__ . '/includes/header.php';
?>

<!-- ==========================================================
     TOPBAR NAVIGATION: BACK TO BIO & DIRECT CONTACT
     ========================================================== -->
<header class="sticky top-0 z-30 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-2xs transition-all">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            
            <!-- Left: Back to Profile Link with Avatar & Perfectly Aligned Text -->
            <a href="<?= base_url() ?>" 
               class="group inline-flex items-center gap-3 text-slate-700 hover:text-slate-900 transition-colors"
               style="display: flex; align-items: center; gap: 12px;">
                <div class="w-10 h-10 rounded-full overflow-hidden border border-slate-200 shadow-2xs flex-shrink-0 group-hover:scale-105 transition-transform bg-slate-100">
                    <img src="<?= $profile_img ?>" alt="<?= e($designer_name) ?>" class="w-full h-full object-cover rounded-full">
                </div>
                <div class="flex flex-col justify-center" style="display: flex; flex-direction: column; justify-content: center;">
                    <span class="font-extrabold text-slate-900 text-sm leading-tight m-0 p-0 group-hover:text-indigo-600 transition-colors">
                        <?= e($designer_name) ?>
                    </span>
                    <span class="text-[11px] text-slate-400 font-medium leading-tight mt-0.5">
                        ← Kembali ke Profil
                    </span>
                </div>
            </a>

            <!-- Right: Direct WhatsApp CTA & Available Badge -->
            <div class="flex items-center gap-3">
                <?php if ($status_available && !empty($status_badge)): ?>
                <div class="hidden md:inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 status-beacon"></span>
                    <span><?= e($status_badge) ?></span>
                </div>
                <?php endif; ?>

                <?php if (!empty($whatsapp_number)): ?>
                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $whatsapp_number) ?>?text=<?= urlencode($whatsapp_message) ?>" 
                   target="_blank" rel="noopener noreferrer" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition-all active:scale-95">
                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                    <span>Konsultasi Project</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<!-- ==========================================================
     PORTFOLIO SHOWCASE & CATEGORY FILTERS
     ========================================================== -->
<main class="flex-1 py-10 sm:py-16 bg-[#F8F9FB] relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Showcase Header & Total Counter -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold mb-3 shadow-2xs">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-indigo-500"></i>
                    <span>Welcome</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                    Galeri Portofolio Desain
                </h1>
                <p class="text-slate-500 text-xs sm:text-sm mt-2 max-w-2xl leading-relaxed">
                    Data pribadi dan identitas disensor demi perlindungan privasi
                </p>
            </div>

            <!-- Total Counter Badge -->
            <div class="flex items-center gap-2 text-xs font-bold text-slate-600 bg-white px-4 py-2.5 rounded-2xl border border-slate-200/80 shadow-2xs self-start md:self-auto">
                <i data-lucide="image" class="w-4 h-4 text-indigo-600"></i>
                <span>Menampilkan <strong id="visible-count" class="text-slate-900 font-black"><?= $total_items ?></strong> karya</span>
            </div>
        </div>

        <!-- Category Filter Tabs (Pill Buttons - Hybrid PHP & JS) -->
        <div id="category-filters" class="flex items-center gap-2 overflow-x-auto pb-4 mb-10 no-scrollbar">
            <a href="portfolio.php" 
               data-filter="all" 
               data-category="all"
               class="category-btn filter-btn <?= ($selected_category === 'all' || empty($selected_category)) ? 'active bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 hover:bg-slate-100 border-slate-200/80' ?> flex-shrink-0 px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold border shadow-xs cursor-pointer transition-all">
                Semua Karya
                <span class="filter-count ml-1.5 px-2 py-0.5 rounded-full <?= ($selected_category === 'all' || empty($selected_category)) ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' ?> text-[11px] font-bold"><?= $all_items_count ?></span>
            </a>
            <?php foreach ($categories as $cat): ?>
            <a href="portfolio.php?category=<?= urlencode($cat['slug']) ?>" 
               data-filter="<?= e($cat['slug']) ?>" 
               data-category="<?= e($cat['slug']) ?>"
               class="category-btn filter-btn <?= ($selected_category === $cat['slug']) ? 'active bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 hover:bg-slate-100 border-slate-200/80' ?> flex-shrink-0 px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold border shadow-xs cursor-pointer transition-all">
                <?= e($cat['name']) ?>
                <span class="filter-count ml-1.5 px-2 py-0.5 rounded-full <?= ($selected_category === $cat['slug']) ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' ?> text-[11px] font-bold"><?= $cat['item_count'] ?></span>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Fluid Multi-Column Grid (1 col mobile, 2 col tablet, 3-4 col desktop) -->
        <?php if (empty($portfolio_items)): ?>
        <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-slate-300 shadow-2xs">
            <i data-lucide="folder-open" class="w-12 h-12 mx-auto text-slate-300 mb-3"></i>
            <h2 class="text-lg font-bold text-slate-800">Belum Ada Karya Portofolio</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Silakan tambahkan portofolio baru melalui Admin CMS Panel.</p>
            <a href="<?= base_url('admin/login.php') ?>" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800">
                <i data-lucide="shield-check" class="w-4 h-4"></i> Buka CMS Admin
            </a>
        </div>
        <?php else: ?>
        <div id="portfolio-grid" class="portfolio-gallery grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 sm:gap-7">
            <?php foreach ($portfolio_items as $index => $item): ?>
            <div class="portfolio-item portfolio-card group bg-white rounded-3xl border border-slate-200/80 overflow-hidden shadow-xs cursor-pointer flex flex-col justify-between select-none"
                 id="portfolio-item-<?= $item['id'] ?>"
                 data-index="<?= $index ?>"
                 data-id="<?= $item['id'] ?>"
                 data-portfolio-id="<?= $item['id'] ?>"
                 data-category="<?= e($item['category_slug']) ?>"
                 data-category-name="<?= e($item['category_name']) ?>"
                 data-title="<?= e($item['title']) ?>"
                 data-image="<?= upload_url($item['image_url']) ?>"
                 data-images='<?= htmlspecialchars(json_encode($item['images'] ?? []), ENT_QUOTES, 'UTF-8') ?>'
                 data-image-count="<?= $item['image_count'] ?? 1 ?>"
                 data-desc="<?= e($item['description'] ?? '') ?>"
                 onclick="openPortfolioModal(this, event)">  
                
                <!-- Artwork Image Stage & Category Pill -->
                <div class="relative aspect-video w-full overflow-hidden bg-slate-100">
                    <img src="<?= upload_url($item['image_url']) ?>" 
                         alt="<?= e($item['title']) ?>" 
                         loading="lazy" 
                         class="artwork-img w-full h-full object-cover">
                    
                    <!-- Top Category Badge -->
                    <div class="absolute top-3 left-3 z-10 pointer-events-none">
                        <span class="px-3 py-1 rounded-lg bg-slate-900/80 backdrop-blur-md text-white text-[11px] font-bold tracking-wide border border-white/20 shadow-xs">
                            <?= e($item['category_name']) ?>
                        </span>
                    </div>

                    <!-- Multi-Slide Carousel Pill Badge (Top Right) -->
                    <?php if (!empty($item['image_count']) && $item['image_count'] > 1): ?>
                    <div class="absolute top-3 right-3 z-10 pointer-events-none">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-950/85 backdrop-blur-md text-white text-[11px] font-bold border border-white/20 shadow-xs">
                            <i data-lucide="layers" class="w-3.5 h-3.5 text-amber-400"></i>
                            <span><?= $item['image_count'] ?> Slides</span>
                        </span>
                    </div>
                    <?php endif; ?>

                    <!-- Overlay Inspect Indicator on Hover -->
                    <div class="overlay-actions absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all duration-300 pointer-events-none">
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/90 backdrop-blur-md text-slate-900 text-xs font-bold shadow-lg transform translate-y-2 group-hover:translate-y-0 transition-transform">
                            <i data-lucide="maximize-2" class="w-4 h-4 text-indigo-600"></i>
                            <span><?= (!empty($item['image_count']) && $item['image_count'] > 1) ? 'Buka Carousel Desain' : 'Lihat Detail Desain' ?></span>
                        </span>
                    </div>
                </div>

                <!-- Artwork Info Card Footer -->
                <div class="p-5 flex-1 flex flex-col justify-between bg-white">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base leading-snug group-hover:text-indigo-600 transition-colors line-clamp-2 mb-1.5">
                            <?= e($item['title']) ?>
                        </h3>
                        <?php if (!empty($item['description'])): ?>
                        <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed font-normal">
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
</main>

<!-- ==========================================================
     INTERACTIVE ARTWORK LIGHTBOX MODAL (MULTI-SLIDE CAROUSEL)
     ========================================================== -->
<div id="portfolio-modal" 
     class="artwork-lightbox portfolio-modal hidden fixed inset-0 z-50 items-center justify-center p-3 sm:p-6 bg-slate-950/85 backdrop-blur-lg"
     role="dialog" aria-modal="true" aria-labelledby="modal-title">
    
    <!-- Lightbox Modal Box -->
    <div class="lightbox-content modal-content relative w-full max-w-5xl bg-white rounded-3xl overflow-hidden shadow-2xl border border-white/20 flex flex-col lg:flex-row max-h-[92vh]">
        
        <!-- Left / Top: High-Res Artwork Stage & Carousel Slider -->
        <div class="relative flex-1 bg-slate-950 flex flex-col items-center justify-center p-4 sm:p-6 min-h-[320px] sm:min-h-[440px] lg:min-h-[520px] overflow-hidden select-none">
            
            <!-- Slide Counter Pill Badge -->
            <div id="modal-slide-counter-wrap" class="hidden absolute top-4 left-4 z-20 px-3.5 py-1.5 rounded-full bg-slate-900/80 backdrop-blur-md text-white text-xs font-bold border border-white/20 shadow-md items-center gap-2">
                <i data-lucide="layers" class="w-3.5 h-3.5 text-amber-400"></i>
                <span id="modal-slide-counter">Slide 1 / 1</span>
            </div>

            <!-- Main Slide Image Preview with Smooth Transition -->
            <div class="relative w-full h-full flex items-center justify-center">
                <img id="modal-image" 
                     src="" 
                     alt="Artwork Preview" 
                     class="max-w-full max-h-[68vh] object-contain rounded-xl shadow-2xl select-none transition-all duration-300">
            </div>

            <!-- Previous / Next Controls Over Image -->
            <button id="modal-prev" type="button" onclick="modalPrev()"
                    class="absolute left-4 top-1/2 -translate-y-1/2 p-3 rounded-full bg-slate-900/75 hover:bg-slate-900 text-white backdrop-blur-md border border-white/20 shadow-lg transition-all hover:scale-110 active:scale-95 z-20 cursor-pointer" 
                    title="Slide Sebelumnya (Panah Kiri)">
                <i data-lucide="chevron-left" class="w-6 h-6"></i>
            </button>

            <button id="modal-next" type="button" onclick="modalNext()"
                    class="absolute right-4 top-1/2 -translate-y-1/2 p-3 rounded-full bg-slate-900/75 hover:bg-slate-900 text-white backdrop-blur-md border border-white/20 shadow-lg transition-all hover:scale-110 active:scale-95 z-20 cursor-pointer" 
                    title="Slide Berikutnya (Panah Kanan)">
                <i data-lucide="chevron-right" class="w-6 h-6"></i>
            </button>

            <!-- Bottom Slide Dots Indicator -->
            <div id="modal-dots-wrap" class="hidden absolute bottom-4 left-1/2 -translate-x-1/2 z-20 items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-950/70 backdrop-blur-md border border-white/10 max-w-[90%] overflow-x-auto no-scrollbar">
                <!-- Injected via JavaScript -->
            </div>
        </div>

        <!-- Right / Bottom: Artwork Details, Slide Gallery Thumbnails & WhatsApp Inquire -->
        <div class="w-full lg:w-96 p-6 sm:p-8 flex flex-col justify-between bg-white overflow-y-auto border-t lg:border-t-0 lg:border-l border-slate-100">
            <div>
                <!-- Top Row: Category Badge & Close Button -->
                <div class="flex items-center justify-between mb-4">
                    <span id="modal-category" 
                          class="px-3.5 py-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-800 text-xs font-bold tracking-wide">
                        Category
                    </span>
                    <button id="modal-close" type="button" onclick="closePortfolioModal()"
                            class="p-2 rounded-full text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-colors cursor-pointer"
                            title="Tutup Modal (Esc)">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Artwork Title -->
                <h3 id="modal-title" class="text-xl sm:text-2xl font-black text-slate-900 leading-tight mb-3">
                    Artwork Title
                </h3>

                <!-- Description -->
                <p id="modal-description" class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-6 font-normal">
                    Artwork detailed description.
                </p>

                <!-- Mini Thumbnail Grid for Multi-Slide Carousels -->
                <div id="modal-thumbs-section" class="hidden mb-6">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                        Pilih Slide Desain
                    </label>
                    <div id="modal-thumbnails-grid" class="grid grid-cols-4 gap-2">
                        <!-- Populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Call to Action: WhatsApp Order / Inquire for Similar Work -->
            <div class="pt-6 mt-6 border-t border-slate-100">
                <a id="modal-inquire" 
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

<!-- ==========================================================
     BACK TO TOP FLOATING BUTTON
     ========================================================== -->
<button id="back-to-top" type="button" 
        class="fixed bottom-6 right-6 p-3 rounded-2xl bg-slate-900 text-white shadow-xl opacity-0 pointer-events-none transition-all duration-300 hover:bg-indigo-600 hover:scale-105 active:scale-95 z-40"
        title="Kembali ke atas">
    <i data-lucide="arrow-up" class="w-5 h-5"></i>
</button>

<!-- ==========================================================
     EXTERNAL JAVASCRIPT (STRICT ROOT-RELATIVE PATH)
     ========================================================== -->
<script src="<?= asset_url('js/main.js') ?>"></script>

<!-- ==========================================================
     STANDALONE FAIL-SAFE MODAL & LIGHTBOX SCRIPT
     Self-contained: Runs independently of external JS/caching
     ========================================================== -->
<script>
(function() {
    'use strict';

    // Helper: Safely resolve relative/absolute image URLs without mixed content
    function resolveSafeUrl(url) {
        if (!url) return '';
        if (url.startsWith('http://') && window.location.protocol === 'https:') {
            return 'https://' + url.substring(7);
        }
        if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('data:')) {
            return url;
        }
        if (url.startsWith('/')) {
            return url;
        }
        const cleanPath = url.replace(/^\/+/, '');
        const base = window.BASE_URL || '/';
        return base.endsWith('/') ? base + cleanPath : base + '/' + cleanPath;
    }

    // State
    let currentProjectImages = [];
    let currentSlideIndex = 0;
    let currentVisibleIndex = 0;

    // Helper: Get Modal and Lightbox Elements (supports both IDs)
    function getModalEl() {
        return document.getElementById('portfolio-modal') || document.getElementById('artwork-lightbox');
    }
    function getImgEl() {
        return document.getElementById('modal-image') || document.getElementById('lightbox-img');
    }
    function getTitleEl() {
        return document.getElementById('modal-title') || document.getElementById('lightbox-title');
    }
    function getCatEl() {
        return document.getElementById('modal-category') || document.getElementById('lightbox-category');
    }
    function getDescEl() {
        return document.getElementById('modal-description') || document.getElementById('modal-desc') || document.getElementById('lightbox-desc');
    }
    function getInquireEl() {
        return document.getElementById('modal-inquire') || document.getElementById('modal-whatsapp') || document.getElementById('lightbox-inquire');
    }
    function getSlideCounterWrap() {
        return document.getElementById('modal-slide-counter-wrap') || document.getElementById('lightbox-slide-counter-wrap');
    }
    function getSlideCounter() {
        return document.getElementById('modal-slide-counter') || document.getElementById('lightbox-slide-counter');
    }
    function getDotsWrap() {
        return document.getElementById('modal-dots-wrap') || document.getElementById('lightbox-dots-wrap');
    }
    function getThumbsSection() {
        return document.getElementById('modal-thumbs-section') || document.getElementById('lightbox-thumbs-section');
    }
    function getThumbsGrid() {
        return document.getElementById('modal-thumbnails-grid') || document.getElementById('lightbox-thumbnails-grid');
    }
    function getPrevBtn() {
        return document.getElementById('modal-prev') || document.getElementById('lightbox-prev');
    }
    function getNextBtn() {
        return document.getElementById('modal-next') || document.getElementById('lightbox-next');
    }

    // Render single slide in active modal
    function renderModalSlide(slideIdx) {
        if (!currentProjectImages || currentProjectImages.length === 0) return;
        const totalSlides = currentProjectImages.length;
        currentSlideIndex = (slideIdx + totalSlides) % totalSlides;
        const currentImgUrl = currentProjectImages[currentSlideIndex];

        const imgEl = getImgEl();
        if (imgEl) {
            imgEl.style.opacity = '0.35';
            imgEl.style.transform = 'scale(0.97)';
            
            const temp = new Image();
            temp.src = currentImgUrl;
            temp.onload = () => {
                imgEl.src = currentImgUrl;
                imgEl.style.opacity = '1';
                imgEl.style.transform = 'scale(1)';
            };
            temp.onerror = () => {
                imgEl.src = currentImgUrl;
                imgEl.style.opacity = '1';
                imgEl.style.transform = 'scale(1)';
            };
        }

        // Slide Counter
        const counterWrap = getSlideCounterWrap();
        const counterEl = getSlideCounter();
        if (counterWrap && counterEl) {
            if (totalSlides > 1) {
                counterWrap.classList.remove('hidden');
                counterWrap.classList.add('flex');
                counterWrap.style.display = 'flex';
                counterEl.textContent = `Slide ${currentSlideIndex + 1} / ${totalSlides}`;
            } else {
                counterWrap.classList.add('hidden');
                counterWrap.classList.remove('flex');
                counterWrap.style.display = 'none';
            }
        }

        // Prev / Next Arrows
        const prevBtn = getPrevBtn();
        const nextBtn = getNextBtn();
        const visibleCards = Array.from(document.querySelectorAll('.portfolio-card:not(.hidden), .portfolio-item:not(.hidden)'));
        const hasMultiple = (visibleCards.length > 1 || totalSlides > 1);
        if (prevBtn) prevBtn.style.display = hasMultiple ? 'flex' : 'none';
        if (nextBtn) nextBtn.style.display = hasMultiple ? 'flex' : 'none';

        // Dots Indicator
        const dotsWrap = getDotsWrap();
        if (dotsWrap) {
            if (totalSlides > 1) {
                dotsWrap.classList.remove('hidden');
                dotsWrap.classList.add('flex');
                dotsWrap.style.display = 'flex';
                dotsWrap.innerHTML = '';
                for (let i = 0; i < totalSlides; i++) {
                    const dot = document.createElement('button');
                    dot.type = 'button';
                    dot.setAttribute('aria-label', `Pindah ke slide ${i + 1}`);
                    dot.className = i === currentSlideIndex 
                        ? 'slide-dot w-6 h-2 rounded-full bg-white transition-all duration-300 shadow-xs cursor-pointer'
                        : 'slide-dot w-2 h-2 rounded-full bg-white/40 hover:bg-white/70 transition-all duration-300 cursor-pointer';
                    dot.onclick = (e) => {
                        e.stopPropagation();
                        renderModalSlide(i);
                    };
                    dotsWrap.appendChild(dot);
                }
            } else {
                dotsWrap.classList.add('hidden');
                dotsWrap.classList.remove('flex');
                dotsWrap.style.display = 'none';
                dotsWrap.innerHTML = '';
            }
        }

        // Sidebar Thumbnails
        const thumbsSection = getThumbsSection();
        const thumbsGrid = getThumbsGrid();
        if (thumbsSection && thumbsGrid) {
            if (totalSlides > 1) {
                thumbsSection.classList.remove('hidden');
                thumbsSection.style.display = 'block';
                thumbsGrid.innerHTML = '';
                currentProjectImages.forEach((imgUrl, sIdx) => {
                    const thumbBtn = document.createElement('button');
                    thumbBtn.type = 'button';
                    thumbBtn.className = sIdx === currentSlideIndex
                        ? 'thumb-card relative rounded-lg overflow-hidden border-2 border-indigo-600 ring-2 ring-indigo-500/30 aspect-video cursor-pointer shadow-sm'
                        : 'thumb-card relative rounded-lg overflow-hidden border border-slate-200 opacity-60 hover:opacity-100 aspect-video cursor-pointer transition-opacity';
                    
                    const thumbImg = document.createElement('img');
                    thumbImg.src = imgUrl;
                    thumbImg.alt = `Slide ${sIdx + 1}`;
                    thumbImg.className = 'w-full h-full object-cover';

                    const badge = document.createElement('span');
                    badge.className = 'absolute bottom-0.5 right-0.5 px-1 rounded bg-black/70 text-white text-[8px] font-bold';
                    badge.textContent = `#${sIdx + 1}`;

                    thumbBtn.appendChild(thumbImg);
                    thumbBtn.appendChild(badge);
                    thumbBtn.onclick = (e) => {
                        e.stopPropagation();
                        renderModalSlide(sIdx);
                    };
                    thumbsGrid.appendChild(thumbBtn);
                });
            } else {
                thumbsSection.classList.add('hidden');
                thumbsSection.style.display = 'none';
                thumbsGrid.innerHTML = '';
            }
        }
    }

    // Open Modal
    function openPortfolioModal(target, event) {
        if (event && event.stopPropagation) {
            if (event.target && event.target.closest && event.target.closest('.no-lightbox, .no-modal')) {
                return;
            }
        }

        let card = null;
        let title = '';
        let category = '';
        let desc = '';
        let image = '';
        let images = [];

        if (typeof target === 'number' || (!isNaN(target) && typeof target === 'string' && target.trim() !== '')) {
            const idx = parseInt(target, 10);
            const cards = Array.from(document.querySelectorAll('.portfolio-card, .portfolio-item'));
            card = cards.find(c => c.getAttribute('data-index') == idx || c.getAttribute('data-id') == idx || c.getAttribute('data-portfolio-id') == idx) || cards[idx];
        } else if (target && target.nodeType === 1) {
            card = target.closest('.portfolio-card, .portfolio-item') || target;
        } else if (typeof target === 'object' && target !== null && !target.nodeType) {
            title = target.title || '';
            category = target.category || target.category_name || '';
            desc = target.description || target.desc || '';
            image = target.image || target.image_url || '';
            images = target.images || [];
        }

        if (card) {
            title = card.getAttribute('data-title') || card.dataset.title || '';
            category = card.getAttribute('data-category-name') || card.getAttribute('data-category') || card.dataset.categoryName || card.dataset.category || '';
            desc = card.getAttribute('data-desc') || card.dataset.desc || card.getAttribute('data-description') || '';
            image = card.getAttribute('data-image') || card.dataset.image || '';
            
            const rawImages = card.getAttribute('data-images') || card.dataset.images;
            if (rawImages) {
                try {
                    images = typeof rawImages === 'string' ? JSON.parse(rawImages) : rawImages;
                } catch (e) {
                    images = [];
                }
            }
        }

        // Normalize images
        let normalizedImages = [];
        if (Array.isArray(images) && images.length > 0) {
            normalizedImages = images.map(img => {
                if (typeof img === 'string') return resolveSafeUrl(img);
                if (img && typeof img.image_url === 'string') return resolveSafeUrl(img.image_url);
                if (img && typeof img.url === 'string') return resolveSafeUrl(img.url);
                return '';
            }).filter(url => url.length > 0);
        }
        if (normalizedImages.length === 0 && image) {
            normalizedImages.push(resolveSafeUrl(image));
        }
        if (normalizedImages.length === 0) {
            normalizedImages.push('/assets/images/sample_thumb_1.svg');
        }

        currentProjectImages = normalizedImages;
        currentSlideIndex = 0;

        const visibleCards = Array.from(document.querySelectorAll('.portfolio-card:not(.hidden), .portfolio-item:not(.hidden)'));
        currentVisibleIndex = card ? visibleCards.indexOf(card) : 0;
        if (currentVisibleIndex < 0) currentVisibleIndex = 0;

        const modal = getModalEl();
        if (!modal) {
            console.error('Portfolio modal not found');
            return;
        }

        const titleEl = getTitleEl();
        const catEl = getCatEl();
        const descEl = getDescEl();
        const inquireEl = getInquireEl();

        if (titleEl) titleEl.textContent = title || 'Artwork Showcase';
        if (catEl) catEl.textContent = category || 'Portfolio';
        if (descEl) descEl.textContent = desc || 'Desain grafis kustom dengan resolusi tinggi, tata letak presisi, dan visual hierarchy yang optimal.';

        if (inquireEl) {
            const rawPhone = inquireEl.getAttribute('data-phone') || '6287794297888';
            const msg = `Halo, saya tertarik dengan karya portofolio desain "${title}" (${category}). Boleh konsultasi untuk project desain serupa?`;
            inquireEl.href = `https://wa.me/${rawPhone}?text=${encodeURIComponent(msg)}`;
        }

        renderModalSlide(0);

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        requestAnimationFrame(() => {
            modal.classList.add('active');
            modal.style.opacity = '1';
            const content = modal.querySelector('.lightbox-content, .modal-content');
            if (content) {
                content.style.opacity = '1';
                content.style.transform = 'scale(1)';
            }
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        });
    }

    // Close Modal
    function closePortfolioModal() {
        const modal = getModalEl();
        if (!modal) return;
        modal.classList.remove('active');
        modal.style.opacity = '0';
        document.body.style.overflow = '';
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            modal.style.display = 'none';
            const imgEl = getImgEl();
            if (imgEl) imgEl.src = '';
        }, 200);
    }

    // Navigation Next / Prev
    function modalNext() {
        if (!currentProjectImages || currentProjectImages.length === 0) return;
        if (currentSlideIndex < currentProjectImages.length - 1) {
            renderModalSlide(currentSlideIndex + 1);
        } else {
            const visibleCards = Array.from(document.querySelectorAll('.portfolio-card:not(.hidden), .portfolio-item:not(.hidden)'));
            if (visibleCards.length > 1) {
                currentVisibleIndex = (currentVisibleIndex + 1) % visibleCards.length;
                openPortfolioModal(visibleCards[currentVisibleIndex]);
            } else if (currentProjectImages.length > 1) {
                renderModalSlide(0);
            }
        }
    }

    function modalPrev() {
        if (!currentProjectImages || currentProjectImages.length === 0) return;
        if (currentSlideIndex > 0) {
            renderModalSlide(currentSlideIndex - 1);
        } else {
            const visibleCards = Array.from(document.querySelectorAll('.portfolio-card:not(.hidden), .portfolio-item:not(.hidden)'));
            if (visibleCards.length > 1) {
                currentVisibleIndex = (currentVisibleIndex - 1 + visibleCards.length) % visibleCards.length;
                const prevCard = visibleCards[currentVisibleIndex];
                openPortfolioModal(prevCard);
                if (currentProjectImages && currentProjectImages.length > 1) {
                    renderModalSlide(currentProjectImages.length - 1);
                }
            } else if (currentProjectImages.length > 1) {
                renderModalSlide(currentProjectImages.length - 1);
            }
        }
    }

    // Category Filter Handler
    function filterCategory(category) {
        const cards = document.querySelectorAll('.portfolio-card, .portfolio-item');
        const buttons = document.querySelectorAll('.category-btn, .filter-btn');
        const counterEl = document.getElementById('visible-count');
        let visibleCount = 0;

        cards.forEach(card => {
            const cat = card.getAttribute('data-category');
            if (category === 'all' || !category || cat === category) {
                card.classList.remove('hidden');
                card.style.display = '';
                visibleCount++;
            } else {
                card.classList.add('hidden');
                card.style.display = 'none';
            }
        });

        if (counterEl) {
            counterEl.textContent = visibleCount;
        }

        buttons.forEach(btn => {
            const btnCat = btn.getAttribute('data-filter') || btn.getAttribute('data-category') || 'all';
            const badge = btn.querySelector('.filter-count');
            if (btnCat === category || (category === 'all' && (btnCat === 'all' || !btnCat))) {
                btn.classList.add('active', 'bg-slate-900', 'text-white', 'border-slate-900');
                btn.classList.remove('bg-white', 'text-slate-600', 'hover:bg-slate-100', 'border-slate-200/80');
                if (badge) badge.className = 'filter-count ml-1.5 px-2 py-0.5 rounded-full bg-white/20 text-white text-[11px] font-bold';
            } else {
                btn.classList.remove('active', 'bg-slate-900', 'text-white', 'border-slate-900');
                btn.classList.add('bg-white', 'text-slate-600', 'hover:bg-slate-100', 'border-slate-200/80');
                if (badge) badge.className = 'filter-count ml-1.5 px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[11px] font-bold';
            }
        });

        if (window.history && window.history.replaceState) {
            const url = new URL(window.location);
            if (category === 'all') {
                url.searchParams.delete('category');
            } else {
                url.searchParams.set('category', category);
            }
            window.history.replaceState({}, '', url);
        }
    }

    // Expose Global API for onclick and external scripts
    window.openPortfolioModal  = openPortfolioModal;
    window.closePortfolioModal = closePortfolioModal;
    window.openLightbox        = openPortfolioModal;
    window.closeLightbox       = closePortfolioModal;
    window.modalNext           = modalNext;
    window.modalPrev           = modalPrev;
    window.showNext            = modalNext;
    window.showPrev            = modalPrev;
    window.renderModalSlide    = renderModalSlide;
    window.filterPortfolio     = filterCategory;
    window.filterCategory      = filterCategory;

    // Direct event bindings on DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        // Init Lucide
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }

        // Category filter buttons
        document.querySelectorAll('.category-btn, .filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const cat = btn.getAttribute('data-filter') || btn.getAttribute('data-category') || 'all';
                filterCategory(cat);
            });
        });

        // Global Event Delegation for Portfolio Card Click
        document.addEventListener('click', (e) => {
            const card = e.target.closest('.portfolio-card, .portfolio-item');
            if (card && !e.target.closest('.no-lightbox, .no-modal')) {
                openPortfolioModal(card, e);
            }
        });

        // Modal backdrop click
        const modal = getModalEl();
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal || e.target.classList.contains('backdrop-blur-lg')) {
                    closePortfolioModal();
                }
            });
        }

        // Keyboard Shortcuts
        document.addEventListener('keydown', (e) => {
            const m = getModalEl();
            if (!m || m.classList.contains('hidden') || m.style.display === 'none') return;
            if (e.key === 'Escape') {
                closePortfolioModal();
            } else if (e.key === 'ArrowLeft') {
                modalPrev();
            } else if (e.key === 'ArrowRight') {
                modalNext();
            }
        });

        // Touch Swipe Navigation
        let touchStartX = 0;
        let touchEndX = 0;
        const img = getImgEl();
        const stage = img ? img.parentElement : null;
        if (stage) {
            stage.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });
            stage.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                if (touchEndX < touchStartX - 40) {
                    modalNext();
                } else if (touchEndX > touchStartX + 40) {
                    modalPrev();
                }
            }, { passive: true });
        }

        // Back to top button
        const backToTopBtn = document.getElementById('back-to-top');
        if (backToTopBtn) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 400) {
                    backToTopBtn.classList.remove('opacity-0', 'pointer-events-none');
                    backToTopBtn.classList.add('opacity-100', 'pointer-events-auto');
                } else {
                    backToTopBtn.classList.add('opacity-0', 'pointer-events-none');
                    backToTopBtn.classList.remove('opacity-100', 'pointer-events-auto');
                }
            });
            backToTopBtn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    });
})();
</script>
</body>
</html>
