<?php
/**
 * Profile, Social & Site Settings
 * Graphic Design Portfolio & CMS
 */

declare(strict_types=1);

$page_title = 'Pengaturan Profil & Kontak';
require_once __DIR__ . '/includes/admin_header.php';

$designer_name    = get_setting('designer_name', 'Dimas Arya');
$designer_role    = get_setting('designer_role', 'Visual & Graphic Designer');
$bio_summary      = get_setting('bio_summary', '');
$status_badge     = get_setting('status_badge', 'Open for Commission');
$status_available = get_setting('status_available', '1') === '1';
$avatar_url       = get_setting('avatar_url', 'assets/images/avatar.svg');

$whatsapp_number  = get_setting('whatsapp_number', '6281234567890');
$whatsapp_message = get_setting('whatsapp_message', 'Halo Dimas, saya tertarik dengan portofolio desain grafis Anda.');
$twitter_url      = get_setting('twitter_url', 'https://twitter.com/');
$twitter_handle   = get_setting('twitter_handle', '@dimasdesign_');
$instagram_url    = get_setting('instagram_url', 'https://instagram.com/');
$email_address    = get_setting('email_address', 'dimas.design@example.com');

$site_title       = get_setting('site_title', 'Dimas — Graphic Designer & Visual Creator');
$site_tagline     = get_setting('site_tagline', 'Crafting eye-catching visuals, brand identities & engaging social graphics.');
$footer_credit    = get_setting('footer_credit', '© 2026 Dimas Arya. All rights reserved.');
?>

<!-- Header -->
<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Pengaturan Profil & Kontak</h1>
    <p class="text-xs sm:text-sm text-slate-500 mt-1">Ubah data profil card Carrd, nomor WhatsApp, akun sosial media, dan informasi website</p>
</div>

