<?php
/**
 * Portfolio Action Processor (CRUD Backend)
 * Graphic Design Portfolio & CMS
 */

declare(strict_types=1);

require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/includes/auth.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . base_url('admin/portfolio.php'));
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!verify_csrf($csrf_token)) {
    flash('error', 'Validasi keamanan gagal (CSRF token invalid). Silakan ulangi.');
    header('Location: ' . base_url('admin/portfolio.php'));
    exit;
}

$action = $_POST['action'] ?? '';
$db = get_db();

switch ($action) {
    case 'create':
        $title       = trim($_POST['title'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $order       = (int)($_POST['display_order'] ?? 0);
        $is_active   = isset($_POST['is_active']) ? 1 : 0;

        if (empty($title) || $category_id <= 0) {
            flash('error', 'Judul karya dan kategori wajib diisi.');
            header('Location: ' . base_url('admin/portfolio.php'));
            exit;
        }

        $files = $_FILES['images'] ?? null;
        $single_file_input = $_FILES['image'] ?? null;

        $has_multiple = ($files && is_array($files['name']) && !empty($files['name'][0]) && $files['error'][0] !== UPLOAD_ERR_NO_FILE);
        $has_single   = ($single_file_input && !is_array($single_file_input['name']) && $single_file_input['error'] === UPLOAD_ERR_OK);

        if (!$has_multiple && !$has_single) {
            flash('error', 'File gambar desain wajib diupload minimal 1 gambar.');
            header('Location: ' . base_url('admin/portfolio.php'));
            exit;
        }

        try {
            $stmt = $db->prepare("INSERT INTO portfolio_items 
                (category_id, title, image_url, description, display_order, is_active) 
                VALUES (:cat, :title, '', :desc, :order, :active)");
            $stmt->execute([
                ':cat'    => $category_id,
                ':title'  => $title,
                ':desc'   => $description,
                ':order'  => $order,
                ':active' => $is_active
            ]);

            $portfolio_id = (int)$db->lastInsertId();
            $uploaded_images = [];

            if ($has_multiple) {
                $total_files = count($files['name']);
                $order_idx = 1;
                for ($i = 0; $i < $total_files; $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $single_file = [
                            'name'     => $files['name'][$i],
                            'type'     => $files['type'][$i],
                            'tmp_name' => $files['tmp_name'][$i],
                            'error'    => $files['error'][$i],
                            'size'     => $files['size'][$i]
                        ];

                        $upload_result = handle_file_upload($single_file, 'portfolio');
                        if ($upload_result['success']) {
                            $uploaded_path = $upload_result['path'];
                            $uploaded_images[] = $uploaded_path;

                            $stmt_img = $db->prepare("INSERT INTO portfolio_images 
                                (portfolio_id, image_url, display_order) 
                                VALUES (:pid, :img, :order)");
                            $stmt_img->execute([
                                ':pid'   => $portfolio_id,
                                ':img'   => $uploaded_path,
                                ':order' => $order_idx++
                            ]);
                        }
                    }
                }
            } elseif ($has_single) {
                $upload_result = handle_file_upload($single_file_input, 'portfolio');
                if ($upload_result['success']) {
                    $uploaded_path = $upload_result['path'];
                    $uploaded_images[] = $uploaded_path;

                    $stmt_img = $db->prepare("INSERT INTO portfolio_images 
                        (portfolio_id, image_url, display_order) 
                        VALUES (:pid, :img, 1)");
                    $stmt_img->execute([
                        ':pid'   => $portfolio_id,
                        ':img'   => $uploaded_path
                    ]);
                }
            }

            if (!empty($uploaded_images)) {
                $cover_image = $uploaded_images[0];
                $stmt_cover = $db->prepare("UPDATE portfolio_items SET image_url = :img WHERE id = :id");
                $stmt_cover->execute([':img' => $cover_image, ':id' => $portfolio_id]);
                
                $count_uploaded = count($uploaded_images);
                flash('success', "Karya desain \"{$title}\" berhasil ditambahkan dengan {$count_uploaded} gambar!");
            } else {
                flash('warning', "Karya tersimpan namun gagal mengupload gambar.");
            }
        } catch (PDOException $e) {
            error_log('Error inserting portfolio item: ' . $e->getMessage());
            flash('error', 'Gagal menyimpan karya ke database: ' . $e->getMessage());
        }

        header('Location: ' . base_url('admin/portfolio.php'));
        exit;

    case 'update':
        $id          = (int)($_POST['id'] ?? 0);
        $title       = trim($_POST['title'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $order       = (int)($_POST['display_order'] ?? 0);
        $is_active   = isset($_POST['is_active']) ? 1 : 0;

        if ($id <= 0 || empty($title) || $category_id <= 0) {
            flash('error', 'Data karya tidak valid.');
            header('Location: ' . base_url('admin/portfolio.php'));
            exit;
        }

        $current_item = get_portfolio_item($id);
        if (!$current_item) {
            flash('error', 'Karya tidak ditemukan.');
            header('Location: ' . base_url('admin/portfolio.php'));
            exit;
        }

        $cover_image_url = $current_item['image_url'];

        $files = $_FILES['images'] ?? null;
        $single_file_input = $_FILES['image'] ?? null;

        $has_multiple = ($files && is_array($files['name']) && !empty($files['name'][0]) && $files['error'][0] !== UPLOAD_ERR_NO_FILE);
        $has_single   = ($single_file_input && !is_array($single_file_input['name']) && $single_file_input['error'] === UPLOAD_ERR_OK);

        $newly_uploaded = [];

        $max_order_stmt = $db->prepare("SELECT COALESCE(MAX(display_order), 0) FROM portfolio_images WHERE portfolio_id = :pid");
        $max_order_stmt->execute([':pid' => $id]);
        $current_max_order = (int)$max_order_stmt->fetchColumn();

        // Safeguard: If portfolio_images has no entries yet for this item but it has an existing cover image, insert it first as #1
        if ($current_max_order === 0 && !empty($cover_image_url)) {
            $check_stmt = $db->prepare("SELECT COUNT(*) FROM portfolio_images WHERE portfolio_id = :pid");
            $check_stmt->execute([':pid' => $id]);
            if ((int)$check_stmt->fetchColumn() === 0) {
                $db->prepare("INSERT INTO portfolio_images (portfolio_id, image_url, display_order) VALUES (:pid, :img, 1)")
                   ->execute([':pid' => $id, ':img' => $cover_image_url]);
                $current_max_order = 1;
            }
        }

        if ($has_multiple) {
            $total_files = count($files['name']);
            for ($i = 0; $i < $total_files; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $single_file = [
                        'name'     => $files['name'][$i],
                        'type'     => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error'    => $files['error'][$i],
                        'size'     => $files['size'][$i]
                    ];

                    $upload_result = handle_file_upload($single_file, 'portfolio');
                    if ($upload_result['success']) {
                        $uploaded_path = $upload_result['path'];
                        $newly_uploaded[] = $uploaded_path;

                        $current_max_order++;
                        $stmt_img = $db->prepare("INSERT INTO portfolio_images 
                            (portfolio_id, image_url, display_order) 
                            VALUES (:pid, :img, :order)");
                        $stmt_img->execute([
                            ':pid'   => $id,
                            ':img'   => $uploaded_path,
                            ':order' => $current_max_order
                        ]);
                    }
                }
            }
        } elseif ($has_single) {
            $upload_result = handle_file_upload($single_file_input, 'portfolio');
            if ($upload_result['success']) {
                $uploaded_path = $upload_result['path'];
                $newly_uploaded[] = $uploaded_path;

                $current_max_order++;
                $stmt_img = $db->prepare("INSERT INTO portfolio_images 
                    (portfolio_id, image_url, display_order) 
                    VALUES (:pid, :img, :order)");
                $stmt_img->execute([
                    ':pid'   => $id,
                    ':img'   => $uploaded_path,
                    ':order' => $current_max_order
                ]);
            }
        }

        if (empty($cover_image_url) && !empty($newly_uploaded)) {
            $cover_image_url = $newly_uploaded[0];
        }

        try {
            $stmt = $db->prepare("UPDATE portfolio_items SET 
                category_id = :cat,
                title = :title,
                image_url = :img,
                description = :desc,
                display_order = :order,
                is_active = :active 
                WHERE id = :id");
            $stmt->execute([
                ':cat'     => $category_id,
                ':title'   => $title,
                ':img'     => $cover_image_url,
                ':desc'    => $description,
                ':order'   => $order,
                ':active'  => $is_active,
                ':id'      => $id
            ]);

            $msg = "Perubahan pada \"{$title}\" berhasil disimpan!";
            if (!empty($newly_uploaded)) {
                $msg .= " (" . count($newly_uploaded) . " gambar baru ditambahkan)";
            }
            flash('success', $msg);
        } catch (PDOException $e) {
            error_log('Error updating portfolio item: ' . $e->getMessage());
            flash('error', 'Gagal memperbarui data karya.');
        }

        header('Location: ' . base_url('admin/portfolio.php'));
        exit;

    case 'delete_image':
        $image_id     = (int)($_POST['image_id'] ?? 0);
        $portfolio_id = (int)($_POST['portfolio_id'] ?? 0);

        if ($image_id <= 0 || $portfolio_id <= 0) {
            flash('error', 'Parameter gambar tidak valid.');
            header('Location: ' . base_url('admin/portfolio.php'));
            exit;
        }

        $stmt = $db->prepare("SELECT * FROM portfolio_images WHERE id = :id AND portfolio_id = :pid");
        $stmt->execute([':id' => $image_id, ':pid' => $portfolio_id]);
        $img_record = $stmt->fetch();

        if ($img_record) {
            $file_path = realpath(__DIR__ . '/' . $img_record['image_url']);
            if ($file_path && file_exists($file_path) && str_contains($img_record['image_url'], 'uploads/portfolio/')) {
                @unlink($file_path);
            }

            $db->prepare("DELETE FROM portfolio_images WHERE id = :id")->execute([':id' => $image_id]);

            $p_stmt = $db->prepare("SELECT image_url FROM portfolio_items WHERE id = :id");
            $p_stmt->execute([':id' => $portfolio_id]);
            $p_item = $p_stmt->fetch();

            if ($p_item && $p_item['image_url'] === $img_record['image_url']) {
                $next_img_stmt = $db->prepare("SELECT image_url FROM portfolio_images WHERE portfolio_id = :pid ORDER BY display_order ASC, id ASC LIMIT 1");
                $next_img_stmt->execute([':pid' => $portfolio_id]);
                $next_img = $next_img_stmt->fetchColumn();
                $new_cover = $next_img ?: '';

                $db->prepare("UPDATE portfolio_items SET image_url = :img WHERE id = :id")->execute([
                    ':img' => $new_cover,
                    ':id'  => $portfolio_id
                ]);
            }

            flash('success', 'Gambar berhasil dihapus dari galeri karya.');
        } else {
            flash('error', 'Gambar tidak ditemukan.');
        }

        header('Location: ' . base_url('admin/portfolio.php'));
        exit;

    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            flash('error', 'ID karya tidak valid.');
            header('Location: ' . base_url('admin/portfolio.php'));
            exit;
        }

        $item = get_portfolio_item($id);
        if ($item) {
            $all_gallery_images = get_portfolio_images($id);
            foreach ($all_gallery_images as $gimg) {
                $file_path = realpath(__DIR__ . '/' . $gimg['image_url']);
                if ($file_path && file_exists($file_path) && str_contains($gimg['image_url'], 'uploads/portfolio/')) {
                    @unlink($file_path);
                }
            }

            $cover_file = realpath(__DIR__ . '/' . $item['image_url']);
            if ($cover_file && file_exists($cover_file) && str_contains($item['image_url'], 'uploads/portfolio/')) {
                @unlink($cover_file);
            }

            $stmt = $db->prepare("DELETE FROM portfolio_items WHERE id = :id");
            $stmt->execute([':id' => $id]);

            flash('success', "Karya \"{$item['title']}\" berhasil dihapus beserta seluruh gambarnya.");
        } else {
            flash('error', 'Karya tidak ditemukan.');
        }

        header('Location: ' . base_url('admin/portfolio.php'));
        exit;

    case 'toggle_status':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            flash('error', 'ID karya tidak valid.');
            header('Location: ' . base_url('admin/portfolio.php'));
            exit;
        }

        $stmt = $db->prepare("UPDATE portfolio_items SET is_active = (1 - is_active) WHERE id = :id");
        $stmt->execute([':id' => $id]);

        flash('info', 'Status visibilitas karya berhasil diperbarui.');
        header('Location: ' . base_url('admin/portfolio.php'));
        exit;

    default:
        header('Location: ' . base_url('admin/portfolio.php'));
        exit;
}
