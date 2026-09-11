<?php
/**
 * Portfolio Management (CRUD)
 * Graphic Design Portfolio & CMS
 */

declare(strict_types=1);

$page_title = 'Kelola Karya Portofolio';
require_once __DIR__ . '/includes/admin_header.php';

$db = get_db();
$categories = get_categories();
$portfolio_items = get_portfolio_items(null, false); // Fetch all including inactive and multi-images
$total_count = count($portfolio_items);

// Calculate dynamic next display order for new artwork
$max_display_order = 0;
foreach ($portfolio_items as $item) {
    $order_val = (int)($item['display_order'] ?? 0);
    if ($order_val > $max_display_order) {
        $max_display_order = $order_val;
    }
}
$next_display_order = max($total_count + 1, $max_display_order + 1);
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Karya Portofolio</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola, upload multi-gambar / carousel, edit dan atur urutan tampilan karya desain grafis</p>
    </div>
    <button type="button" 
            data-open-modal="modal-add-portfolio"
            class="px-5 py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2 active:scale-95 cursor-pointer">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Upload Karya Baru</span>
    </button>
</div>

<!-- Filters & Search Bar -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-4 sm:p-6 mb-8">
    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        
        <!-- Search Input -->
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i data-lucide="search" class="w-4 h-4"></i>
            </div>
            <input type="text" id="admin-search-input" 
                   placeholder="Cari judul desain..." 
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
        </div>

        <!-- Category Dropdown Filter -->
        <div class="flex items-center gap-3">
            <select id="admin-category-filter" 
                    class="py-2.5 pl-3.5 pr-8 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                <option value="all">Semua Kategori</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= e($cat['slug']) ?>"><?= e($cat['name']) ?> (<?= $cat['item_count'] ?>)</option>
                <?php endforeach; ?>
            </select>

            <span class="text-xs font-semibold text-slate-500 whitespace-nowrap bg-slate-100 px-3 py-2 rounded-xl">
                <span id="admin-item-count" class="font-bold text-slate-900"><?= $total_count ?></span> karya
            </span>
        </div>
    </div>
</div>

