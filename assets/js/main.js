/**
 * Main Client-Side JavaScript
 * Aesthetic Graphic Design Portfolio & Multi-Slide Carousel Lightbox
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Lucide Icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // --- Helper function to resolve upload URL ---
    function resolveUploadUrl(url) {
        if (!url) return '';
        if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('data:')) {
            return url;
        }
        const cleanPath = url.replace(/^\/+/, '');
        // Determine site root
        const loc = window.location.pathname;
        const baseDir = loc.substring(0, loc.lastIndexOf('/') + 1);
        return baseDir + cleanPath;
    }

    // 2. Category Filtering Logic
    const filterButtons = document.querySelectorAll('.filter-btn');
    const portfolioCards = document.querySelectorAll('.portfolio-card');
    const itemsCountEl = document.getElementById('visible-count');

    function filterPortfolio(category) {
        let visibleCount = 0;

        portfolioCards.forEach(card => {
            const cardCategory = card.getAttribute('data-category');
            if (category === 'all' || cardCategory === category) {
                card.classList.remove('hidden');
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'scale(1)';
                }, 50);
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        if (itemsCountEl) {
            itemsCountEl.textContent = visibleCount;
        }
        
        refreshPortfolioData();
    }

    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            filterButtons.forEach(b => {
                b.classList.remove('active', 'bg-slate-900', 'text-white');
                b.classList.add('bg-white', 'text-slate-600', 'hover:bg-slate-100');
            });
            btn.classList.add('active', 'bg-slate-900', 'text-white');
            btn.classList.remove('bg-white', 'text-slate-600', 'hover:bg-slate-100');

            const category = btn.getAttribute('data-filter');
            filterPortfolio(category);
        });
    });

    // 3. Multi-Slide Carousel Lightbox Management
    const lightbox = document.getElementById('artwork-lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxTitle = document.getElementById('lightbox-title');
    const lightboxCat = document.getElementById('lightbox-category');
    const lightboxDesc = document.getElementById('lightbox-desc');
    const lightboxInquire = document.getElementById('lightbox-inquire');
    const lightboxClose = document.getElementById('lightbox-close');
    const lightboxPrev = document.getElementById('lightbox-prev');
    const lightboxNext = document.getElementById('lightbox-next');
    const slideCounterWrap = document.getElementById('lightbox-slide-counter-wrap');
    const slideCounter = document.getElementById('lightbox-slide-counter');
    const dotsWrap = document.getElementById('lightbox-dots-wrap');
    const thumbsSection = document.getElementById('lightbox-thumbs-section');
    const thumbsGrid = document.getElementById('lightbox-thumbnails-grid');

    // Collect all active portfolio items from DOM
    let portfolioData = [];
    let currentProjectIdx = -1;
    let currentSlideIdx = 0;

    function refreshPortfolioData() {
        portfolioData = [];
        const activeCards = Array.from(portfolioCards).filter(card => !card.classList.contains('hidden'));
        
        activeCards.forEach((card, idx) => {
            card.setAttribute('data-visible-idx', idx);

            // Parse images
            let rawImages = [];
            try {
                const attrVal = card.getAttribute('data-images');
                if (attrVal) rawImages = JSON.parse(attrVal);
            } catch (e) {
                rawImages = [];
            }

            let normalizedImages = [];
            if (Array.isArray(rawImages) && rawImages.length > 0) {
                normalizedImages = rawImages.map(img => {
                    if (typeof img === 'string') return resolveUploadUrl(img);
                    if (img && typeof img.image_url === 'string') return resolveUploadUrl(img.image_url);
                    return '';
                }).filter(url => url.length > 0);
            }

            if (normalizedImages.length === 0) {
                const singleImg = card.getAttribute('data-image');
                if (singleImg) normalizedImages.push(resolveUploadUrl(singleImg));
            }

            portfolioData.push({
                index: idx,
                title: card.getAttribute('data-title') || '',
                category: card.getAttribute('data-category-name') || '',
                desc: card.getAttribute('data-desc') || '',
                images: normalizedImages,
                element: card
            });
        });
    }

    function renderSlide(slideIdx) {
        if (currentProjectIdx < 0 || currentProjectIdx >= portfolioData.length) return;
        const project = portfolioData[currentProjectIdx];
        const totalSlides = project.images.length;
        if (totalSlides === 0) return;

        // Ensure within bounds
        currentSlideIdx = (slideIdx + totalSlides) % totalSlides;
        const currentImgUrl = project.images[currentSlideIdx];

        // Animate image transition
        if (lightboxImg) {
            lightboxImg.style.opacity = '0.35';
            lightboxImg.style.transform = 'scale(0.97)';
            
            const tempImg = new Image();
            tempImg.src = currentImgUrl;
            tempImg.onload = () => {
                lightboxImg.src = currentImgUrl;
                lightboxImg.style.opacity = '1';
                lightboxImg.style.transform = 'scale(1)';
            };
            tempImg.onerror = () => {
                lightboxImg.src = currentImgUrl;
                lightboxImg.style.opacity = '1';
                lightboxImg.style.transform = 'scale(1)';
            };
        }

        // Update counter
        if (slideCounter) {
            slideCounter.textContent = `Slide ${currentSlideIdx + 1} / ${totalSlides}`;
        }

        // Update active dot in bottom indicator
        if (dotsWrap) {
            const allDots = dotsWrap.querySelectorAll('.slide-dot');
            allDots.forEach((dot, dIdx) => {
                if (dIdx === currentSlideIdx) {
                    dot.className = 'slide-dot w-6 h-2 rounded-full bg-white transition-all duration-300 shadow-xs cursor-pointer';
                } else {
                    dot.className = 'slide-dot w-2 h-2 rounded-full bg-white/40 hover:bg-white/70 transition-all duration-300 cursor-pointer';
                }
            });
        }

        // Update active thumbnail in sidebar grid
        if (thumbsGrid) {
            const allThumbCards = thumbsGrid.querySelectorAll('.thumb-card');
            allThumbCards.forEach((tc, tIdx) => {
                if (tIdx === currentSlideIdx) {
                    tc.className = 'thumb-card relative rounded-lg overflow-hidden border-2 border-indigo-600 ring-2 ring-indigo-500/30 aspect-video cursor-pointer shadow-sm';
                } else {
                    tc.className = 'thumb-card relative rounded-lg overflow-hidden border border-slate-200 opacity-60 hover:opacity-100 aspect-video cursor-pointer transition-opacity';
                }
            });
        }
    }

    function openLightbox(projectIdx, startSlide = 0) {
        refreshPortfolioData();
        if (projectIdx < 0 || projectIdx >= portfolioData.length) return;

        currentProjectIdx = projectIdx;
        const project = portfolioData[currentProjectIdx];
        const totalSlides = project.images.length;
        const validStartSlide = Math.min(Math.max(0, startSlide), Math.max(0, totalSlides - 1));

        if (lightboxTitle) lightboxTitle.textContent = project.title;
        if (lightboxCat) lightboxCat.textContent = project.category;
        if (lightboxDesc) lightboxDesc.textContent = project.desc || 'Desain grafis kustom dengan resolusi tinggi, tata letak presisi, dan visual hierarchy yang optimal.';

        // WhatsApp Inquire Link with project title
        if (lightboxInquire) {
            const rawPhone = lightboxInquire.getAttribute('data-phone') || '6281234567890';
            const msg = `Halo Dimas, saya tertarik dengan portofolio desain "${project.title}" (${project.category}). Boleh konsultasi untuk project desain serupa?`;
            lightboxInquire.href = `https://wa.me/${rawPhone}?text=${encodeURIComponent(msg)}`;
        }

        // Always show next/prev arrows if more than 1 project exists OR more than 1 slide in current project
        const hasMultiple = (portfolioData.length > 1 || totalSlides > 1);
        if (lightboxPrev) {
            if (hasMultiple) lightboxPrev.classList.remove('hidden');
            else lightboxPrev.classList.add('hidden');
        }
        if (lightboxNext) {
            if (hasMultiple) lightboxNext.classList.remove('hidden');
            else lightboxNext.classList.add('hidden');
        }

        // Handle multi-slide vs single-slide controls
        if (totalSlides > 1) {
            if (slideCounterWrap) {
                slideCounterWrap.classList.remove('hidden');
                slideCounterWrap.classList.add('flex');
            }

            // Build Bottom Dots
            if (dotsWrap) {
                dotsWrap.classList.remove('hidden');
                dotsWrap.classList.add('flex');
                dotsWrap.innerHTML = '';
                for (let i = 0; i < totalSlides; i++) {
                    const dot = document.createElement('button');
                    dot.type = 'button';
                    dot.setAttribute('aria-label', `Pindah ke slide ${i + 1}`);
                    dot.className = i === validStartSlide 
                        ? 'slide-dot w-6 h-2 rounded-full bg-white transition-all duration-300 shadow-xs cursor-pointer'
                        : 'slide-dot w-2 h-2 rounded-full bg-white/40 hover:bg-white/70 transition-all duration-300 cursor-pointer';
                    dot.addEventListener('click', (e) => {
                        e.stopPropagation();
                        renderSlide(i);
                    });
                    dotsWrap.appendChild(dot);
                }
            }

            // Build Sidebar Thumbnails
            if (thumbsSection && thumbsGrid) {
                thumbsSection.classList.remove('hidden');
                thumbsGrid.innerHTML = '';
                project.images.forEach((imgUrl, sIdx) => {
                    const thumbBtn = document.createElement('button');
                    thumbBtn.type = 'button';
                    thumbBtn.className = sIdx === validStartSlide
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
                    thumbBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        renderSlide(sIdx);
                    });
                    thumbsGrid.appendChild(thumbBtn);
                });
            }
        } else {
            // Single slide only
            if (slideCounterWrap) {
                slideCounterWrap.classList.add('hidden');
                slideCounterWrap.classList.remove('flex');
            }
            if (dotsWrap) {
                dotsWrap.classList.add('hidden');
                dotsWrap.classList.remove('flex');
                dotsWrap.innerHTML = '';
            }
            if (thumbsSection) {
                thumbsSection.classList.add('hidden');
            }
        }

        renderSlide(validStartSlide);

        if (lightbox) {
            lightbox.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                lightbox.classList.add('active');
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }, 10);
        }
    }

    function closeLightbox() {
        if (!lightbox) return;
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
        setTimeout(() => {
            lightbox.classList.add('hidden');
            if (lightboxImg) lightboxImg.src = '';
        }, 200);
    }

    function showPrev() {
        if (portfolioData.length === 0 || currentProjectIdx < 0) return;
        const project = portfolioData[currentProjectIdx];
        const totalSlides = project.images.length;
        
        // If current project has previous slides, move to previous slide
        if (currentSlideIdx > 0) {
            renderSlide(currentSlideIdx - 1);
        } else {
            // Reached first slide of current project -> navigate to previous project's last slide
            if (portfolioData.length > 1) {
                const prevProjectIdx = (currentProjectIdx - 1 + portfolioData.length) % portfolioData.length;
                const prevProject = portfolioData[prevProjectIdx];
                const prevLastSlide = Math.max(0, (prevProject.images ? prevProject.images.length : 1) - 1);
                openLightbox(prevProjectIdx, prevLastSlide);
            } else if (totalSlides > 1) {
                renderSlide(totalSlides - 1);
            }
        }
    }

    function showNext() {
        if (portfolioData.length === 0 || currentProjectIdx < 0) return;
        const project = portfolioData[currentProjectIdx];
        const totalSlides = project.images.length;

        // If current project has more slides ahead, move to next slide
        if (currentSlideIdx < totalSlides - 1) {
            renderSlide(currentSlideIdx + 1);
        } else {
            // Reached last slide of current project -> navigate to next project's first slide
            if (portfolioData.length > 1) {
                const nextProjectIdx = (currentProjectIdx + 1) % portfolioData.length;
                openLightbox(nextProjectIdx, 0);
            } else if (totalSlides > 1) {
                renderSlide(0);
            }
        }
    }

    // Attach click listeners to cards
    portfolioCards.forEach(card => {
        card.addEventListener('click', (e) => {
            if (e.target.closest('.no-lightbox')) return;
            
            refreshPortfolioData();
            const visibleIdx = parseInt(card.getAttribute('data-visible-idx') || '0', 10);
            openLightbox(visibleIdx, 0);
        });
    });

    if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
    if (lightboxPrev) lightboxPrev.addEventListener('click', (e) => { e.stopPropagation(); showPrev(); });
    if (lightboxNext) lightboxNext.addEventListener('click', (e) => { e.stopPropagation(); showNext(); });

    // Close on backdrop click
    if (lightbox) {
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox || e.target.classList.contains('lightbox-backdrop')) {
                closeLightbox();
            }
        });
    }

    // Touch Swipe Navigation for Mobile Lightbox
    let touchStartX = 0;
    let touchEndX = 0;
    const stageContainer = lightboxImg ? lightboxImg.parentElement : null;

    if (stageContainer) {
        stageContainer.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        stageContainer.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });
    }

    function handleSwipe() {
        const threshold = 40;
        if (touchEndX < touchStartX - threshold) {
            showNext();
        } else if (touchEndX > touchStartX + threshold) {
            showPrev();
        }
    }

    // Keyboard Shortcuts (Esc, Arrows)
    document.addEventListener('keydown', (e) => {
        if (!lightbox || lightbox.classList.contains('hidden')) return;

        if (e.key === 'Escape') {
            closeLightbox();
        } else if (e.key === 'ArrowLeft') {
            showPrev();
        } else if (e.key === 'ArrowRight') {
            showNext();
        }
    });

    // 4. Smooth Scroll from Landing Pill
    const scrollTrigger = document.querySelector('a[href="#portfolio-gallery"]');
    if (scrollTrigger) {
        scrollTrigger.addEventListener('click', (e) => {
            e.preventDefault();
            const gallery = document.getElementById('portfolio-gallery');
            if (gallery) {
                gallery.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // 5. Back to Top Button
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

    // Initial data index build
    refreshPortfolioData();
});
