<?php
/**
 * Settings Action Processor
 * Graphic Design Portfolio & CMS
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . base_url('admin/settings.php'));
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!verify_csrf($csrf_token)) {
    flash('error', 'Validasi keamanan gagal. Silakan ulangi.');
    header('Location: ' . base_url('admin/settings.php'));
    exit;
}

$action = $_POST['action'] ?? '';
$db = get_db();

if ($action === 'save_settings') {
    $keys = [
        'designer_name',
        'designer_role',
        'bio_summary',
        'status_badge',
        'whatsapp_number',
        'whatsapp_message',
        'twitter_url',
        'twitter_handle',
        'instagram_url',
        'email_address',
        'site_title',
        'site_tagline',
        'footer_credit'
    ];

    foreach ($keys as $k) {
        if (isset($_POST[$k])) {
            update_setting($k, trim((string)$_POST[$k]));
        }
    }

    // Availability checkbox toggle
    $status_available = isset($_POST['status_available']) ? '1' : '0';
    update_setting('status_available', $status_available);

    // Handle Avatar Upload if provided
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_result = handle_file_upload($_FILES['avatar'], 'avatar');
        if ($upload_result['success']) {
            update_setting('avatar_url', $upload_result['path']);
        } else {
            flash('warning', 'Pengaturan disimpan, tetapi upload foto avatar gagal: ' . $upload_result['error']);
            header('Location: ' . base_url('admin/settings.php'));
            exit;
        }
    }

    flash('success', 'Semua pengaturan profil, sosial media, dan website berhasil diperbarui!');
    header('Location: ' . base_url('admin/settings.php'));
    exit;
}

if ($action === 'change_password') {
    $user = current_user();
    if (!$user) {
        flash('error', 'Sesi tidak valid.');
        header('Location: ' . base_url('admin/login.php'));
        exit;
    }

    $current_pass = $_POST['current_password'] ?? '';
    $new_pass     = $_POST['new_password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    if (empty($current_pass) || empty($new_pass) || empty($confirm_pass)) {
        flash('error', 'Semua kolom password wajib diisi.');
        header('Location: ' . base_url('admin/settings.php'));
        exit;
    }

    if ($new_pass !== $confirm_pass) {
        flash('error', 'Password baru dan konfirmasi password tidak cocok.');
        header('Location: ' . base_url('admin/settings.php'));
        exit;
    }

    if (strlen($new_pass) < 6) {
        flash('error', 'Password baru minimal harus 6 karakter.');
        header('Location: ' . base_url('admin/settings.php'));
        exit;
    }

    // Verify current password
    $stmt = $db->prepare("SELECT password FROM users WHERE id = :id");
    $stmt->execute([':id' => $user['id']]);
    $stored_hash = $stmt->fetchColumn();

    if (!$stored_hash || !password_verify($current_pass, $stored_hash)) {
        flash('error', 'Password saat ini salah.');
        header('Location: ' . base_url('admin/settings.php'));
        exit;
    }

    // Update with new password hash
    $new_hash = password_hash($new_pass, PASSWORD_BCRYPT);
    $update_stmt = $db->prepare("UPDATE users SET password = :p WHERE id = :id");
    $update_stmt->execute([':p' => $new_hash, ':id' => $user['id']]);

    flash('success', 'Password admin berhasil diperbarui! Silakan gunakan password baru pada login berikutnya.');
    header('Location: ' . base_url('admin/settings.php'));
    exit;
}

header('Location: ' . base_url('admin/settings.php'));
exit;
