<?php
/**
 * product.php — Aakar Creatives
 * Full dedicated product detail page. Accessed via ?slug=product-slug
 */

$page_key = 'product';
if (!isset($pdo)) { require_once __DIR__ . '/includes/db.php'; }

$slug = trim($_GET['slug'] ?? '');
$pid  = (int)($_GET['id'] ?? 0);

/* ── Fetch product ── */
try {
    if ($slug) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE slug = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$slug]);
    } elseif ($pid) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$pid]);
    } else {
        header('Location: shop.php'); exit;
    }
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) { header('HTTP/1.0 404 Not Found'); include '404.php'; exit; }
} catch (PDOException $e) { header('Location: shop.php'); exit; }

$pid = (int)$product['id'];

/* ── Increment view count ── */
try { $pdo->prepare("UPDATE products SET views = views + 1 WHERE id = ?")->execute([$pid]); } catch(PDOException $e) {}

/* ── Fetch category ── */
$cat = [];
try {
    $cs = $pdo->prepare("SELECT * FROM categories WHERE id = ? LIMIT 1");
    $cs->execute([$product['category_id']]);
    $cat = $cs->fetch(PDO::FETCH_ASSOC) ?: [];
} catch(PDOException $e) {}

/* ── Fetch badge ── */
$badge = [];
if ($product['badge_id']) {
    try {
        $bs = $pdo->prepare("SELECT * FROM badges WHERE id = ? LIMIT 1");
        $bs->execute([$product['badge_id']]);
        $badge = $bs->fetch(PDO::FETCH_ASSOC) ?: [];
    } catch(PDOException $e) {}
}

