<?php
/**
 * shop.php — Aakar Creatives
 * Product cards link directly to product.php — no quick-view modal.
 */

$page_key = 'shop';
if (!isset($pdo)) { require_once __DIR__ . '/includes/db.php'; }
include 'includes/header.php';

$pre_cat    = trim($_GET['category'] ?? '');
$pre_occ    = trim($_GET['occasion'] ?? '');
$pre_search = trim($_GET['q'] ?? '');

try {
    $wa_stmt   = $pdo->query("SELECT phone_number FROM whatsapp_settings WHERE is_primary = 1 LIMIT 1");
    $wa_row    = $wa_stmt->fetch(PDO::FETCH_ASSOC);
    $wa_number = $wa_row ? preg_replace('/\D/', '', $wa_row['phone_number']) : '919510360227';
} catch (PDOException $e) { $wa_number = '919510360227'; }

$cat_stmt = $pdo->query(
    "SELECT c.id, c.name, c.slug, COUNT(p.id) AS product_count
       FROM categories c
       LEFT JOIN products p ON p.category_id = c.id AND p.status = 'active'
      WHERE c.is_active = 1
      GROUP BY c.id ORDER BY c.sort_order ASC, c.name ASC"
);
$categories   = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);
$total_active = array_sum(array_column($categories, 'product_count'));

$occ_stmt = $pdo->query(
    "SELECT o.id, o.name, o.slug, o.icon_emoji, COUNT(po.product_id) AS gift_count
       FROM occasions o
       LEFT JOIN product_occasions po ON po.occasion_id = o.id
      WHERE o.is_active = 1 AND o.slug != 'all'
      GROUP BY o.id ORDER BY o.sort_order ASC"
);
$occasions = $occ_stmt->fetchAll(PDO::FETCH_ASSOC);

$badge_stmt = $pdo->query("SELECT id, name, color_hex FROM badges WHERE is_active = 1 ORDER BY id ASC");
$badges     = $badge_stmt->fetchAll(PDO::FETCH_ASSOC);

$price_min = 149;
$price_max = 5000;

$prod_stmt = $pdo->query(
    "SELECT p.id, p.name, p.slug, p.short_description,
        p.price, p.discount_price, p.delivery_days,
        p.is_featured, p.is_new_arrival, p.is_trending, p.is_bestseller,
        p.in_stock, p.views, p.whatsapp_clicks, p.tags, p.whatsapp_message,
        b.id AS badge_id, b.name AS badge_name, b.color_hex AS badge_color,
        c.id AS cat_id, c.name AS cat_name, c.slug AS cat_slug,
        (SELECT pm.file_url FROM product_media pm WHERE pm.product_id = p.id AND pm.is_primary = 1 LIMIT 1) AS primary_image,
        (SELECT GROUP_CONCAT(o.name ORDER BY o.sort_order SEPARATOR '||')
           FROM product_occasions po JOIN occasions o ON o.id = po.occasion_id
          WHERE po.product_id = p.id) AS occasion_names,
        (SELECT GROUP_CONCAT(o.slug ORDER BY o.sort_order SEPARATOR '||')
           FROM product_occasions po JOIN occasions o ON o.id = po.occasion_id
          WHERE po.product_id = p.id) AS occasion_slugs
       FROM products p
       LEFT JOIN badges b ON b.id = p.badge_id
       LEFT JOIN categories c ON c.id = p.category_id
      WHERE p.status = 'active'
      ORDER BY p.is_featured DESC, p.sort_order ASC, p.views DESC"
);
$products_raw = $prod_stmt->fetchAll(PDO::FETCH_ASSOC);

