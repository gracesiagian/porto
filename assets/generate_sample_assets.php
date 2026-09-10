<?php
/**
 * Sample Visual Asset Generator for Graphic Design Portfolio
 */

$dirs = [
    __DIR__ . '/images',
    __DIR__ . '/../uploads/portfolio',
    __DIR__ . '/../uploads/avatar'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// 1. Avatar SVG
$avatarSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="200" height="200">
  <defs>
    <linearGradient id="grad-avatar" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#3B82F6"/>
      <stop offset="50%" stop-color="#6366F1"/>
      <stop offset="100%" stop-color="#EC4899"/>
    </linearGradient>
    <linearGradient id="grad-hair" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#1E293B"/>
      <stop offset="100%" stop-color="#0F172A"/>
    </linearGradient>
  </defs>
  <circle cx="100" cy="100" r="96" fill="url(#grad-avatar)" stroke="#FFFFFF" stroke-width="4"/>
  <circle cx="100" cy="90" r="42" fill="#FDE68A"/>
  <path d="M 60 78 Q 100 45 140 78 Q 148 55 130 42 Q 100 30 70 42 Q 52 55 60 78 Z" fill="url(#grad-hair)"/>
  <circle cx="85" cy="88" r="5" fill="#1E293B"/>
  <circle cx="115" cy="88" r="5" fill="#1E293B"/>
  <path d="M 88 105 Q 100 118 112 105" stroke="#1E293B" stroke-width="4" stroke-linecap="round" fill="none"/>
  <path d="M 40 185 Q 100 135 160 185" fill="#1E293B"/>
  <path d="M 75 145 L 100 170 L 125 145 Z" fill="#3B82F6"/>
  <circle cx="150" cy="150" r="24" fill="#10B981" stroke="#FFFFFF" stroke-width="3"/>
  <path d="M 142 150 L 148 156 L 160 142" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
</svg>';
file_put_contents(__DIR__ . '/images/avatar.svg', $avatarSvg);
file_put_contents(__DIR__ . '/../uploads/avatar/default_avatar.svg', $avatarSvg);

// Graphic Sample Visuals Generator Helper
function generateArtworkSvg($title, $category, $theme, $aspectRatio = '16/9') {
    $width = 800;
    $height = ($aspectRatio === '16/9') ? 450 : (($aspectRatio === '4/5') ? 1000 : 600);

    $colors = match($theme) {
        'neon_game' => ['#090A0F', '#1E1B4B', '#4338CA', '#EC4899', '#06B6D4', '#F43F5E'],
        'tech_review' => ['#0F172A', '#1E293B', '#3B82F6', '#60A5FA', '#10B981', '#F59E0B'],
        'podcast' => ['#18181B', '#27272A', '#8B5CF6', '#A78BFA', '#F43F5E', '#FB7185'],
        'poster_creative' => ['#0F172A', '#312E81', '#6366F1', '#EC4899', '#FDE047', '#38BDF8'],
        'poster_promo' => ['#1E1B4B', '#4C1D95', '#F59E0B', '#EF4444', '#10B981', '#38BDF8'],
        'poster_health' => ['#064E3B', '#065F46', '#10B981', '#A7F3D0', '#F59E0B', '#6EE7B7'],
        'price_studio' => ['#18181B', '#27272A', '#E4E4E7', '#F4F4F5', '#3B82F6', '#A1A1AA'],
        'price_cafe' => ['#451A03', '#78350F', '#D97706', '#FDE68A', '#FEF3C7', '#B45309'],
        'diklat_corp' => ['#0C4A6E', '#0369A1', '#38BDF8', '#E0F2FE', '#F8FAFC', '#0284C7'],
        'diklat_annual' => ['#1E3A8A', '#1D4ED8', '#60A5FA', '#DBEAFE', '#F1F5F9', '#2563EB'],
        'socmed_agency' => ['#111827', '#1F2937', '#8B5CF6', '#EC4899', '#F3F4F6', '#A855F7'],
        default => ['#0F172A', '#1E293B', '#3B82F6', '#60A5FA', '#F8FAFC', '#94A3B8']
    };

    $c0 = $colors[0];
    $c1 = $colors[1];
    $c2 = $colors[2];
    $c3 = $colors[3];
    $c4 = $colors[4];

    $escapedTitle = htmlspecialchars($title, ENT_XML1);
    $escapedCat = htmlspecialchars($category, ENT_XML1);

    return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 $width $height" width="$width" height="$height">
  <defs>
    <linearGradient id="bg-$theme" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="$c0"/>
      <stop offset="60%" stop-color="$c1"/>
      <stop offset="100%" stop-color="$c2"/>
    </linearGradient>
    <linearGradient id="glow-$theme" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="$c3" stop-opacity="0.8"/>
      <stop offset="100%" stop-color="$c4" stop-opacity="0.2"/>
    </linearGradient>
    <filter id="blur-filter" x="-20%" y="-20%" width="140%" height="140%">
      <feGaussianBlur stdDeviation="50"/>
    </filter>
  </defs>

  <!-- Background -->
  <rect width="100%" height="100%" fill="url(#bg-$theme)"/>

  <!-- Decorative Ambient Glow Circles -->
  <circle cx="150" cy="120" r="180" fill="$c3" opacity="0.35" filter="url(#blur-filter)"/>
  <circle cx="680" cy="380" r="220" fill="$c4" opacity="0.28" filter="url(#blur-filter)"/>

  <!-- Geometric Grid Accent Overlay -->
  <g opacity="0.08" stroke="#FFFFFF" stroke-width="1">
    <line x1="0" y1="100" x2="$width" y2="100"/>
    <line x1="0" y1="200" x2="$width" y2="200"/>
    <line x1="0" y1="300" x2="$width" y2="300"/>
    <line x1="0" y1="400" x2="$width" y2="400"/>
    <line x1="200" y1="0" x2="200" y2="$height"/>
    <line x1="400" y1="0" x2="400" y2="$height"/>
    <line x1="600" y1="0" x2="600" y2="$height"/>
  </g>

  <!-- Modern Design Frame & Glass Card Mockup -->
  <g transform="translate(60, 50)">
    <!-- Glassmorphism Card Box -->
    <rect width="680" height="350" rx="20" fill="#FFFFFF" fill-opacity="0.07" stroke="#FFFFFF" stroke-opacity="0.2" stroke-width="1.5"/>

    <!-- Category Pill Badge -->
    <rect x="40" y="40" width="160" height="32" rx="16" fill="$c3" fill-opacity="0.3" stroke="$c3" stroke-width="1.5"/>
    <text x="120" y="61" fill="#FFFFFF" font-family="'Plus Jakarta Sans', system-ui, sans-serif" font-size="12" font-weight="700" text-anchor="middle" letter-spacing="1">$escapedCat</text>

    <!-- Main Title -->
    <text x="40" y="125" fill="#FFFFFF" font-family="'Plus Jakarta Sans', system-ui, sans-serif" font-size="28" font-weight="800" letter-spacing="-0.5">
      $escapedTitle
    </text>

    <!-- Design Badges & Tools Mockup -->
    <g transform="translate(40, 160)">
      <rect width="100" height="26" rx="6" fill="#000000" fill-opacity="0.4"/>
      <text x="50" y="17" fill="#E2E8F0" font-family="sans-serif" font-size="11" font-weight="600" text-anchor="middle">PHOTOSHOP</text>

      <rect x="110" y="0" width="110" height="26" rx="6" fill="#000000" fill-opacity="0.4"/>
      <text x="165" y="17" fill="#E2E8F0" font-family="sans-serif" font-size="11" font-weight="600" text-anchor="middle">ILLUSTRATOR</text>

      <rect x="230" y="0" width="80" height="26" rx="6" fill="#000000" fill-opacity="0.4"/>
      <text x="270" y="17" fill="#E2E8F0" font-family="sans-serif" font-size="11" font-weight="600" text-anchor="middle">FIGMA</text>
    </g>

    <!-- Visual Mockup Element (Graphic Visualizer) -->
    <g transform="translate(40, 220)">
      <!-- Waveform / Bar Chart / Art Preview -->
      <rect x="0" y="50" width="30" height="40" rx="4" fill="$c3" opacity="0.9"/>
      <rect x="40" y="30" width="30" height="60" rx="4" fill="$c4" opacity="0.9"/>
      <rect x="80" y="10" width="30" height="80" rx="4" fill="#FFFFFF" opacity="0.95"/>
      <rect x="120" y="25" width="30" height="65" rx="4" fill="$c3" opacity="0.9"/>
      <rect x="160" y="45" width="30" height="45" rx="4" fill="$c4" opacity="0.8"/>
      <rect x="200" y="15" width="30" height="75" rx="4" fill="#FFFFFF" opacity="0.9"/>
      <rect x="240" y="35" width="30" height="55" rx="4" fill="$c3" opacity="0.9"/>
    </g>

    <!-- Right-side Graphic Illustration Stamp -->
    <g transform="translate(460, 110)">
      <circle cx="100" cy="100" r="70" fill="none" stroke="$c3" stroke-width="4" stroke-dasharray="8 6"/>
      <circle cx="100" cy="100" r="50" fill="$c4" opacity="0.2"/>
      <polygon points="100,60 135,125 65,125" fill="#FFFFFF" opacity="0.9"/>
      <circle cx="100" cy="105" r="12" fill="$c0"/>
    </g>
  </g>

  <!-- Watermark / Brand Signature -->
  <text x="730" y="420" fill="#FFFFFF" opacity="0.5" font-family="'Plus Jakarta Sans', sans-serif" font-size="12" font-weight="600" text-anchor="end">
    DIMAS ARYA • DESIGN PORTFOLIO
  </text>
</svg>
SVG;
}

$items = [
    ['sample_thumb_1.svg', 'Gaming Stream Highlights YouTube Thumbnail', 'THUMBNAIL', 'neon_game'],
    ['sample_thumb_2.svg', 'Tech Review & Unboxing YouTube Thumbnail', 'THUMBNAIL', 'tech_review'],
    ['sample_thumb_3.svg', 'Podcast & Deep Talk Series Thumbnail', 'THUMBNAIL', 'podcast'],
    ['sample_poster_1.svg', 'Creative Design Workshop Event Poster', 'POSTER / INFOGRAFIS', 'poster_creative'],
    ['sample_poster_2.svg', 'E-Commerce Festival Promo Infographic', 'POSTER / INFOGRAFIS', 'poster_promo'],
    ['sample_poster_3.svg', 'Health & Wellness Campaign Poster', 'POSTER / INFOGRAFIS', 'poster_health'],
    ['sample_price_1.svg', 'Photography & Videography Service Pricelist', 'PRICELIST', 'price_studio'],
    ['sample_price_2.svg', 'Culinary & Cafe Menu Pricelist Showcase', 'PRICELIST', 'price_cafe'],
    ['sample_diklat_1.svg', 'Cover & Layout Laporan Diklat Kepemimpinan', 'LAPORAN DIKLAT', 'diklat_corp'],
    ['sample_diklat_2.svg', 'Annual Corporate Training Digest (Laporan Akhir)', 'LAPORAN DIKLAT', 'diklat_annual'],
    ['sample_socmed_1.svg', 'Social Media Carousels for Digital Agency', 'SOCIAL MEDIA', 'socmed_agency'],
    ['sample_socmed_2.svg', 'Brand Identity & Visual Style Guide', 'BRANDING', 'poster_creative']
];

foreach ($items as $item) {
    $svg = generateArtworkSvg($item[1], $item[2], $item[3]);
    file_put_contents(__DIR__ . '/images/' . $item[0], $svg);
    file_put_contents(__DIR__ . '/../uploads/portfolio/' . $item[0], $svg);
}

echo "Asset generation completed successfully!\n";
