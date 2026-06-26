<?php
/**
 * Aakar Creatives — about.php
 * Dynamic, production-ready. Pulls live data from DB.
 */

$page_key = 'about';
if (!isset($pdo)) { require_once __DIR__ . '/includes/db.php'; }

/* ── Live DB data ── */
try {
    /* Product & category counts */
    $stat_stmt = $pdo->query(
        "SELECT
           (SELECT COUNT(*) FROM products   WHERE status = 'active') AS total_products,
           (SELECT COUNT(*) FROM categories WHERE is_active = 1)     AS total_categories,
           (SELECT COUNT(*) FROM orders     WHERE status NOT IN ('cancelled','pending')) AS total_orders,
           (SELECT COUNT(*) FROM customers) AS total_customers"
    );
    $stats = $stat_stmt->fetch(PDO::FETCH_ASSOC);

    /* Featured testimonials */
    $test_stmt = $pdo->query(
        "SELECT t.name, t.instagram, t.rating, t.review, t.created_at,
                p.name AS product_name
           FROM testimonials t
           LEFT JOIN products p ON p.id = t.product_id
          WHERE t.is_approved = 1
          ORDER BY t.is_featured DESC, t.sort_order ASC, t.created_at DESC
          LIMIT 4"
    );
    $testimonials = $test_stmt->fetchAll(PDO::FETCH_ASSOC);

    /* Categories for "What We Create" section */
    $cat_stmt = $pdo->query(
        "SELECT c.name, c.slug, c.description, c.image_url,
                COUNT(p.id) AS product_count
           FROM categories c
           LEFT JOIN products p ON p.category_id = c.id AND p.status = 'active'
          WHERE c.is_active = 1
          GROUP BY c.id
          ORDER BY c.sort_order ASC
          LIMIT 6"
    );
    $categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);

    /* Top products by views for "Most Loved" strip */
    $top_stmt = $pdo->query(
        "SELECT p.name, p.slug, p.price, p.discount_price,
                b.name AS badge_name, b.color_hex AS badge_color,
                c.name AS cat_name,
                (SELECT pm.file_url FROM product_media pm WHERE pm.product_id = p.id AND pm.is_primary = 1 LIMIT 1) AS primary_image
           FROM products p
           LEFT JOIN badges b ON b.id = p.badge_id
           LEFT JOIN categories c ON c.id = p.category_id
          WHERE p.status = 'active'
          ORDER BY p.views DESC, p.is_bestseller DESC
          LIMIT 4"
    );
    $top_products = $top_stmt->fetchAll(PDO::FETCH_ASSOC);

    $wa_stmt   = $pdo->query("SELECT phone_number FROM whatsapp_settings WHERE is_primary = 1 LIMIT 1");
    $wa_row    = $wa_stmt->fetch(PDO::FETCH_ASSOC);
    $wa_number = $wa_row ? preg_replace('/\D/', '', $wa_row['phone_number']) : '919510360227';

} catch (PDOException $e) {
    $stats        = ['total_products' => '50+', 'total_categories' => 6, 'total_orders' => 0, 'total_customers' => 0];
    $testimonials = [];
    $categories   = [];
    $top_products = [];
    $wa_number    = '919510360227';
}

/* ── Static team data (extend or move to DB as needed) ── */
$team = [
    [
        'name'    => 'Amit Ghoyal',
        'role'    => 'Founder & Studio Director',
        'bio'     => 'The creative force behind Aakar Creatives. Amit started this studio with a simple belief — every gift should carry a story worth remembering.',
        'initials'=> 'AG',
        'color'   => '#b85c6e',
    ],
    [
        'name'    => 'Manisha Ghoyal',
        'role'    => 'Operations & Support',
        'bio'     => 'The backbone of our workshop. Manisha ensures every order is handled with warmth, precision, and the care it deserves.',
        'initials'=> 'MG',
        'color'   => '#6b85c8',
    ],
    [
        'name'    => 'Viral Ghoyal',
        'role'    => 'Crochet Artisan',
        'bio'     => 'Expert crochet artist who transforms yarn and thread into beautiful handmade creations — each one a small work of art.',
        'initials'=> 'VG',
        'color'   => '#5a9e7a',
    ],
];

