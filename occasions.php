<?php
/**
 * Aakar Creatives — occasions.php
 * Dynamic, production-ready. Pulls live occasions + products from DB.
 */

$page_key = 'occasions';
if (!isset($pdo)) { require_once __DIR__ . '/includes/db.php'; }

/* ── Active occasion from URL ── */
$active_slug = trim($_GET['occasion'] ?? 'all');

try {
    /* All occasions with product counts */
    $occ_stmt = $pdo->query(
        "SELECT o.id, o.name, o.slug, o.icon_emoji, o.image_url,
                o.banner_color, o.description,
                COUNT(po.product_id) AS gift_count
           FROM occasions o
           LEFT JOIN product_occasions po ON po.occasion_id = o.id
           LEFT JOIN products p ON p.id = po.product_id AND p.status = 'active'
          WHERE o.is_active = 1
          GROUP BY o.id
          ORDER BY o.sort_order ASC, o.name ASC"
    );
    $occasions = $occ_stmt->fetchAll(PDO::FETCH_ASSOC);

    /* Resolve active occasion ID */
    $active_occ = null;
    foreach ($occasions as $o) {
        if ($o['slug'] === $active_slug) { $active_occ = $o; break; }
    }
    if (!$active_occ && $occasions) {
        $active_occ  = $occasions[0];
        $active_slug = $active_occ['slug'];
    }
    $active_occ_id = $active_occ ? (int)$active_occ['id'] : 0;

    /* Products for the active occasion (or all if slug = 'all') */
    if ($active_slug === 'all') {
        $prod_stmt = $pdo->query(
            "SELECT p.id, p.name, p.slug, p.short_description,
                    p.price, p.discount_price, p.delivery_days,
                    p.is_featured, p.is_new_arrival, p.is_trending, p.is_bestseller,
                    p.in_stock, p.views, p.whatsapp_clicks, p.whatsapp_message,
                    b.name AS badge_name, b.color_hex AS badge_color,
                    c.name AS cat_name, c.slug AS cat_slug,
                    (SELECT pm.file_url FROM product_media pm WHERE pm.product_id = p.id AND pm.is_primary = 1 LIMIT 1) AS primary_image
               FROM products p
               LEFT JOIN badges b ON b.id = p.badge_id
               LEFT JOIN categories c ON c.id = p.category_id
              WHERE p.status = 'active'
              ORDER BY p.is_featured DESC, p.views DESC, p.sort_order ASC"
        );
    } else {
        $prod_stmt = $pdo->prepare(
            "SELECT p.id, p.name, p.slug, p.short_description,
                    p.price, p.discount_price, p.delivery_days,
                    p.is_featured, p.is_new_arrival, p.is_trending, p.is_bestseller,
                    p.in_stock, p.views, p.whatsapp_clicks, p.whatsapp_message,
                    b.name AS badge_name, b.color_hex AS badge_color,
                    c.name AS cat_name, c.slug AS cat_slug,
                    (SELECT pm.file_url FROM product_media pm WHERE pm.product_id = p.id AND pm.is_primary = 1 LIMIT 1) AS primary_image
               FROM products p
               JOIN product_occasions po ON po.product_id = p.id
               LEFT JOIN badges b ON b.id = p.badge_id
               LEFT JOIN categories c ON c.id = p.category_id
              WHERE p.status = 'active' AND po.occasion_id = ?
              ORDER BY p.is_featured DESC, p.views DESC, p.sort_order ASC"
        );
        $prod_stmt->execute([$active_occ_id]);
    }
    $products = $prod_stmt->fetchAll(PDO::FETCH_ASSOC);

    /* WA number */
    $wa_row    = $pdo->query("SELECT phone_number FROM whatsapp_settings WHERE is_primary = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $wa_number = $wa_row ? preg_replace('/\D/', '', $wa_row['phone_number']) : '919510360227';

} catch (PDOException $e) {
    $occasions   = [];
    $products    = [];
    $active_occ  = null;
    $active_slug = 'all';
    $wa_number   = '919510360227';
}

/* ── Static occasion icons (fallback when DB has no emoji) ── */
$occ_icons = [
    'all'            => 'ti-gift',
    'anniversary'    => 'ti-heart',
    'birthday'       => 'ti-cake',
    'valentines-day' => 'ti-heart-handshake',
    'rakhi'          => 'ti-ribbon',
    'mothers-day'    => 'ti-flower',
    'friendship-day' => 'ti-users',
    'diwali'         => 'ti-sparkles',
];

$page_title = ($active_occ ? htmlspecialchars($active_occ['name']) . ' Gifts' : 'Shop by Occasion') . ' — Aakar Creatives';

/* JSON for JS */
$products_json = json_encode(array_map(fn($p) => [
    'id'         => (int)$p['id'],
    'name'       => $p['name'],
    'slug'       => $p['slug'],
    'desc'       => $p['short_description'] ?? '',
    'price'      => (float)$p['price'],
    'salePrice'  => $p['discount_price'] ? (float)$p['discount_price'] : null,
    'delivery'   => $p['delivery_days'] ?? '3–5 Working Days',
    'catName'    => $p['cat_name'],
    'catSlug'    => $p['cat_slug'],
    'badgeName'  => $p['badge_name'],
    'badgeColor' => $p['badge_color'],
    'primaryImg' => $p['primary_image'],
    'inStock'    => (bool)$p['in_stock'],
    'isFeatured' => (bool)$p['is_featured'],
    'isNew'      => (bool)$p['is_new_arrival'],
    'isTrending' => (bool)$p['is_trending'],
    'waMessage'  => $p['whatsapp_message'] ?? '',
], $products), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);

include 'includes/header.php';
?>
<style>
/* ═══════════════════════════════════════════════
   OCCASIONS PAGE
   Inherits all CSS variables from main stylesheet
═══════════════════════════════════════════════ */

/* Animations */
@keyframes fadeUp   { from { opacity:0; transform:translateY(18px) } to { opacity:1; transform:none } }
@keyframes fadeIn   { from { opacity:0 } to { opacity:1 } }
@keyframes scaleIn  { from { opacity:0; transform:scale(.96) } to { opacity:1; transform:scale(1) } }

/* Breadcrumb */
.breadcrumb{max-width:var(--max-w);margin:0 auto;padding:18px 40px;display:flex;align-items:center;gap:8px;font-size:12.5px;font-weight:600;color:var(--text-light)}
.breadcrumb a{color:var(--text-light);text-decoration:none;transition:color var(--transition)}
.breadcrumb a:hover{color:var(--rose)}
.breadcrumb svg{width:14px;height:14px;stroke:var(--text-light);fill:none;stroke-width:2}
.breadcrumb span{color:var(--text-dark);font-weight:700}

/* ── HERO ── */
.occ-hero{background:linear-gradient(135deg,var(--rose-pale,#fdf5f7) 0%,var(--cream-deep,#f4ede8) 60%,var(--cream,#faf7f4) 100%);border-bottom:1px solid var(--border-light);overflow:hidden;position:relative}
.occ-hero::before{content:'';position:absolute;top:-100px;right:-100px;width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(184,92,110,.07) 0%,transparent 70%);pointer-events:none}
.occ-hero-inner{max-width:var(--max-w);margin:0 auto;padding:56px 40px 48px;display:flex;flex-direction:column;align-items:center;text-align:center}
.occ-hero-tag{font-size:10px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:var(--rose);margin-bottom:16px;display:inline-flex;align-items:center;gap:10px;animation:fadeIn .5s ease both}
.occ-hero-tag::before,.occ-hero-tag::after{content:'';width:24px;height:1px;background:var(--gold,#c9a96e)}
.occ-hero-inner h1{font-family:var(--ff-body);font-size:clamp(28px,4vw,48px);font-weight:700;color:var(--text-dark);letter-spacing:-1.5px;line-height:1.05;margin-bottom:12px;animation:fadeUp .5s .08s ease both}
.occ-hero-inner h1 em{font-family:var(--ff-serif,serif);font-style:italic;font-weight:300;color:var(--rose)}
.occ-hero-sub{font-size:15px;font-weight:500;color:var(--text-mid);max-width:460px;line-height:1.7;animation:fadeUp .5s .16s ease both}

/* ── OCCASION TABS ── */
.occ-tabs-wrap{background:var(--white);border-bottom:1px solid var(--border-light);position:sticky;top:0;z-index:200;box-shadow:0 1px 0 var(--border-light)}
.occ-tabs-inner{max-width:var(--max-w);margin:0 auto;padding:0 40px;display:flex;gap:0;overflow-x:auto;scrollbar-width:none;-ms-overflow-style:none}
.occ-tabs-inner::-webkit-scrollbar{display:none}
.occ-tab{display:inline-flex;align-items:center;gap:7px;padding:16px 20px;font-family:var(--ff-body);font-size:12.5px;font-weight:700;color:var(--text-light);background:none;border:none;border-bottom:2.5px solid transparent;cursor:pointer;white-space:nowrap;transition:color var(--transition),border-color var(--transition);text-decoration:none;position:relative}
.occ-tab i{font-size:16px;transition:transform .25s ease}
.occ-tab:hover{color:var(--text-dark)}
.occ-tab.active{color:var(--rose);border-bottom-color:var(--rose)}
.occ-tab.active i{transform:scale(1.15)}
.occ-tab-count{font-size:10px;font-weight:700;background:var(--cream-deep,#f4ede8);color:var(--text-light);border-radius:20px;padding:2px 7px;transition:all var(--transition)}
.occ-tab.active .occ-tab-count{background:rgba(184,92,110,.12);color:var(--rose)}

/* ── ACTIVE OCCASION BANNER ── */
.occ-banner{padding:36px 40px 0;max-width:var(--max-w);margin:0 auto}
.occ-banner-inner{background:var(--white);border:1px solid var(--border-light);border-radius:var(--radius);padding:28px 32px;display:flex;align-items:center;gap:24px;box-shadow:var(--shadow-xs);animation:scaleIn .35s ease both}
.occ-banner-icon{width:64px;height:64px;border-radius:18px;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:var(--rose-bg,#fdf5f7)}
.occ-banner-icon i{font-size:30px;color:var(--rose)}
.occ-banner-text h2{font-size:20px;font-weight:700;color:var(--text-dark);margin-bottom:4px}
.occ-banner-text p{font-size:13.5px;color:var(--text-mid);line-height:1.6;margin:0}
.occ-banner-count{margin-left:auto;text-align:center;flex-shrink:0;padding-left:24px;border-left:1px solid var(--border-light)}
.occ-banner-count-num{font-size:28px;font-weight:700;color:var(--rose);line-height:1}
.occ-banner-count-label{font-size:10.5px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-light);margin-top:4px}

/* ── MAIN LAYOUT ── */
.occ-main{max-width:var(--max-w);margin:0 auto;padding:28px 40px 80px}

/* Toolbar */
.occ-toolbar{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:13px 18px;background:var(--white);border:1px solid var(--border-light);border-radius:var(--radius);margin-bottom:22px;box-shadow:var(--shadow-xs);flex-wrap:wrap}
.occ-result-count{font-size:13px;font-weight:600;color:var(--text-mid)}
.occ-result-count strong{color:var(--text-dark)}
.toolbar-right{display:flex;align-items:center;gap:10px}
.sort-select{border:1.5px solid var(--border);border-radius:8px;padding:8px 14px;font-family:var(--ff-body);font-size:12.5px;font-weight:600;color:var(--text-dark);background:var(--white);outline:none;cursor:pointer;transition:border-color var(--transition)}
.sort-select:focus{border-color:var(--rose)}
.view-toggle{display:flex;border:1.5px solid var(--border);border-radius:8px;overflow:hidden}
.view-btn{width:36px;height:36px;display:flex;align-items:center;justify-content:center;background:none;border:none;cursor:pointer;color:var(--text-light);transition:all var(--transition)}
.view-btn:hover{background:var(--rose-bg,#fdf5f7);color:var(--rose)}
.view-btn.active{background:var(--rose);color:white}
.view-btn svg{width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:1.8}

/* ── PRODUCTS GRID ── */
#occasionsGrid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
#occasionsGrid.list-view{grid-template-columns:1fr}

/* Product card */
.product-card{background:var(--white);border:1px solid var(--border-light);border-radius:var(--radius);overflow:hidden;transition:transform .3s ease,box-shadow .3s ease,border-color .3s ease;box-shadow:var(--shadow-xs);position:relative;display:flex;flex-direction:column;text-decoration:none}
.product-card:hover{transform:translateY(-5px);box-shadow:var(--shadow-md);border-color:var(--rose-light)}
.product-card.out-of-stock{opacity:.65}
.product-img-wrap{position:relative;height:220px;overflow:hidden;background:var(--cream,#faf7f4);flex-shrink:0}
.product-img-wrap img{width:100%;height:100%;object-fit:cover;transition:transform .55s ease;display:block}
.product-card:hover .product-img-wrap img{transform:scale(1.07)}
.product-img-placeholder{width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;font-size:11px;font-weight:600;color:var(--text-light);letter-spacing:.8px;text-transform:uppercase}
.product-hover-overlay{position:absolute;inset:0;background:rgba(30,21,25,.38);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .28s ease;backdrop-filter:blur(2px)}
.product-card:hover .product-hover-overlay{opacity:1}
.view-details-btn{padding:10px 24px;background:white;border:none;border-radius:50px;font-family:var(--ff-body);font-size:12px;font-weight:700;color:var(--text-dark);letter-spacing:.5px;box-shadow:var(--shadow-md);transform:translateY(10px);transition:transform .22s ease;pointer-events:none}
.product-card:hover .view-details-btn{transform:translateY(0)}
.wishlist-btn{position:absolute;top:12px;right:12px;width:34px;height:34px;background:rgba(255,255,255,.95);border-radius:50%;border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 10px rgba(0,0,0,.1);backdrop-filter:blur(8px);transition:all var(--transition);z-index:2}
.wishlist-btn:hover{background:var(--rose);transform:scale(1.1)}
.wishlist-btn svg{width:15px;height:15px;stroke:#b85c6e;fill:none;stroke-width:2;transition:all var(--transition)}
.wishlist-btn:hover svg,.wishlist-btn.wishlisted svg{stroke:var(--rose);fill:var(--rose)}
.wishlist-btn:hover svg{stroke:white;fill:white}
.product-badge-tag{position:absolute;top:12px;left:12px;padding:5px 13px;border-radius:20px;font-size:9px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:white;z-index:2}
.out-of-stock-tag{position:absolute;bottom:12px;left:12px;padding:4px 12px;border-radius:20px;font-size:9.5px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;background:rgba(30,21,25,.75);color:rgba(255,255,255,.85);z-index:2}
.product-info{padding:16px 16px 12px;display:flex;flex-direction:column;flex:1}
.product-cat-pill{font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(184,92,110,.6);margin-bottom:5px}
.product-name{font-size:15px;font-weight:700;color:var(--text-dark);line-height:1.35;margin-bottom:5px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.product-desc{font-size:12.5px;font-weight:500;color:rgba(30,21,25,.5);line-height:1.55;margin-bottom:12px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;flex:1}
.product-footer{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:auto}
.price-block{display:flex;align-items:center;gap:6px;flex-wrap:wrap}
.product-price{font-size:19px;font-weight:700;color:#b85c6e}
.product-orig{font-size:12px;font-weight:500;color:rgba(30,21,25,.3);text-decoration:line-through}
.product-pct{font-size:10.5px;font-weight:700;color:#2e8b57;background:#e8f5ef;padding:3px 7px;border-radius:20px}
.card-actions-row{display:flex;gap:6px;margin-top:10px}
.card-enquire-btn{flex:1;display:flex;align-items:center;justify-content:center;gap:5px;padding:8px 10px;background:var(--rose);color:white;border:none;border-radius:8px;font-family:var(--ff-body);font-size:11.5px;font-weight:700;cursor:pointer;transition:background var(--transition);white-space:nowrap}
.card-enquire-btn:hover{background:var(--rose-hover)}
.card-enquire-btn svg{width:13px;height:13px;stroke:white;fill:none;stroke-width:2;flex-shrink:0}
.card-wa-btn{width:34px;height:34px;border-radius:8px;background:#25d366;border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;transition:transform .2s ease;box-shadow:0 2px 8px rgba(37,211,102,.25)}
.card-wa-btn:hover{transform:scale(1.08)}
.card-wa-btn svg{width:15px;height:15px;fill:white}

/* List view */
#occasionsGrid.list-view .product-card{display:grid;grid-template-columns:200px 1fr;flex-direction:unset}
#occasionsGrid.list-view .product-img-wrap{height:100%;min-height:160px;border-radius:0}
#occasionsGrid.list-view .product-info{padding:20px 22px}
#occasionsGrid.list-view .product-name{font-size:17px;-webkit-line-clamp:unset}
#occasionsGrid.list-view .product-desc{-webkit-line-clamp:3}

/* Skeleton */
.skeleton-card{background:var(--white);border:1px solid var(--border-light);border-radius:var(--radius);overflow:hidden}
.skeleton-img{height:220px;background:linear-gradient(90deg,var(--cream,#faf7f4) 25%,var(--cream-deep,#f4ede8) 50%,var(--cream,#faf7f4) 75%);background-size:200% 100%;animation:shimmer 1.4s infinite}
.skeleton-info{padding:16px 18px}
.skeleton-line{height:12px;border-radius:6px;background:linear-gradient(90deg,var(--cream,#faf7f4) 25%,var(--cream-deep,#f4ede8) 50%,var(--cream,#faf7f4) 75%);background-size:200% 100%;animation:shimmer 1.4s infinite;margin-bottom:10px}
.skeleton-line.short{width:60%}
.skeleton-line.price{width:40%;height:18px;margin-top:14px}
@keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}

/* Empty state */
.empty-state{grid-column:1/-1;text-align:center;padding:80px 20px;color:var(--text-light);animation:fadeUp .4s ease both}
.empty-state-icon{width:72px;height:72px;border-radius:50%;background:var(--rose-bg,#fdf5f7);display:flex;align-items:center;justify-content:center;margin:0 auto 20px}
.empty-state-icon i{font-size:32px;color:rgba(184,92,110,.4)}
.empty-state h3{font-size:20px;font-weight:700;color:var(--text-dark);margin-bottom:8px}
.empty-state p{font-size:14px;max-width:300px;margin:0 auto 24px;line-height:1.65}
.empty-state-btn{display:inline-flex;align-items:center;gap:7px;padding:11px 28px;background:var(--rose);color:white;border:none;border-radius:50px;font-family:var(--ff-body);font-size:13px;font-weight:700;cursor:pointer;transition:background var(--transition);text-decoration:none}
.empty-state-btn:hover{background:var(--rose-hover)}

/* Pagination */
.pagination-wrap{display:flex;justify-content:center;align-items:center;gap:8px;margin-top:40px;flex-wrap:wrap}
.page-nav-btn{padding:8px 18px;border:1.5px solid var(--border);background:var(--white);border-radius:8px;font-family:var(--ff-body);font-size:13px;font-weight:700;color:var(--text-dark);cursor:pointer;transition:all var(--transition)}
.page-nav-btn:hover:not(:disabled){border-color:var(--rose);color:var(--rose)}
.page-nav-btn:disabled{opacity:.4;cursor:not-allowed}
.page-num-btn{width:38px;height:38px;display:flex;align-items:center;justify-content:center;border:1.5px solid var(--border);background:var(--white);border-radius:50%;font-family:var(--ff-body);font-size:13px;font-weight:700;color:var(--text-dark);cursor:pointer;transition:all var(--transition)}
.page-num-btn:hover{border-color:var(--rose);color:var(--rose)}
.page-num-btn.active{background:var(--rose);border-color:var(--rose);color:white}

/* Mobile bottom tab bar for occasions */
.occ-mobile-nav{display:none}

/* Responsive */
@media(max-width:1100px){#occasionsGrid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:768px){
  .breadcrumb{padding:14px 20px}
  .occ-hero-inner{padding:40px 20px 36px}
  .occ-tabs-inner{padding:0 20px}
  .occ-tab{padding:14px 16px;font-size:12px}
  .occ-banner{padding:20px 20px 0}
  .occ-banner-inner{padding:20px;gap:14px}
  .occ-banner-count{display:none}
  .occ-main{padding:20px 20px 60px}
  #occasionsGrid{grid-template-columns:repeat(2,1fr);gap:14px}
  #occasionsGrid.list-view{grid-template-columns:1fr}
  #occasionsGrid.list-view .product-card{grid-template-columns:130px 1fr}
  .product-img-wrap{height:180px}
}
@media(max-width:480px){
  #occasionsGrid{grid-template-columns:1fr}
  .occ-toolbar{gap:10px}
}
</style>

<!-- BREADCRUMB -->
<nav class="breadcrumb" aria-label="Breadcrumb">
  <a href="index.php">Home</a>
  <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
  <?php if ($active_slug !== 'all' && $active_occ): ?>
    <a href="occasions.php">Occasions</a>
    <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
    <span><?= htmlspecialchars($active_occ['name']) ?></span>
  <?php else: ?>
    <span>Shop by Occasion</span>
  <?php endif ?>
</nav>

<!-- ══════════════════════════════════════════════
     HERO
══════════════════════════════════════════════ -->
<section class="occ-hero" aria-label="Shop by occasion hero">
  <div class="occ-hero-inner">
    <div class="occ-hero-tag">Curated Collections</div>
    <h1>
      Gifts for every <em>occasion</em>
    </h1>
    <p class="occ-hero-sub">Browse handcrafted gifts organised around the moments that matter most to you.</p>
  </div>
</section>

<!-- ══════════════════════════════════════════════
     OCCASION TABS
══════════════════════════════════════════════ -->
<nav class="occ-tabs-wrap" aria-label="Occasion navigation" id="occTabsBar">
  <div class="occ-tabs-inner" id="occTabsInner">
    <?php foreach ($occasions as $occ):
        $icon  = $occ_icons[$occ['slug']] ?? 'ti-gift';
        $is_active = ($occ['slug'] === $active_slug);
    ?>
    <a class="occ-tab<?= $is_active ? ' active' : '' ?>"
       href="occasions.php?occasion=<?= urlencode($occ['slug']) ?>"
       data-slug="<?= htmlspecialchars($occ['slug']) ?>"
       aria-current="<?= $is_active ? 'page' : 'false' ?>">
      <i class="ti <?= $icon ?>" aria-hidden="true"></i>
      <?= htmlspecialchars($occ['name']) ?>
      <span class="occ-tab-count"><?= (int)$occ['gift_count'] ?></span>
    </a>
    <?php endforeach ?>
  </div>
</nav>

<!-- ══════════════════════════════════════════════
     ACTIVE OCCASION BANNER
══════════════════════════════════════════════ -->
<?php if ($active_occ): ?>
<div class="occ-banner" id="occBanner">
  <div class="occ-banner-inner">
    <div class="occ-banner-icon">
      <i class="ti <?= $occ_icons[$active_occ['slug']] ?? 'ti-gift' ?>" aria-hidden="true"></i>
    </div>
    <div class="occ-banner-text">
      <h2><?= htmlspecialchars($active_occ['name']) ?> Gifts</h2>
      <p><?= $active_occ['description']
            ? htmlspecialchars($active_occ['description'])
            : 'Handcrafted gifts specially curated for this occasion — made with love and premium finishing.' ?></p>
    </div>
    <div class="occ-banner-count">
      <div class="occ-banner-count-num"><?= count($products) ?></div>
      <div class="occ-banner-count-label">Gifts available</div>
    </div>
  </div>
</div>
<?php endif ?>

<!-- ══════════════════════════════════════════════
     MAIN PRODUCT AREA
══════════════════════════════════════════════ -->
<main class="occ-main" id="occMain" aria-label="Gifts for <?= htmlspecialchars($active_occ['name'] ?? 'all occasions') ?>">

  <!-- Toolbar -->
  <div class="occ-toolbar">
    <div class="occ-result-count">Showing <strong id="resultCount"><?= count($products) ?></strong> gifts</div>
    <div class="toolbar-right">
      <select class="sort-select" id="sortSelect" onchange="renderGrid()" aria-label="Sort gifts">
        <option value="featured">Featured First</option>
        <option value="popular">Most Popular</option>
        <option value="new">Newest</option>
        <option value="price-low">Price: Low → High</option>
        <option value="price-high">Price: High → Low</option>
        <option value="discount">Best Discount</option>
      </select>
      <div class="view-toggle" role="group" aria-label="View mode">
        <button class="view-btn active" id="gridViewBtn" title="Grid view" onclick="setView('grid')">
          <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        </button>
        <button class="view-btn" id="listViewBtn" title="List view" onclick="setView('list')">
          <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Grid / skeleton -->
  <div id="occasionsGrid" aria-live="polite">
    <?php for ($i = 0; $i < 6; $i++): ?>
    <div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-info"><div class="skeleton-line short"></div><div class="skeleton-line"></div><div class="skeleton-line"></div><div class="skeleton-line price"></div></div></div>
    <?php endfor ?>
  </div>

  <!-- Pagination -->
  <div class="pagination-wrap" id="paginationWrap"></div>

</main>

<script>
/* ════ DATA ════ */
const PRODUCTS  = <?= $products_json ?>;
const WA_NUMBER = "<?= htmlspecialchars($wa_number, ENT_QUOTES) ?>";
const PER_PAGE  = 12;

/* ════ STATE ════ */
let currentPage = 1;
let sortedList  = [...PRODUCTS];
let currentView = 'grid';

/* ════ INIT ════ */
document.addEventListener('DOMContentLoaded', () => {
  /* Scroll active tab into view */
  const activeTab = document.querySelector('.occ-tab.active');
  if (activeTab) activeTab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });

  renderGrid();
});

/* ════ SORT + RENDER ════ */
function renderGrid() {
  const val = document.getElementById('sortSelect').value;
  sortedList = [...PRODUCTS];
  switch(val) {
    case 'popular':    sortedList.sort((a,b) => 0); break; /* already ordered by views from PHP */
    case 'new':        sortedList.sort((a,b) => (b.isNew?1:0)-(a.isNew?1:0)); break;
    case 'price-low':  sortedList.sort((a,b) => a.price-b.price); break;
    case 'price-high': sortedList.sort((a,b) => b.price-a.price); break;
    case 'discount':   sortedList.sort((a,b) => {
      const da = a.salePrice ? (1-a.price/a.salePrice) : 0;
      const db = b.salePrice ? (1-b.price/b.salePrice) : 0;
      return db-da;
    }); break;
    default: sortedList.sort((a,b)=>(b.isFeatured?1:0)-(a.isFeatured?1:0));
  }
  currentPage = 1;
  renderPage();
}

function renderPage() {
  const grid = document.getElementById('occasionsGrid');
  const countEl = document.getElementById('resultCount');
  if (countEl) countEl.textContent = sortedList.length;

  if (!sortedList.length) {
    grid.innerHTML = `<div class="empty-state">
      <div class="empty-state-icon"><i class="ti ti-gift" aria-hidden="true"></i></div>
      <h3>No gifts found</h3>
      <p>We're adding new gifts for this occasion soon. Explore all our collections in the meantime.</p>
      <a href="occasions.php?occasion=all" class="empty-state-btn">
        <i class="ti ti-arrow-left" aria-hidden="true"></i> Browse All Gifts
      </a>
    </div>`;
    document.getElementById('paginationWrap').innerHTML = '';
    return;
  }

  const start = (currentPage-1)*PER_PAGE;
  const items = sortedList.slice(start, start+PER_PAGE);
  grid.innerHTML = items.map(p => buildCard(p)).join('');

  /* Staggered fade-up animation */
  grid.querySelectorAll('.product-card').forEach((card, i) => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(18px)';
    setTimeout(() => {
      card.style.transition = 'opacity .35s ease, transform .35s ease, box-shadow .3s ease, border-color .3s ease';
      card.style.opacity = '1';
      card.style.transform = 'none';
    }, i * 40);
  });

  renderPagination();
}

/* ════ PAGINATION ════ */
function renderPagination() {
  const wrap  = document.getElementById('paginationWrap');
  const total = Math.ceil(sortedList.length / PER_PAGE);
  if (total <= 1) { wrap.innerHTML = ''; return; }
  let html = `<button class="page-nav-btn" ${currentPage===1?'disabled':''} onclick="goPage(${currentPage-1})">← Prev</button>`;
  for (let i = 1; i <= total; i++) {
    html += `<button class="page-num-btn ${currentPage===i?'active':''}" onclick="goPage(${i})">${i}</button>`;
  }
  html += `<button class="page-nav-btn" ${currentPage===total?'disabled':''} onclick="goPage(${currentPage+1})">Next →</button>`;
  wrap.innerHTML = html;
}

function goPage(p) {
  currentPage = p;
  renderPage();
  document.getElementById('occMain').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/* ════ CARD BUILDER ════ */
function buildCard(p) {
  const pct = (p.salePrice && p.price) ? Math.round((1-p.price/p.salePrice)*100) : 0;
  const imgHtml = p.primaryImg
    ? `<img src="${esc(p.primaryImg)}" alt="${esc(p.name)}" loading="lazy">`
    : `<div class="product-img-placeholder" style="background:linear-gradient(150deg,#f7eef0,#edd8dd)">
         <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="rgba(184,92,110,0.25)" stroke-width="1.2">
           <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
         </svg>
         <span>${esc(p.catName)}</span>
       </div>`;

  return `
  <a class="product-card${!p.inStock?' out-of-stock':''}"
     href="product.php?slug=${encodeURIComponent(p.slug)}"
     aria-label="${esc(p.name)}">
    <div class="product-img-wrap">
      ${imgHtml}
      <button class="wishlist-btn"
              onclick="event.preventDefault();event.stopPropagation();toggleWish(this,${p.id})"
              aria-label="Add ${esc(p.name)} to wishlist">
        <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      </button>
      ${p.badgeName ? `<span class="product-badge-tag" style="background:${p.badgeColor||'#b85c6e'}">${esc(p.badgeName)}</span>` : ''}
      ${!p.inStock  ? `<span class="out-of-stock-tag">Out of Stock</span>` : ''}
      <div class="product-hover-overlay">
        <span class="view-details-btn">View Details</span>
      </div>
    </div>
    <div class="product-info">
      <div class="product-cat-pill">${esc(p.catName)}</div>
      <div class="product-name">${esc(p.name)}</div>
      <div class="product-desc">${esc(p.desc)}</div>
      <div class="product-footer">
        <div class="price-block">
          <span class="product-price">₹${fmt(p.price)}</span>
          ${p.salePrice ? `<span class="product-orig">₹${fmt(p.salePrice)}</span>` : ''}
          ${pct > 0   ? `<span class="product-pct">-${pct}%</span>` : ''}
        </div>
      </div>
      <div class="card-actions-row">
        <button class="card-enquire-btn"
                onclick="event.preventDefault();event.stopPropagation();addCart(${p.id})"
                ${!p.inStock?'disabled style="opacity:.5;cursor:not-allowed"':''}>
          <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          Add to Cart
        </button>
        <button class="card-wa-btn"
                onclick="event.preventDefault();event.stopPropagation();quickWA(${p.id})"
                aria-label="WhatsApp enquiry for ${esc(p.name)}">
          <svg viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
        </button>
      </div>
    </div>
  </a>`;
}

/* ════ ACTIONS ════ */
function addCart(id) {
  const p = PRODUCTS.find(x => x.id === id);
  if (!p || !p.inStock) return;
  try {
    const cart = JSON.parse(localStorage.getItem('aakar_cart') || '[]');
    const idx  = cart.findIndex(i => String(i.id) === String(p.id));
    if (idx > -1) cart[idx].qty = Math.min((cart[idx].qty||1)+1, 10);
    else cart.push({ id:p.id, name:p.name, price:p.price, discount_price:p.salePrice, image_url:p.primaryImg, category:p.catName, qty:1 });
    localStorage.setItem('aakar_cart', JSON.stringify(cart));
    if (window.updateAllBadges) window.updateAllBadges();
  } catch(e) {}
  toast(p.name + ' added to cart! 🛍', 'success');
}

function toggleWish(btn, id) {
  const p = PRODUCTS.find(x => x.id === id);
  if (!p) return;
  let inList = false;
  try {
    const wl = JSON.parse(localStorage.getItem('aakar_wishlist') || '[]');
    const ei = wl.findIndex(w => String(w.id) === String(p.id));
    if (ei > -1) { wl.splice(ei, 1); inList = false; }
    else { wl.push({ id:p.id, name:p.name, price:p.price, discount_price:p.salePrice, image_url:p.primaryImg, category:p.catName }); inList = true; }
    localStorage.setItem('aakar_wishlist', JSON.stringify(wl));
    if (window.updateAllBadges) window.updateAllBadges();
  } catch(e) {}
  btn.classList.toggle('wishlisted', inList);
  toast(inList ? 'Added to wishlist ❤️' : 'Removed from wishlist', inList ? 'success' : 'info');
}

function quickWA(id) {
  const p = PRODUCTS.find(x => x.id === id);
  if (!p) return;
  const msg = p.waMessage
    ? p.waMessage.replace('{product_name}',p.name).replace('{price}',fmt(p.price)).replace('{category}',p.catName)
    : `Hello Aakar Creatives! 🌸\n\nI'm interested in:\n\n📦 *${p.name}*\n🏷 *Category:* ${p.catName}\n💰 *Price:* ₹${fmt(p.price)}\n🚚 *Delivery:* ${p.delivery||'3–5 Working Days'}\n\nKindly share more details. 😊`;
  window.open(`https://wa.me/${WA_NUMBER}?text=${encodeURIComponent(msg)}`, '_blank');
}

/* ════ VIEW TOGGLE ════ */
function setView(v) {
  currentView = v;
  document.getElementById('occasionsGrid').classList.toggle('list-view', v==='list');
  document.getElementById('gridViewBtn').classList.toggle('active', v==='grid');
  document.getElementById('listViewBtn').classList.toggle('active', v==='list');
}

/* ════ TOAST ════ */
function toast(msg, type) {
  if (window.acToast) { window.acToast(msg, type||'info'); return; }
  let c = document.getElementById('_oc_toast');
  if (!c) {
    c = document.createElement('div'); c.id='_oc_toast';
    c.style.cssText='position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none';
    document.body.appendChild(c);
    const s = document.createElement('style');
    s.textContent='@keyframes _tin{from{opacity:0;transform:translateX(24px)}to{opacity:1;transform:none}}@keyframes _tout{to{opacity:0;transform:translateX(24px)}}';
    document.head.appendChild(s);
  }
  const t = document.createElement('div');
  t.style.cssText='background:#2d1a22;color:#fff;padding:11px 18px;border-radius:10px;font-size:13.5px;font-weight:600;border-left:3px solid #b85c6e;animation:_tin .28s ease both';
  t.textContent = msg;
  c.appendChild(t);
  setTimeout(() => { t.style.animation='_tout .28s ease both'; setTimeout(()=>t.remove(),300); }, 2400);
}

/* ════ UTILS ════ */
function fmt(n) { return Number(n).toLocaleString('en-IN'); }
function esc(s) { if (!s) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>

<?php include 'includes/footer.php'; ?>