<form action="<?= base_url('admin/settings_action.php') ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
    <input type="hidden" name="action" value="save_settings">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <!-- SECTION 1: PROFIL & FLOATING CARD (CARRD AESTHETIC) -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <div class="flex items-center gap-3 pb-6 mb-6 border-b border-slate-100">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <i data-lucide="user-check" class="w-5 h-5"></i>
            </div>
            <div>
                <h2 class="text-base font-extrabold text-slate-900">Profil Designer (Landing Profile Card)</h2>
                <p class="text-xs text-slate-500">Tampilan bio card utama di halaman beranda</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Avatar Upload & Live Preview -->
            <div class="flex flex-col items-center justify-center text-center p-6 bg-slate-50/70 rounded-2xl border border-slate-200/80">
                <div class="relative w-28 h-28 mb-4">
                    <img id="avatar_preview" 
                         src="<?= upload_url($avatar_url) ?>" 
                         alt="Avatar" 
                         class="w-full h-full object-cover rounded-full bg-white p-1 border-2 border-indigo-100 shadow-md">
                </div>
                <label class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold cursor-pointer hover:bg-slate-800 transition-colors shadow-xs">
                    <span>Ganti Foto Avatar</span>
                    <input type="file" name="avatar" data-preview="avatar_preview" accept=".jpg,.jpeg,.png,.webp,.svg" class="hidden">
                </label>
                <p class="text-[11px] text-slate-400 mt-2">JPG, PNG, WEBP, atau SVG</p>
            </div>

            <!-- Profile Info Fields -->
            <div class="lg:col-span-2 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="designer_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Lengkap / Brand <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="designer_name" id="designer_name" required
                               value="<?= e($designer_name) ?>"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="designer_role" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Role / Profesi
                        </label>
                        <input type="text" name="designer_role" id="designer_role"
                               value="<?= e($designer_role) ?>"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label for="bio_summary" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Bio / Ringkasan Keahlian
                    </label>
                    <textarea name="bio_summary" id="bio_summary" rows="3"
                              class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"><?= e($bio_summary) ?></textarea>
                </div>

                <!-- Status Badge & Availability -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label for="status_badge" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Teks Status Badge
                        </label>
                        <input type="text" name="status_badge" id="status_badge"
                               value="<?= e($status_badge) ?>"
                               placeholder="Contoh: Open for Commissions"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>

                    <div class="flex items-center sm:pt-6">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="status_available" value="1" <?= $status_available ? 'checked' : '' ?>
                                   class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                            <span class="text-xs font-bold text-slate-700">Tampilkan Beacon Status Hijau (Aktif)</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 2: SOCIAL MEDIA & WHATSAPP SETTINGS -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <div class="flex items-center gap-3 pb-6 mb-6 border-b border-slate-100">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i data-lucide="message-square" class="w-5 h-5"></i>
            </div>
            <div>
                <h2 class="text-base font-extrabold text-slate-900">Kontak WhatsApp & Social Links</h2>
                <p class="text-xs text-slate-500">Tombol aksi pill dan integrasi chat order langsung</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- WhatsApp Number -->
            <div>
                <label for="whatsapp_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nomor WhatsApp (dengan kode negara)
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                        <i data-lucide="phone" class="w-4 h-4"></i>
                    </div>
                    <input type="text" name="whatsapp_number" id="whatsapp_number"
                           value="<?= e($whatsapp_number) ?>"
                           placeholder="Contoh: 6281234567890"
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Gunakan format angka tanpa spasi/simbol (+628... -> 628...)</p>
            </div>

            <!-- WhatsApp Message -->
            <div>
                <label for="whatsapp_message" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Pesan Default Konsultasi WhatsApp
                </label>
                <input type="text" name="whatsapp_message" id="whatsapp_message"
                       value="<?= e($whatsapp_message) ?>"
                       placeholder="Halo Dimas, saya ingin konsultasi project desain..."
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <!-- Twitter / X -->
            <div>
                <label for="twitter_url" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Link Profil Twitter / X
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i data-lucide="twitter" class="w-4 h-4"></i>
                    </div>
                    <input type="url" name="twitter_url" id="twitter_url"
                           value="<?= e($twitter_url) ?>"
                           placeholder="https://twitter.com/username"
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>

            <!-- Instagram -->
            <div>
                <label for="instagram_url" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Link Profil Instagram
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-pink-500">
                        <i data-lucide="instagram" class="w-4 h-4"></i>
                    </div>
                    <input type="url" name="instagram_url" id="instagram_url"
                           value="<?= e($instagram_url) ?>"
                           placeholder="https://instagram.com/username"
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>

            <!-- Email -->
            <div class="sm:col-span-2">
                <label for="email_address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Alamat Email Kontak
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </div>
                    <input type="email" name="email_address" id="email_address"
                           value="<?= e($email_address) ?>"
                           placeholder="designer@example.com"
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 3: SITE SEO & FOOTER -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <div class="flex items-center gap-3 pb-6 mb-6 border-b border-slate-100">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <i data-lucide="globe" class="w-5 h-5"></i>
            </div>
            <div>
                <h2 class="text-base font-extrabold text-slate-900">SEO & Teks Website</h2>
                <p class="text-xs text-slate-500">Judul browser tab, meta deskripsi, dan teks footer</p>
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="site_title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Judul Website (Browser Tab)
                    </label>
                    <input type="text" name="site_title" id="site_title"
                           value="<?= e($site_title) ?>"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label for="footer_credit" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Teks Hak Cipta Footer
                    </label>
                    <input type="text" name="footer_credit" id="footer_credit"
                           value="<?= e($footer_credit) ?>"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label for="site_tagline" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Tagline & Meta Description
                </label>
                <input type="text" name="site_tagline" id="site_tagline"
                       value="<?= e($site_tagline) ?>"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
        </div>
    </div>

    <!-- Save Settings Button -->
    <div class="flex items-center justify-end">
        <button type="submit" 
                class="px-8 py-3.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm shadow-xl transition-all active:scale-95 flex items-center gap-2">
            <i data-lucide="check" class="w-4 h-4"></i>
            <span>Simpan Semua Pengaturan</span>
        </button>
    </div>
</form>

<!-- SECTION 4: GANTI PASSWORD ADMIN -->
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8 mt-12">
    <div class="flex items-center gap-3 pb-6 mb-6 border-b border-slate-100">
        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
            <i data-lucide="key" class="w-5 h-5"></i>
        </div>
        <div>
            <h2 class="text-base font-extrabold text-slate-900">Keamanan Akun Admin</h2>
            <p class="text-xs text-slate-500">Perbarui kata sandi login admin Anda</p>
        </div>
    </div>

    <form action="<?= base_url('admin/settings_action.php') ?>" method="POST" class="max-w-xl space-y-4">
        <input type="hidden" name="action" value="change_password">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <div>
            <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                Password Saat Ini <span class="text-rose-500">*</span>
            </label>
            <input type="password" name="current_password" id="current_password" required
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="new_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Password Baru <span class="text-rose-500">*</span>
                </label>
                <input type="password" name="new_password" id="new_password" required minlength="6"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none">
            </div>

            <div>
                <label for="confirm_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Konfirmasi Password Baru <span class="text-rose-500">*</span>
                </label>
                <input type="password" name="confirm_password" id="confirm_password" required minlength="6"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none">
            </div>
        </div>

        <button type="submit" 
                class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md transition-all">
            Perbarui Password
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