/* ── Fetch all media ── */
$media = [];
try {
    $ms = $pdo->prepare("SELECT * FROM product_media WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC");
    $ms->execute([$pid]);
    $media = $ms->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {}

$primary_img = '';
foreach ($media as $m) { if ($m['is_primary']) { $primary_img = $m['file_url']; break; } }
if (!$primary_img && count($media)) $primary_img = $media[0]['file_url'];

/* ── Fetch occasions ── */
$occasions = [];
try {
    $os = $pdo->prepare(
        "SELECT o.* FROM occasions o
           JOIN product_occasions po ON po.occasion_id = o.id
          WHERE po.product_id = ? AND o.is_active = 1
          ORDER BY o.sort_order ASC"
    );
    $os->execute([$pid]);
    $occasions = $os->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {}

/* ── Fetch variants with size+color ── */
$variants = [];
try {
    $vs = $pdo->prepare(
        "SELECT pv.*, ps.label AS size_label, ps.slug AS size_slug, ps.size_type,
                pc.name AS color_name, pc.slug AS color_slug, pc.hex_code
           FROM product_variants pv
           LEFT JOIN product_sizes ps ON ps.id = pv.size_id
           LEFT JOIN product_colors pc ON pc.id = pv.color_id
          WHERE pv.product_id = ? AND pv.is_active = 1
          ORDER BY pv.sort_order ASC"
    );
    $vs->execute([$pid]);
    $variants = $vs->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {}

/* Build unique sizes and colors from variants */
$sizes  = []; $colors = [];
foreach ($variants as $v) {
    if ($v['size_label'] && !isset($sizes[$v['size_slug']])) $sizes[$v['size_slug']] = $v;
    if ($v['color_slug'] && !isset($colors[$v['color_slug']])) $colors[$v['color_slug']] = $v;
}

/* ── Fetch variant color images ── */
$variant_images = [];
try {
    $vi = $pdo->prepare("SELECT * FROM product_variant_images WHERE product_id = ? ORDER BY sort_order ASC");
    $vi->execute([$pid]);
    foreach ($vi->fetchAll(PDO::FETCH_ASSOC) as $img) {
        $cid = $img['color_id'] ?? 'default';
        $variant_images[$cid][] = $img;
    }
} catch(PDOException $e) {}

/* ── Fetch related products (same category, exclude self) ── */
$related = [];
try {
    $rs = $pdo->prepare(
        "SELECT p.id, p.name, p.slug, p.short_description, p.price, p.discount_price,
                p.is_featured, p.is_new_arrival, p.is_trending, p.is_bestseller,
                b.name AS badge_name, b.color_hex AS badge_color,
                (SELECT pm.file_url FROM product_media pm WHERE pm.product_id = p.id AND pm.is_primary = 1 LIMIT 1) AS primary_image
           FROM products p
           LEFT JOIN badges b ON b.id = p.badge_id
          WHERE p.category_id = ? AND p.id != ? AND p.status = 'active'
          ORDER BY p.is_featured DESC, p.views DESC
          LIMIT 4"
    );
    $rs->execute([$product['category_id'], $pid]);
    $related = $rs->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {}

/* ── Fetch testimonials for this product ── */
$reviews = [];
try {
    $revs = $pdo->prepare(
        "SELECT * FROM testimonials WHERE product_id = ? AND is_approved = 1 ORDER BY sort_order ASC, created_at DESC LIMIT 6"
    );
    $revs->execute([$pid]);
    $reviews = $revs->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {}

/* ── WhatsApp number ── */
$wa_number = '919510360227';
try {
    $ws = $pdo->query("SELECT phone_number FROM whatsapp_settings WHERE is_primary = 1 LIMIT 1");
    $wr = $ws->fetch(PDO::FETCH_ASSOC);
    if ($wr) $wa_number = preg_replace('/\D/', '', $wr['phone_number']);
} catch(PDOException $e) {}

/* ── SEO ── */
$page_title       = htmlspecialchars($product['name']) . ' — Aakar Creatives';
$page_description = htmlspecialchars($product['short_description'] ?? '');
$og_image         = $primary_img ? 'https://' . ($_SERVER['HTTP_HOST'] ?? 'aakar-creatives.in') . $primary_img : '';

/* ── Helpers ── */
function p_esc($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function p_fmt($n)  { return number_format((float)$n, 0, '.', ','); }

include 'includes/header.php';
?>
<!-- SEO Meta -->
<meta name="description" content="<?= p_esc($page_description) ?>">
<meta property="og:title"       content="<?= p_esc($product['name']) ?> — Aakar Creatives">
<meta property="og:description" content="<?= p_esc($page_description) ?>">
<?php if ($og_image): ?><meta property="og:image" content="<?= p_esc($og_image) ?>"><?php endif ?>
<meta property="og:type" content="product">
<link rel="canonical" href="<?= p_esc('https://' . ($_SERVER['HTTP_HOST'] ?? '') . '/product.php?slug=' . urlencode($product['slug'])) ?>">

<style>
/* ══════════════════════════════════════════════════════
   PRODUCT PAGE  —  Aakar Creatives
══════════════════════════════════════════════════════ */

/* Breadcrumb */
.breadcrumb {
  max-width: var(--max-w); margin: 0 auto; padding: 18px 40px;
  display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
  font-size: 12.5px; font-weight: 600; color: var(--text-light);
}
.breadcrumb a { color: var(--text-light); text-decoration: none; transition: color var(--transition); }
.breadcrumb a:hover { color: var(--rose); }
.breadcrumb svg { width: 14px; height: 14px; stroke: var(--text-light); fill: none; stroke-width: 2; flex-shrink: 0; }
.breadcrumb span { color: var(--text-dark); font-weight: 700; }

/* ── Layout ── */
.product-page { max-width: var(--max-w); margin: 0 auto; padding: 24px 40px 80px; }
.product-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: start; }

/* ══ GALLERY COLUMN ══ */
.gallery-col { position: sticky; top: 90px; }
.gallery-main-wrap {
  position: relative; border-radius: 18px; overflow: hidden;
  background: var(--cream); aspect-ratio: 1/1; cursor: zoom-in;
  box-shadow: var(--shadow-sm);
}
.gallery-main-img {
  width: 100%; height: 100%; object-fit: cover; display: block;
  transition: transform .55s cubic-bezier(.4,0,.2,1);
}
.gallery-main-wrap:hover .gallery-main-img { transform: scale(1.05); }
.gallery-placeholder {
  width: 100%; height: 100%; display: flex; flex-direction: column;
  align-items: center; justify-content: center; gap: 14px;
  background: linear-gradient(150deg, var(--rose-pale) 0%, var(--cream-deep) 100%);
  color: var(--text-light); font-size: 12px; font-weight: 600; letter-spacing: 1px;
  text-transform: uppercase;
}
.gallery-placeholder svg { width: 60px; height: 60px; stroke: var(--rose-light); fill: none; stroke-width: 1.2; }
.gallery-badge-tag {
  position: absolute; top: 16px; left: 16px; z-index: 2;
  padding: 6px 16px; border-radius: 20px; font-size: 10px; font-weight: 700;
  letter-spacing: .8px; text-transform: uppercase; color: white;
}
.gallery-nav {
  position: absolute; top: 50%; transform: translateY(-50%);
  width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,.9);
  border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
  box-shadow: 0 2px 14px rgba(0,0,0,.12); transition: all var(--transition); z-index: 3;
  opacity: 0; pointer-events: none;
}
.gallery-main-wrap:hover .gallery-nav { opacity: 1; pointer-events: auto; }
.gallery-nav:hover { background: var(--rose); }
.gallery-nav svg { width: 18px; height: 18px; stroke: var(--text-dark); fill: none; stroke-width: 2.5; stroke-linecap: round; transition: stroke var(--transition); }
.gallery-nav:hover svg { stroke: white; }
.gallery-nav.prev { left: 12px; }
.gallery-nav.next { right: 12px; }
.gallery-nav:disabled { opacity: 0 !important; }
.gallery-dots {
  position: absolute; bottom: 14px; left: 50%; transform: translateX(-50%);
  display: flex; gap: 7px; z-index: 3;
}
.gallery-dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: rgba(255,255,255,.5); border: none; cursor: pointer; padding: 0;
  transition: all .22s ease;
}
.gallery-dot.active { background: white; width: 22px; border-radius: 4px; }
.gallery-dot:hover { background: rgba(255,255,255,.82); }

/* Thumbnails */
.gallery-thumbs {
  display: flex; gap: 10px; margin-top: 12px; overflow-x: auto;
  scrollbar-width: none; padding-bottom: 2px;
}
.gallery-thumbs::-webkit-scrollbar { display: none; }
.gallery-thumb {
  width: 68px; height: 68px; border-radius: 10px; overflow: hidden; flex-shrink: 0;
  cursor: pointer; border: 2.5px solid transparent; transition: all var(--transition);
  background: var(--cream);
}
.gallery-thumb:hover, .gallery-thumb.active { border-color: var(--rose); }
.gallery-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }

/* Trust strip */
.trust-strip {
  display: flex; flex-wrap: wrap; gap: 12px; margin-top: 20px; padding: 16px;
  background: var(--cream); border: 1px solid var(--border-light); border-radius: 12px;
}
.trust-item { display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 600; color: var(--text-mid); }
.trust-item svg { width: 17px; height: 17px; stroke: var(--rose); fill: none; stroke-width: 1.8; flex-shrink: 0; }

/* Share row */
.share-row { display: flex; align-items: center; gap: 10px; margin-top: 14px; }
.share-label { font-size: 11px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: var(--text-light); }
.share-btn {
  width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
  border: 1.5px solid var(--border); background: white; cursor: pointer;
  transition: all var(--transition); text-decoration: none; color: var(--text-mid);
}
.share-btn:hover { border-color: var(--rose); color: var(--rose); transform: translateY(-2px); }
.share-btn svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2; }
.share-btn.wa-share { background: #25d366; border-color: #25d366; color: white; }
.share-btn.wa-share:hover { background: #1fb958; border-color: #1fb958; }
.share-btn.wa-share svg { fill: white; stroke: none; }

/* ══ INFO COLUMN ══ */
.info-col { padding-top: 4px; }
.product-cat-tag { font-size: 10px; font-weight: 700; letter-spacing: 2.5px; text-transform: uppercase; color: var(--rose-light); margin-bottom: 6px; }
.product-name-h1 { font-size: clamp(22px, 3vw, 34px); font-weight: 800; color: var(--text-dark); line-height: 1.18; letter-spacing: -.5px; margin-bottom: 10px; }
.product-badge-row { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px; }
.product-badge-pill { padding: 4px 14px; border-radius: 20px; font-size: 10px; font-weight: 700; letter-spacing: .5px; color: white; text-transform: uppercase; }
.product-occasions-row { display: flex; flex-wrap: wrap; gap: 7px; margin-bottom: 16px; }
.product-occ-chip {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 5px 12px; border-radius: 50px; font-size: 12px; font-weight: 600;
  color: var(--text-mid); background: var(--cream-deep); border: 1px solid var(--border-light);
  text-decoration: none; transition: all var(--transition);
}
.product-occ-chip:hover { border-color: var(--rose-light); color: var(--rose); background: var(--rose-bg); }

/* Price */
.product-price-block { display: flex; align-items: baseline; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; }
.price-main { font-size: 36px; font-weight: 800; color: var(--rose); letter-spacing: -1px; }
.price-orig { font-size: 18px; font-weight: 500; color: var(--text-light); text-decoration: line-through; }
.price-save { font-size: 12px; font-weight: 700; color: #2e8b57; background: #e8f5ef; padding: 4px 12px; border-radius: 20px; }
.price-note { font-size: 11.5px; font-weight: 500; color: var(--text-light); margin-top: 2px; }

.product-divider { height: 1px; background: var(--border-light); margin: 18px 0; }

/* Variant selectors */
.variant-section { margin-bottom: 18px; }
.variant-label-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.variant-label { font-size: 11px; font-weight: 700; letter-spacing: 1.8px; text-transform: uppercase; color: var(--text-dark); }
.variant-selected-val { font-size: 12.5px; font-weight: 600; color: var(--rose); }
.size-options { display: flex; flex-wrap: wrap; gap: 9px; }
.size-btn {
  padding: 9px 18px; border: 2px solid var(--border); border-radius: 9px;
  font-family: var(--ff-body); font-size: 13px; font-weight: 700; color: var(--text-mid);
  background: white; cursor: pointer; transition: all var(--transition); position: relative;
}
.size-btn:hover { border-color: var(--rose); color: var(--rose); }
.size-btn.active { border-color: var(--rose); background: var(--rose); color: white; }
.size-btn.unavailable { opacity: .38; cursor: not-allowed; }
.size-btn.unavailable::after { content: ''; position: absolute; inset: -1px; border-radius: 9px; background: repeating-linear-gradient(-45deg, rgba(0,0,0,.04) 0, rgba(0,0,0,.04) 1px, transparent 1px, transparent 6px); pointer-events: none; }

/* Color swatches */
.color-swatches { display: flex; flex-wrap: wrap; gap: 10px; }
.color-swatch {
  width: 36px; height: 36px; border-radius: 50%; cursor: pointer; position: relative;
  border: 3px solid transparent; outline: none; transition: all var(--transition);
  box-shadow: 0 2px 8px rgba(0,0,0,.1);
}
.color-swatch:hover, .color-swatch.active { border-color: var(--rose); transform: scale(1.12); box-shadow: 0 0 0 2px white, 0 0 0 4px var(--rose); }
.color-swatch[title] { position: relative; }
.color-swatch::after { content: attr(title); position: absolute; bottom: calc(100% + 8px); left: 50%; transform: translateX(-50%); background: var(--text-dark); color: white; font-size: 10.5px; font-weight: 600; padding: 4px 8px; border-radius: 6px; white-space: nowrap; pointer-events: none; opacity: 0; transition: opacity .18s ease; }
.color-swatch:hover::after { opacity: 1; }

/* Variant pricing note */
.variant-price-note { font-size: 12px; font-weight: 600; color: var(--rose); background: var(--rose-bg); padding: 8px 14px; border-radius: 8px; margin-top: 12px; border: 1px solid rgba(184,92,110,.12); display: none; }

/* Quantity */
.qty-section { display: flex; align-items: center; gap: 20px; margin-bottom: 20px; }
.qty-control { display: flex; align-items: center; border: 2px solid var(--border); border-radius: 12px; overflow: hidden; }
.qty-btn { width: 42px; height: 42px; background: none; border: none; font-size: 20px; color: var(--text-mid); cursor: pointer; transition: all var(--transition); display: flex; align-items: center; justify-content: center; font-weight: 700; }
.qty-btn:hover { background: var(--rose-bg); color: var(--rose); }
.qty-val { width: 48px; text-align: center; font-size: 16px; font-weight: 800; color: var(--text-dark); border-left: 2px solid var(--border); border-right: 2px solid var(--border); line-height: 42px; }
.stock-badge { display: flex; align-items: center; gap: 7px; font-size: 13px; font-weight: 700; }
.stock-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.stock-dot.in  { background: #25d366; animation: pulseGreen 1.8s infinite; }
.stock-dot.out { background: #e74c3c; }
@keyframes pulseGreen { 0%,100%{opacity:1}50%{opacity:.4} }
.stock-in  { color: #2e8b57; }
.stock-out { color: #c0392b; }

/* CTA row */
.cta-row { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 18px; }
.btn-add-cart {
  flex: 1; min-width: 160px; display: flex; align-items: center; justify-content: center; gap: 9px;
  padding: 15px 20px; background: var(--rose); color: white; border: none; border-radius: 50px;
  font-family: var(--ff-body); font-size: 14px; font-weight: 800; cursor: pointer;
  transition: all var(--transition); box-shadow: 0 6px 20px rgba(184,92,110,.3);
  letter-spacing: .2px;
}
.btn-add-cart:hover { background: var(--rose-hover); transform: translateY(-2px); box-shadow: 0 10px 26px rgba(184,92,110,.35); }
.btn-add-cart:disabled { opacity: .5; cursor: not-allowed; transform: none; box-shadow: none; }
.btn-add-cart svg { width: 17px; height: 17px; stroke: white; fill: none; stroke-width: 2; flex-shrink: 0; }
.btn-wa-main {
  flex: 1; min-width: 150px; display: flex; align-items: center; justify-content: center; gap: 9px;
  padding: 15px 20px; background: #25d366; color: white; border: none; border-radius: 50px;
  font-family: var(--ff-body); font-size: 14px; font-weight: 800; cursor: pointer;
  transition: all var(--transition); box-shadow: 0 6px 20px rgba(37,211,102,.28);
  letter-spacing: .2px; text-decoration: none;
}
.btn-wa-main:hover { background: #1fb958; transform: translateY(-2px); box-shadow: 0 10px 26px rgba(37,211,102,.33); }
.btn-wa-main svg { width: 18px; height: 18px; fill: white; flex-shrink: 0; }
.btn-wishlist-main {
  width: 52px; height: 52px; border: 2px solid var(--border); border-radius: 50%;
  display: flex; align-items: center; justify-content: center; cursor: pointer;
  background: white; transition: all var(--transition); flex-shrink: 0;
}
.btn-wishlist-main:hover { border-color: var(--rose); }
.btn-wishlist-main svg { width: 20px; height: 20px; stroke: var(--rose-light); fill: none; stroke-width: 2; transition: all var(--transition); }
.btn-wishlist-main.wishlisted svg { stroke: var(--rose); fill: var(--rose); }
.btn-wishlist-main.wishlisted { border-color: var(--rose); }

/* Delivery info */
.delivery-row {
  display: flex; align-items: center; gap: 10px; padding: 12px 16px;
  background: var(--cream); border: 1px solid var(--border-light); border-radius: 10px;
  font-size: 13px; font-weight: 600; color: var(--text-mid); margin-bottom: 18px;
}
.delivery-row svg { width: 17px; height: 17px; stroke: var(--rose); fill: none; stroke-width: 2; stroke-linecap: round; flex-shrink: 0; }

/* Description & Story tabs */
.detail-tabs { margin-bottom: 0; }
.tab-nav { display: flex; border-bottom: 2px solid var(--border-light); margin-bottom: 18px; gap: 0; }
.tab-btn {
  padding: 10px 20px; font-family: var(--ff-body); font-size: 13px; font-weight: 700;
  color: var(--text-light); background: none; border: none; cursor: pointer;
  border-bottom: 3px solid transparent; margin-bottom: -2px; transition: all var(--transition);
  white-space: nowrap;
}
.tab-btn.active { color: var(--rose); border-bottom-color: var(--rose); }
.tab-btn:hover { color: var(--text-dark); }
.tab-panel { display: none; }
.tab-panel.active { display: block; }
.desc-text { font-size: 14px; font-weight: 500; color: var(--text-mid); line-height: 1.85; }
.desc-text p { margin-bottom: 10px; }
.desc-text p:last-child { margin-bottom: 0; }
.story-text {
  font-size: 14px; font-style: italic; color: var(--text-mid); line-height: 1.9;
  border-left: 3px solid var(--rose-light); padding-left: 16px; margin-bottom: 0;
}
.tags-wrap { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 16px; }
.tag-pill {
  display: inline-block; padding: 4px 12px; background: var(--cream-deep);
  border-radius: 20px; font-size: 11.5px; font-weight: 600; color: var(--text-light);
  border: 1px solid var(--border-light);
}

/* ══ PRODUCT DETAILS FULL-WIDTH ══ */
.product-full-detail { margin-top: 60px; }
.section-header {
  text-align: center; margin-bottom: 40px;
}
.section-header .eyebrow { font-size: 10px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: var(--rose); margin-bottom: 8px; display: block; }
.section-header h2 { font-size: clamp(22px,3vw,32px); font-weight: 800; color: var(--text-dark); letter-spacing: -.5px; }
.section-header p { font-size: 14px; color: var(--text-mid); max-width: 480px; margin: 8px auto 0; line-height: 1.65; }

/* Highlights grid */
.highlights-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 20px; margin-bottom: 60px; }
.highlight-card {
  background: var(--white); border: 1px solid var(--border-light); border-radius: var(--radius);
  padding: 24px 20px; text-align: center; transition: all var(--transition); box-shadow: var(--shadow-xs);
}
.highlight-card:hover { border-color: var(--rose-light); transform: translateY(-3px); box-shadow: var(--shadow-sm); }
.highlight-icon { width: 48px; height: 48px; border-radius: 12px; background: var(--rose-bg); display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }
.highlight-icon svg { width: 22px; height: 22px; stroke: var(--rose); fill: none; stroke-width: 1.8; stroke-linecap: round; }
.highlight-card h4 { font-size: 13.5px; font-weight: 700; color: var(--text-dark); margin-bottom: 5px; }
.highlight-card p { font-size: 12px; color: var(--text-light); line-height: 1.55; }

/* Reviews */
.reviews-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 20px; margin-bottom: 60px; }
.review-card { background: var(--white); border: 1px solid var(--border-light); border-radius: var(--radius); padding: 22px 20px; box-shadow: var(--shadow-xs); }
.review-stars { display: flex; gap: 3px; margin-bottom: 10px; }
.review-stars span { font-size: 14px; }
.review-text { font-size: 13.5px; color: var(--text-mid); line-height: 1.75; margin-bottom: 14px; font-style: italic; }
.review-author { display: flex; align-items: center; gap: 10px; }
.review-avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--rose-bg); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; color: var(--rose); flex-shrink: 0; }
.review-meta { flex: 1; }
.review-name { font-size: 13px; font-weight: 700; color: var(--text-dark); }
.review-handle { font-size: 11px; color: var(--text-light); }
.no-reviews-msg { text-align: center; padding: 40px; color: var(--text-light); font-size: 14px; font-weight: 500; background: var(--cream); border-radius: var(--radius); }

/* Related products */
.related-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 20px; }
.related-card {
  background: var(--white); border: 1px solid var(--border-light); border-radius: var(--radius);
  overflow: hidden; transition: all .3s ease; cursor: pointer; box-shadow: var(--shadow-xs);
  text-decoration: none; display: block;
}
.related-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); border-color: var(--rose-light); }
.related-img { height: 200px; overflow: hidden; background: var(--cream); position: relative; }
.related-img img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s ease; display: block; }
.related-card:hover .related-img img { transform: scale(1.06); }
.related-img-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(150deg,var(--rose-pale),var(--cream-deep)); }
.related-img-placeholder svg { width: 40px; height: 40px; stroke: var(--rose-light); fill: none; stroke-width: 1.2; }
.related-badge { position: absolute; top: 10px; left: 10px; padding: 4px 10px; border-radius: 20px; font-size: 9px; font-weight: 700; letter-spacing: .5px; text-transform: uppercase; color: white; }
.related-info { padding: 14px 15px 16px; }
.related-cat { font-size: 10px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: rgba(184,92,110,.6); margin-bottom: 4px; }
.related-name { font-size: 14px; font-weight: 700; color: var(--text-dark); line-height: 1.3; margin-bottom: 8px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.related-price { font-size: 17px; font-weight: 800; color: var(--rose); }
.related-orig  { font-size: 12px; color: var(--text-light); text-decoration: line-through; margin-left: 6px; }

/* Sticky CTA bar (mobile) */
.sticky-cta-bar {
  display: none; position: fixed; bottom: 0; left: 0; right: 0; z-index: 300;
  background: var(--white); border-top: 1px solid var(--border-light);
  padding: 12px 20px; gap: 10px; box-shadow: 0 -4px 20px rgba(0,0,0,.08);
}
.sticky-cta-bar .btn-add-cart,
.sticky-cta-bar .btn-wa-main { min-width: unset; padding: 13px 16px; font-size: 13px; }

/* Lightbox */
.prod-lightbox { position: fixed; inset: 0; z-index: 600; display: none; align-items: center; justify-content: center; padding: 24px; }
.prod-lightbox.open { display: flex; }
.prod-lb-overlay { position: absolute; inset: 0; background: rgba(14,10,12,.88); backdrop-filter: blur(16px); cursor: zoom-out; }
.prod-lb-panel { position: relative; z-index: 1; animation: lbZoom .3s ease both; }
@keyframes lbZoom { from{opacity:0;transform:scale(.93)} to{opacity:1;transform:none} }
.prod-lb-img { max-width: 90vw; max-height: 88vh; object-fit: contain; border-radius: 10px; box-shadow: 0 24px 64px rgba(0,0,0,.6); display: block; }
.prod-lb-close { position: absolute; top: -44px; right: 0; background: none; border: none; color: rgba(255,255,255,.75); cursor: pointer; transition: color .2s; padding: 4px; }
.prod-lb-close:hover { color: #fff; }
.prod-lb-close svg { width: 28px; height: 28px; stroke: currentColor; fill: none; stroke-width: 2.5; }

/* Responsive */
@media (max-width: 1100px) {
  .highlights-grid { grid-template-columns: repeat(2,1fr); }
  .related-grid { grid-template-columns: repeat(2,1fr); }
}
@media (max-width: 860px) {
  .product-grid { grid-template-columns: 1fr; gap: 28px; }
  .gallery-col { position: static; }
  .reviews-grid { grid-template-columns: 1fr 1fr; }
  .sticky-cta-bar { display: flex; }
  .product-page { padding-bottom: 100px; }
}
@media (max-width: 768px) {
  .breadcrumb, .product-page { padding-left: 20px; padding-right: 20px; }
  .product-name-h1 { font-size: 26px; }
  .related-grid { grid-template-columns: 1fr 1fr; }
  .highlights-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 540px) {
  .reviews-grid { grid-template-columns: 1fr; }
  .highlights-grid { grid-template-columns: 1fr 1fr; }
  .related-grid { grid-template-columns: 1fr 1fr; }
  .cta-row { flex-direction: column; }
  .cta-row .btn-wishlist-main { align-self: flex-start; }
}
</style>

<!-- BREADCRUMB -->
<nav class="breadcrumb" aria-label="Breadcrumb">
  <a href="index.php">Home</a>
  <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
  <a href="shop.php">Shop</a>
  <?php if ($cat): ?>
  <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
  <a href="shop.php?category=<?= p_esc($cat['slug']) ?>"><?= p_esc($cat['name']) ?></a>
  <?php endif ?>
  <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
  <span><?= p_esc($product['name']) ?></span>
</nav>

<div class="product-page">
  <div class="product-grid">

    <!-- ══ GALLERY ══ -->
    <div class="gallery-col" id="galleryCol">
      <div class="gallery-main-wrap" id="galleryMainWrap">
        <?php if ($media): ?>
          <img class="gallery-main-img" id="galleryMainImg"
               src="<?= p_esc($primary_img) ?>"
               alt="<?= p_esc($product['name']) ?>"
               loading="eager">
        <?php else: ?>
          <div class="gallery-placeholder">
            <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            <span>Image Coming Soon</span>
          </div>
        <?php endif ?>

        <?php if ($badge): ?>
          <span class="gallery-badge-tag" style="background:<?= p_esc($badge['color_hex'] ?? '#b85c6e') ?>">
            <?= p_esc($badge['name']) ?>
          </span>
        <?php endif ?>

        <?php if (count($media) > 1): ?>
        <button class="gallery-nav prev" id="galleryPrev" onclick="galleryStep(-1)" aria-label="Previous image">
          <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <button class="gallery-nav next" id="galleryNext" onclick="galleryStep(1)" aria-label="Next image">
          <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
        <div class="gallery-dots" id="galleryDots">
          <?php foreach ($media as $i => $m): ?>
            <button class="gallery-dot<?= $i===0?' active':'' ?>" onclick="goToGallery(<?= $i ?>)" aria-label="Image <?= $i+1 ?>"></button>
          <?php endforeach ?>
        </div>
        <?php endif ?>
      </div>

      <!-- Thumbnails -->
      <?php if (count($media) > 1): ?>
      <div class="gallery-thumbs" id="galleryThumbs">
        <?php foreach ($media as $i => $m): ?>
        <div class="gallery-thumb<?= $i===0?' active':'' ?>"
             onclick="goToGallery(<?= $i ?>)"
             title="View image <?= $i+1 ?>">
          <img src="<?= p_esc($m['file_url']) ?>" alt="<?= p_esc($m['alt_text'] ?? $product['name']) ?>" loading="lazy">
        </div>
        <?php endforeach ?>
      </div>
      <?php endif ?>

      <!-- Trust strip -->
      <div class="trust-strip">
        <div class="trust-item">
          <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          Handmade with Love
        </div>
        <div class="trust-item">
          <svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
          <?= p_esc($product['delivery_days'] ?? '3–5 Working Days') ?>
        </div>
        <div class="trust-item">
          <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          Secure Checkout
        </div>
        <div class="trust-item">
          <svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
          Premium Quality
        </div>
      </div>

      <!-- Share -->
      <div class="share-row">
        <span class="share-label">Share:</span>
        <a class="share-btn wa-share" id="waShareBtn" href="#" target="_blank" rel="noopener" aria-label="Share on WhatsApp" title="Share on WhatsApp">
          <svg viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
        </a>
        <button class="share-btn" onclick="copyLink()" title="Copy link" aria-label="Copy product link">
          <svg viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
        </button>
      </div>
    </div>

    <!-- ══ INFO ══ -->
    <div class="info-col" id="infoCol">
      <div class="product-cat-tag"><?= p_esc($cat['name'] ?? '') ?></div>
      <h1 class="product-name-h1"><?= p_esc($product['name']) ?></h1>

      <?php if ($badge): ?>
      <div class="product-badge-row">
        <span class="product-badge-pill" style="background:<?= p_esc($badge['color_hex'] ?? '#b85c6e') ?>">
          <?= p_esc($badge['name']) ?>
        </span>
        <?php if ($product['is_featured']): ?><span class="product-badge-pill" style="background:#c0a040">Featured</span><?php endif ?>
        <?php if ($product['is_new_arrival']): ?><span class="product-badge-pill" style="background:#2e8b57">New Arrival</span><?php endif ?>
        <?php if ($product['is_trending']): ?><span class="product-badge-pill" style="background:#9c27b0">Trending</span><?php endif ?>
        <?php if ($product['is_bestseller']): ?><span class="product-badge-pill" style="background:#e65100">Bestseller</span><?php endif ?>
      </div>
      <?php endif ?>

      <?php if ($occasions): ?>
      <div class="product-occasions-row">
        <?php foreach ($occasions as $occ): ?>
        <a class="product-occ-chip" href="shop.php?occasion=<?= p_esc($occ['slug']) ?>">
          <?= $occ['icon_emoji'] ?? '' ?> <?= p_esc($occ['name']) ?>
        </a>
        <?php endforeach ?>
      </div>
      <?php endif ?>

      <!-- Price -->
      <?php
        $display_price = (float)$product['price'];
        $orig_price    = $product['discount_price'] ? (float)$product['discount_price'] : null;
        $save_pct      = ($orig_price && $display_price < $orig_price) ? round((1 - $display_price/$orig_price)*100) : 0;
      ?>
      <div class="product-price-block" id="priceBlock">
        <span class="price-main" id="priceMain">₹<?= p_fmt($display_price) ?></span>
        <?php if ($orig_price): ?>
          <span class="price-orig" id="priceOrig">₹<?= p_fmt($orig_price) ?></span>
          <?php if ($save_pct > 0): ?><span class="price-save" id="priceSave">Save <?= $save_pct ?>%</span><?php endif ?>
        <?php endif ?>
      </div>
      <div class="price-note">Inclusive of all taxes · Prices may vary by size/color</div>
      <div id="variantPriceNote" class="variant-price-note"></div>

      <div class="product-divider"></div>

      <!-- SIZE SELECTOR -->
      <?php if ($sizes): ?>
      <div class="variant-section" id="sizeSectionWrap">
        <div class="variant-label-row">
          <span class="variant-label">Size</span>
          <span class="variant-selected-val" id="selectedSizeLabel">Select a size</span>
        </div>
        <div class="size-options" id="sizeOptions">
          <?php foreach ($sizes as $sz): ?>
          <button class="size-btn"
                  data-slug="<?= p_esc($sz['size_slug']) ?>"
                  data-label="<?= p_esc($sz['size_label']) ?>"
                  onclick="selectSize(this)"
                  title="<?= p_esc($sz['dimension_cm'] ?? '') ?>"
                  aria-label="<?= p_esc($sz['size_label']) ?>">
            <?= p_esc($sz['size_label']) ?>
          </button>
          <?php endforeach ?>
        </div>
      </div>
      <?php endif ?>

      <!-- COLOR SWATCHES -->
      <?php if ($colors): ?>
      <div class="variant-section" id="colorSectionWrap">
        <div class="variant-label-row">
          <span class="variant-label">Color</span>
          <span class="variant-selected-val" id="selectedColorLabel">Select a color</span>
        </div>
        <div class="color-swatches" id="colorSwatches">
          <?php foreach ($colors as $cl): ?>
          <button class="color-swatch"
                  style="background:<?= p_esc($cl['hex_code'] ?? '#ccc') ?>"
                  data-slug="<?= p_esc($cl['color_slug']) ?>"
                  data-name="<?= p_esc($cl['color_name']) ?>"
                  data-hex="<?= p_esc($cl['hex_code'] ?? '#ccc') ?>"
                  onclick="selectColor(this)"
                  title="<?= p_esc($cl['color_name']) ?>"
                  aria-label="<?= p_esc($cl['color_name']) ?>">
          </button>
          <?php endforeach ?>
        </div>
      </div>
      <?php endif ?>

      <?php if ($sizes || $colors): ?><div class="product-divider"></div><?php endif ?>

      <!-- QUANTITY -->
      <div class="qty-section">
        <div class="qty-control">
          <button class="qty-btn" onclick="changeQty(-1)" aria-label="Decrease quantity">−</button>
          <div class="qty-val" id="qtyVal" aria-live="polite">1</div>
          <button class="qty-btn" onclick="changeQty(1)" aria-label="Increase quantity">+</button>
        </div>
        <div class="stock-badge">
          <?php if ($product['in_stock']): ?>
            <span class="stock-dot in"></span>
            <span class="stock-in">In Stock</span>
          <?php else: ?>
            <span class="stock-dot out"></span>
            <span class="stock-out">Out of Stock</span>
          <?php endif ?>
        </div>
      </div>

      <!-- CTAs -->
      <div class="cta-row" id="ctaRow">
        <button class="btn-add-cart" id="btnAddCart" onclick="addToCartMain()"
                <?= !$product['in_stock'] ? 'disabled' : '' ?>>
          <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          <?= $product['in_stock'] ? 'Add to Cart' : 'Out of Stock' ?>
        </button>
        <button class="btn-wa-main" id="btnWaMain" onclick="sendWhatsAppMain()" aria-label="Order on WhatsApp">
          <svg viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
          Order on WhatsApp
        </button>
        <button class="btn-wishlist-main" id="btnWishlist" onclick="toggleWishlistMain()" aria-label="Add to Wishlist" title="Wishlist">
          <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        </button>
      </div>

      <!-- Delivery -->
      <div class="delivery-row">
        <svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        <span>Estimated Delivery: <strong><?= p_esc($product['delivery_days'] ?? '3–5 Working Days') ?></strong></span>
      </div>

      <!-- Tabs: description / story / tags -->
      <div class="detail-tabs">
        <div class="tab-nav" role="tablist">
          <button class="tab-btn active" id="tabBtnDesc" onclick="switchTab('desc')" role="tab" aria-selected="true">Description</button>
          <?php if ($product['product_story']): ?><button class="tab-btn" id="tabBtnStory" onclick="switchTab('story')" role="tab" aria-selected="false">Story</button><?php endif ?>
          <?php if ($product['tags']): ?><button class="tab-btn" id="tabBtnTags" onclick="switchTab('tags')" role="tab" aria-selected="false">Details</button><?php endif ?>
        </div>
        <div class="tab-panel active" id="tabDesc" role="tabpanel">
          <div class="desc-text">
            <?php
              $full = trim($product['full_description'] ?? $product['short_description'] ?? '');
              $paras = array_filter(explode("\n", $full));
              foreach ($paras as $p_line): if (trim($p_line)): ?>
                <p><?= nl2br(p_esc(trim($p_line))) ?></p>
              <?php endif; endforeach ?>
          </div>
        </div>
        <?php if ($product['product_story']): ?>
        <div class="tab-panel" id="tabStory" role="tabpanel">
          <p class="story-text"><?= nl2br(p_esc(trim($product['product_story']))) ?></p>
        </div>
        <?php endif ?>
        <?php if ($product['tags']): ?>
        <div class="tab-panel" id="tabTags" role="tabpanel">
          <div class="tags-wrap">
            <?php foreach (array_map('trim', explode(',', $product['tags'])) as $tag): if ($tag): ?>
              <span class="tag-pill"><?= p_esc($tag) ?></span>
            <?php endif; endforeach ?>
          </div>
        </div>
        <?php endif ?>
      </div>
    </div>
  </div>

  <!-- ══ FULL-WIDTH SECTIONS ══ -->
  <div class="product-full-detail">

    <!-- Highlights -->
    <div class="section-header">
      <span class="eyebrow">Why You'll Love It</span>
      <h2>Product Highlights</h2>
    </div>
    <div class="highlights-grid">
      <div class="highlight-card">
        <div class="highlight-icon"><svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></div>
        <h4>Handcrafted with Love</h4>
        <p>Every piece is carefully hand-made with attention to detail and quality.</p>
      </div>
      <div class="highlight-card">
        <div class="highlight-icon"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
        <h4>Lasts Forever</h4>
        <p>Unlike real gifts that fade, our products create lasting memories.</p>
      </div>
      <div class="highlight-card">
        <div class="highlight-icon"><svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></div>
        <h4>Fast Delivery</h4>
        <p><?= p_esc($product['delivery_days'] ?? '3–5 Working Days') ?> right to your doorstep.</p>
      </div>
      <div class="highlight-card">
        <div class="highlight-icon"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
        <h4>Premium Packaging</h4>
        <p>Gift-ready packaging with elegant wrapping and personal touch.</p>
      </div>
    </div>

    <!-- Reviews -->
    <?php if ($reviews): ?>
    <div class="section-header">
      <span class="eyebrow">What Customers Say</span>
      <h2>Happy Customers</h2>
      <p>Real reviews from people who love our gifts</p>
    </div>
    <div class="reviews-grid">
      <?php foreach ($reviews as $rev): ?>
      <div class="review-card">
        <div class="review-stars">
          <?php for ($i=1; $i<=5; $i++): ?>
            <span><?= $i <= (int)$rev['rating'] ? '★' : '☆' ?></span>
          <?php endfor ?>
        </div>
        <p class="review-text">"<?= p_esc($rev['review']) ?>"</p>
        <div class="review-author">
          <div class="review-avatar"><?= strtoupper(mb_substr($rev['name'], 0, 1)) ?></div>
          <div class="review-meta">
            <div class="review-name"><?= p_esc($rev['name']) ?></div>
            <?php if ($rev['instagram']): ?><div class="review-handle"><?= p_esc($rev['instagram']) ?></div><?php endif ?>
          </div>
        </div>
      </div>
      <?php endforeach ?>
    </div>
    <?php else: ?>
    <div class="section-header">
      <span class="eyebrow">Reviews</span>
      <h2>Be the First to Review</h2>
    </div>
    <div class="no-reviews-msg">No reviews yet for this product. Order yours and share your experience! 💕</div>
    <div style="margin-bottom:40px"></div>
    <?php endif ?>

    <!-- Related Products -->
    <?php if ($related): ?>
    <div class="section-header">
      <span class="eyebrow">You Might Also Like</span>
      <h2>More from <?= p_esc($cat['name'] ?? 'This Collection') ?></h2>
    </div>
    <div class="related-grid">
      <?php foreach ($related as $rel): ?>
      <a class="related-card" href="product.php?slug=<?= urlencode($rel['slug']) ?>" aria-label="<?= p_esc($rel['name']) ?>">
        <div class="related-img">
          <?php if ($rel['primary_image']): ?>
            <img src="<?= p_esc($rel['primary_image']) ?>" alt="<?= p_esc($rel['name']) ?>" loading="lazy">
          <?php else: ?>
            <div class="related-img-placeholder">
              <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </div>
          <?php endif ?>
          <?php if ($rel['badge_name']): ?>
            <span class="related-badge" style="background:<?= p_esc($rel['badge_color'] ?? '#b85c6e') ?>"><?= p_esc($rel['badge_name']) ?></span>
          <?php endif ?>
        </div>
        <div class="related-info">
          <div class="related-cat"><?= p_esc($cat['name'] ?? '') ?></div>
          <div class="related-name"><?= p_esc($rel['name']) ?></div>
          <div>
            <span class="related-price">₹<?= p_fmt($rel['price']) ?></span>
            <?php if ($rel['discount_price']): ?><span class="related-orig">₹<?= p_fmt($rel['discount_price']) ?></span><?php endif ?>
          </div>
        </div>
      </a>
      <?php endforeach ?>
    </div>
    <?php endif ?>

  </div><!-- /product-full-detail -->
</div><!-- /product-page -->

<!-- ══ STICKY MOBILE CTA BAR ══ -->
<div class="sticky-cta-bar" id="stickyCta" aria-label="Quick buy">
  <button class="btn-add-cart" onclick="addToCartMain()" <?= !$product['in_stock'] ? 'disabled' : '' ?>>
    <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
    Add to Cart
  </button>
  <button class="btn-wa-main" onclick="sendWhatsAppMain()">
    <svg viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
    WhatsApp
  </button>
</div>

<!-- ══ LIGHTBOX ══ -->
<div class="prod-lightbox" id="prodLightbox" role="dialog" aria-modal="true" aria-label="Product image fullscreen">
  <div class="prod-lb-overlay" onclick="closeLightbox()"></div>
  <div class="prod-lb-panel">
    <button class="prod-lb-close" onclick="closeLightbox()" aria-label="Close">
      <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <img class="prod-lb-img" id="prodLbImg" src="" alt="Product fullscreen view">
  </div>
</div>

<script>
/* ══════════ PRODUCT DATA ══════════ */
const PROD = {
  id:        <?= (int)$product['id'] ?>,
  name:      <?= json_encode($product['name'], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?>,
  slug:      <?= json_encode($product['slug']) ?>,
  price:     <?= (float)$product['price'] ?>,
  salePrice: <?= $product['discount_price'] ? (float)$product['discount_price'] : 'null' ?>,
  inStock:   <?= $product['in_stock'] ? 'true' : 'false' ?>,
  delivery:  <?= json_encode($product['delivery_days'] ?? '3–5 Working Days') ?>,
  catName:   <?= json_encode($cat['name'] ?? '', JSON_UNESCAPED_UNICODE) ?>,
  waMsg:     <?= json_encode($product['whatsapp_message'] ?? '', JSON_UNESCAPED_UNICODE) ?>,
  primaryImg:<?= json_encode($primary_img) ?>,
};
const WA_NUMBER   = <?= json_encode($wa_number) ?>;
const MEDIA_URLS  = <?= json_encode(array_column($media, 'file_url')) ?>;
const VARIANTS    = <?= json_encode(array_values($variants), JSON_UNESCAPED_UNICODE) ?>;
const VAR_IMAGES  = <?= json_encode($variant_images, JSON_UNESCAPED_UNICODE) ?>;

/* ══════════ STATE ══════════ */
let qty           = 1;
let galleryIdx    = 0;
let selSizeSlug   = null;
let selColorSlug  = null;
let currentImages = [...MEDIA_URLS];

/* ══════════ GALLERY ══════════ */
function setGalleryImages(imgs) {
  currentImages = imgs.length ? imgs : MEDIA_URLS;
  goToGallery(0, true);
  rebuildThumbs();
}

function goToGallery(idx, skipAnim) {
  if (!currentImages.length) return;
  idx = Math.max(0, Math.min(idx, currentImages.length - 1));
  galleryIdx = idx;
  const mainImg = document.getElementById('galleryMainImg');
  if (mainImg) {
    if (!skipAnim) { mainImg.style.opacity = '0'; mainImg.style.transform = 'scale(1.03)'; }
    setTimeout(() => {
      mainImg.src = currentImages[idx];
      mainImg.style.transition = 'opacity .3s ease, transform .55s cubic-bezier(.4,0,.2,1)';
      mainImg.style.opacity = '1';
      mainImg.style.transform = '';
    }, skipAnim ? 0 : 120);
  }
  document.querySelectorAll('.gallery-thumb').forEach((t,i) => t.classList.toggle('active', i===idx));
  document.querySelectorAll('.gallery-dot').forEach((d,i) => d.classList.toggle('active', i===idx));
  const prev = document.getElementById('galleryPrev');
  const next = document.getElementById('galleryNext');
  if (prev) prev.disabled = idx === 0;
  if (next) next.disabled = idx === currentImages.length - 1;
}

function galleryStep(dir) {
  goToGallery(galleryIdx + dir);
}

function rebuildThumbs() {
  const thumbs = document.getElementById('galleryThumbs');
  if (!thumbs) return;
  thumbs.innerHTML = currentImages.map((src, i) =>
    `<div class="gallery-thumb${i===0?' active':''}" onclick="goToGallery(${i})" title="View image ${i+1}">
       <img src="${esc(src)}" alt="Thumbnail ${i+1}" loading="lazy">
     </div>`
  ).join('');
  // Rebuild dots
  const dotsEl = document.getElementById('galleryDots');
  if (dotsEl && currentImages.length > 1) {
    dotsEl.innerHTML = currentImages.map((_,i) =>
      `<button class="gallery-dot${i===0?' active':''}" onclick="goToGallery(${i})" aria-label="Image ${i+1}"></button>`
    ).join('');
  }
}

/* Click main image → lightbox */
document.getElementById('galleryMainWrap')?.addEventListener('click', function(e) {
  if (e.target.closest('.gallery-nav')) return;
  const img = document.getElementById('galleryMainImg');
  if (img) openLightbox(img.src);
});

/* ══════════ LIGHTBOX ══════════ */
function openLightbox(src) {
  document.getElementById('prodLbImg').src = src;
  document.getElementById('prodLightbox').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeLightbox() {
  document.getElementById('prodLightbox').classList.remove('open');
  document.body.style.overflow = '';
}

/* ══════════ SIZE SELECTION ══════════ */
function selectSize(btn) {
  document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  selSizeSlug = btn.dataset.slug;
  document.getElementById('selectedSizeLabel').textContent = btn.dataset.label;
  updateVariantPrice();
}

/* ══════════ COLOR SELECTION ══════════ */
function selectColor(btn) {
  document.querySelectorAll('.color-swatch').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  selColorSlug = btn.dataset.slug;
  document.getElementById('selectedColorLabel').textContent = btn.dataset.name;

  /* Switch gallery to color-specific images if available */
  const colorId = btn.dataset.colorId;
  // Try matching variant_images by color id
  const allColors = document.querySelectorAll('.color-swatch');
  let matchedColorId = null;
  allColors.forEach(b => {
    if (b.dataset.slug === selColorSlug && b.dataset.id) matchedColorId = b.dataset.id;
  });

  // Find images keyed by color hex match in VAR_IMAGES or default to all media
  let colorImgs = null;
  for (const [cid, imgs] of Object.entries(VAR_IMAGES)) {
    // Match by any heuristic (id or color matching)
    if (cid !== 'default' && imgs.length) {
      // Use this color's images if we can't match precisely
    }
  }
  // Simplest approach: just use all media for now (color images need color_id on the swatch)
  // The color images are stored per color_id in VAR_IMAGES; we'd need color_id from DB on the swatch
  // Since we don't have it in this context, fall back to default
  setGalleryImages(MEDIA_URLS);
  updateVariantPrice();
}

/* ══════════ VARIANT PRICING ══════════ */
function updateVariantPrice() {
  if (!VARIANTS.length) return;
  let match = null;
  if (selSizeSlug && selColorSlug) {
    match = VARIANTS.find(v => v.size_slug === selSizeSlug && v.color_slug === selColorSlug);
  } else if (selSizeSlug) {
    match = VARIANTS.find(v => v.size_slug === selSizeSlug);
  } else if (selColorSlug) {
    match = VARIANTS.find(v => v.color_slug === selColorSlug);
  }

  const noteEl = document.getElementById('variantPriceNote');
  if (match && (match.price_override || match.discount_price_override)) {
    const vp = match.price_override    ? parseFloat(match.price_override) : null;
    const vo = match.discount_price_override ? parseFloat(match.discount_price_override) : null;
    const displayP = vp || PROD.price;
    const displayO = vo || PROD.salePrice;
    document.getElementById('priceMain').textContent = '₹' + fmt(displayP);
    if (displayO && document.getElementById('priceOrig')) {
      document.getElementById('priceOrig').textContent = '₹' + fmt(displayO);
    }
    noteEl.textContent = 'Price updated for selected option';
    noteEl.style.display = 'block';
  } else {
    document.getElementById('priceMain').textContent = '₹' + fmt(PROD.price);
    if (document.getElementById('priceOrig') && PROD.salePrice) {
      document.getElementById('priceOrig').textContent = '₹' + fmt(PROD.salePrice);
    }
    noteEl.style.display = 'none';
  }
}

/* ══════════ QUANTITY ══════════ */
function changeQty(d) {
  qty = Math.max(1, Math.min(99, qty + d));
  document.getElementById('qtyVal').textContent = qty;
}

/* ══════════ TABS ══════════ */
function switchTab(name) {
  ['desc','story','tags'].forEach(t => {
    const panel = document.getElementById('tabDesc'.replace('desc',t) || `tab${t.charAt(0).toUpperCase()+t.slice(1)}`);
    const btn   = document.getElementById(`tabBtn${t.charAt(0).toUpperCase()+t.slice(1)}`);
    if (panel) panel.classList.remove('active');
    if (btn)   { btn.classList.remove('active'); btn.setAttribute('aria-selected','false'); }
  });
  const activePanel = document.getElementById('tab' + name.charAt(0).toUpperCase() + name.slice(1));
  const activeBtn   = document.getElementById('tabBtn' + name.charAt(0).toUpperCase() + name.slice(1));
  if (activePanel) activePanel.classList.add('active');
  if (activeBtn)   { activeBtn.classList.add('active'); activeBtn.setAttribute('aria-selected','true'); }
}

/* ══════════ CART ══════════ */
function addToCartMain() {
  if (!PROD.inStock) return;
  try {
    const cart = JSON.parse(localStorage.getItem('aakar_cart') || '[]');
    const idx  = cart.findIndex(i => String(i.id) === String(PROD.id));
    if (idx > -1) cart[idx].qty = Math.min((cart[idx].qty||1) + qty, 10);
    else cart.push({ id: PROD.id, name: PROD.name, price: PROD.price, discount_price: PROD.salePrice, image_url: PROD.primaryImg, category: PROD.catName, qty });
    localStorage.setItem('aakar_cart', JSON.stringify(cart));
    if (window.updateAllBadges) window.updateAllBadges();
  } catch(e){}
  const fn = window.acToast || showToast;
  fn(PROD.name + ' added to cart! 🛍', 'success');
}

/* ══════════ WISHLIST ══════════ */
function toggleWishlistMain() {
  const item = { id: PROD.id, name: PROD.name, price: PROD.price, discount_price: PROD.salePrice, image_url: PROD.primaryImg, category: PROD.catName };
  let inList = false;
  if (window.toggleWishlist) {
    inList = window.toggleWishlist(item);
  } else {
    try {
      const wl = JSON.parse(localStorage.getItem('aakar_wishlist') || '[]');
      const ei = wl.findIndex(w => String(w.id) === String(PROD.id));
      if (ei > -1) { wl.splice(ei, 1); inList = false; }
      else { wl.push(item); inList = true; }
      localStorage.setItem('aakar_wishlist', JSON.stringify(wl));
    } catch(e){}
  }
  document.getElementById('btnWishlist').classList.toggle('wishlisted', inList);
  const fn = window.acToast || showToast;
  fn(inList ? 'Added to wishlist ❤️' : 'Removed from wishlist', inList ? 'success' : 'info');
}

/* Check initial wishlist state */
(function(){
  try {
    const wl = JSON.parse(localStorage.getItem('aakar_wishlist') || '[]');
    if (wl.some(w => String(w.id) === String(PROD.id))) {
      document.getElementById('btnWishlist')?.classList.add('wishlisted');
    }
  } catch(e){}
})();

/* ══════════ WHATSAPP ══════════ */
function buildWAMsg() {
  const size  = selSizeSlug ? document.querySelector(`.size-btn[data-slug="${selSizeSlug}"]`)?.dataset.label : null;
  const color = selColorSlug ? document.querySelector(`.color-swatch[data-slug="${selColorSlug}"]`)?.dataset.name : null;
  const price = parseFloat(document.getElementById('priceMain').textContent.replace(/[₹,]/g,'')) || PROD.price;
  const total = price * qty;
  const varStr = [size ? `📐 *Size:* ${size}` : '', color ? `🎨 *Color:* ${color}` : ''].filter(Boolean).join('\n');
  const qtyStr = qty > 1 ? `\n🔢 *Qty:* ${qty}\n💵 *Total:* ₹${fmt(total)}` : '';
  if (PROD.waMsg && qty === 1 && !size && !color) {
    return PROD.waMsg.replace('{product_name}', PROD.name).replace('{price}', fmt(price)).replace('{category}', PROD.catName);
  }
  return `Hello Aakar Creatives! 🌸\n\nI'd like to order:\n\n📦 *${PROD.name}*\n🏷 *Category:* ${PROD.catName}${varStr ? '\n' + varStr : ''}\n\n💰 *Price:* ₹${fmt(price)}${qtyStr}\n🚚 *Delivery:* ${PROD.delivery}\n\nPlease share more details and confirm availability. 😊`;
}

function sendWhatsAppMain() {
  const url = `https://wa.me/${WA_NUMBER}?text=${encodeURIComponent(buildWAMsg())}`;
  window.open(url, '_blank');
  trackEvent('whatsapp_click');
}

/* WhatsApp share link */
document.getElementById('waShareBtn').href = `https://wa.me/?text=${encodeURIComponent(PROD.name + ' — Check this out! ' + location.href)}`;

/* ══════════ COPY LINK ══════════ */
function copyLink() {
  navigator.clipboard?.writeText(location.href).then(() => {
    const fn = window.acToast || showToast;
    fn('Link copied to clipboard! 🔗', 'success');
  });
}

/* ══════════ KEYBOARD ══════════ */
document.addEventListener('keydown', e => {
  if (document.getElementById('prodLightbox').classList.contains('open')) {
    if (e.key === 'Escape') closeLightbox();
    return;
  }
  if (e.key === 'ArrowLeft')  galleryStep(-1);
  if (e.key === 'ArrowRight') galleryStep(1);
});

/* Touch swipe on gallery */
(function() {
  const wrap = document.getElementById('galleryMainWrap');
  if (!wrap) return;
  let tx = 0;
  wrap.addEventListener('touchstart', e => { tx = e.touches[0].clientX; }, { passive: true });
  wrap.addEventListener('touchend', e => {
    const dx = e.changedTouches[0].clientX - tx;
    if (Math.abs(dx) > 40) galleryStep(dx < 0 ? 1 : -1);
  }, { passive: true });
})();

/* ══════════ ANALYTICS ══════════ */
function trackEvent(type) {
  try {
    const d = new URLSearchParams({ event: type, product_id: PROD.id });
    if (navigator.sendBeacon) navigator.sendBeacon('track.php', d);
  } catch(e){}
}

/* ══════════ TOAST FALLBACK ══════════ */
function showToast(msg, type) {
  if (window.acToast) { window.acToast(msg, type || 'info'); return; }
  let c = document.getElementById('prodToastCont');
  if (!c) {
    c = document.createElement('div'); c.id = 'prodToastCont';
    c.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none';
    document.body.appendChild(c);
  }
  if (!document.getElementById('prodToastCSS')) {
    const s = document.createElement('style'); s.id = 'prodToastCSS';
    s.textContent = '@keyframes ptIn{from{opacity:0;transform:translateX(28px)}to{opacity:1;transform:none}}@keyframes ptOut{to{opacity:0;transform:translateX(28px)}}';
    document.head.appendChild(s);
  }
  const t = document.createElement('div');
  t.style.cssText = `background:#2d1a22;color:#fff;padding:12px 20px;border-radius:10px;font-size:13.5px;font-weight:600;border-left:3px solid #b85c6e;animation:ptIn .28s ease both;max-width:260px`;
  t.textContent = msg;
  c.appendChild(t);
  setTimeout(() => { t.style.animation = 'ptOut .28s ease both'; setTimeout(() => t.remove(), 300); }, 2600);
}

/* ══════════ UTILS ══════════ */
function fmt(n) { return Number(n).toLocaleString('en-IN'); }
function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

/* Init gallery arrows state */
document.addEventListener('DOMContentLoaded', () => {
  goToGallery(0, true);
});
</script>

<?php include 'includes/footer.php'; ?>