$products_js = [];
foreach ($products_raw as $p) {
    $occasions_arr = $p['occasion_names'] ? array_values(array_filter(explode('||', $p['occasion_names']))) : [];
    $occ_slugs_arr = $p['occasion_slugs'] ? array_values(array_filter(explode('||', $p['occasion_slugs']))) : [];
    $products_js[] = [
        'id'           => (int)$p['id'],
        'name'         => $p['name'],
        'slug'         => $p['slug'],
        'desc'         => $p['short_description'] ?? '',
        'price'        => (float)$p['price'],
        'salePrice'    => $p['discount_price'] ? (float)$p['discount_price'] : null,
        'delivery'     => $p['delivery_days'] ?? '3–5 Working Days',
        'catId'        => (int)$p['cat_id'],
        'catName'      => $p['cat_name'],
        'catSlug'      => $p['cat_slug'],
        'badgeName'    => $p['badge_name'],
        'badgeColor'   => $p['badge_color'],
        'primaryImg'   => $p['primary_image'],
        'occasions'    => $occasions_arr,
        'occSlugs'     => $occ_slugs_arr,
        'tags'         => $p['tags'] ? array_map('trim', explode(',', $p['tags'])) : [],
        'inStock'      => (bool)$p['in_stock'],
        'isFeatured'   => (bool)$p['is_featured'],
        'isNew'        => (bool)$p['is_new_arrival'],
        'isTrending'   => (bool)$p['is_trending'],
        'isBestseller' => (bool)$p['is_bestseller'],
        'views'        => (int)$p['views'],
        'waMessage'    => $p['whatsapp_message'] ?? '',
    ];
}
$products_json = json_encode($products_js, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
?>

<style>
/* ══════════════════════════════════════════════════════
   SHOP PAGE STYLES
══════════════════════════════════════════════════════ */

.breadcrumb {
  max-width: var(--max-w); margin: 0 auto; padding: 18px 40px;
  display: flex; align-items: center; gap: 8px;
  font-size: 12.5px; font-weight: 600; color: var(--text-light);
}
.breadcrumb a { color: var(--text-light); transition: color var(--transition); text-decoration: none; }
.breadcrumb a:hover { color: var(--rose); }
.breadcrumb svg { width: 14px; height: 14px; stroke: var(--text-light); fill: none; stroke-width: 2; }
.breadcrumb span { color: var(--text-dark); font-weight: 700; }

.shop-hero {
  background: linear-gradient(135deg, var(--rose-pale) 0%, var(--cream-deep) 60%, var(--cream) 100%);
  border-bottom: 1px solid var(--border-light); position: relative; overflow: hidden;
}
.shop-hero::before {
  content: ''; position: absolute; top: -60px; right: -60px; width: 280px; height: 280px;
  border-radius: 50%; background: radial-gradient(circle, rgba(184,92,110,0.1) 0%, transparent 70%); pointer-events: none;
}
.shop-hero-inner { max-width: var(--max-w); margin: 0 auto; padding: 44px 40px 40px; display: flex; flex-direction: column; align-items: center; text-align: center; }
.shop-hero-tag { font-size: 10px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: var(--rose); margin-bottom: 12px; display: inline-flex; align-items: center; gap: 10px; }
.shop-hero-tag::before { content: ''; width: 24px; height: 1px; background: var(--gold); }
.shop-hero-inner h1 { font-family: var(--ff-body); font-size: clamp(26px,4vw,44px); font-weight: 700; color: var(--text-dark); letter-spacing: -1px; line-height: 1.1; margin-bottom: 10px; }
.shop-hero-inner h1 em { font-family: var(--ff-serif); font-style: italic; font-weight: 300; color: var(--rose); }
.shop-hero-sub { font-size: 14.5px; font-weight: 500; color: var(--text-mid); max-width: 420px; line-height: 1.65; margin: 0 auto; }

.shop-search-wrap { max-width: var(--max-w); margin: 0 auto; padding: 20px 40px 0; }
.shop-search-bar {
  display: flex; align-items: center; background: var(--white); border: 1.5px solid var(--border);
  border-radius: 50px; overflow: hidden; box-shadow: var(--shadow-xs); max-width: 640px;
  transition: border-color var(--transition), box-shadow var(--transition);
}
.shop-search-bar:focus-within { border-color: var(--rose); box-shadow: 0 0 0 3px rgba(184,92,110,0.08); }
.shop-search-bar > svg { width: 18px; height: 18px; stroke: var(--text-light); fill: none; stroke-width: 2; margin-left: 20px; flex-shrink: 0; }
.shop-search-input { flex: 1; border: none; outline: none; padding: 13px 16px; font-family: var(--ff-body); font-size: 14px; font-weight: 500; color: var(--text-dark); background: transparent; }
.shop-search-input::placeholder { color: var(--text-light); }
.shop-search-clear { padding: 0 16px; cursor: pointer; font-size: 20px; color: var(--text-light); display: none; line-height: 1; transition: color var(--transition); background: none; border: none; }
.shop-search-clear:hover { color: var(--rose); }

.shop-layout { max-width: var(--max-w); margin: 0 auto; padding: 28px 40px 80px; display: grid; grid-template-columns: 268px 1fr; gap: 32px; align-items: start; }

/* ── Sidebar ── */
.sidebar { display: flex; flex-direction: column; gap: 18px; position: sticky; top: 94px; }
.filter-card { background: var(--white); border: 1px solid var(--border-light); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow-xs); }
.filter-card-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px 14px; border-bottom: 1px solid var(--border-light); }
.filter-card-title { font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--text-dark); }
.filter-clear-btn { font-size: 11px; font-weight: 600; color: var(--rose); cursor: pointer; background: none; border: none; padding: 0; transition: opacity var(--transition); }
.filter-clear-btn:hover { opacity: 0.7; }
.filter-card-body { padding: 14px 16px 16px; }
.cat-filter-list { display: flex; flex-direction: column; gap: 4px; }
.cat-filter-btn { width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 9px 12px; border-radius: 8px; font-family: var(--ff-body); font-size: 13px; font-weight: 600; color: var(--text-mid); background: none; border: none; cursor: pointer; text-align: left; transition: all var(--transition); }
.cat-filter-btn:hover, .cat-filter-btn.active { background: var(--rose-bg); color: var(--rose); }
.cat-count { font-size: 10.5px; font-weight: 700; color: var(--text-light); background: var(--cream-deep); border-radius: 20px; padding: 2px 8px; min-width: 28px; text-align: center; transition: all var(--transition); }
.cat-filter-btn.active .cat-count { background: rgba(184,92,110,0.15); color: var(--rose); }
.price-inputs { display: flex; gap: 8px; align-items: center; }
.price-input { flex: 1; border: 1.5px solid var(--border); border-radius: 8px; padding: 9px 12px; font-family: var(--ff-body); font-size: 13px; font-weight: 600; color: var(--text-dark); outline: none; transition: border-color var(--transition); min-width: 0; }
.price-input:focus { border-color: var(--rose); }
.price-sep { font-size: 14px; color: var(--text-light); font-weight: 700; flex-shrink: 0; }
.occ-chip-grid { display: flex; flex-wrap: wrap; gap: 7px; }
.occ-chip { display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; border-radius: 50px; font-family: var(--ff-body); font-size: 11.5px; font-weight: 600; color: var(--text-mid); background: var(--cream-deep); border: 1.5px solid transparent; cursor: pointer; transition: all var(--transition); user-select: none; }
.occ-chip:hover { border-color: var(--rose-light); color: var(--rose); background: var(--rose-bg); }
.occ-chip.active { background: var(--rose); color: white; border-color: var(--rose); }
.badge-chip-grid { display: flex; flex-wrap: wrap; gap: 7px; }
.badge-chip { display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 50px; font-family: var(--ff-body); font-size: 11px; font-weight: 700; cursor: pointer; transition: all var(--transition); border: 1.5px solid transparent; color: var(--text-mid); background: var(--cream-deep); user-select: none; }
.badge-chip.active { color: white; }
.badge-chip:hover { transform: translateY(-1px); box-shadow: var(--shadow-xs); }

/* ── Main ── */
.shop-main { min-width: 0; }
.shop-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 14px 18px; background: var(--white); border: 1px solid var(--border-light); border-radius: var(--radius); margin-bottom: 20px; box-shadow: var(--shadow-xs); flex-wrap: wrap; }
.shop-result-count { font-size: 13px; font-weight: 600; color: var(--text-mid); }
.shop-result-count strong { color: var(--text-dark); }
.toolbar-right { display: flex; align-items: center; gap: 10px; }
.sort-select { border: 1.5px solid var(--border); border-radius: 8px; padding: 8px 14px; font-family: var(--ff-body); font-size: 12.5px; font-weight: 600; color: var(--text-dark); background: var(--white); outline: none; cursor: pointer; transition: border-color var(--transition); }
.sort-select:focus { border-color: var(--rose); }
.view-toggle { display: flex; border: 1.5px solid var(--border); border-radius: 8px; overflow: hidden; }
.view-btn { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: none; border: none; cursor: pointer; color: var(--text-light); transition: all var(--transition); }
.view-btn:hover { background: var(--rose-bg); color: var(--rose); }
.view-btn.active { background: var(--rose); color: white; }
.view-btn svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
.mobile-filter-btn { display: none; align-items: center; gap: 7px; padding: 8px 16px; border: 1.5px solid var(--border); border-radius: 8px; background: white; font-family: var(--ff-body); font-size: 12.5px; font-weight: 700; color: var(--text-dark); cursor: pointer; transition: all var(--transition); }
.mobile-filter-btn:hover { border-color: var(--rose); color: var(--rose); }
.mobile-filter-btn svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2; }
.filter-count-badge { background: var(--rose); color: white; font-size: 10px; font-weight: 700; border-radius: 50%; width: 18px; height: 18px; display: none; align-items: center; justify-content: center; }
.filter-count-badge.show { display: flex; }
.active-filters { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
.active-filter-pill { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px 5px 14px; background: var(--rose-bg); border: 1px solid var(--rose-light); border-radius: 50px; font-size: 12px; font-weight: 600; color: var(--rose); }
.active-filter-pill button { display: flex; align-items: center; justify-content: center; width: 16px; height: 16px; border-radius: 50%; background: rgba(184,92,110,0.15); border: none; cursor: pointer; color: var(--rose); padding: 0; transition: background var(--transition); }
.active-filter-pill button:hover { background: var(--rose); color: white; }
.active-filter-pill button svg { width: 8px; height: 8px; stroke: currentColor; fill: none; stroke-width: 3; }

/* ── Products Grid ── */
#productsGrid { display: grid; grid-template-columns: repeat(3,1fr); gap: 20px; }
#productsGrid.list-view { grid-template-columns: 1fr; }

/* ── Product Card ── */
.product-card {
  background: var(--white); border: 1px solid var(--border-light); border-radius: var(--radius);
  overflow: hidden; transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
  box-shadow: var(--shadow-xs); position: relative; display: flex; flex-direction: column;
  text-decoration: none; /* card is an <a> tag */
}
.product-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); border-color: var(--rose-light); }
.product-card.out-of-stock { opacity: 0.65; }
.product-img-wrap { position: relative; height: 220px; overflow: hidden; background: var(--cream); flex-shrink: 0; }
.product-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform .55s ease; display: block; }
.product-card:hover .product-img-wrap img { transform: scale(1.07); }
.product-img-placeholder { width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; font-size: 11px; font-weight: 600; color: var(--text-light); letter-spacing: .8px; text-transform: uppercase; }

