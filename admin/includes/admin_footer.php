<?php
/**
 * Admin Panel Footer
 * Graphic Design Portfolio & CMS
 */

declare(strict_types=1);
?>
    </main>

    <!-- Admin Footer -->
    <footer class="mt-auto border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p>Admin CMS Panel • PHP 8.2 Modular Architecture</p>
            <p><?= e(get_setting('footer_credit', '© 2026 Dimas Arya. All rights reserved.')) ?></p>
        </div>
    </footer>

    <!-- Global Toast Alert Container -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

    <!-- Admin JavaScript -->
    <script src="<?= asset_url('js/admin.js') ?>"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