$values = [
    [
        'icon' => 'ti-heart',
        'title' => 'Made with love',
        'desc' => 'Every product starts with emotion. We craft things we would be proud to give ourselves.'
    ],
    [
        'icon' => 'ti-needle-thread',
        'title' => 'Handcrafted quality',
        'desc' => 'No two pieces are identical. Each one is finished by hand with careful attention to detail.'
    ],
    [
        'icon' => 'ti-star',
        'title' => 'Premium materials',
        'desc' => 'We source only the finest materials because your gift deserves nothing less.'
    ],
    [
        'icon' => 'ti-truck-delivery',
        'title' => 'Delivered with care',
        'desc' => 'Thoughtfully packaged and shipped so your gift arrives exactly as intended.'
    ],
    [
        'icon' => 'ti-message-circle',
        'title' => 'Personal touch',
        'desc' => 'We work closely with you to create truly custom experiences that feel personal.'
    ],
    [
        'icon' => 'ti-shield-check',
        'title' => 'Quality guaranteed',
        'desc' => 'We stand behind every product. If something is not right, we make it right.'
    ],
];


$page_title = 'About Us — Aakar Creatives';
include 'includes/header.php';

/* ── Helpers ── */
function fmt_num($n) {
    if ($n >= 1000) return round($n / 1000, 1) . 'K+';
    return $n > 0 ? $n . '+' : '—';
}
function stars_html($rating) {
    $out = '';
    for ($i = 1; $i <= 5; $i++) {
        $out .= '<svg width="14" height="14" viewBox="0 0 24 24" fill="' . ($i <= $rating ? '#b85c6e' : 'none') . '" stroke="#b85c6e" stroke-width="1.8"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
    }
    return $out;
}
?>
<style>
/* ═══════════════════════════════════════════════
   ABOUT PAGE — inherits shop.php CSS variables
═══════════════════════════════════════════════ */

/* Breadcrumb */
.breadcrumb{max-width:var(--max-w);margin:0 auto;padding:18px 40px;display:flex;align-items:center;gap:8px;font-size:12.5px;font-weight:600;color:var(--text-light)}
.breadcrumb a{color:var(--text-light);transition:color var(--transition);text-decoration:none}
.breadcrumb a:hover{color:var(--rose)}
.breadcrumb svg{width:14px;height:14px;stroke:var(--text-light);fill:none;stroke-width:2}
.breadcrumb span{color:var(--text-dark);font-weight:700}

