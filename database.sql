-- ==========================================================
-- Database Schema for Graphic Design Portfolio & CMS (porto_db)
-- ==========================================================

CREATE DATABASE IF NOT EXISTS porto_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE porto_db;

-- 1. Admin Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Site Profile & Settings Table
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) UNIQUE NOT NULL,
    setting_value TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Portfolio Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Portfolio Works / Items Table
CREATE TABLE IF NOT EXISTS portfolio_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    description TEXT NULL,
    client_name VARCHAR(100) NULL,
    tools_used VARCHAR(150) NULL,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    views_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Portfolio Images Table (Supports multiple image uploads / carousels per project)
CREATE TABLE IF NOT EXISTS portfolio_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    portfolio_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (portfolio_id) REFERENCES portfolio_items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================
-- SEED DATA
-- ==========================================================

-- Default Admin User (username: admin, password: admin123)
-- Hash generated using password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO users (id, username, password, email) VALUES
(1, 'admin', '$2y$10$lyy4SOMk1HGuKk8HR.rSD.aDMBBtS9.WVN08URBvKzUP/ikmT/B1.', 'designer@portfolio.local')
ON DUPLICATE KEY UPDATE username=VALUES(username), password=VALUES(password);

-- Site Settings & Profile Seed
INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_title', 'Dimas — Graphic Designer & Visual Creator'),
('site_tagline', 'Crafting eye-catching visuals, brand identities & engaging social graphics.'),
('designer_name', 'Dimas Arya'),
('designer_role', 'Visual & Graphic Designer'),
('bio_summary', 'Graphic Designer based in Indonesia with 4+ years of experience specializing in YouTube Thumbnails, Marketing Posters, Service Pricelists, and Corporate Training Reports (Laporan Diklat). Delivering impactful visuals that drive engagement.'),
('status_badge', 'Open for Commissions & Freelance'),
('status_available', '1'),
('whatsapp_number', '6281234567890'),
('whatsapp_message', 'Halo Dimas, saya tertarik dengan portofolio desain grafis Anda. Ingin konsultasi project design:'),
('twitter_handle', '@dimasdesign_'),
('twitter_url', 'https://twitter.com/'),
('instagram_url', 'https://instagram.com/'),
('email_address', 'dimas.design@example.com'),
('avatar_url', 'assets/faviconyell.jpg'),
('footer_credit', '© 2026 Dimas Arya. All rights reserved.')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);

-- Categories Seed
INSERT INTO categories (id, name, slug, display_order) VALUES
(1, 'Thumbnail', 'thumbnail', 1),
(2, 'Poster / Infografis', 'poster-infografis', 2),
(3, 'Pricelist', 'pricelist', 3),
(4, 'Laporan Diklat', 'laporan-diklat', 4),
(5, 'Social Media & Branding', 'social-media-branding', 5)
ON DUPLICATE KEY UPDATE name=VALUES(name), slug=VALUES(slug), display_order=VALUES(display_order);

-- Sample Portfolio Items Seed
INSERT INTO portfolio_items (id, category_id, title, image_url, description, client_name, tools_used, display_order, is_active) VALUES
(1, 1, 'Gaming Stream Highlights YouTube Thumbnail', 'assets/images/sample_thumb_1.svg', 'High-contrast, action-packed gaming thumbnail designed to boost CTR on competitive gaming highlights videos.', 'Streamer ID', 'Adobe Photoshop, Blender', 1, 1),
(2, 1, 'Tech Review & Unboxing YouTube Thumbnail', 'assets/images/sample_thumb_2.svg', 'Clean, modern gadget review thumbnail with vibrant neon accent lighting and crisp typography.', 'GadgetZone Tech', 'Adobe Photoshop, Lightroom', 2, 1),
(3, 1, 'Podcast & Deep Talk Series Thumbnail', 'assets/images/sample_thumb_3.svg', 'Intimate, cinematic podcast thumbnail featuring expressive facial cuts and subtle ambient glow.', 'Ruang Bicara Show', 'Adobe Photoshop', 3, 1),
(4, 2, 'Creative Design Workshop Event Poster', 'assets/images/sample_poster_1.svg', 'Aesthetic typography-driven poster for a national creative design workshop with glassmorphism elements.', 'Creative Hub ID', 'Adobe Illustrator, Photoshop', 4, 1),
(5, 2, 'E-Commerce Festival Promo Infographic', 'assets/images/sample_poster_2.svg', 'Colorful, data-driven infographic poster highlighting discount stages, voucher mechanics, and schedule.', 'Nusantara Marketplace', 'Adobe Illustrator, Figma', 5, 1),
(6, 2, 'Health & Wellness Campaign Poster', 'assets/images/sample_poster_3.svg', 'Minimalist, organic aesthetic poster raising awareness on mental well-being and mindful living.', 'HealthyLiving Foundation', 'Adobe Illustrator', 6, 1),
(7, 3, 'Photography & Videography Service Pricelist', 'assets/images/sample_price_1.svg', 'Luxurious editorial-style rate card with package tiers, deliverable breakdowns, and clean iconography.', 'Luminary Visuals Studio', 'Figma, Adobe InDesign', 7, 1),
(8, 3, 'Culinary & Cafe Menu Pricelist Showcase', 'assets/images/sample_price_2.svg', 'Warm aesthetic food & beverage pricelist catalog with appetizing product framing and readable hierarchy.', 'Kopi Senja Roastery', 'Adobe Illustrator, InDesign', 8, 1),
(9, 4, 'Cover & Layout Laporan Diklat Kepemimpinan', 'assets/images/sample_diklat_1.svg', 'Formal yet contemporary corporate layout design for regional civil leadership training report.', 'BPSDMD Regional Office', 'Adobe InDesign, Photoshop', 9, 1),
(10, 4, 'Annual Corporate Training Digest (Laporan Akhir)', 'assets/images/sample_diklat_2.svg', 'Modern corporate report booklet layout featuring custom data charts, structured summaries, and photo grids.', 'Telco Global Corp', 'Adobe InDesign, Illustrator', 10, 1),
(11, 5, 'Social Media Carousels for Digital Agency', 'assets/images/sample_socmed_1.svg', 'Swipeable educational carousel kit for Instagram focusing on branding tips and typography hierarchy.', 'PixelCraft Studio', 'Figma, Adobe Illustrator', 11, 1),
(12, 5, 'Brand Identity & Visual Style Guide', 'assets/images/sample_socmed_2.svg', 'Complete branding system including logo usage, color harmony, typography pairing, and mockups.', 'Aura Botanicals Co.', 'Adobe Illustrator, Figma', 12, 1)
ON DUPLICATE KEY UPDATE title=VALUES(title), image_url=VALUES(image_url), description=VALUES(description);