<!-- Portfolio Items Table -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="py-4 px-6">Preview</th>
                    <th class="py-4 px-6">Judul & Kategori</th>
                    <th class="py-4 px-6 text-center">Urutan</th>
                    <th class="py-4 px-6 text-center">Status</th>
                    <th class="py-4 px-6 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                <?php if (empty($portfolio_items)): ?>
                <tr>
                    <td colspan="5" class="text-center py-12 text-slate-400">
                        <i data-lucide="image-off" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                        <p class="font-semibold text-slate-600">Belum ada karya portofolio.</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($portfolio_items as $item): ?>
                <tr class="portfolio-row hover:bg-slate-50/80 transition-colors"
                    data-id="<?= $item['id'] ?>"
                    data-title="<?= e($item['title']) ?>"
                    data-category="<?= e($item['category_slug']) ?>">
                    
                    <!-- Artwork Thumbnail & Slide Indicator -->
                    <td class="py-4 px-6">
                        <div class="relative w-20 h-14 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shadow-2xs flex-shrink-0 group">
                            <img src="<?= upload_url($item['image_url']) ?>" 
                                 alt="<?= e($item['title']) ?>" 
                                 class="w-full h-full object-cover">
                            
                            <?php if (!empty($item['image_count']) && $item['image_count'] > 1): ?>
                            <span class="absolute bottom-1 right-1 px-1.5 py-0.5 rounded-md bg-slate-950/80 backdrop-blur-xs text-white text-[10px] font-black flex items-center gap-0.5 shadow-xs">
                                <i data-lucide="layers" class="w-3 h-3"></i>
                                <?= $item['image_count'] ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </td>

                    <!-- Title & Category -->
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900 block leading-snug line-clamp-1">
                                <?= e($item['title']) ?>
                            </span>
                            <?php if (!empty($item['image_count']) && $item['image_count'] > 1): ?>
                            <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-200 whitespace-nowrap">
                                Carousel (<?= $item['image_count'] ?> Slide)
                            </span>
                            <?php endif; ?>
                        </div>
                        <span class="inline-block mt-1 px-2.5 py-0.5 rounded-md bg-indigo-50 text-indigo-700 text-[10px] font-bold border border-indigo-100">
                            <?= e($item['category_name']) ?>
                        </span>
                    </td>

                    <!-- Display Order -->
                    <td class="py-4 px-6 text-center">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 text-slate-700 font-bold text-xs">
                            <?= (int)$item['display_order'] ?>
                        </span>
                    </td>

                    <!-- Visibility Toggle -->
                    <td class="py-4 px-6 text-center">
                        <form action="<?= base_url('admin/portfolio_action.php') ?>" method="POST" class="inline-block">
                            <input type="hidden" name="action" value="toggle_status">
                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <button type="submit" 
                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold transition-all <?= $item['is_active'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border border-slate-200 hover:bg-slate-200' ?>" 
                                    title="Klik untuk ubah status visibilitas">
                                <span class="w-1.5 h-1.5 rounded-full <?= $item['is_active'] ? 'bg-emerald-500' : 'bg-slate-400' ?>"></span>
                                <?= $item['is_active'] ? 'Tampil' : 'Draft' ?>
                            </button>
                        </form>
                    </td>

                    <!-- Actions (Edit / Delete) -->
                    <td class="py-4 px-6 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <button type="button" 
                                    class="btn-edit-portfolio p-2 rounded-xl text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
                                    title="Edit Karya"
                                    data-id="<?= $item['id'] ?>"
                                    data-title="<?= e($item['title']) ?>"
                                    data-category-id="<?= $item['category_id'] ?>"
                                    data-desc="<?= e($item['description'] ?? '') ?>"
                                    data-order="<?= $item['display_order'] ?>"
                                    data-active="<?= $item['is_active'] ?>"
                                    data-image="<?= upload_url($item['image_url']) ?>"
                                    data-images='<?= htmlspecialchars(json_encode($item['images'] ?? []), ENT_QUOTES, 'UTF-8') ?>'>
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>

                            <button type="button" 
                                    class="btn-delete-portfolio p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                    title="Hapus Karya"
                                    data-id="<?= $item['id'] ?>"
                                    data-title="<?= e($item['title']) ?>">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ==========================================================
     MODAL: TAMBAH KARYA PORTOFOLIO BARU (MULTI-IMAGE SUPPORT)
     ========================================================== -->
<div id="modal-add-portfolio" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm overflow-y-auto">
    <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden my-8">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-slate-50/50">
            <div>
                <h3 class="text-lg font-black text-slate-900 tracking-tight">Upload Karya Portofolio Baru</h3>
                <p class="text-xs text-slate-500 mt-0.5">Unggah 1 atau beberapa gambar (carousel / multi-slide) untuk karya Anda</p>
            </div>
            <button type="button" data-close-modal="modal-add-portfolio" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl hover:bg-slate-100">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Modal Form -->
        <form action="<?= base_url('admin/portfolio_action.php') ?>" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-5">
            <input type="hidden" name="action" value="create">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <!-- Multiple Image File Upload with Live Previews -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    File Gambar Desain <span class="text-rose-500">*</span> 
                    <span class="text-[11px] font-normal text-slate-400 normal-case">(Bisa pilih lebih dari 1 gambar sekaligus)</span>
                </label>
                
                <div class="border-2 border-dashed border-slate-200 hover:border-indigo-400 transition-colors bg-slate-50/50 rounded-2xl p-4 text-center relative cursor-pointer">
                    <input type="file" name="images[]" id="add_image_input" multiple required accept=".jpg,.jpeg,.png,.webp,.svg"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    
                    <!-- Placeholder state -->
                    <div id="add_placeholder" class="upload-placeholder flex flex-col items-center justify-center py-5">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-3">
                            <i data-lucide="upload-cloud" class="w-6 h-6"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-800">Klik atau drag & drop file artwork</p>
                        <p class="text-xs text-slate-400 mt-1">Format: JPG, PNG, WEBP, SVG (Maks. 10MB/file)</p>
                        <span class="mt-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-[11px] font-semibold">
                            <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                            Mendukung Multiple Upload (Slide/Carousel)
                        </span>
                    </div>

                    <!-- Multi-Image Live Preview Grid -->
                    <div id="add_preview_container" class="hidden text-left">
                        <div class="flex items-center justify-between mb-3 px-1">
                            <span id="add_preview_badge" class="text-xs font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-100">
                                0 Gambar Terpilih
                            </span>
                            <span class="text-[11px] font-medium text-slate-400">Klik area untuk mengganti file</span>
                        </div>
                        <div id="add_preview_grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5 max-h-56 overflow-y-auto p-1 bg-white rounded-xl border border-slate-100">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Title & Category Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="add_title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Judul Karya <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" id="add_title" required
                           placeholder="Contoh: Gaming YouTube Thumbnail"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label for="add_category_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kategori <span class="text-rose-500">*</span>
                    </label>
                    <select name="category_id" id="add_category_id" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none cursor-pointer">
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="add_description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Deskripsi / Catatan Desain
                </label>
                <textarea name="description" id="add_description" rows="3"
                          placeholder="Jelaskan konsep, target audience, atau visual hierarchy desain ini..."
                          class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
            </div>

            <!-- Display Order & Active Checkbox -->
            <div class="flex items-center justify-between pt-2">
                <div class="flex items-center gap-2">
                    <label for="add_display_order" class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Urutan Tampil:
                    </label>
                    <input type="number" name="display_order" id="add_display_order" value="<?= $next_display_order ?>" min="0"
                           class="w-20 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm text-center font-bold">
                    <span class="text-[11px] text-slate-400 font-medium">(Otomatis: urutan ke-<?= $next_display_order ?>)</span>
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked
                           class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                    <span class="text-xs font-bold text-slate-700">Langsung Tampilkan di Portofolio</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" data-close-modal="modal-add-portfolio"
                        class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition-all">
                    Upload Artwork
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==========================================================
     MODAL: EDIT KARYA PORTOFOLIO & KELOLA MULTI-GAMBAR
     ========================================================== -->
<div id="modal-edit-portfolio" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm overflow-y-auto">
    <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden my-8">
        
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-slate-50/50">
            <div>
                <h3 class="text-lg font-black text-slate-900 tracking-tight">Edit Karya Portofolio</h3>
                <p class="text-xs text-slate-500 mt-0.5">Perbarui informasi karya, kelola galeri gambar atau tambah slide baru</p>
            </div>
            <button type="button" data-close-modal="modal-edit-portfolio" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl hover:bg-slate-100">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Form -->
        <form action="<?= base_url('admin/portfolio_action.php') ?>" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-5">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_item_id" value="">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <!-- Existing Images Gallery -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Galeri Gambar Saat Ini
                    </label>
                    <span id="edit_existing_count_badge" class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full">
                        0 Slide
                    </span>
                </div>
                <div id="edit_existing_images_grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-200 min-h-[90px] max-h-56 overflow-y-auto">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>

            <!-- Upload Additional / New Images (Append) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Tambah Gambar / Slide Baru <span class="text-indigo-600 font-semibold normal-case">(Otomatis di-append ke karya)</span>
                </label>
                <div class="border-2 border-dashed border-indigo-200 hover:border-indigo-400 bg-indigo-50/20 hover:bg-indigo-50/40 transition-colors rounded-2xl p-4 text-center relative cursor-pointer">
                    <input type="file" name="images[]" id="edit_image_input" multiple accept=".jpg,.jpeg,.png,.webp,.svg"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    
                    <div id="edit_placeholder" class="flex flex-col items-center justify-center py-2">
                        <i data-lucide="plus-circle" class="w-6 h-6 text-indigo-500 mb-1"></i>
                        <p class="text-xs font-bold text-indigo-600">Klik untuk memilih gambar baru</p>
                        <p class="text-[11px] text-slate-400">Gambar baru akan di-append ke galeri karya tanpa menghapus gambar lama</p>
                    </div>

                    <div id="edit_preview_container" class="hidden text-left">
                        <div class="flex items-center justify-between mb-2 px-1">
                            <span id="edit_preview_badge" class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-100">
                                0 Gambar Baru Dipilih
                            </span>
                            <span class="text-[11px] text-slate-400">Akan ditambahkan ke galeri di atas</span>
                        </div>
                        <div id="edit_preview_grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 max-h-40 overflow-y-auto p-1 bg-white rounded-xl border border-slate-100">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Title & Category Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="edit_title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Judul Karya <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" id="edit_title" required
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label for="edit_category_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kategori <span class="text-rose-500">*</span>
                    </label>
                    <select name="category_id" id="edit_category_id" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none cursor-pointer">
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="edit_description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Deskripsi
                </label>
                <textarea name="description" id="edit_description" rows="3"
                          class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
            </div>

            <!-- Display Order & Active Checkbox -->
            <div class="flex items-center justify-between pt-2">
                <div class="flex items-center gap-2">
                    <label for="edit_display_order" class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Urutan Tampil:
                    </label>
                    <input type="number" name="display_order" id="edit_display_order" min="0"
                           class="w-20 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm text-center font-bold">
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1"
                           class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                    <span class="text-xs font-bold text-slate-700">Tampilkan di Portofolio</span>
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" data-close-modal="modal-edit-portfolio"
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

<!-- ==========================================================
     MODAL: KONFIRMASI HAPUS KARYA
     ========================================================== -->
<div id="modal-delete-confirm" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
    <div class="w-full max-w-md bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-100 text-center">
        <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-4 border border-rose-100">
            <i data-lucide="alert-triangle" class="w-7 h-7"></i>
        </div>
        <h3 class="text-lg font-black text-slate-900">Hapus Karya Ini?</h3>
        <p class="text-xs text-slate-500 mt-2">
            Anda akan menghapus karya <strong id="delete_item_title" class="text-slate-900"></strong> beserta seluruh slide gambarnya secara permanen. Tindakan ini tidak dapat dibatalkan.
        </p>

        <form action="<?= base_url('admin/portfolio_action.php') ?>" method="POST" class="mt-6 flex items-center justify-center gap-3">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" id="delete_item_id" value="">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <button type="button" data-close-modal="modal-delete-confirm" 
                    class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                Batal
            </button>
            <button type="submit" 
                    class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md shadow-rose-600/20 transition-all">
                Ya, Hapus Sekarang
            </button>
        </form>
    </div>
</div>

<!-- Form tersembunyi untuk delete individual image -->
<form id="delete-single-image-form" action="<?= base_url('admin/portfolio_action.php') ?>" method="POST" class="hidden">
    <input type="hidden" name="action" value="delete_image">
    <input type="hidden" name="image_id" id="del_single_image_id" value="">
    <input type="hidden" name="portfolio_id" id="del_single_portfolio_id" value="">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
</form>

<?php if (isset($_GET['action']) && $_GET['action'] === 'add'): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const addModal = document.getElementById('modal-add-portfolio');
        if (addModal) {
            addModal.classList.remove('hidden');
            addModal.classList.add('flex');
        }
    });
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- HELPER BASE URL ---
    const baseUrl = '<?= base_path() === '/' ? '' : rtrim(base_path(), '/') ?>';
    const uploadUrl = (path) => {
        if (!path) return '';
        if (path.startsWith('http://') && window.location.protocol === 'https:') {
            return 'https://' + path.substring(7);
        }
        if (path.startsWith('http://') || path.startsWith('https://')) return path;
        if (path.startsWith('/')) return path;
        return (baseUrl ? baseUrl + '/' : '/') + path.replace(/^\/+/, '');
    };

    // --- FUNGSI BUKA & TUTUP MODAL ---
    const openModal = (id) => {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    };

    const closeModal = (id) => {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    };

    document.querySelectorAll('[data-close-modal]').forEach(btn => {
        btn.addEventListener('click', () => {
            closeModal(btn.getAttribute('data-close-modal'));
        });
    });

    document.querySelectorAll('[data-open-modal]').forEach(btn => {
        btn.addEventListener('click', () => {
            openModal(btn.getAttribute('data-open-modal'));
        });
    });

    window.addEventListener('click', (e) => {
        ['modal-add-portfolio', 'modal-edit-portfolio', 'modal-delete-confirm'].forEach(id => {
            const modal = document.getElementById(id);
            if (modal && e.target === modal) {
                closeModal(id);
            }
        });
    });

    // --- SEARCH & FILTER TABEL ---
    const searchInput = document.getElementById('admin-search-input');
    const categoryFilter = document.getElementById('admin-category-filter');
    const rows = document.querySelectorAll('.portfolio-row');
    const countDisplay = document.getElementById('admin-item-count');

    function filterTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const cat = categoryFilter ? categoryFilter.value : 'all';
        let visible = 0;

        rows.forEach(row => {
            const title = row.getAttribute('data-title').toLowerCase();
            const rowCat = row.getAttribute('data-category');
            const matchSearch = !query || title.includes(query);
            const matchCat = (cat === 'all') || (rowCat === cat);

            if (matchSearch && matchCat) {
                row.classList.remove('hidden');
                visible++;
            } else {
                row.classList.add('hidden');
            }
        });

        if (countDisplay) countDisplay.textContent = visible;
    }

    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (categoryFilter) categoryFilter.addEventListener('change', filterTable);

    // --- MULTI-IMAGE PREVIEW GENERATOR ---
    function setupMultiImagePreview(inputId, placeholderId, containerId, gridId, badgeId, isEdit = false) {
        const input = document.getElementById(inputId);
        const placeholder = document.getElementById(placeholderId);
        const container = document.getElementById(containerId);
        const grid = document.getElementById(gridId);
        const badge = document.getElementById(badgeId);

        if (!input || !grid) return;

        input.addEventListener('change', () => {
            grid.innerHTML = '';
            const files = Array.from(input.files);

            if (files.length > 0) {
                if (placeholder) placeholder.classList.add('hidden');
                if (container) container.classList.remove('hidden');
                if (badge) badge.textContent = `${files.length} Gambar Dipilih`;

                files.forEach((file, index) => {
                    const card = document.createElement('div');
                    card.className = 'relative rounded-xl overflow-hidden bg-slate-100 border border-slate-200 aspect-video group shadow-2xs';

                    const img = document.createElement('img');
                    img.className = 'w-full h-full object-cover';
                    img.alt = file.name;

                    const label = document.createElement('span');
                    label.className = index === 0 
                        ? 'absolute top-1 left-1 px-1.5 py-0.5 rounded-md bg-indigo-600 text-white text-[9px] font-black shadow-xs'
                        : 'absolute top-1 left-1 px-1.5 py-0.5 rounded-md bg-slate-900/80 text-white text-[9px] font-bold shadow-xs';
                    label.textContent = index === 0 ? '#1 (Cover)' : `#${index + 1}`;

                    const sizeBadge = document.createElement('span');
                    sizeBadge.className = 'absolute bottom-1 right-1 px-1.5 py-0.5 rounded-md bg-black/60 text-white text-[8px] font-medium backdrop-blur-xs';
                    sizeBadge.textContent = (file.size / (1024 * 1024)).toFixed(1) + ' MB';

                    card.appendChild(img);
                    card.appendChild(label);
                    card.appendChild(sizeBadge);
                    grid.appendChild(card);

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                if (placeholder) placeholder.classList.remove('hidden');
                if (container) container.classList.add('hidden');
            }
        });
    }

    setupMultiImagePreview('add_image_input', 'add_placeholder', 'add_preview_container', 'add_preview_grid', 'add_preview_badge', false);
    setupMultiImagePreview('edit_image_input', 'edit_placeholder', 'edit_preview_container', 'edit_preview_grid', 'edit_preview_badge', true);

    // --- EVENT LISTENER TOMBOL EDIT ---
    document.querySelectorAll('.btn-edit-portfolio').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const title = btn.getAttribute('data-title');
            const catId = btn.getAttribute('data-category-id');
            const desc = btn.getAttribute('data-desc');
            const order = btn.getAttribute('data-order');
            const active = btn.getAttribute('data-active');
            let images = [];
            try {
                images = JSON.parse(btn.getAttribute('data-images') || '[]');
            } catch (err) {
                images = [];
            }

            document.getElementById('edit_item_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_category_id').value = catId;
            document.getElementById('edit_description').value = desc;
            document.getElementById('edit_display_order').value = order;
            document.getElementById('edit_is_active').checked = (active == '1');

            // Reset new image input preview in edit modal
            const editInput = document.getElementById('edit_image_input');
            if (editInput) editInput.value = '';
            const editPlaceholder = document.getElementById('edit_placeholder');
            const editPreviewContainer = document.getElementById('edit_preview_container');
            const editPreviewGrid = document.getElementById('edit_preview_grid');
            if (editPlaceholder) editPlaceholder.classList.remove('hidden');
            if (editPreviewContainer) editPreviewContainer.classList.add('hidden');
            if (editPreviewGrid) editPreviewGrid.innerHTML = '';

            // Populate existing images gallery in edit modal
            const existingGrid = document.getElementById('edit_existing_images_grid');
            const countBadge = document.getElementById('edit_existing_count_badge');
            
            if (existingGrid) {
                existingGrid.innerHTML = '';
                if (images.length === 0 && btn.getAttribute('data-image')) {
                    images = [{ id: 0, image_url: btn.getAttribute('data-image'), display_order: 1 }];
                }

                if (countBadge) {
                    countBadge.textContent = `${images.length} Slide`;
                }

                if (images.length === 0) {
                    existingGrid.innerHTML = '<p class="text-xs text-slate-400 col-span-full py-4 text-center">Belum ada gambar yang diunggah.</p>';
                } else {
                    images.forEach((imgObj, idx) => {
                        const imgUrl = uploadUrl(imgObj.image_url);
                        const card = document.createElement('div');
                        card.className = 'relative rounded-xl overflow-hidden bg-white border border-slate-200 aspect-video shadow-2xs group';

                        const imgEl = document.createElement('img');
                        imgEl.src = imgUrl;
                        imgEl.alt = title;
                        imgEl.className = 'w-full h-full object-cover';

                        const badge = document.createElement('span');
                        badge.className = idx === 0 
                            ? 'absolute top-1 left-1 px-1.5 py-0.5 rounded-md bg-indigo-600 text-white text-[9px] font-black shadow-xs z-10'
                            : 'absolute top-1 left-1 px-1.5 py-0.5 rounded-md bg-slate-900/80 text-white text-[9px] font-bold shadow-xs z-10';
                        badge.textContent = idx === 0 ? '#1 (Cover)' : `#${idx + 1}`;

                        card.appendChild(imgEl);
                        card.appendChild(badge);

                        // If image has a database ID and there is more than 1 image, allow individual deletion
                        if (imgObj.id && images.length > 1) {
                            const delBtn = document.createElement('button');
                            delBtn.type = 'button';
                            delBtn.className = 'absolute top-1.5 right-1.5 p-1.5 rounded-lg bg-rose-600/90 hover:bg-rose-600 text-white active:scale-95 transition-all shadow-md z-10 cursor-pointer flex items-center justify-center';
                            delBtn.title = `Hapus slide #${idx + 1}`;
                            delBtn.setAttribute('aria-label', `Hapus slide #${idx + 1}`);
                            delBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>';
                            
                            delBtn.addEventListener('click', (e) => {
                                e.stopPropagation();
                                if (confirm(`Hapus slide gambar #${idx + 1} dari galeri karya ini? Tindakan ini tidak dapat dibatalkan.`)) {
                                    document.getElementById('del_single_image_id').value = imgObj.id;
                                    document.getElementById('del_single_portfolio_id').value = id;
                                    document.getElementById('delete-single-image-form').submit();
                                }
                            });
                            card.appendChild(delBtn);
                        }

                        existingGrid.appendChild(card);
                    });
                }
            }

            openModal('modal-edit-portfolio');
        });
    });

    // --- EVENT LISTENER TOMBOL HAPUS ---
    document.querySelectorAll('.btn-delete-portfolio').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const title = btn.getAttribute('data-title');

            document.getElementById('delete_item_id').value = id;
            document.getElementById('delete_item_title').textContent = title;

            openModal('modal-delete-confirm');
        });
    });
});
</script>