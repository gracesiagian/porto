<?php
/**
 * Category Management (CRUD)
 * Graphic Design Portfolio & CMS
 */

declare(strict_types=1);

$page_title = 'Kelola Kategori Portofolio';
require_once __DIR__ . '/includes/admin_header.php';

$categories = get_categories();
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Kategori Portofolio</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola filter tab galeri seperti Thumbnail, Poster, Pricelist</p>
    </div>
    <button type="button" 
            data-open-modal="modal-add-category"
            class="px-5 py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2 active:scale-95 cursor-pointer">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Tambah Kategori Baru</span>
    </button>
</div>

<!-- Categories Table Card -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="py-4 px-6">Nama Kategori</th>
                    <th class="py-4 px-6">Slug URL</th>
                    <th class="py-4 px-6 text-center">Jumlah Karya</th>
                    <th class="py-4 px-6 text-center">Urutan Tab</th>
                    <th class="py-4 px-6 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                <?php foreach ($categories as $cat): ?>
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <!-- Category Name -->
                    <td class="py-4 px-6">
                        <span class="font-bold text-slate-900 flex items-center gap-2">
                            <i data-lucide="tag" class="w-4 h-4 text-indigo-500"></i>
                            <?= e($cat['name']) ?>
                        </span>
                    </td>

                    <!-- Slug -->
                    <td class="py-4 px-6">
                        <code class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 text-xs font-mono">
                            <?= e($cat['slug']) ?>
                        </code>
                    </td>

                    <!-- Count -->
                    <td class="py-4 px-6 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700">
                            <?= (int)$cat['item_count'] ?> Karya
                        </span>
                    </td>

                    <!-- Display Order -->
                    <td class="py-4 px-6 text-center font-bold text-slate-700">
                        <?= (int)$cat['display_order'] ?>
                    </td>

                    <!-- Actions -->
                    <td class="py-4 px-6 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <button type="button" 
                                    class="p-2 rounded-xl text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 transition-colors btn-edit-category"
                                    data-id="<?= $cat['id'] ?>"
                                    data-name="<?= e($cat['name']) ?>"
                                    data-slug="<?= e($cat['slug']) ?>"
                                    data-order="<?= $cat['display_order'] ?>">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>

                            <form action="<?= base_url('admin/categories_action.php') ?>" method="POST" class="inline-block" 
                                  onsubmit="return confirm('Hapus kategori ini? Semua karya di dalam kategori ini juga akan terhapus.');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ==========================================================
     MODAL: TAMBAH KATEGORI BARU
     ========================================================== -->
<div id="modal-add-category" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-lg font-black text-slate-900 tracking-tight">Tambah Kategori Baru</h3>
            <button type="button" data-close-modal="modal-add-category" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl hover:bg-slate-100">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="<?= base_url('admin/categories_action.php') ?>" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="action" value="create">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div>
                <label for="cat_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="cat_name" required
                       placeholder="Contoh: Social Media Kit"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <label for="cat_slug" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Slug URL (Opsional, otomatis digenerate)
                </label>
                <input type="text" name="slug" id="cat_slug"
                       placeholder="contoh: social-media-kit"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <label for="cat_order" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Urutan Posisi Tab
                </label>
                <input type="number" name="display_order" id="cat_order" value="1" min="0"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" data-close-modal="modal-add-category"
                        class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition-all">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==========================================================
     MODAL: EDIT KATEGORI
     ========================================================== -->
<div id="modal-edit-category" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-lg font-black text-slate-900 tracking-tight">Edit Kategori</h3>
            <button type="button" data-close-modal="modal-edit-category" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl hover:bg-slate-100">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="<?= base_url('admin/categories_action.php') ?>" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_cat_id" value="">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div>
                <label for="edit_cat_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="edit_cat_name" required
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <label for="edit_cat_slug" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Slug URL
                </label>
                <input type="text" name="slug" id="edit_cat_slug" required
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <label for="edit_cat_order" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Urutan Posisi Tab
                </label>
                <input type="number" name="display_order" id="edit_cat_order" min="0"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" data-close-modal="modal-edit-category"
                        class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const editButtons = document.querySelectorAll('.btn-edit-category');
        const editModal = document.getElementById('modal-edit-category');

        editButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('edit_cat_id').value = btn.getAttribute('data-id');
                document.getElementById('edit_cat_name').value = btn.getAttribute('data-name');
                document.getElementById('edit_cat_slug').value = btn.getAttribute('data-slug');
                document.getElementById('edit_cat_order').value = btn.getAttribute('data-order');

                if (editModal) {
                    editModal.classList.remove('hidden');
                    editModal.classList.add('flex');
                }
            });
        });
    });
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