/* Page section wrapper */
.ab-section{padding:80px 40px;max-width:var(--max-w);margin:0 auto}
.ab-section.full-bleed{max-width:none;padding-left:0;padding-right:0}
.ab-section-bg{background:var(--rose-pale,#fdf5f7)}

/* Section label */
.section-label{font-size:10px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:var(--rose);margin-bottom:12px;display:inline-flex;align-items:center;gap:10px}
.section-label::before{content:'';width:24px;height:1px;background:var(--gold,#c9a96e)}

/* Section heading */
.section-heading{font-family:var(--ff-body);font-size:clamp(26px,3.5vw,42px);font-weight:700;color:var(--text-dark);letter-spacing:-1px;line-height:1.1;margin-bottom:16px}
.section-heading em{font-family:var(--ff-serif,serif);font-style:italic;font-weight:300;color:var(--rose)}
.section-sub{font-size:15px;color:var(--text-mid);line-height:1.7;max-width:560px}

/* ── HERO ── */
.about-hero{background:linear-gradient(135deg,var(--rose-pale,#fdf5f7) 0%,var(--cream-deep,#f4ede8) 60%,var(--cream,#faf7f4) 100%);border-bottom:1px solid var(--border-light);position:relative;overflow:hidden}
.about-hero::after{content:'';position:absolute;bottom:-80px;right:-80px;width:320px;height:320px;border-radius:50%;background:radial-gradient(circle,rgba(184,92,110,.08) 0%,transparent 70%);pointer-events:none}
.about-hero-inner{max-width:var(--max-w);margin:0 auto;padding:64px 40px 68px;display:grid;grid-template-columns:1fr 420px;gap:64px;align-items:center}
.about-hero-text .section-label{margin-bottom:18px}
.about-hero-text h1{font-family:var(--ff-body);font-size:clamp(32px,4.5vw,54px);font-weight:700;color:var(--text-dark);letter-spacing:-1.5px;line-height:1.05;margin-bottom:20px}
.about-hero-text h1 em{display:block;font-family:var(--ff-serif,serif);font-style:italic;font-weight:300;color:var(--rose)}
.about-hero-text p{font-size:15.5px;color:var(--text-mid);line-height:1.75;margin-bottom:28px;max-width:480px}
.about-hero-btns{display:flex;gap:12px;flex-wrap:wrap}
.btn-primary{display:inline-flex;align-items:center;gap:8px;padding:13px 28px;background:var(--rose);color:#fff;border:none;border-radius:50px;font-family:var(--ff-body);font-size:13px;font-weight:700;letter-spacing:.5px;cursor:pointer;transition:background var(--transition),transform .2s ease;text-decoration:none}
.btn-primary:hover{background:var(--rose-hover);transform:translateY(-2px)}
.btn-outline{display:inline-flex;align-items:center;gap:8px;padding:13px 28px;background:transparent;color:var(--text-dark);border:1.5px solid var(--border);border-radius:50px;font-family:var(--ff-body);font-size:13px;font-weight:700;letter-spacing:.5px;cursor:pointer;transition:all var(--transition);text-decoration:none}
.btn-outline:hover{border-color:var(--rose);color:var(--rose)}
.about-hero-visual{position:relative;height:340px}
.hero-visual-card{background:var(--white);border:1px solid var(--border-light);border-radius:var(--radius);padding:24px 28px;box-shadow:var(--shadow-xs)}
.hero-quote{font-family:var(--ff-serif,serif);font-size:20px;font-style:italic;color:var(--text-dark);line-height:1.55;margin-bottom:20px}
.hero-quote-mark{font-size:48px;color:var(--rose);line-height:1;font-family:var(--ff-serif,serif);display:block;margin-bottom:-10px;opacity:.4}
.hero-quote-author{font-size:13px;font-weight:700;color:var(--rose);letter-spacing:.5px}
.hero-stat-row{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:18px}
.hero-stat{background:var(--cream,#faf7f4);border-radius:10px;padding:14px 10px;text-align:center}
.hero-stat-num{font-size:22px;font-weight:700;color:var(--text-dark);line-height:1}
.hero-stat-label{font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-light);margin-top:4px}

/* ── STORY ── */
.story-grid{display:grid;grid-template-columns:1fr 1fr;gap:72px;align-items:center}
.story-visual{position:relative;height:460px;border-radius:var(--radius);background:linear-gradient(145deg,var(--rose-pale,#fdf5f7),var(--cream-deep,#f4ede8));overflow:hidden;border:1px solid var(--border-light)}
.story-visual-inner{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:16px;padding:32px}
.story-visual-icon{width:80px;height:80px;border-radius:50%;background:rgba(184,92,110,.12);display:flex;align-items:center;justify-content:center}
.story-visual-icon i{font-size:36px;color:var(--rose)}
.story-visual-title{font-family:var(--ff-serif,serif);font-size:26px;font-style:italic;color:var(--text-dark);text-align:center;line-height:1.3}
.story-visual-badge{display:inline-flex;align-items:center;gap:6px;background:var(--rose);color:#fff;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:6px 16px;border-radius:50px}
.story-content .section-label{margin-bottom:16px}
.story-content h2{font-family:var(--ff-body);font-size:clamp(24px,3vw,38px);font-weight:700;color:var(--text-dark);letter-spacing:-1px;line-height:1.1;margin-bottom:20px}
.story-content h2 em{font-family:var(--ff-serif,serif);font-style:italic;font-weight:300;color:var(--rose);display:block}
.story-content p{font-size:14.5px;color:var(--text-mid);line-height:1.8;margin-bottom:16px}
.story-quote{background:var(--rose-bg,#fdf5f7);border-left:3px solid var(--rose);border-radius:0 10px 10px 0;padding:18px 22px;margin:22px 0}
.story-quote p{font-family:var(--ff-serif,serif);font-style:italic;font-size:16px;color:var(--text-dark);margin:0 0 8px}
.story-quote cite{font-size:12px;font-weight:700;color:var(--rose);letter-spacing:.5px;font-style:normal}

/* ── STATS BANNER ── */
.stats-banner{background:var(--text-dark,#1e1519);padding:56px 40px}
.stats-inner{max-width:var(--max-w);margin:0 auto;display:grid;grid-template-columns:repeat(4,1fr);gap:40px;text-align:center}
.stat-item-num{font-size:clamp(32px,4vw,48px);font-weight:700;color:#fff;line-height:1;margin-bottom:8px}
.stat-item-label{font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,.45)}
.stat-item-accent{width:28px;height:2px;background:var(--rose);border-radius:2px;margin:10px auto 0}

/* ── CRAFT / CATEGORIES ── */
.craft-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
.craft-card{background:var(--white);border:1px solid var(--border-light);border-radius:var(--radius);overflow:hidden;transition:transform .3s ease,box-shadow .3s ease,border-color .3s ease;box-shadow:var(--shadow-xs)}
.craft-card:hover{transform:translateY(-5px);box-shadow:var(--shadow-md);border-color:var(--rose-light)}
.craft-card-img{height:180px;overflow:hidden;background:linear-gradient(145deg,var(--rose-pale,#fdf5f7),var(--cream-deep,#f4ede8));display:flex;align-items:center;justify-content:center;position:relative}
.craft-card-img img{width:100%;height:100%;object-fit:cover;transition:transform .5s ease}
.craft-card:hover .craft-card-img img{transform:scale(1.06)}
.craft-card-img-placeholder{display:flex;flex-direction:column;align-items:center;gap:10px}
.craft-card-img-placeholder i{font-size:42px;color:rgba(184,92,110,.35)}
.craft-card-body{padding:18px 20px 22px}
.craft-card-count{font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(184,92,110,.6);margin-bottom:6px}
.craft-card-name{font-size:16px;font-weight:700;color:var(--text-dark);margin-bottom:6px}
.craft-card-desc{font-size:13px;color:var(--text-mid);line-height:1.65}
.craft-card-link{display:inline-flex;align-items:center;gap:5px;margin-top:14px;font-size:12px;font-weight:700;color:var(--rose);text-decoration:none;transition:gap var(--transition)}
.craft-card-link:hover{gap:8px}
.craft-card-link i{font-size:14px}

/* ── VALUES ── */
.values-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
.value-card{background:var(--white);border:1px solid var(--border-light);border-radius:var(--radius);padding:28px 24px;box-shadow:var(--shadow-xs);transition:transform .3s ease,box-shadow .3s ease}
.value-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-md)}
.value-icon-wrap{width:52px;height:52px;border-radius:14px;background:var(--rose-bg,#fdf5f7);display:flex;align-items:center;justify-content:center;margin-bottom:18px}
.value-icon-wrap i{font-size:24px;color:var(--rose)}
.value-title{font-size:15px;font-weight:700;color:var(--text-dark);margin-bottom:8px}
.value-desc{font-size:13px;color:var(--text-mid);line-height:1.7}

/* ── TEAM ── */
.team-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:26px}
.team-card{background:var(--white);border:1px solid var(--border-light);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow-xs);transition:transform .3s ease,box-shadow .3s ease}
.team-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-md)}
.team-card-top{height:200px;display:flex;align-items:center;justify-content:center;position:relative}
.team-avatar{width:88px;height:88px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:30px;font-weight:700;letter-spacing:-1px;border:3px solid rgba(255,255,255,.3);box-shadow:0 4px 20px rgba(0,0,0,.12)}
.team-card-body{padding:22px 22px 26px;text-align:center}
.team-name{font-size:17px;font-weight:700;color:var(--text-dark);margin-bottom:4px}
.team-role{font-size:10.5px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--rose);margin-bottom:12px}
.team-bio{font-size:13px;color:var(--text-mid);line-height:1.7}

/* ── TOP PRODUCTS ── */
.top-products-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:18px}
.mini-product-card{background:var(--white);border:1px solid var(--border-light);border-radius:var(--radius);overflow:hidden;transition:transform .3s ease,box-shadow .3s ease;text-decoration:none;display:block;box-shadow:var(--shadow-xs)}
.mini-product-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-md);border-color:var(--rose-light)}
.mini-product-img{height:160px;background:linear-gradient(145deg,var(--rose-pale,#fdf5f7),var(--cream-deep,#f4ede8));overflow:hidden;position:relative;display:flex;align-items:center;justify-content:center}
.mini-product-img img{width:100%;height:100%;object-fit:cover;transition:transform .5s ease}
.mini-product-card:hover .mini-product-img img{transform:scale(1.07)}
.mini-product-img-placeholder i{font-size:36px;color:rgba(184,92,110,.3)}
.mini-badge{position:absolute;top:10px;left:10px;padding:4px 10px;border-radius:20px;font-size:9px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:#fff}
.mini-product-info{padding:14px 16px 16px}
.mini-cat{font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(184,92,110,.55);margin-bottom:4px}
.mini-name{font-size:13.5px;font-weight:700;color:var(--text-dark);line-height:1.3;margin-bottom:8px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.mini-price{font-size:16px;font-weight:700;color:var(--rose)}
.mini-orig{font-size:11px;font-weight:500;color:rgba(30,21,25,.3);text-decoration:line-through;margin-left:5px}

/* ── TESTIMONIALS ── */
.testimonials-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:20px}
.testi-card{background:var(--white);border:1px solid var(--border-light);border-radius:var(--radius);padding:26px 28px;box-shadow:var(--shadow-xs)}
.testi-stars{display:flex;gap:2px;margin-bottom:14px}
.testi-review{font-size:14px;color:var(--text-mid);line-height:1.75;margin-bottom:18px;font-style:italic}
.testi-footer{display:flex;align-items:center;gap:12px}
.testi-avatar{width:38px;height:38px;border-radius:50%;background:var(--rose-bg,#fdf5f7);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:var(--rose);flex-shrink:0}
.testi-name{font-size:13px;font-weight:700;color:var(--text-dark)}
.testi-handle{font-size:11px;color:var(--text-light)}
.testi-product-tag{margin-top:4px;display:inline-block;font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:rgba(184,92,110,.6)}

/* ── CONTACT / CTA ── */
.cta-section{background:var(--text-dark,#1e1519);padding:80px 40px}
.cta-inner{max-width:var(--max-w);margin:0 auto;display:grid;grid-template-columns:1fr 400px;gap:64px;align-items:start}
.cta-heading{font-family:var(--ff-body);font-size:clamp(26px,3.5vw,42px);font-weight:700;color:#fff;letter-spacing:-1px;line-height:1.1;margin-bottom:16px}
.cta-heading em{font-family:var(--ff-serif,serif);font-style:italic;font-weight:300;color:var(--rose-light,#d4899a);display:block}
.cta-text{font-size:14.5px;color:rgba(255,255,255,.55);line-height:1.8;margin-bottom:28px}
.cta-wa-btn{display:inline-flex;align-items:center;gap:10px;padding:14px 28px;background:#25d366;color:#fff;border:none;border-radius:50px;font-family:var(--ff-body);font-size:13px;font-weight:700;cursor:pointer;transition:transform .2s ease;text-decoration:none;box-shadow:0 4px 16px rgba(37,211,102,.25)}
.cta-wa-btn:hover{transform:translateY(-2px)}
.cta-wa-btn svg{width:18px;height:18px;fill:white;flex-shrink:0}
.cta-form-card{background:#fff;border-radius:var(--radius);padding:32px 28px}
.form-group{margin-bottom:16px}
.form-label{display:block;font-size:11.5px;font-weight:700;color:var(--text-mid);letter-spacing:.5px;margin-bottom:7px}
.form-control{width:100%;padding:12px 16px;border:1.5px solid var(--border,#ecdde3);border-radius:10px;outline:none;font-family:var(--ff-body);font-size:13.5px;color:var(--text-dark);background:var(--cream,#faf7f4);transition:border-color var(--transition)}
.form-control:focus{border-color:var(--rose)}
textarea.form-control{min-height:110px;resize:vertical}
.form-submit-btn{width:100%;padding:13px;background:var(--rose);color:#fff;border:none;border-radius:50px;font-family:var(--ff-body);font-size:13px;font-weight:700;cursor:pointer;transition:background var(--transition);letter-spacing:.5px}
.form-submit-btn:hover{background:var(--rose-hover)}
.contact-info-row{display:flex;gap:10px;margin-bottom:24px;flex-wrap:wrap}
.contact-chip{display:inline-flex;align-items:center;gap:7px;padding:8px 16px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);border-radius:50px;font-size:12px;font-weight:600;color:rgba(255,255,255,.75)}
.contact-chip i{font-size:15px;color:var(--rose-light,#d4899a)}

/* Responsive */
@media(max-width:1100px){
  .about-hero-inner{grid-template-columns:1fr;gap:36px}
  .about-hero-visual{height:auto}
  .story-grid{grid-template-columns:1fr}
  .story-visual{height:300px}
  .craft-grid,.values-grid{grid-template-columns:repeat(2,1fr)}
  .top-products-strip{grid-template-columns:repeat(2,1fr)}
  .stats-inner{grid-template-columns:repeat(2,1fr);gap:28px}
  .cta-inner{grid-template-columns:1fr}
}
@media(max-width:768px){
  .breadcrumb{padding:14px 20px}
  .ab-section{padding:56px 20px}
  .stats-banner,.cta-section{padding:48px 20px}
  .about-hero-inner{padding:44px 20px 48px}
  .team-grid{grid-template-columns:1fr}
  .testimonials-grid{grid-template-columns:1fr}
  .craft-grid,.values-grid{grid-template-columns:1fr}
  .top-products-strip{grid-template-columns:repeat(2,1fr)}
  .stats-inner{grid-template-columns:repeat(2,1fr)}
  .cta-inner{grid-template-columns:1fr}
}
@media(max-width:480px){
  .top-products-strip{grid-template-columns:1fr}
  .stats-inner{grid-template-columns:1fr}
  .about-hero-btns{flex-direction:column;align-items:flex-start}
  .hero-stat-row{grid-template-columns:repeat(3,1fr)}
}
</style>

<!-- BREADCRUMB -->
<nav class="breadcrumb" aria-label="Breadcrumb">
  <a href="index.php">Home</a>
  <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
  <span>About Us</span>
</nav>

<!-- ══════════════════════════════════════════════
     HERO
══════════════════════════════════════════════ -->
<section class="about-hero" aria-label="About Aakar Creatives">
  <div class="about-hero-inner">

    <div class="about-hero-text">
      <div class="section-label">Our Story</div>
      <h1>
        We turn feelings
        <em>into gifts.</em>
      </h1>
      <p>Aakar Creatives is a handcrafted luxury gifting studio built on one belief — that the right gift can turn an ordinary moment into a memory that lasts forever.</p>
      <div class="about-hero-btns">
        <a href="shop.php" class="btn-primary">
          <i class="ti ti-gift" aria-hidden="true"></i> Explore Our Gifts
        </a>
        <a href="#contact" class="btn-outline">
          <i class="ti ti-message-circle" aria-hidden="true"></i> Get in Touch
        </a>
      </div>
    </div>

    <div class="about-hero-visual">
      <div class="hero-visual-card">
        <span class="hero-quote-mark">"</span>
        <p class="hero-quote">Every gift should feel personal, emotional and unforgettable.</p>
        <div class="hero-quote-author">— Amit Ghoyal, Founder</div>
        <div class="hero-stat-row">
          <div class="hero-stat">
            <div class="hero-stat-num"><?= ($stats['total_products'] > 0) ? $stats['total_products'] . '+' : '50+' ?></div>
            <div class="hero-stat-label">Gifts</div>
          </div>
          <div class="hero-stat">
            <div class="hero-stat-num"><?= ($stats['total_categories'] > 0) ? $stats['total_categories'] : '6' ?></div>
            <div class="hero-stat-label">Categories</div>
          </div>
          <div class="hero-stat">
            <div class="hero-stat-num">100%</div>
            <div class="hero-stat-label">Handmade</div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- ══════════════════════════════════════════════
     STATS BANNER
══════════════════════════════════════════════ -->
<div class="stats-banner" role="region" aria-label="Our numbers">
  <div class="stats-inner">
    <div>
      <div class="stat-item-num"><?= ($stats['total_products'] > 0) ? $stats['total_products'] . '+' : '50+' ?></div>
      <div class="stat-item-label">Unique gifts crafted</div>
      <div class="stat-item-accent"></div>
    </div>
    <div>
      <div class="stat-item-num"><?= ($stats['total_customers'] > 0) ? fmt_num($stats['total_customers']) : '200+' ?></div>
      <div class="stat-item-label">Happy customers</div>
      <div class="stat-item-accent"></div>
    </div>
    <div>
      <div class="stat-item-num"><?= ($stats['total_orders'] > 0) ? fmt_num($stats['total_orders']) : '150+' ?></div>
      <div class="stat-item-label">Orders delivered</div>
      <div class="stat-item-accent"></div>
    </div>
    <div>
      <div class="stat-item-num">100%</div>
      <div class="stat-item-label">Handmade with love</div>
      <div class="stat-item-accent"></div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════
     OUR STORY
══════════════════════════════════════════════ -->
<section id="story" aria-label="Our story">
  <div class="ab-section">
    <div class="story-grid">

      <div class="story-visual">
        <div class="story-visual-inner">
          <div class="story-visual-icon">
            <img src="public/images/aakar_creatives_logo.png" alt="aakar-creatives-logo">
          </div>
          <div class="story-visual-title">A family vision,<br>built with love.</div>
          <div class="story-visual-badge">
            <i class="ti ti-map-pin" aria-hidden="true"></i> Surat, India
          </div>
        </div>
      </div>

      <div class="story-content">
        <div class="section-label">Who We Are</div>
        <h2>
          Born from a dream
          <em>to make gifting matter.</em>
        </h2>
        <p>Aakar Creatives began as a family passion project — the idea that gifting, done right, can be one of the most powerful ways to express love, gratitude, and connection.</p>
        <p>What started as a small workshop has grown into a full gifting studio, crafting everything from handmade crochet bouquets and photo frames to custom magazines and embroidered keepsakes. Every product leaves our studio carrying a piece of the emotion you wanted to convey.</p>
        <div class="story-quote">
          <p>Flowers may fade, but emotions stay forever. We make things that stay.</p>
          <cite>— Amit Ghoyal, Founder</cite>
        </div>
        <p>Today Aakar Creatives proudly delivers handcrafted gifting experiences across India — with the same care and warmth as day one.</p>
      </div>

    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════
     WHAT WE CREATE (Dynamic categories)
══════════════════════════════════════════════ -->
<?php if ($categories): ?>
<section class="ab-section-bg" aria-label="Our product categories">
  <div class="ab-section">
    <div style="text-align:center;margin-bottom:48px">
      <div class="section-label" style="justify-content:center">What We Create</div>
      <h2 class="section-heading">Our gifting <em>universe</em></h2>
      <p class="section-sub" style="margin:0 auto">Every category is crafted with its own story, aesthetic, and emotional intention.</p>
    </div>

    <div class="craft-grid">
      <?php
      $cat_icons = [
          'photo-frames'   => 'ti-photo',
          'photo-magazines'=> 'ti-book',
          'gift-boxes'     => 'ti-box',
          'bouquets'       => 'ti-plant',
          'rochet-ouquet'  => 'ti-needle-thread',
          'threaded-memories-shirt' => 'ti-shirt',
      ];
      foreach ($categories as $cat):
          $icon = $cat_icons[$cat['slug']] ?? 'ti-gift';
      ?>
      <div class="craft-card">
        <div class="craft-card-img">
          <?php if ($cat['image_url']): ?>
            <img src="<?= htmlspecialchars($cat['image_url']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" loading="lazy">
          <?php else: ?>
            <div class="craft-card-img-placeholder">
              <i class="ti <?= $icon ?>" aria-hidden="true"></i>
            </div>
          <?php endif ?>
        </div>
        <div class="craft-card-body">
          <div class="craft-card-count"><?= (int)$cat['product_count'] ?> gifts available</div>
          <div class="craft-card-name"><?= htmlspecialchars($cat['name']) ?></div>
          <?php if ($cat['description']): ?>
          <div class="craft-card-desc"><?= htmlspecialchars($cat['description']) ?></div>
          <?php endif ?>
          <a href="shop.php?category=<?= urlencode($cat['slug']) ?>" class="craft-card-link">
            Explore <i class="ti ti-arrow-right" aria-hidden="true"></i>
          </a>
        </div>
      </div>
      <?php endforeach ?>
    </div>
  </div>
</section>
<?php endif ?>

<!-- ══════════════════════════════════════════════
     MOST LOVED PRODUCTS (Dynamic)
══════════════════════════════════════════════ -->
<?php if ($top_products): ?>
<section aria-label="Most loved products">
  <div class="ab-section">
    <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:36px;flex-wrap:wrap;gap:16px">
      <div>
        <div class="section-label">Most Loved</div>
        <h2 class="section-heading" style="margin-bottom:0">Customer <em>favourites</em></h2>
      </div>
      <a href="shop.php" class="btn-outline" style="font-size:12px;padding:10px 22px">
        View All Gifts <i class="ti ti-arrow-right" aria-hidden="true"></i>
      </a>
    </div>

    <div class="top-products-strip">
      <?php foreach ($top_products as $tp): ?>
      <a href="product.php?slug=<?= urlencode($tp['slug']) ?>" class="mini-product-card" aria-label="<?= htmlspecialchars($tp['name']) ?>">
        <div class="mini-product-img">
          <?php if ($tp['primary_image']): ?>
            <img src="<?= htmlspecialchars($tp['primary_image']) ?>" alt="<?= htmlspecialchars($tp['name']) ?>" loading="lazy">
          <?php else: ?>
            <i class="ti ti-heart" aria-hidden="true" class="mini-product-img-placeholder"></i>
          <?php endif ?>
          <?php if ($tp['badge_name']): ?>
          <span class="mini-badge" style="background:<?= htmlspecialchars($tp['badge_color'] ?? '#b85c6e') ?>"><?= htmlspecialchars($tp['badge_name']) ?></span>
          <?php endif ?>
        </div>
        <div class="mini-product-info">
          <div class="mini-cat"><?= htmlspecialchars($tp['cat_name']) ?></div>
          <div class="mini-name"><?= htmlspecialchars($tp['name']) ?></div>
          <div>
            <span class="mini-price">₹<?= number_format($tp['price'], 0) ?></span>
            <?php if ($tp['discount_price']): ?>
            <span class="mini-orig">₹<?= number_format($tp['discount_price'], 0) ?></span>
            <?php endif ?>
          </div>
        </div>
      </a>
      <?php endforeach ?>
    </div>
  </div>
</section>
<?php endif ?>

<!-- ══════════════════════════════════════════════
     OUR VALUES
══════════════════════════════════════════════ -->
<section class="ab-section-bg" aria-label="Our values">
  <div class="ab-section">
    <div style="text-align:center;margin-bottom:48px">
      <div class="section-label" style="justify-content:center">Our Values</div>
      <h2 class="section-heading">What makes us <em>different</em></h2>
      <p class="section-sub" style="margin:0 auto">Six principles that guide every product, every order, every interaction.</p>
    </div>

    <div class="values-grid">
      <?php foreach ($values as $v): ?>
      <div class="value-card">
        <div class="value-icon-wrap">
          <i class="ti <?= $v['icon'] ?>" aria-hidden="true"></i>
        </div>
        <div class="value-title"><?= htmlspecialchars($v['title']) ?></div>
        <div class="value-desc"><?= htmlspecialchars($v['desc']) ?></div>
      </div>
      <?php endforeach ?>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════
     TEAM
══════════════════════════════════════════════ -->
<section aria-label="Our team">
  <div class="ab-section">
    <div style="text-align:center;margin-bottom:48px">
      <div class="section-label" style="justify-content:center">The Team</div>
      <h2 class="section-heading">People behind <em>every gift</em></h2>
    </div>

    <div class="team-grid">
      <?php foreach ($team as $member): ?>
      <div class="team-card">
        <div class="team-card-top" style="background:linear-gradient(145deg,<?= $member['color'] ?>22,<?= $member['color'] ?>11)">
          <div class="team-avatar" style="background:<?= $member['color'] ?>">
            <?= htmlspecialchars($member['initials']) ?>
          </div>
        </div>
        <div class="team-card-body">
          <div class="team-name"><?= htmlspecialchars($member['name']) ?></div>
          <div class="team-role"><?= htmlspecialchars($member['role']) ?></div>
          <div class="team-bio"><?= htmlspecialchars($member['bio']) ?></div>
        </div>
      </div>
      <?php endforeach ?>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════
     TESTIMONIALS (Dynamic)
══════════════════════════════════════════════ -->
<?php if ($testimonials): ?>
<section class="ab-section-bg" aria-label="Customer reviews">
  <div class="ab-section">
    <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:40px;flex-wrap:wrap;gap:16px">
      <div>
        <div class="section-label">Real Stories</div>
        <h2 class="section-heading" style="margin-bottom:0">What customers <em>say</em></h2>
      </div>
    </div>

    <div class="testimonials-grid">
      <?php foreach ($testimonials as $t): ?>
      <div class="testi-card">
        <div class="testi-stars" aria-label="Rating: <?= (int)$t['rating'] ?> out of 5">
          <?= stars_html((int)$t['rating']) ?>
        </div>
        <p class="testi-review">"<?= htmlspecialchars($t['review']) ?>"</p>
        <?php if ($t['product_name']): ?>
        <div class="testi-product-tag">About: <?= htmlspecialchars($t['product_name']) ?></div>
        <?php endif ?>
        <div class="testi-footer">
          <div class="testi-avatar" aria-hidden="true"><?= strtoupper(substr($t['name'], 0, 1)) ?></div>
          <div>
            <div class="testi-name"><?= htmlspecialchars($t['name']) ?></div>
            <?php if ($t['instagram']): ?>
            <div class="testi-handle"><?= htmlspecialchars($t['instagram']) ?></div>
            <?php endif ?>
          </div>
        </div>
      </div>
      <?php endforeach ?>
    </div>
  </div>
</section>
<?php endif ?>

<!-- ══════════════════════════════════════════════
     CONTACT / CTA
══════════════════════════════════════════════ -->
<section id="contact" class="cta-section" aria-label="Contact us">
  <div class="cta-inner">

    <div>
      <div class="section-label" style="color:var(--rose-light,#d4899a)">Let's Connect</div>
      <h2 class="cta-heading">
        Let's create
        <em>something beautiful.</em>
      </h2>
      <p class="cta-text">Reach out for custom orders, personalised gifts, bulk gifting, or just to say hello. We'd love to hear from you.</p>

      <div class="contact-info-row">
        <div class="contact-chip">
          <i class="ti ti-brand-whatsapp" aria-hidden="true"></i>
          +91 <?= htmlspecialchars($wa_number) ?>
        </div>
        <div class="contact-chip">
          <i class="ti ti-map-pin" aria-hidden="true"></i>
          Surat, India
        </div>
      </div>

      <a href="https://wa.me/<?= htmlspecialchars($wa_number) ?>?text=<?= urlencode("Hello Aakar Creatives! 🌸 I'd love to learn more about your handcrafted gifts. Please help me with a custom order. 😊") ?>"
         class="cta-wa-btn" target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp">
        <svg viewBox="0 0 448 512" aria-hidden="true"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
        Chat on WhatsApp
      </a>
    </div>

    <div class="cta-form-card" role="form" aria-label="Contact form">
      <div style="margin-bottom:22px">
        <div style="font-size:17px;font-weight:700;color:var(--text-dark);margin-bottom:4px">Send us a message</div>
        <div style="font-size:13px;color:var(--text-light)">We reply within a few hours.</div>
      </div>

      <form method="POST" action="contact-submit.php" novalidate>
        <div class="form-group">
          <label class="form-label" for="contact-name">Full name</label>
          <input class="form-control" type="text" id="contact-name" name="name" placeholder="Your name" required autocomplete="name">
        </div>
        <div class="form-group">
          <label class="form-label" for="contact-phone">WhatsApp number</label>
          <input class="form-control" type="tel" id="contact-phone" name="phone" placeholder="+91 XXXXXXXXXX" autocomplete="tel">
        </div>
        <div class="form-group">
          <label class="form-label" for="contact-message">Your message</label>
          <textarea class="form-control" id="contact-message" name="message" placeholder="Tell us what you're looking for…" required></textarea>
        </div>
        <button type="submit" class="form-submit-btn">Send Message</button>
      </form>
    </div>

  </div>
</section>

<?php include 'includes/footer.php'; ?>