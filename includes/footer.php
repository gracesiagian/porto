<?php
/**
 * Public Site Footer
 * Graphic Design Portfolio & CMS
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';

$designer_name = get_setting('designer_name', 'Dimas Arya');
$footer_credit = get_setting('footer_credit', '© 2026 Dimas Arya. All rights reserved.');
$twitter_url   = get_setting('twitter_url', 'https://twitter.com/');
$instagram_url = get_setting('instagram_url', 'https://instagram.com/');
$email_address = get_setting('email_address', 'dimas.design@example.com');
?>
    <!-- Public Footer -->
    <footer class="mt-auto border-t border-slate-200/80 bg-white/70 backdrop-blur-md py-10 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <!-- Left: Branding & Status -->
                <div class="flex items-center space-x-3 text-center md:text-left">
                    <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-sm tracking-wider">
                        <?= strtoupper(substr(e($designer_name), 0, 1)) ?>
                    </div>
                    <div>
                        <p class="font-bold text-slate-800 text-sm"><?= e($designer_name) ?></p>
                        <p class="text-xs text-slate-500"><?= e(get_setting('designer_role', 'Graphic & Visual Designer')) ?></p>
                    </div>
                </div>

                <!-- Center: Social / Contact Badges -->
                <div class="flex items-center space-x-2">
                    <?php if ($twitter_url): ?>
                    <a href="<?= e($twitter_url) ?>" target="_blank" rel="noopener noreferrer" 
                       class="p-2.5 rounded-full text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-all" title="Twitter / X">
                        <i data-lucide="twitter" class="w-4 h-4"></i>
                    </a>
                    <?php endif; ?>

                    <?php if ($instagram_url): ?>
                    <a href="<?= e($instagram_url) ?>" target="_blank" rel="noopener noreferrer" 
                       class="p-2.5 rounded-full text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-all" title="Instagram">
                        <i data-lucide="instagram" class="w-4 h-4"></i>
                    </a>
                    <?php endif; ?>

                    <?php if ($email_address): ?>
                    <a href="mailto:<?= e($email_address) ?>" 
                       class="p-2.5 rounded-full text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-all" title="Email Inquiries">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Right: Copyright & Admin Portal Lock -->
                <div class="flex items-center space-x-4 text-xs text-slate-500">
                    <span><?= e($footer_credit) ?></span>
                    <a href="<?= base_url('admin/login.php') ?>" 
                       class="text-slate-400 hover:text-slate-700 p-1.5 rounded-md hover:bg-slate-100 transition-colors inline-flex items-center gap-1" 
                       title="Admin Panel Login">
                        <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                        <span class="sr-only">Admin CMS</span>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating Back to Top Button -->
    <button id="back-to-top" type="button" 
            class="fixed bottom-6 right-6 z-40 p-3 bg-slate-900 text-white rounded-full shadow-lg hover:bg-slate-800 hover:scale-110 active:scale-95 transition-all duration-300 opacity-0 pointer-events-none"
            title="Scroll to Top">
        <i data-lucide="arrow-up" class="w-5 h-5"></i>
    </button>

    <!-- Public Scripts -->
    <script src="<?= asset_url('js/main.js') ?>"></script>
</body>
</html>