/* Hover overlay — "View Details" */
.product-hover-overlay {
  position: absolute; inset: 0; background: rgba(30,21,25,.38);
  display: flex; align-items: center; justify-content: center;
  opacity: 0; transition: opacity .28s ease; backdrop-filter: blur(2px);
}
.product-card:hover .product-hover-overlay { opacity: 1; }
.view-details-btn {
  padding: 10px 24px; background: white; border: none; border-radius: 50px;
  font-family: var(--ff-body); font-size: 12px; font-weight: 700; color: var(--text-dark);
  letter-spacing: .5px; transition: all .2s ease; transform: translateY(10px);
  box-shadow: var(--shadow-md); pointer-events: none; /* the whole card is the link */
}
.product-card:hover .view-details-btn { transform: translateY(0); }

/* Wishlist & badge overlays */
.wishlist-btn {
  position: absolute; top: 12px; right: 12px; width: 34px; height: 34px;
  background: rgba(255,255,255,.95); border-radius: 50%; border: none;
  display: flex; align-items: center; justify-content: center; cursor: pointer;
  box-shadow: 0 2px 10px rgba(0,0,0,.1); backdrop-filter: blur(8px);
  transition: all var(--transition); z-index: 2;
}
.wishlist-btn:hover { background: var(--rose); transform: scale(1.1); }
.wishlist-btn svg { width: 15px; height: 15px; stroke: #b85c6e; fill: none; stroke-width: 2; transition: all var(--transition); }
.wishlist-btn:hover svg, .wishlist-btn.wishlisted svg { stroke: var(--rose); fill: var(--rose); }
.wishlist-btn.wishlisted { background: rgba(255,255,255,.95); }
.wishlist-btn.wishlisted:hover svg { stroke: white; fill: white; }
.wishlist-btn.wishlisted:hover { background: var(--rose); }
.product-badge-tag { position: absolute; top: 12px; left: 12px; padding: 6px 14px; border-radius: 20px; font-size: 9px; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; color: white; z-index: 2; }
.out-of-stock-tag { position: absolute; bottom: 12px; left: 12px; padding: 4px 12px; border-radius: 20px; font-size: 9.5px; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; background: rgba(30,21,25,.75); color: rgba(255,255,255,.85); z-index: 2; }

/* Card info */
.product-info { padding: 16px 16px 12px; display: flex; flex-direction: column; flex: 1; }
.product-category-pill { display: inline-block; font-size: 10px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: rgba(184,92,110,.6); margin-bottom: 5px; }
.product-name { font-size: 15px; font-weight: 700; color: var(--text-dark); line-height: 1.35; margin-bottom: 5px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.product-desc-short { font-size: 12.5px; font-weight: 500; color: rgba(30,21,25,.5); line-height: 1.5; margin-bottom: 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; flex: 1; }
.product-footer { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: auto; }
.price-block { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.product-price { font-size: 19px; font-weight: 700; color: #b85c6e; }
.product-orig-price { font-size: 12px; font-weight: 500; color: rgba(30,21,25,.3); text-decoration: line-through; }
.product-discount-pct { font-size: 10.5px; font-weight: 700; color: #2e8b57; background: #e8f5ef; padding: 3px 7px; border-radius: 20px; }

/* Card action row */
.card-actions-row { display: flex; gap: 6px; margin-top: 10px; }
.card-add-cart-btn {
  flex: 1; display: flex; align-items: center; justify-content: center; gap: 5px;
  padding: 8px 10px; background: var(--rose); color: white; border: none; border-radius: 8px;
  font-family: var(--ff-body); font-size: 11.5px; font-weight: 700; cursor: pointer;
  transition: background var(--transition); white-space: nowrap;
}
.card-add-cart-btn:hover { background: var(--rose-hover); }
.card-add-cart-btn svg { width: 13px; height: 13px; stroke: white; fill: none; stroke-width: 2; flex-shrink: 0; }
.card-wa-btn {
  width: 34px; height: 34px; border-radius: 8px; background: #25d366; border: none;
  display: flex; align-items: center; justify-content: center; cursor: pointer; flex-shrink: 0;
  transition: transform .2s ease, box-shadow .2s ease; box-shadow: 0 2px 8px rgba(37,211,102,.25);
}
.card-wa-btn:hover { transform: scale(1.08); }
.card-wa-btn svg { width: 15px; height: 15px; fill: white; }

/* List view */
#productsGrid.list-view .product-card { display: grid; grid-template-columns: 200px 1fr; flex-direction: unset; }
#productsGrid.list-view .product-img-wrap { height: 100%; min-height: 160px; border-radius: 0; }
#productsGrid.list-view .product-info { padding: 20px 22px; }
#productsGrid.list-view .product-name { font-size: 17px; -webkit-line-clamp: unset; }
#productsGrid.list-view .product-desc-short { -webkit-line-clamp: 3; }
#productsGrid.list-view .product-price { font-size: 20px; }

/* Pagination */
.pagination-wrap { display: flex; justify-content: center; align-items: center; gap: 8px; margin-top: 40px; padding: 20px 0; flex-wrap: wrap; }
.page-nav-btn { padding: 8px 16px; border: 1.5px solid var(--border); background: var(--white); border-radius: 8px; font-family: var(--ff-body); font-size: 13px; font-weight: 700; color: var(--text-dark); cursor: pointer; transition: all var(--transition); }
.page-nav-btn:hover:not(:disabled) { border-color: var(--rose); color: var(--rose); }
.page-nav-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.page-number-btn { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border: 1.5px solid var(--border); background: var(--white); border-radius: 50%; font-family: var(--ff-body); font-size: 13px; font-weight: 700; color: var(--text-dark); cursor: pointer; transition: all var(--transition); }
.page-number-btn:hover { border-color: var(--rose); color: var(--rose); }
.page-number-btn.active { background: var(--rose); border-color: var(--rose); color: white; }

/* Empty state */
.empty-state { grid-column: 1/-1; text-align: center; padding: 80px 20px; color: var(--text-light); }
.empty-state svg { width: 56px; height: 56px; stroke: var(--border); fill: none; stroke-width: 1.2; margin-bottom: 18px; display: block; margin-left: auto; margin-right: auto; }
.empty-state h3 { font-size: 20px; font-weight: 700; color: var(--text-dark); margin-bottom: 8px; }
.empty-state p { font-size: 14px; max-width: 320px; margin: 0 auto 24px; line-height: 1.6; }
.empty-reset-btn { padding: 11px 28px; background: var(--rose); color: white; border: none; border-radius: 50px; font-family: var(--ff-body); font-size: 13px; font-weight: 700; cursor: pointer; transition: background var(--transition); }
.empty-reset-btn:hover { background: var(--rose-hover); }

/* Skeleton */
.skeleton-card { background: var(--white); border: 1px solid var(--border-light); border-radius: var(--radius); overflow: hidden; }
.skeleton-img { height: 220px; background: linear-gradient(90deg, var(--cream) 25%, var(--cream-deep) 50%, var(--cream) 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; }
.skeleton-info { padding: 16px 18px; }
.skeleton-line { height: 12px; border-radius: 6px; background: linear-gradient(90deg, var(--cream) 25%, var(--cream-deep) 50%, var(--cream) 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; margin-bottom: 10px; }
.skeleton-line.short { width: 60%; }
.skeleton-line.price { width: 40%; height: 18px; margin-top: 14px; }
@keyframes shimmer { 0%{background-position:200% 0}100%{background-position:-200% 0} }

/* Mobile filter drawer */
.mobile-filter-drawer { position: fixed; inset: 0; z-index: 400; display: none; }
.mobile-filter-drawer.open { display: block; }
.mfd-overlay { position: absolute; inset: 0; background: rgba(20,14,17,.5); backdrop-filter: blur(4px); }
.mfd-panel { position: fixed; left: 0; top: 0; bottom: 0; width: 300px; background: var(--white); overflow-y: auto; transform: translateX(-100%); transition: transform .36s cubic-bezier(.4,0,.2,1); box-shadow: var(--shadow-lg); display: flex; flex-direction: column; }
.mobile-filter-drawer.open .mfd-panel { transform: translateX(0); }
.mfd-header { padding: 20px 20px 16px; border-bottom: 1px solid var(--border-light); display: flex; align-items: center; justify-content: space-between; background: var(--white); flex-shrink: 0; }
.mfd-title { font-size: 15px; font-weight: 700; color: var(--text-dark); }
.mfd-close { width: 32px; height: 32px; border-radius: 8px; border: none; background: none; cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--text-mid); transition: all var(--transition); }
.mfd-close:hover { background: var(--rose-bg); color: var(--rose); }
.mfd-close svg { width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2.5; }
.mfd-body { padding: 16px; display: flex; flex-direction: column; gap: 20px; flex: 1; overflow-y: auto; }
.mfd-footer { padding: 16px 20px; border-top: 1px solid var(--border-light); display: flex; gap: 10px; background: var(--white); flex-shrink: 0; }
.mfd-apply { flex: 1; padding: 12px; background: var(--rose); color: white; border: none; border-radius: 50px; font-family: var(--ff-body); font-size: 13px; font-weight: 700; cursor: pointer; transition: background var(--transition); }
.mfd-apply:hover { background: var(--rose-hover); }
.mfd-reset { padding: 12px 18px; border: 1.5px solid var(--border); border-radius: 50px; background: none; font-family: var(--ff-body); font-size: 13px; font-weight: 600; color: var(--text-mid); cursor: pointer; transition: all var(--transition); }
.mfd-reset:hover { border-color: var(--rose); color: var(--rose); }
.mfd-section-title { font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--text-dark); margin-bottom: 10px; }

/* Responsive */
@media (max-width: 1100px) {
  .shop-layout { grid-template-columns: 240px 1fr; }
  #productsGrid { grid-template-columns: repeat(2,1fr); }
}
@media (max-width: 768px) {
  .breadcrumb, .shop-search-wrap { padding-left: 20px; padding-right: 20px; }
  .shop-hero-inner { padding: 32px 20px 28px; }
  .shop-layout { grid-template-columns: 1fr; padding: 20px 20px 60px; gap: 0; }
  .sidebar { display: none; }
  .mobile-filter-btn { display: flex; }
  #productsGrid { grid-template-columns: repeat(2,1fr); gap: 14px; }
  #productsGrid.list-view { grid-template-columns: 1fr; }
  #productsGrid.list-view .product-card { grid-template-columns: 130px 1fr; }
  #productsGrid.list-view .product-img-wrap { min-height: 130px; }
  .product-img-wrap { height: 180px; }
  .sort-select { font-size: 12px; padding: 7px 10px; }
}
@media (max-width: 480px) {
  #productsGrid { grid-template-columns: 1fr; }
}
</style>

<!-- BREADCRUMB -->
<nav class="breadcrumb" aria-label="Breadcrumb">
  <a href="index.php">Home</a>
  <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
  <span>Shop All Gifts</span>
</nav>

<!-- SHOP HERO -->
<div class="shop-hero">
  <div class="shop-hero-inner">
    <div class="shop-hero-tag">Our Collection</div>
    <h1>Discover <em>Every Gift</em></h1>
    <p class="shop-hero-sub"><?= $total_active ?>+ handpicked gifts for every person, every occasion, every feeling.</p>
  </div>
</div>

<!-- INLINE SEARCH -->
<div class="shop-search-wrap">
  <div class="shop-search-bar">
    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input class="shop-search-input" type="search" id="shopSearch"
           placeholder="Search gifts, categories, occasions…"
           value="<?= htmlspecialchars($pre_search) ?>"
           autocomplete="off" aria-label="Search products"/>
    <button class="shop-search-clear" id="shopSearchClear" aria-label="Clear"<?= $pre_search ? ' style="display:block"' : '' ?>>×</button>
  </div>
</div>

<div class="shop-layout">

  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebarDesktop" aria-label="Filters">
    <div class="filter-card">
      <div class="filter-card-header">
        <span class="filter-card-title">Categories</span>
        <button class="filter-clear-btn" onclick="clearFilter('cat')">Clear</button>
      </div>
      <div class="filter-card-body">
        <div class="cat-filter-list">
          <button class="cat-filter-btn active" data-cat="all" onclick="setCategory('all',this)">
            All Gifts <span class="cat-count"><?= $total_active ?></span>
          </button>
          <?php foreach ($categories as $cat): ?>
          <button class="cat-filter-btn" data-cat="<?= htmlspecialchars($cat['slug']) ?>"
                  onclick="setCategory('<?= htmlspecialchars($cat['slug']) ?>',this)">
            <?= htmlspecialchars($cat['name']) ?> <span class="cat-count"><?= (int)$cat['product_count'] ?></span>
          </button>
          <?php endforeach ?>
        </div>
      </div>
    </div>

    <div class="filter-card">
      <div class="filter-card-header">
        <span class="filter-card-title">Price Range</span>
        <button class="filter-clear-btn" onclick="clearFilter('price')">Reset</button>
      </div>
      <div class="filter-card-body">
        <div class="price-inputs">
          <input class="price-input" type="number" id="priceMinD" value="<?= $price_min ?>" min="<?= $price_min ?>" max="<?= $price_max ?>" oninput="applyFilters()" aria-label="Min price"/>
          <span class="price-sep">—</span>
          <input class="price-input" type="number" id="priceMaxD" value="<?= $price_max ?>" min="<?= $price_min ?>" max="<?= $price_max ?>" oninput="applyFilters()" aria-label="Max price"/>
        </div>
      </div>
    </div>

    <div class="filter-card">
      <div class="filter-card-header">
        <span class="filter-card-title">Occasions</span>
        <button class="filter-clear-btn" onclick="clearFilter('occ')">Clear</button>
      </div>
      <div class="filter-card-body">
        <div class="occ-chip-grid" id="occChipsDesktop">
          <?php foreach ($occasions as $occ): ?>
          <span class="occ-chip" data-slug="<?= htmlspecialchars($occ['slug']) ?>"
                onclick="toggleOcc(this,'<?= htmlspecialchars($occ['slug']) ?>')">
            <?= $occ['icon_emoji'] ?? '' ?> <?= htmlspecialchars($occ['name']) ?>
          </span>
          <?php endforeach ?>
        </div>
      </div>
    </div>

    <?php if ($badges): ?>
    <div class="filter-card">
      <div class="filter-card-header">
        <span class="filter-card-title">Collection</span>
        <button class="filter-clear-btn" onclick="clearFilter('badge')">Clear</button>
      </div>
      <div class="filter-card-body">
        <div class="badge-chip-grid" id="badgeChipsDesktop">
          <?php foreach ($badges as $b): ?>
          <span class="badge-chip" data-badge="<?= (int)$b['id'] ?>"
                style="--badge-color:<?= htmlspecialchars($b['color_hex']) ?>"
                onclick="toggleBadge(this,<?= (int)$b['id'] ?>)">
            <?= htmlspecialchars($b['name']) ?>
          </span>
          <?php endforeach ?>
        </div>
      </div>
    </div>
    <?php endif ?>
  </aside>

  <!-- MAIN -->
  <main class="shop-main" id="shopMain">
    <div class="shop-toolbar">
      <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
        <button class="mobile-filter-btn" id="mobileFilterBtn" onclick="openMobileFilters()" aria-label="Open filters">
          <svg viewBox="0 0 24 24"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="20" y2="12"/><line x1="12" y1="18" x2="20" y2="18"/></svg>
          Filters <span class="filter-count-badge" id="filterCountBadge">0</span>
        </button>
        <div class="shop-result-count">Showing <strong id="resultCount">0</strong> gifts</div>
      </div>
      <div class="toolbar-right">
        <select class="sort-select" id="sortSelect" onchange="applyFilters()" aria-label="Sort">
          <option value="featured">Featured First</option>
          <option value="new">Newest First</option>
          <option value="popular">Most Popular</option>
          <option value="price-low">Price: Low → High</option>
          <option value="price-high">Price: High → Low</option>
          <option value="discount">Best Discount</option>
        </select>
        <div class="view-toggle" role="group" aria-label="View mode">
          <button class="view-btn active" id="gridViewBtn" title="Grid" onclick="setView('grid')">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
          </button>
          <button class="view-btn" id="listViewBtn" title="List" onclick="setView('list')">
            <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
          </button>
        </div>
      </div>
    </div>

    <div class="active-filters" id="activeFilters" aria-live="polite"></div>

    <div id="productsGrid" aria-label="Products" aria-live="polite">
      <?php for ($i = 0; $i < 6; $i++): ?>
      <div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-info"><div class="skeleton-line short"></div><div class="skeleton-line"></div><div class="skeleton-line"></div><div class="skeleton-line price"></div></div></div>
      <?php endfor ?>
    </div>

    <div class="pagination-wrap" id="paginationWrap"></div>
  </main>
</div>

<!-- MOBILE FILTER DRAWER -->
<div class="mobile-filter-drawer" id="mobileFilterDrawer" role="dialog" aria-modal="true" aria-label="Filters">
  <div class="mfd-overlay" onclick="closeMobileFilters()"></div>
  <div class="mfd-panel">
    <div class="mfd-header">
      <span class="mfd-title">Filter Gifts</span>
      <button class="mfd-close" onclick="closeMobileFilters()" aria-label="Close filters">
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="mfd-body">
      <div>
        <div class="mfd-section-title">Categories</div>
        <div class="cat-filter-list">
          <button class="cat-filter-btn active" data-cat="all" onclick="setCategory('all',this)">All Gifts <span class="cat-count"><?= $total_active ?></span></button>
          <?php foreach ($categories as $cat): ?>
          <button class="cat-filter-btn" data-cat="<?= htmlspecialchars($cat['slug']) ?>" onclick="setCategory('<?= htmlspecialchars($cat['slug']) ?>',this)">
            <?= htmlspecialchars($cat['name']) ?> <span class="cat-count"><?= (int)$cat['product_count'] ?></span>
          </button>
          <?php endforeach ?>
        </div>
      </div>
      <div>
        <div class="mfd-section-title">Price Range</div>
        <div class="price-inputs">
          <input class="price-input" type="number" id="priceMinM" value="<?= $price_min ?>" min="<?= $price_min ?>" max="<?= $price_max ?>" oninput="syncPriceInputs('M')" aria-label="Min price"/>
          <span class="price-sep">—</span>
          <input class="price-input" type="number" id="priceMaxM" value="<?= $price_max ?>" min="<?= $price_min ?>" max="<?= $price_max ?>" oninput="syncPriceInputs('M')" aria-label="Max price"/>
        </div>
      </div>
      <div>
        <div class="mfd-section-title">Occasions</div>
        <div class="occ-chip-grid">
          <?php foreach ($occasions as $occ): ?>
          <span class="occ-chip" data-slug="<?= htmlspecialchars($occ['slug']) ?>" onclick="toggleOcc(this,'<?= htmlspecialchars($occ['slug']) ?>')">
            <?= $occ['icon_emoji'] ?? '' ?> <?= htmlspecialchars($occ['name']) ?>
          </span>
          <?php endforeach ?>
        </div>
      </div>
      <?php if ($badges): ?>
      <div>
        <div class="mfd-section-title">Collection</div>
        <div class="badge-chip-grid">
          <?php foreach ($badges as $b): ?>
          <span class="badge-chip" data-badge="<?= (int)$b['id'] ?>" onclick="toggleBadge(this,<?= (int)$b['id'] ?>)"><?= htmlspecialchars($b['name']) ?></span>
          <?php endforeach ?>
        </div>
      </div>
      <?php endif ?>
    </div>
    <div class="mfd-footer">
      <button class="mfd-reset" onclick="resetAllFilters()">Reset All</button>
      <button class="mfd-apply" onclick="closeMobileFilters()">Apply Filters</button>
    </div>
  </div>
</div>

<script>
/* ════ DATA ════ */
const PRODUCTS  = <?= $products_json ?>;
const WA_NUMBER = "<?= htmlspecialchars($wa_number, ENT_QUOTES) ?>";
const PRICE_MIN = <?= $price_min ?>;
const PRICE_MAX = <?= $price_max ?>;

/* ════ STATE ════ */
let activeCat    = '<?= addslashes($pre_cat) ?>' || 'all';
let activeOccs   = new Set(<?= $pre_occ ? '["'.addslashes($pre_occ).'"]' : '[]' ?>);
let activeBadges = new Set();
let searchQuery  = '<?= addslashes($pre_search) ?>';
let currentView  = 'grid';
let currentPage  = 1;
const PER_PAGE   = 15;
let filteredList = [];

function getWishlist() { try { return JSON.parse(localStorage.getItem('aakar_wishlist')) || []; } catch(e) { return []; } }

/* ════ INIT ════ */
document.addEventListener('DOMContentLoaded', () => {
  syncPriceInputs('D');
  if (activeCat && activeCat !== 'all') {
    document.querySelectorAll('.cat-filter-btn').forEach(b => b.classList.toggle('active', b.dataset.cat === activeCat));
  }
  if (activeOccs.size) {
    document.querySelectorAll('.occ-chip').forEach(c => { if (activeOccs.has(c.dataset.slug)) c.classList.add('active'); });
  }
  const si = document.getElementById('shopSearch');
  const sc = document.getElementById('shopSearchClear');
  if (si && searchQuery) { si.value = searchQuery; if (sc) sc.style.display = 'block'; }

  if (si) {
    let debTimer;
    si.addEventListener('input', () => {
      clearTimeout(debTimer);
      searchQuery = si.value.trim();
      if (sc) sc.style.display = searchQuery ? 'block' : 'none';
      debTimer = setTimeout(applyFilters, 220);
    });
  }
  if (sc) {
    sc.addEventListener('click', () => {
      const si2 = document.getElementById('shopSearch');
      if (si2) si2.value = '';
      sc.style.display = 'none';
      searchQuery = '';
      applyFilters();
      if (si2) si2.focus();
    });
  }
  applyFilters();
});

/* ════ FILTERS ════ */
function applyFilters() {
  let list = [...PRODUCTS];
  const sortVal = document.getElementById('sortSelect')?.value || 'featured';
  const pMin = getPriceMin(), pMax = getPriceMax();
  const q = searchQuery.toLowerCase().trim();

  if (q) {
    list = list.filter(p =>
      p.name.toLowerCase().includes(q) ||
      (p.desc && p.desc.toLowerCase().includes(q)) ||
      (p.catName && p.catName.toLowerCase().includes(q)) ||
      (p.tags && p.tags.some(t => t.toLowerCase().includes(q))) ||
      (p.occasions && p.occasions.some(o => o.toLowerCase().includes(q)))
    );
  }
  if (activeCat && activeCat !== 'all') list = list.filter(p => p.catSlug === activeCat);
  if (activeOccs.size)   list = list.filter(p => p.occSlugs && p.occSlugs.some(s => activeOccs.has(s)));
  if (activeBadges.size) list = list.filter(p => activeBadges.has(p.badgeName));
  list = list.filter(p => p.price >= pMin && p.price <= pMax);
  list = sortList(list, sortVal);
  filteredList = list;
  currentPage  = 1;
  renderPage();
  updateActiveFilterPills();
  updateFilterCount();
  updateURL();
}

function sortList(list, val) {
  switch(val) {
    case 'price-low':  return list.sort((a,b) => a.price - b.price);
    case 'price-high': return list.sort((a,b) => b.price - a.price);
    case 'popular':    return list.sort((a,b) => (b.views||0) - (a.views||0));
    case 'new':        return list.sort((a,b) => (b.isNew?1:0) - (a.isNew?1:0));
    case 'discount':   return list.sort((a,b) => {
      const da = a.salePrice ? (1 - a.price/a.salePrice) : 0;
      const db = b.salePrice ? (1 - b.price/b.salePrice) : 0;
      return db - da;
    });
    default: return list.sort((a,b) => (b.isFeatured?1:0) - (a.isFeatured?1:0));
  }
}

/* ════ RENDER ════ */
function renderPage() {
  const grid = document.getElementById('productsGrid');
  const resultEl = document.getElementById('resultCount');
  if (resultEl) resultEl.textContent = filteredList.length;

  if (!filteredList.length) {
    grid.innerHTML = `<div class="empty-state">
      <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <h3>No gifts found</h3>
      <p>Try adjusting your filters or search term.</p>
      <button class="empty-reset-btn" onclick="resetAllFilters()">Clear All Filters</button>
    </div>`;
    document.getElementById('paginationWrap').innerHTML = '';
    return;
  }
  const start = (currentPage - 1) * PER_PAGE;
  const items = filteredList.slice(start, start + PER_PAGE);
  grid.innerHTML = items.map(p => buildCard(p)).join('');
  grid.querySelectorAll('.product-card').forEach((card, i) => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(16px)';
    setTimeout(() => {
      card.style.transition = 'opacity .36s ease, transform .36s ease, box-shadow .3s ease, border-color .3s ease';
      card.style.opacity = '1';
      card.style.transform = 'none';
    }, i * 32);
  });
  renderPagination();
}

function renderPagination() {
  const wrap = document.getElementById('paginationWrap');
  const total = Math.ceil(filteredList.length / PER_PAGE);
  if (total <= 1) { wrap.innerHTML = ''; return; }
  let html = `<button class="page-nav-btn" ${currentPage===1?'disabled':''} onclick="changePage(${currentPage-1})">← Prev</button>`;
  for (let i = 1; i <= total; i++) html += `<button class="page-number-btn ${currentPage===i?'active':''}" onclick="changePage(${i})">${i}</button>`;
  html += `<button class="page-nav-btn" ${currentPage===total?'disabled':''} onclick="changePage(${currentPage+1})">Next →</button>`;
  wrap.innerHTML = html;
}

function changePage(p) {
  currentPage = p;
  renderPage();
  document.getElementById('shopMain').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/* ════ CARD BUILDER ════ */
function buildCard(p) {
  const isWished = getWishlist().some(w => String(w.id) === String(p.id));
  const pct = (p.salePrice && p.price) ? Math.round((1 - p.price/p.salePrice) * 100) : 0;
  const imgHtml = p.primaryImg
    ? `<img src="${esc(p.primaryImg)}" alt="${esc(p.name)}" loading="lazy">`
    : `<div class="product-img-placeholder" style="background:linear-gradient(150deg,#f7eef0,#edd8dd)">
         <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="rgba(184,92,110,0.3)" stroke-width="1.2">
           <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
         </svg>
         <span>${esc(p.catName)}</span>
       </div>`;

  /* The entire card is a link to product.php */
  return `
  <a class="product-card${!p.inStock?' out-of-stock':''}"
     href="product.php?slug=${encodeURIComponent(p.slug)}"
     data-id="${p.id}"
     aria-label="${esc(p.name)}">
    <div class="product-img-wrap">
      ${imgHtml}
      <button class="wishlist-btn${isWished?' wishlisted':''}"
              onclick="event.preventDefault();event.stopPropagation();toggleCardWishlist(this,${p.id})"
              aria-label="${isWished?'Remove from':'Add to'} wishlist">
        <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      </button>
      ${p.badgeName ? `<span class="product-badge-tag" style="background:${p.badgeColor||'#b85c6e'}">${esc(p.badgeName)}</span>` : ''}
      ${!p.inStock  ? `<span class="out-of-stock-tag">Out of Stock</span>` : ''}
      <div class="product-hover-overlay">
        <span class="view-details-btn">View Details</span>
      </div>
    </div>
    <div class="product-info">
      <div class="product-category-pill">${esc(p.catName)}</div>
      <div class="product-name">${esc(p.name)}</div>
      <div class="product-desc-short">${esc(p.desc)}</div>
      <div class="product-footer">
        <div class="price-block">
          <span class="product-price">₹${fmt(p.price)}</span>
          ${p.salePrice ? `<span class="product-orig-price">₹${fmt(p.salePrice)}</span>` : ''}
          ${pct > 0   ? `<span class="product-discount-pct">-${pct}%</span>` : ''}
        </div>
      </div>
      <div class="card-actions-row">
        <button class="card-add-cart-btn"
                onclick="event.preventDefault();event.stopPropagation();cardAddToCart(${p.id})"
                ${!p.inStock ? 'disabled style="opacity:.5;cursor:not-allowed"' : ''}>
          <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          Add to Cart
        </button>
        <button class="card-wa-btn"
                onclick="event.preventDefault();event.stopPropagation();quickWA(${p.id})"
                aria-label="WhatsApp enquiry" title="Enquire on WhatsApp">
          <svg viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
        </button>
      </div>
    </div>
  </a>`;
}

/* ════ CARD ACTIONS ════ */
function cardAddToCart(id) {
  const p = PRODUCTS.find(x => x.id === id);
  if (!p || !p.inStock) return;
  if (window.addToCart) {
    window.addToCart({ id: p.id, name: p.name, price: p.price, discount_price: p.salePrice, image_url: p.primaryImg, category: p.catName, qty: 1 });
  } else {
    try {
      const cart = JSON.parse(localStorage.getItem('aakar_cart') || '[]');
      const idx  = cart.findIndex(i => String(i.id) === String(p.id));
      if (idx > -1) cart[idx].qty = Math.min((cart[idx].qty||1) + 1, 10);
      else cart.push({ id: p.id, name: p.name, price: p.price, discount_price: p.salePrice, image_url: p.primaryImg, category: p.catName, qty: 1 });
      localStorage.setItem('aakar_cart', JSON.stringify(cart));
      if (window.updateAllBadges) window.updateAllBadges();
    } catch(e) {}
    const fn = window.acToast || showToast;
    fn(p.name + ' added to cart! 🛍', 'success');
  }
}

function toggleCardWishlist(btn, id) {
  const p = PRODUCTS.find(x => x.id === id);
  if (!p) return;
  let inList = false;
  if (window.toggleWishlist) {
    inList = window.toggleWishlist({ id: p.id, name: p.name, price: p.price, discount_price: p.salePrice, image_url: p.primaryImg, category: p.catName });
  } else {
    try {
      const wl = JSON.parse(localStorage.getItem('aakar_wishlist') || '[]');
      const ei = wl.findIndex(w => String(w.id) === String(p.id));
      if (ei > -1) { wl.splice(ei, 1); inList = false; }
      else { wl.push({ id: p.id, name: p.name, price: p.price, discount_price: p.salePrice, image_url: p.primaryImg, category: p.catName }); inList = true; }
      localStorage.setItem('aakar_wishlist', JSON.stringify(wl));
      if (window.updateAllBadges) window.updateAllBadges();
    } catch(e) {}
    const fn = window.acToast || showToast;
    fn(inList ? 'Added to wishlist ❤️' : 'Removed from wishlist', inList ? 'success' : 'info');
  }
  btn.classList.toggle('wishlisted', !!inList);
}

function quickWA(id) {
  const p = PRODUCTS.find(x => x.id === id);
  if (!p) return;
  const msg = p.waMessage
    ? p.waMessage.replace('{product_name}', p.name).replace('{price}', fmt(p.price)).replace('{category}', p.catName)
    : `Hello Aakar Creatives! 🌸\n\nI'd like more details:\n\n📦 *${p.name}*\n🏷 *Category:* ${p.catName}\n💰 *Price:* ₹${fmt(p.price)}\n🚚 *Delivery:* ${p.delivery||'3–5 Working Days'}\n\nPlease share details & customisation options. 😊`;
  window.open(`https://wa.me/${WA_NUMBER}?text=${encodeURIComponent(msg)}`, '_blank');
  try {
    const d = new URLSearchParams({ event: 'whatsapp_click', product_id: id });
    if (navigator.sendBeacon) navigator.sendBeacon('track.php', d);
  } catch(e) {}
}

/* ════ FILTER SETTERS ════ */
function setCategory(slug, btn) {
  activeCat = slug;
  document.querySelectorAll('.cat-filter-btn').forEach(b => b.classList.toggle('active', b.dataset.cat === slug));
  applyFilters();
}
function toggleOcc(el, slug) {
  el.classList.toggle('active');
  const on = el.classList.contains('active');
  document.querySelectorAll(`.occ-chip[data-slug="${slug}"]`).forEach(c => c.classList.toggle('active', on));
  on ? activeOccs.add(slug) : activeOccs.delete(slug);
  applyFilters();
}
function toggleBadge(el, badgeId) {
  const name = el.textContent.trim();
  el.classList.toggle('active');
  const on = el.classList.contains('active');
  if (on) { el.style.background = el.style.getPropertyValue('--badge-color') || '#b85c6e'; el.style.color = 'white'; activeBadges.add(name); }
  else { el.style.background = ''; el.style.color = ''; activeBadges.delete(name); }
  document.querySelectorAll(`.badge-chip[data-badge="${badgeId}"]`).forEach(c => { c.className = el.className; c.style.background = el.style.background; c.style.color = el.style.color; });
  applyFilters();
}
function clearFilter(type) {
  if (type === 'cat')   { activeCat = 'all'; document.querySelectorAll('.cat-filter-btn').forEach(b => b.classList.toggle('active', b.dataset.cat==='all')); }
  if (type === 'occ')   { activeOccs.clear(); document.querySelectorAll('.occ-chip').forEach(c => c.classList.remove('active')); }
  if (type === 'badge') { activeBadges.clear(); document.querySelectorAll('.badge-chip').forEach(c => { c.classList.remove('active'); c.style.background=''; c.style.color=''; }); }
  if (type === 'price') { document.querySelectorAll('#priceMinD,#priceMinM').forEach(el => el.value = PRICE_MIN); document.querySelectorAll('#priceMaxD,#priceMaxM').forEach(el => el.value = PRICE_MAX); }
  applyFilters();
}
function resetAllFilters() {
  activeCat='all'; activeOccs.clear(); activeBadges.clear(); searchQuery='';
  document.querySelectorAll('.cat-filter-btn').forEach(b => b.classList.toggle('active', b.dataset.cat==='all'));
  document.querySelectorAll('.occ-chip,.badge-chip').forEach(c => { c.classList.remove('active'); c.style.background=''; c.style.color=''; });
  document.querySelectorAll('#priceMinD,#priceMinM').forEach(el => el.value = PRICE_MIN);
  document.querySelectorAll('#priceMaxD,#priceMaxM').forEach(el => el.value = PRICE_MAX);
  const si = document.getElementById('shopSearch'); const sc = document.getElementById('shopSearchClear');
  if (si) si.value = ''; if (sc) sc.style.display = 'none';
  applyFilters();
}

/* Price helpers */
function getPriceMin() { return parseInt(document.getElementById('priceMinD')?.value ?? PRICE_MIN) || PRICE_MIN; }
function getPriceMax() { return parseInt(document.getElementById('priceMaxD')?.value ?? PRICE_MAX) || PRICE_MAX; }
function syncPriceInputs(src) {
  const min = document.getElementById(`priceMin${src}`)?.value;
  const max = document.getElementById(`priceMax${src}`)?.value;
  document.querySelectorAll('#priceMinD,#priceMinM').forEach(el => el.value = min);
  document.querySelectorAll('#priceMaxD,#priceMaxM').forEach(el => el.value = max);
  applyFilters();
}

/* ════ ACTIVE FILTER PILLS ════ */
function updateActiveFilterPills() {
  const container = document.getElementById('activeFilters');
  const pills = [];
  if (activeCat && activeCat !== 'all') {
    const label = document.querySelector(`.cat-filter-btn[data-cat="${activeCat}"]`)?.textContent?.replace(/\d+/,'').trim() || activeCat;
    pills.push(makePill(label, () => clearFilter('cat')));
  }
  activeOccs.forEach(slug => {
    const label = document.querySelector(`.occ-chip[data-slug="${slug}"]`)?.textContent?.trim() || slug;
    pills.push(makePill(label, () => { activeOccs.delete(slug); document.querySelectorAll(`.occ-chip[data-slug="${slug}"]`).forEach(c=>c.classList.remove('active')); applyFilters(); }));
  });
  activeBadges.forEach(name => {
    pills.push(makePill(name, () => { activeBadges.delete(name); document.querySelectorAll('.badge-chip').forEach(c => { if(c.textContent.trim()===name){ c.classList.remove('active'); c.style.background=''; c.style.color=''; } }); applyFilters(); }));
  });
  if (searchQuery) pills.push(makePill(`"${searchQuery}"`, () => { const si=document.getElementById('shopSearch'); const sc=document.getElementById('shopSearchClear'); if(si)si.value=''; if(sc)sc.style.display='none'; searchQuery=''; applyFilters(); }));
  container.innerHTML = pills.join('');
}
function makePill(label, fn) {
  const id = 'pill_' + Math.random().toString(36).substr(2,6);
  setTimeout(() => { const el=document.getElementById(id); if(el) el.onclick=fn; }, 0);
  return `<div class="active-filter-pill">${esc(label)}<button id="${id}"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></div>`;
}
function updateFilterCount() {
  let cnt = (activeCat !== 'all') ? 1 : 0;
  cnt += activeOccs.size + activeBadges.size + (searchQuery ? 1 : 0);
  const badge = document.getElementById('filterCountBadge');
  if (badge) { badge.textContent = cnt; badge.classList.toggle('show', cnt > 0); }
}

/* ════ URL / VIEW / MOBILE FILTERS ════ */
function updateURL() {
  const params = new URLSearchParams();
  if (activeCat && activeCat !== 'all') params.set('category', activeCat);
  if (activeOccs.size) params.set('occasion', [...activeOccs].join(','));
  if (searchQuery) params.set('q', searchQuery);
  const qs = params.toString();
  history.replaceState({}, '', qs ? `${location.pathname}?${qs}` : location.pathname);
}
function setView(v) {
  currentView = v;
  document.getElementById('productsGrid').classList.toggle('list-view', v==='list');
  document.getElementById('gridViewBtn').classList.toggle('active', v==='grid');
  document.getElementById('listViewBtn').classList.toggle('active', v==='list');
}
function openMobileFilters()  { document.getElementById('mobileFilterDrawer').classList.add('open');    document.body.style.overflow='hidden'; }
function closeMobileFilters() { document.getElementById('mobileFilterDrawer').classList.remove('open'); document.body.style.overflow=''; }

/* ════ TOAST FALLBACK ════ */
function showToast(msg, type) {
  if (window.acToast) { window.acToast(msg, type||'info'); return; }
  let c = document.getElementById('shopToastContainer');
  if (!c) { c = document.createElement('div'); c.id='shopToastContainer'; c.style.cssText='position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none'; document.body.appendChild(c); }
  const t = document.createElement('div');
  t.style.cssText='background:#2d1a22;color:#fff;padding:12px 20px;border-radius:10px;font-size:13.5px;font-weight:600;border-left:3px solid #b85c6e;animation:toastIn .3s ease both';
  t.textContent = msg;
  if (!document.getElementById('shopToastCSS')) {
    const s = document.createElement('style'); s.id='shopToastCSS';
    s.textContent='@keyframes toastIn{from{opacity:0;transform:translateX(30px)}to{opacity:1;transform:none}}@keyframes toastOut{from{opacity:1}to{opacity:0;transform:translateX(30px)}}';
    document.head.appendChild(s);
  }
  c.appendChild(t);
  setTimeout(() => { t.style.animation='toastOut .3s ease both'; setTimeout(()=>t.remove(), 320); }, 2400);
}

/* ════ UTILS ════ */
function fmt(n) { return Number(n).toLocaleString('en-IN'); }
function esc(s) { if (!s) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>

<?php include 'includes/footer.php'; ?>
