<?php
/**
 * Categories Action Processor
 * Graphic Design Portfolio & CMS
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . base_url('admin/categories.php'));
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!verify_csrf($csrf_token)) {
    flash('error', 'Validasi keamanan gagal. Silakan ulangi.');
    header('Location: ' . base_url('admin/categories.php'));
    exit;
}

$action = $_POST['action'] ?? '';
$db = get_db();

function slugify(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'cat-' . uniqid() : $text;
}

switch ($action) {
    case 'create':
        $name  = trim($_POST['name'] ?? '');
        $slug  = trim($_POST['slug'] ?? '');
        $order = (int)($_POST['display_order'] ?? 0);

        if (empty($name)) {
            flash('error', 'Nama kategori wajib diisi.');
            header('Location: ' . base_url('admin/categories.php'));
            exit;
        }

        if (empty($slug)) {
            $slug = slugify($name);
        } else {
            $slug = slugify($slug);
        }

        try {
            $stmt = $db->prepare("INSERT INTO categories (name, slug, display_order) VALUES (:name, :slug, :order)");
            $stmt->execute([':name' => $name, ':slug' => $slug, ':order' => $order]);
            flash('success', "Kategori \"{$name}\" berhasil ditambahkan!");
        } catch (PDOException $e) {
            flash('error', 'Gagal menambahkan kategori (kemungkinan slug sudah ada).');
        }

        header('Location: ' . base_url('admin/categories.php'));
        exit;

    case 'update':
        $id    = (int)($_POST['id'] ?? 0);
        $name  = trim($_POST['name'] ?? '');
        $slug  = trim($_POST['slug'] ?? '');
        $order = (int)($_POST['display_order'] ?? 0);

        if ($id <= 0 || empty($name)) {
            flash('error', 'Data kategori tidak valid.');
            header('Location: ' . base_url('admin/categories.php'));
            exit;
        }

        if (empty($slug)) {
            $slug = slugify($name);
        } else {
            $slug = slugify($slug);
        }

        try {
            $stmt = $db->prepare("UPDATE categories SET name = :name, slug = :slug, display_order = :order WHERE id = :id");
            $stmt->execute([':name' => $name, ':slug' => $slug, ':order' => $order, ':id' => $id]);
            flash('success', "Kategori \"{$name}\" berhasil diperbarui!");
        } catch (PDOException $e) {
            flash('error', 'Gagal memperbarui kategori.');
        }

        header('Location: ' . base_url('admin/categories.php'));
        exit;

    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            flash('error', 'ID kategori tidak valid.');
            header('Location: ' . base_url('admin/categories.php'));
            exit;
        }

        try {
            $stmt = $db->prepare("DELETE FROM categories WHERE id = :id");
            $stmt->execute([':id' => $id]);
            flash('success', 'Kategori dan seluruh karya di dalamnya berhasil dihapus.');
        } catch (PDOException $e) {
            flash('error', 'Gagal menghapus kategori.');
        }

        header('Location: ' . base_url('admin/categories.php'));
        exit;

    default:
        header('Location: ' . base_url('admin/categories.php'));
        exit;
}
