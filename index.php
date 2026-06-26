<?php
// ============================================================
//  AAKAR CREATIVES — index.php  (Production Ready)
//  Dynamic data from MySQL · Analytics tracking · Newsletter
// ============================================================

require_once 'includes/db.php';   // provides $pdo (PDO instance)

// ── Analytics: track homepage visit ─────────────────────────
try {
    $ip_hash = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '');
    $ua       = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 300);
    $ref      = substr($_SERVER['HTTP_REFERER']    ?? '', 0, 300);
    $pdo->prepare("INSERT INTO analytics_events (event_type,ip_hash,user_agent,referrer) VALUES ('homepage_visit',?,?,?)")
        ->execute([$ip_hash, $ua, $ref]);
} catch (Exception $e) { /* silent */ }

// ── Newsletter AJAX handler ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newsletter_email'])) {
    header('Content-Type: application/json');
    $email = filter_var(trim($_POST['newsletter_email']), FILTER_VALIDATE_EMAIL);
    if (!$email) {
        echo json_encode(['ok' => false, 'msg' => 'Please enter a valid email address.']);
        exit;
    }
    try {
        $exists = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
        $exists->execute([$email]);
        if ($exists->fetch()) {
            echo json_encode(['ok' => false, 'msg' => 'You\'re already subscribed — thank you!']);
        } else {
            $pdo->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)")->execute([$email]);
            echo json_encode(['ok' => true, 'msg' => 'Subscribed! Welcome to the Aakar family 🌸']);
        }
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'msg' => 'Something went wrong. Please try again.']);
    }
    exit;
}

// ── Fetch: featured categories ───────────────────────────────
try {
    $cats = $pdo->query(
        "SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order ASC, id ASC LIMIT 6"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $cats = []; }

// ── Fetch: 1 bestseller + 1 new arrival per active category ──
// Strategy: for each active category, pick at most 1 bestseller and 1 new arrival.
// If neither flag is set, fall back to any 1 active in-stock product.
// Final list is capped at 8 cards for the grid.
$products = [];
try {
    // Pull all active categories (reuse $cats if available)
    $cat_ids = array_column($cats, 'id');

    if (!empty($cat_ids)) {
        $placeholders = implode(',', array_fill(0, count($cat_ids), '?'));

        // Fetch candidates: bestsellers and new arrivals for those categories
        $stmt = $pdo->prepare(
            "SELECT
                p.id, p.name, p.slug, p.price, p.discount_price,
                p.short_description, p.in_stock, p.is_bestseller, p.is_new_arrival,
                p.is_trending, p.is_featured, p.category_id,
                b.name  AS badge_name,
                b.color_hex AS badge_color,
                pm.file_url AS primary_image
             FROM products p
             LEFT JOIN badges b  ON b.id  = p.badge_id  AND b.is_active = 1
             LEFT JOIN product_media pm ON pm.product_id = p.id AND pm.is_primary = 1
             WHERE p.status = 'active'
               AND p.in_stock = 1
               AND p.category_id IN ($placeholders)
               AND (p.is_bestseller = 1 OR p.is_new_arrival = 1)
             ORDER BY p.is_bestseller DESC, p.is_new_arrival DESC, p.id DESC"
        );
        $stmt->execute($cat_ids);
        $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group by category: pick 1 bestseller + 1 new arrival each
        $by_cat = [];
        foreach ($candidates as $p) {
            $cid = (int)$p['category_id'];
            if (!isset($by_cat[$cid])) {
                $by_cat[$cid] = ['bestseller' => null, 'new_arrival' => null];
            }
            if ($p['is_bestseller'] && $by_cat[$cid]['bestseller'] === null) {
                $by_cat[$cid]['bestseller'] = $p;
            }
            if ($p['is_new_arrival'] && $by_cat[$cid]['new_arrival'] === null) {
                $by_cat[$cid]['new_arrival'] = $p;
            }
        }

        // Collect selected products (avoid duplicates by product id)
        $selected_ids = [];
        foreach ($cat_ids as $cid) {
            if (!isset($by_cat[$cid])) continue;
            foreach (['bestseller', 'new_arrival'] as $slot) {
                $p = $by_cat[$cid][$slot] ?? null;
                if ($p && !in_array($p['id'], $selected_ids, true)) {
                    $products[]     = $p;
                    $selected_ids[] = $p['id'];
                }
            }
        }

        // Fallback: for categories that contributed nothing, add 1 any product
        $covered_cats = array_unique(array_column($products, 'category_id'));
        $missing_cats = array_diff($cat_ids, $covered_cats);

        if (!empty($missing_cats)) {
            $mp = implode(',', array_fill(0, count($missing_cats), '?'));
            // Also exclude already-selected ids to avoid dupes
            $ex = !empty($selected_ids)
                ? 'AND p.id NOT IN (' . implode(',', array_fill(0, count($selected_ids), '?')) . ')'
                : '';
            $fallback_stmt = $pdo->prepare(
                "SELECT
                    p.id, p.name, p.slug, p.price, p.discount_price,
                    p.short_description, p.in_stock, p.is_bestseller, p.is_new_arrival,
                    p.is_trending, p.is_featured, p.category_id,
                    b.name  AS badge_name,
                    b.color_hex AS badge_color,
                    pm.file_url AS primary_image
                 FROM products p
                 LEFT JOIN badges b  ON b.id  = p.badge_id  AND b.is_active = 1
                 LEFT JOIN product_media pm ON pm.product_id = p.id AND pm.is_primary = 1
                 WHERE p.status = 'active'
                   AND p.in_stock = 1
                   AND p.category_id IN ($mp)
                   $ex
                 ORDER BY p.category_id ASC, p.is_featured DESC, p.id ASC"
            );
            $params = array_merge($missing_cats, $selected_ids);
            $fallback_stmt->execute($params);
            $fallback_rows = $fallback_stmt->fetchAll(PDO::FETCH_ASSOC);

            // Pick 1 per missing category
            $seen_fb_cats = [];
            foreach ($fallback_rows as $p) {
                $cid = (int)$p['category_id'];
                if (!in_array($cid, $seen_fb_cats, true) && !in_array($p['id'], $selected_ids, true)) {
                    $products[]     = $p;
                    $selected_ids[] = $p['id'];
                    $seen_fb_cats[] = $cid;
                }
            }
        }

        // Cap at 8 for the 4-col grid (2 rows)
        $products = array_slice($products, 0, 8);
    }
} catch (Exception $e) { $products = []; }

// ── Fetch: occasions ─────────────────────────────────────────
try {
    $occasions = $pdo->query(
        "SELECT o.*, COUNT(po.product_id) AS gift_count
         FROM occasions o
         LEFT JOIN product_occasions po ON po.occasion_id = o.id
         WHERE o.is_active = 1 AND o.slug != 'all'
         GROUP BY o.id
         ORDER BY o.sort_order ASC
         LIMIT 6"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    try {
        $occasions = $pdo->query(
            "SELECT * FROM occasions WHERE is_active = 1 AND slug != 'all' ORDER BY sort_order ASC LIMIT 6"
        )->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e2) { $occasions = []; }
}

// ── Fetch: testimonials ──────────────────────────────────────
try {
    $testimonials = $pdo->query(
        "SELECT t.*, c.city FROM testimonials t
         LEFT JOIN customers c ON c.id = t.customer_id
         WHERE t.is_approved = 1
         ORDER BY t.is_featured DESC, t.sort_order ASC, t.id DESC
         LIMIT 3"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $testimonials = []; }

// ── Helper: WhatsApp number ──────────────────────────────────
$wa_number = '919510360227';

// ── Helper: star renderer ─────────────────────────────────────
function renderStars(int $rating): string {
    $out = '';
    for ($i = 1; $i <= 5; $i++) {
        $out .= $i <= $rating
            ? '<span class="star filled">&#9733;</span>'
            : '<span class="star empty">&#9733;</span>';
    }
    return $out;
}

// ── Helper: category SVG icon ────────────────────────────────
function categoryIcon(string $slug): string {
    $icons = [
        'photo-frames'    => '<path d="M3 3h18v18H3z" rx="2"/><path d="M7 7h10v10H7z" rx="1"/>',
        'photo-magazines' => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>',
        'gift-boxes'      => '<path d="M20 12v10H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>',
        'memory-boxes'    => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>',
        'bouquets'        => '<path d="M12 22V12"/><path d="M12 12C12 7 7 2 7 2s0 5 5 10"/><path d="M12 12c0-5 5-10 5-10s0 5-5 10"/>',
        'crochet-bouquet' => '<path d="M12 22V12"/><path d="M12 12C9 8 6 4 8 2s4 3 4 10"/><path d="M12 12c3-4 6-8 4-10s-4 3-4 10"/><path d="M8 16c-2-1-4-3-3-5s3 1 7 1-5 2-4 4"/>',
        'personalized'    => '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>',
        'corporate-gifts' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
    ];
    return $icons[$slug] ?? $icons['gift-boxes'];
}

// ── Helper: occasion SVG icon ─────────────────────────────────
function occasionIcon(string $slug): string {
    $icons = [
        'anniversary'    => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
        'birthday'       => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
        'valentines-day' => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
        'rakhi'          => '<circle cx="12" cy="12" r="10"/><path d="M8 12h8"/><path d="M12 8l4 4-4 4"/>',
        'mothers-day'    => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        'friendship-day' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'diwali'         => '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>',
        'housewarming'   => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
        'graduation'     => '<circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/>',
        'corporate'      => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
    ];
    return $icons[$slug] ?? $icons['birthday'];
}

// ── Helper: gradient classes ──────────────────────────────────
$cat_gradients  = ['bg-frames','bg-boxes','bg-mags','bg-blooms','bg-personal','bg-corp','bg-memory','bg-crochet'];
$prod_gradients = ['bg-p1','bg-p2','bg-p3','bg-p4','bg-p5','bg-p6','bg-p7','bg-p8'];

// ── Helper: format price ──────────────────────────────────────
function fmtPrice(float $p): string {
    return '₹' . number_format($p, 0, '.', ',');
}

// ── Helper: WhatsApp link for product ────────────────────────
function waLink(array $p, string $number): string {
    $msg = $p['whatsapp_message']
        ?? "Hello Aakar Creatives 🌸\n\nI'm interested in:\n\n*Product:* {product_name}\n*Price:* ₹{price}\n\nPlease share more details. 😊";
    $msg = str_replace(['{product_name}','{price}'], [$p['name'], number_format((float)$p['price'], 0)], $msg);
    return 'https://wa.me/' . $number . '?text=' . rawurlencode($msg);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Aakar Creatives – Gifts That Speak Hearts</title>
<meta name="description" content="Discover handcrafted, personalised gifts for every occasion. Photo frames, crochet bouquets, gift boxes & more – delivered across India with love."/>
<meta name="keywords" content="personalised gifts, crochet bouquet, photo frame, gift box, anniversary gift, birthday gift, Ahmedabad gifts"/>
<meta property="og:title" content="Aakar Creatives – Gifts That Speak Hearts"/>
<meta property="og:description" content="Handpicked gifts for every occasion, made with love."/>
<meta property="og:type" content="website"/>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400;1,600&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="public/css/global.css"/>

<style>
/* ============================================================
   INDEX-PAGE ONLY STYLES  (complements global.css)
   ============================================================ */

/* ── HERO ──────────────────────────────────────────────────── */
.hero{min-height:calc(100vh - 118px);display:grid;grid-template-columns:1fr 1fr;overflow:hidden;position:relative}
.hero-left{background:linear-gradient(145deg,var(--cream) 0%,var(--rose-bg) 100%);display:flex;align-items:center;padding:80px 64px 80px 80px;position:relative;overflow:hidden}
.hero-left::before{content:'';position:absolute;bottom:-80px;left:-80px;width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(212,137,154,.12) 0%,transparent 70%);pointer-events:none}
.hero-content{position:relative;z-index:1;max-width:500px}
.hero-eyebrow-text{font-size:11px;font-weight:700;color:var(--rose);letter-spacing:3px;text-transform:uppercase}
.hero-h1{font-family:var(--ff-body);font-size:clamp(40px,5vw,60px);font-weight:700;color:var(--text-dark);line-height:1.08;letter-spacing:-1.5px;margin-bottom:4px}
.hero-script{font-family:var(--ff-serif);font-style:italic;font-weight:300;font-size:clamp(44px,5.5vw,66px);color:var(--rose);line-height:1.15;display:block;margin-bottom:32px;letter-spacing:-.5px}
.hero-desc{font-size:15.5px;font-weight:500;color:var(--text-mid);line-height:1.75;margin-bottom:44px;max-width:380px}
.hero-ctas{display:flex;align-items:center;gap:20px;flex-wrap:wrap}
.btn-primary{display:inline-flex;align-items:center;gap:10px;background:var(--rose);color:white;font-family:var(--ff-body);font-size:12.5px;font-weight:700;letter-spacing:1.4px;text-transform:uppercase;padding:15px 30px;border-radius:50px;transition:all var(--transition);box-shadow:0 4px 24px rgba(184,92,110,.32);border:none;cursor:pointer;text-decoration:none}
.btn-primary:hover{background:var(--rose-hover);transform:translateY(-2px);box-shadow:0 8px 32px rgba(184,92,110,.42)}
.btn-primary svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round}
.btn-ghost{display:inline-flex;align-items:center;gap:8px;color:var(--text-mid);font-size:13.5px;font-weight:600;transition:color var(--transition);border:none;background:none;cursor:pointer;padding:0;text-decoration:none}
.btn-ghost:hover{color:var(--rose)}
.btn-ghost svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2}
.hero-right{position:relative;overflow:hidden;background:linear-gradient(160deg,#f0d5dc 0%,#dea8b6 40%,#c4849a 100%)}
.hero-right img{width:100%;height:100%;object-fit:cover;display:block}
.hero-right::before{content:'';position:absolute;top:0;left:0;bottom:0;width:100px;background:linear-gradient(to right,var(--cream),transparent);z-index:1;pointer-events:none}

/* ── TRUST BAR ─────────────────────────────────────────────── */
.trust-bar{background:var(--white);border-top:1px solid var(--border-light);border-bottom:1px solid var(--border-light)}
.trust-inner{max-width:var(--max-w);margin:0 auto;padding:0 40px;display:grid;grid-template-columns:repeat(4,1fr)}
.trust-item{display:flex;align-items:center;gap:18px;padding:30px 28px;border-right:1px solid var(--border-light);transition:background var(--transition)}
.trust-item:last-child{border-right:none}
.trust-item:hover{background:var(--rose-bg)}
.trust-icon{width:46px;height:46px;background:var(--rose-pale);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.trust-icon svg{width:20px;height:20px;stroke:var(--rose);fill:none;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round}
.trust-title{font-size:13.5px;font-weight:700;color:var(--text-dark);margin-bottom:2px}
.trust-desc{font-size:12px;font-weight:500;color:var(--text-light);line-height:1.45}

/* ── SECTION HELPERS ───────────────────────────────────────── */
.section{padding:96px 0}
.section-inner{max-width:var(--max-w);margin:0 auto;padding:0 40px}
.section-header{text-align:center;margin-bottom:56px}
.section-tag{font-size:10.5px;font-weight:700;color:var(--rose);letter-spacing:3px;text-transform:uppercase;margin-bottom:14px;display:flex;align-items:center;justify-content:center;gap:12px}
.section-tag::before,.section-tag::after{content:'';width:32px;height:1px;background:var(--gold-light)}
.section-title{font-family:var(--ff-body);font-size:clamp(28px,3.5vw,40px);font-weight:700;color:var(--text-dark);letter-spacing:-.8px;line-height:1.1}
.section-title em{font-family:var(--ff-serif);font-style:italic;font-weight:300;color:var(--rose)}
.section-sub{font-size:15px;font-weight:500;color:var(--text-light);margin-top:12px;max-width:460px;margin-left:auto;margin-right:auto;line-height:1.65}

/* ── CATEGORIES ────────────────────────────────────────────── */
.categories{background:var(--cream)}
.categories-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:18px}
.cat-card{border-radius:var(--radius);overflow:hidden;background:var(--white);border:1px solid var(--border-light);box-shadow:var(--shadow-xs);cursor:pointer;transition:all var(--transition);text-decoration:none;display:flex;flex-direction:column;height:100%}
.cat-card:hover{transform:translateY(-6px);box-shadow:var(--shadow-md);border-color:var(--rose-light)}
.cat-img-wrap{height:150px;overflow:hidden}
.cat-img-inner{width:100%;height:100%;display:flex;align-items:center;justify-content:center;transition:transform .5s ease}
.cat-card:hover .cat-img-inner{transform:scale(1.06)}
.cat-img-inner img{width:100%;height:100%;object-fit:cover}
.cat-img-inner svg{width:48px;height:48px;stroke:currentColor;fill:none;stroke-width:1.5;stroke-linecap:round;stroke-linejoin:round;opacity:.7}
.cat-info{padding:14px 14px 16px;text-align:center;flex-grow:1;display:flex;flex-direction:column;justify-content:center}
.cat-name{font-size:13px;font-weight:700;color:var(--text-dark);margin-bottom:2px}
.cat-sub{font-size:11px;font-weight:500;color:var(--text-light)}
.bg-frames{background:linear-gradient(150deg,#f0e8dc 0%,#deccb4 100%);color:#8c6440}
.bg-boxes{background:linear-gradient(150deg,#fce8ed 0%,#e8c4cf 100%);color:#a84060}
.bg-mags{background:linear-gradient(150deg,#ece8e0 0%,#d4cfc2 100%);color:#706860}
.bg-blooms{background:linear-gradient(150deg,#fae8e8 0%,#e0b0b0 100%);color:#a05050}
.bg-personal{background:linear-gradient(150deg,#e8f0e8 0%,#b8ccb8 100%);color:#508050}
.bg-corp{background:linear-gradient(150deg,#e8e4f0 0%,#c4b8d4 100%);color:#6050a0}
.bg-memory{background:linear-gradient(150deg,#e8edf0 0%,#b8c8d4 100%);color:#406080}
.bg-crochet{background:linear-gradient(150deg,#f8e8f0 0%,#d8b0c8 100%);color:#904070}

/* ── PRODUCTS ──────────────────────────────────────────────── */
.bestsellers{background:var(--white)}
.products-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:22px}
.product-card{border-radius:var(--radius);background:var(--white);border:1px solid var(--border-light);overflow:hidden;transition:all var(--transition);box-shadow:var(--shadow-xs);display:flex;flex-direction:column;height:100%}
.product-card:hover{transform:translateY(-6px);box-shadow:var(--shadow-md);border-color:var(--rose-light)}
.product-img-wrap{position:relative;height:230px;overflow:hidden;cursor:pointer}
.product-img-inner{width:100%;height:100%;display:flex;align-items:center;justify-content:center;transition:transform .5s ease}
.product-card:hover .product-img-inner{transform:scale(1.05)}
.product-img-inner img{width:100%;height:100%;object-fit:cover}
.product-img-inner svg{width:64px;height:64px;stroke:currentColor;fill:none;stroke-width:1.3;stroke-linecap:round;stroke-linejoin:round;opacity:.6}
.product-badge{position:absolute;top:13px;left:13px;color:white;font-size:9.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:4px 11px;border-radius:20px;z-index:2}
.product-out-of-stock{position:absolute;inset:0;background:rgba(255,255,255,.7);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--text-mid);letter-spacing:1px;text-transform:uppercase;z-index:3}
.product-info{padding:18px 20px 20px;flex-grow:1;display:flex;flex-direction:column;justify-content:space-between}
.product-name{font-size:14.5px;font-weight:700;color:var(--text-dark);margin-bottom:6px;cursor:pointer;line-height:1.4}
.product-desc{font-size:12px;font-weight:500;color:var(--text-light);margin-bottom:14px;line-height:1.5;flex-grow:1}
.product-footer{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.price-group{display:flex;align-items:baseline;gap:8px}
.product-price{font-size:17px;font-weight:700;color:var(--rose)}
.product-original-price{font-size:13px;font-weight:500;color:var(--text-light);text-decoration:line-through}
.product-stars{display:flex;align-items:center;gap:2px}
.star{font-size:12px}
.star.filled{color:var(--gold)}
.star.empty{color:var(--border)}
.wa-btn{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:11px;background:#25d366;color:white;border-radius:10px;font-size:12.5px;font-weight:700;letter-spacing:.5px;transition:all var(--transition);border:none;cursor:pointer;text-decoration:none}
.wa-btn:hover{background:#1ebe5d;transform:translateY(-1px);box-shadow:0 4px 16px rgba(37,211,102,.3)}
.wa-btn svg{width:16px;height:16px;fill:white;flex-shrink:0}
.bg-p1{background:linear-gradient(150deg,#f4e8d8 0%,#dcc8a8 100%);color:#8c6440}
.bg-p2{background:linear-gradient(150deg,#f6e8d0 0%,#d8b87a 100%);color:#a07830}
.bg-p3{background:linear-gradient(150deg,#fce8e8 0%,#e4a8a8 100%);color:#a04040}
.bg-p4{background:linear-gradient(150deg,#e8ecf6 0%,#c0c8dc 100%);color:#4060a0}
.bg-p5{background:linear-gradient(150deg,#eef0e8 0%,#c4ccb0 100%);color:#607040}
.bg-p6{background:linear-gradient(150deg,#f8eae4 0%,#dcc0b4 100%);color:#805040}
.bg-p7{background:linear-gradient(150deg,#e8f0f8 0%,#b8cce0 100%);color:#406080}
.bg-p8{background:linear-gradient(150deg,#f0e8f4 0%,#ccb8d8 100%);color:#6040a0}

/* ── PRODUCT TYPE LABEL (Bestseller / New Arrival) ─────────── */
.product-type-label{display:inline-flex;align-items:center;gap:5px;font-size:10px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;margin-bottom:6px}
.label-best{color:var(--gold)}
.label-new{color:#4a9e6e}

/* ── PERSONALIZE BANNER ────────────────────────────────────── */
.personalize-section{background:var(--rose-pale);overflow:hidden}
.personalize-inner{max-width:var(--max-w);margin:0 auto;display:grid;grid-template-columns:1fr 1fr;min-height:520px}
.personalize-content{padding:80px 64px 80px 80px;display:flex;flex-direction:column;justify-content:center}
.personalize-tag{font-size:10.5px;font-weight:700;color:var(--rose);letter-spacing:3px;text-transform:uppercase;margin-bottom:20px;display:flex;align-items:center;gap:12px}
.personalize-tag::before{content:'';width:28px;height:1px;background:var(--gold)}
.personalize-h{font-family:var(--ff-body);font-size:clamp(32px,3.5vw,46px);font-weight:700;color:var(--text-dark);line-height:1.1;letter-spacing:-1px;margin-bottom:6px}
.personalize-script{font-family:var(--ff-serif);font-style:italic;font-weight:300;font-size:clamp(36px,4vw,52px);color:var(--rose);display:block;line-height:1.2;margin-bottom:28px}
.personalize-desc{font-size:15px;font-weight:500;color:var(--text-mid);line-height:1.75;max-width:380px;margin-bottom:40px}
.personalize-steps{display:flex;gap:24px;margin-bottom:40px;flex-wrap:wrap}
.pstep{display:flex;align-items:center;gap:10px}
.pstep-num{width:28px;height:28px;border-radius:50%;background:var(--rose);color:white;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.pstep-text{font-size:13px;font-weight:600;color:var(--text-mid)}
.personalize-img-wrap{position:relative;overflow:hidden}
.personalize-img-inner{width:100%;height:100%;background:linear-gradient(160deg,#d4a0ac 0%,#b87880 50%,#9c6070 100%);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;min-height:380px;position:relative}
.personalize-img-inner img{width:100%;height:100%;object-fit:cover;position:absolute;inset:0}
.personalize-img-inner > *:not(img){position:relative;z-index:1}

/* ── OCCASIONS ─────────────────────────────────────────────── */
.occasions-section{background:var(--cream-deep)}
.occasions-grid-home{display:grid;grid-template-columns:repeat(5,1fr);gap:16px}
.occ-card{border-radius:var(--radius);border:1px solid var(--border-light);background:var(--white);padding:28px 20px;text-align:center;cursor:pointer;transition:all var(--transition);box-shadow:var(--shadow-xs);text-decoration:none;display:block}
.occ-card:hover{transform:translateY(-5px);box-shadow:var(--shadow-md);border-color:var(--rose-light);background:var(--rose-bg)}
.occ-icon{width:52px;height:52px;border-radius:14px;background:var(--rose-pale);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;transition:background var(--transition)}
.occ-card:hover .occ-icon{background:var(--rose)}
.occ-icon svg{width:24px;height:24px;stroke:var(--rose);fill:none;stroke-width:1.6;stroke-linecap:round;stroke-linejoin:round;transition:stroke var(--transition)}
.occ-card:hover .occ-icon svg{stroke:white}
.occ-name{font-size:14px;font-weight:700;color:var(--text-dark);margin-bottom:3px}
.occ-count{font-size:11.5px;font-weight:500;color:var(--text-light)}

/* ── TESTIMONIALS ──────────────────────────────────────────── */
.testimonials{background:var(--white)}
.testimonials-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
.testi-card{border-radius:var(--radius);background:var(--cream);border:1px solid var(--border-light);padding:32px 28px;transition:all var(--transition)}
.testi-card:hover{border-color:var(--rose-light);box-shadow:var(--shadow-sm);transform:translateY(-3px)}
.testi-stars{display:flex;gap:3px;margin-bottom:18px}
.testi-quote{font-size:15px;font-weight:500;color:var(--text-mid);line-height:1.75;margin-bottom:22px;font-style:italic;font-family:var(--ff-serif)}
.testi-author{display:flex;align-items:center;gap:12px}
.testi-avatar{width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--rose-pale),var(--rose-light));display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;color:var(--rose);flex-shrink:0}
.testi-name{font-size:13.5px;font-weight:700;color:var(--text-dark)}
.testi-loc{font-size:11.5px;font-weight:500;color:var(--text-light)}
.testi-handle{font-size:11px;font-weight:600;color:var(--rose);margin-top:1px}

/* ── NEWSLETTER ────────────────────────────────────────────── */
.newsletter{background:var(--text-dark);padding:72px 40px;text-align:center;position:relative;overflow:hidden}
.newsletter::before{content:'';position:absolute;top:-120px;left:50%;transform:translateX(-50%);width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,rgba(184,92,110,.15) 0%,transparent 70%);pointer-events:none}
.newsletter-inner{max-width:520px;margin:0 auto;position:relative}
.newsletter-tag{font-size:10px;font-weight:700;color:var(--rose-light);letter-spacing:3px;text-transform:uppercase;margin-bottom:16px;display:flex;align-items:center;justify-content:center;gap:12px}
.newsletter-tag::before,.newsletter-tag::after{content:'';width:24px;height:1px;background:rgba(212,137,154,.4)}
.newsletter-title{font-size:clamp(26px,3vw,36px);font-weight:700;color:var(--white);margin-bottom:10px;letter-spacing:-.5px}
.newsletter-sub{font-size:14.5px;font-weight:500;color:rgba(255,255,255,.55);margin-bottom:36px;line-height:1.65}
.newsletter-form{display:flex;border-radius:50px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.3)}
.newsletter-input{flex:1;padding:16px 26px;border:none;font-family:var(--ff-body);font-size:14px;font-weight:500;color:var(--text-dark);outline:none;background:rgba(255,255,255,.97);min-width:0}
.newsletter-input::placeholder{color:var(--text-light)}
.newsletter-btn{padding:16px 28px;background:var(--rose);color:white;font-family:var(--ff-body);font-size:12.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;border:none;cursor:pointer;white-space:nowrap;transition:background var(--transition)}
.newsletter-btn:hover{background:var(--rose-hover)}
.newsletter-msg{margin-top:14px;font-size:13px;font-weight:600;display:none;padding:10px 16px;border-radius:8px}
.newsletter-msg.success{display:block;background:rgba(76,175,125,.15);color:#4caf7d}
.newsletter-msg.error{display:block;background:rgba(220,80,80,.1);color:#dc5050}

/* ── FLOATING WA BUTTON ────────────────────────────────────── */
.wa-float{position:fixed;bottom:28px;right:28px;z-index:100;width:56px;height:56px;background:#25d366;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(37,211,102,.4);transition:all var(--transition);text-decoration:none}
.wa-float:hover{transform:scale(1.1);box-shadow:0 8px 32px rgba(37,211,102,.5)}
.wa-float svg{width:28px;height:28px;fill:white}
.wa-float-pulse{position:absolute;inset:0;border-radius:50%;background:#25d366;animation:waPulse 2s ease-out infinite;z-index:-1}
@keyframes waPulse{0%{transform:scale(1);opacity:.7}100%{transform:scale(1.8);opacity:0}}

/* ── EMPTY STATE ───────────────────────────────────────────── */
.empty-state{grid-column:1/-1;text-align:center;padding:48px 20px;color:var(--text-light);font-size:14px;font-weight:500}

/* ── ANIMATIONS ────────────────────────────────────────────── */
@keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.animate{animation:fadeUp .6s ease both}
.delay-1{animation-delay:.1s}
.delay-2{animation-delay:.2s}
.delay-3{animation-delay:.3s}
.delay-4{animation-delay:.4s}
.reveal{opacity:0;transform:translateY(24px);transition:opacity .72s cubic-bezier(.4,0,.2,1),transform .72s cubic-bezier(.4,0,.2,1)}
.reveal.visible{opacity:1;transform:translateY(0)}

/* ── RESPONSIVE ────────────────────────────────────────────── */
@media(max-width:1100px){
  .categories-grid{grid-template-columns:repeat(3,1fr)}
  .products-grid{grid-template-columns:repeat(2,1fr);gap:20px}
  .occasions-grid-home{grid-template-columns:repeat(3,1fr)}
}
@media(max-width:768px){
  .hero{grid-template-columns:1fr;min-height:auto}
  .hero-left{padding:56px 24px 48px;min-height:520px}
  .hero-right{height:280px}
  .trust-inner{grid-template-columns:repeat(2,1fr);padding:10px 0}
  .trust-item{flex-direction:column;text-align:center;justify-content:center;padding:24px 16px;border-right:1px solid var(--border-light);border-bottom:1px solid var(--border-light);gap:10px}
  .trust-item:nth-child(2n){border-right:none}
  .trust-item:nth-last-child(-n+2){border-bottom:none}
  .section{padding:64px 0}
  .section-inner{padding:0 20px}
  .categories-grid{grid-template-columns:repeat(2,1fr);gap:14px}
  .products-grid{grid-template-columns:repeat(2,1fr);gap:16px}
  .product-img-wrap{height:180px}
  .personalize-inner{grid-template-columns:1fr}
  .personalize-content{padding:56px 24px}
  .personalize-img-wrap{height:260px}
  .occasions-grid-home{grid-template-columns:repeat(2,1fr)}
  .testimonials-grid{grid-template-columns:1fr}
  .newsletter{padding:56px 24px}
  .newsletter-form{flex-direction:column;border-radius:12px}
  .newsletter-input{border-radius:10px 10px 0 0;padding:15px 20px}
  .newsletter-btn{border-radius:0 0 10px 10px;padding:15px 20px}
}
@media(max-width:480px){
  .trust-inner{grid-template-columns:1fr}
  .trust-item{border-right:none!important;border-bottom:1px solid var(--border-light)!important}
  .trust-item:last-child{border-bottom:none!important}
  .products-grid{grid-template-columns:1fr}
  .hero-left{padding:48px 20px 44px}
  .hero-ctas{flex-direction:column;align-items:flex-start}
}
</style>
</head>
<body>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<!-- ═══════════════════════════════════════════════
     HERO
════════════════════════════════════════════════ -->
<section class="hero" aria-label="Hero">
  <div class="hero-left">
    <div class="hero-content">
      <div class="hero-eyebrow animate">
        <span class="hero-eyebrow-text">Handcrafted with Love · 2026</span>
      </div>
      <h1 class="hero-h1 animate delay-1">Thoughtful Gifts,</h1>
      <span class="hero-script animate delay-2">Beautiful Memories.</span>
      <p class="hero-desc animate delay-3">
        Discover handcrafted crochet bouquets, personalised frames, and curated gift boxes for every occasion — made to surprise, chosen to be cherished.
      </p>
      <div class="hero-ctas animate delay-4">
        <a href="shop.php" class="btn-primary">
          Explore Collection
          <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
        <a href="about.php" class="btn-ghost">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
          Our Story
        </a>
      </div>
    </div>
  </div>
  <div class="hero-right" aria-hidden="true">
    <img src="public/images/hero-banner.png" alt="Beautiful handcrafted gifts from Aakar Creatives" loading="eager" onerror="this.style.display='none'">
  </div>
</section>

<!-- ═══════════════════════════════════════════════
     TRUST BAR
════════════════════════════════════════════════ -->
<div class="trust-bar" role="list" aria-label="Trust signals">
  <div class="trust-inner">
    <div class="trust-item reveal" role="listitem">
      <div class="trust-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24"><path d="M20 12v10H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
      </div>
      <div>
        <div class="trust-title">Curated With Love</div>
        <div class="trust-desc">Handpicked for every occasion</div>
      </div>
    </div>
    <div class="trust-item reveal" role="listitem">
      <div class="trust-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
      </div>
      <div>
        <div class="trust-title">Premium Quality</div>
        <div class="trust-desc">Finest materials, beautiful craft</div>
      </div>
    </div>
    <div class="trust-item reveal" role="listitem">
      <div class="trust-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
      </div>
      <div>
        <div class="trust-title">Fast Delivery</div>
        <div class="trust-desc">3–5 working days, pan India</div>
      </div>
    </div>
    <div class="trust-item reveal" role="listitem">
      <div class="trust-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
      </div>
      <div>
        <div class="trust-title">WhatsApp Support</div>
        <div class="trust-desc">Friendly help anytime</div>
      </div>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════
     CATEGORIES
════════════════════════════════════════════════ -->
<section class="section categories" aria-labelledby="cats-heading">
  <div class="section-inner">
    <div class="section-header reveal">
      <div class="section-tag">Browse</div>
      <h2 class="section-title" id="cats-heading">Shop by <em>Category</em></h2>
      <p class="section-sub">From heartfelt crochet bouquets to personalised frames — find the perfect gift.</p>
    </div>
    <div class="categories-grid">
      <?php if (empty($cats)): ?>
        <div class="empty-state">Categories coming soon.</div>
      <?php else: ?>
        <?php foreach ($cats as $i => $cat):
            $grad = $cat_gradients[$i % count($cat_gradients)];
            $icon = categoryIcon($cat['slug']);
        ?>
        <a href="shop.php?category=<?= urlencode($cat['slug']) ?>"
           class="cat-card reveal"
           style="transition-delay:<?= ($i * 0.07) ?>s"
           aria-label="Browse <?= htmlspecialchars($cat['name']) ?>">
          <div class="cat-img-wrap">
            <div class="cat-img-inner <?= $grad ?>">
              <?php if (!empty($cat['image_url'])): ?>
                <img src="<?= htmlspecialchars($cat['image_url']) ?>"
                     alt="<?= htmlspecialchars($cat['name']) ?>"
                     loading="lazy">
              <?php else: ?>
                <svg viewBox="0 0 24 24"><?= $icon ?></svg>
              <?php endif; ?>
            </div>
          </div>
          <div class="cat-info">
            <div class="cat-name"><?= htmlspecialchars($cat['name']) ?></div>
            <div class="cat-sub"><?= htmlspecialchars($cat['description'] ?? '') ?></div>
          </div>
        </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════
     FEATURED PRODUCTS (1 bestseller + 1 new per category)
════════════════════════════════════════════════ -->
<section class="section bestsellers" aria-labelledby="bs-heading">
  <div class="section-inner">
    <div class="section-header reveal">
      <div class="section-tag">Handpicked</div>
      <h2 class="section-title" id="bs-heading">Our <em>Best Picks</em></h2>
      <p class="section-sub">One bestseller &amp; one new arrival from each category — chosen with love.</p>
    </div>
    <div class="products-grid">
      <?php if (empty($products)): ?>
        <div class="empty-state">Products coming soon — check back shortly!</div>
      <?php else: ?>
        <?php foreach ($products as $i => $p):
            $grad   = $prod_gradients[$i % count($prod_gradients)];
            $waHref = waLink($p, $wa_number);
            $hasImg = !empty($p['primary_image']);
            $hasDis = !empty($p['discount_price']) && (float)$p['discount_price'] > (float)$p['price'];

            // Determine label type
            $label = '';
            if ($p['is_bestseller']) {
                $label = '<span class="product-type-label label-best">⭐ Bestseller</span>';
            } elseif ($p['is_new_arrival']) {
                $label = '<span class="product-type-label label-new">✦ New Arrival</span>';
            } elseif ($p['is_trending']) {
                $label = '<span class="product-type-label" style="color:var(--rose)">🔥 Trending</span>';
            }

            // Badge color
            $badge_color = htmlspecialchars($p['badge_color'] ?? '#b85c6e');

            // Category icon for placeholder
            $cat_slug_for_icon = '';
            foreach ($cats as $c) {
                if ((int)$c['id'] === (int)$p['category_id']) {
                    $cat_slug_for_icon = $c['slug'];
                    break;
                }
            }
        ?>
        <article class="product-card reveal"
                 style="transition-delay:<?= ($i % 4) * 0.08 ?>s"
                 aria-label="<?= htmlspecialchars($p['name']) ?>">

          <!-- Image -->
          <div class="product-img-wrap"
               onclick="trackView(<?= (int)$p['id'] ?>); window.location='shop.php?product=<?= urlencode($p['slug']) ?>'">
            <div class="product-img-inner <?= $grad ?>">
              <?php if ($hasImg): ?>
                <img src="<?= htmlspecialchars($p['primary_image']) ?>"
                     alt="<?= htmlspecialchars($p['name']) ?>"
                     loading="lazy">
              <?php else: ?>
                <svg viewBox="0 0 24 24"><?= categoryIcon($cat_slug_for_icon ?: 'gift-boxes') ?></svg>
              <?php endif; ?>
            </div>

            <?php if (!$p['in_stock']): ?>
              <div class="product-out-of-stock">Out of Stock</div>
            <?php endif; ?>

            <?php if (!empty($p['badge_name'])): ?>
              <span class="product-badge" style="background:<?= $badge_color ?>">
                <?= htmlspecialchars($p['badge_name']) ?>
              </span>
            <?php elseif ($p['is_new_arrival']): ?>
              <span class="product-badge" style="background:#4a9e6e">New</span>
            <?php elseif ($p['is_trending']): ?>
              <span class="product-badge" style="background:#c9a96e">Trending</span>
            <?php endif; ?>
          </div>

          <!-- Info -->
          <div class="product-info">
            <div>
              <?= $label ?>
              <div class="product-name"
                   onclick="window.location='shop.php?product=<?= urlencode($p['slug']) ?>'">
                <?= htmlspecialchars($p['name']) ?>
              </div>
              <?php if (!empty($p['short_description'])): ?>
                <div class="product-desc"><?= htmlspecialchars($p['short_description']) ?></div>
              <?php endif; ?>
            </div>

            <div>
              <div class="product-footer">
                <div class="price-group">
                  <div class="product-price"><?= fmtPrice((float)$p['price']) ?></div>
                  <?php if ($hasDis): ?>
                    <div class="product-original-price"><?= fmtPrice((float)$p['discount_price']) ?></div>
                  <?php endif; ?>
                </div>
                <div class="product-stars" aria-label="5 out of 5 stars">
                  <?= renderStars(5) ?>
                </div>
              </div>

              <?php if ($p['in_stock']): ?>
                <a href="<?= htmlspecialchars($waHref) ?>"
                   class="wa-btn"
                   target="_blank"
                   rel="noopener"
                   onclick="trackWa(<?= (int)$p['id'] ?>)"
                   aria-label="Enquire about <?= htmlspecialchars($p['name']) ?> on WhatsApp">
                  <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.062.522 4.002 1.44 5.698L0 24l6.435-1.418A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.887 0-3.656-.497-5.193-1.366l-.372-.22-3.821.842.863-3.716-.242-.383A9.975 9.975 0 0 1 2 12c0-5.514 4.486-10 10-10s10 4.486 10 10-4.486 10-10 10z"/>
                  </svg>
                  Enquire on WhatsApp
                </a>
              <?php else: ?>
                <button class="wa-btn" style="background:#aaa;cursor:not-allowed" disabled>Out of Stock</button>
              <?php endif; ?>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <?php if (!empty($products)): ?>
    <div style="text-align:center;margin-top:48px">
      <a href="shop.php" class="btn-primary" style="margin:0 auto">
        View All Gifts
        <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </a>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- ═══════════════════════════════════════════════
     PERSONALISE BANNER
════════════════════════════════════════════════ -->
<section class="personalize-section" aria-labelledby="pers-heading">
  <div class="personalize-inner">
    <div class="personalize-content reveal">
      <div class="personalize-tag">Customise</div>
      <h2 class="personalize-h" id="pers-heading">Make It Personal,</h2>
      <span class="personalize-script">Make It Perfect.</span>
      <p class="personalize-desc">
        Add names, dates, photos, or heartfelt messages to transform any gift into an unforgettable keepsake.
      </p>
      <div class="personalize-steps">
        <div class="pstep"><div class="pstep-num">1</div><div class="pstep-text">Choose Gift</div></div>
        <div class="pstep"><div class="pstep-num">2</div><div class="pstep-text">Personalise</div></div>
        <div class="pstep"><div class="pstep-num">3</div><div class="pstep-text">Delivered</div></div>
      </div>
      <a href="https://wa.me/<?= htmlspecialchars($wa_number) ?>?text=<?= rawurlencode("Hello Aakar Creatives 🌸\n\nI'd love to create a personalised gift! Please guide me.") ?>"
         class="btn-primary"
         target="_blank"
         rel="noopener"
         style="align-self:flex-start">
        Create Your Gift
        <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </a>
    </div>
    <div class="personalize-img-wrap" aria-hidden="true">
      <div class="personalize-img-inner">
        <img src="public/images/personalize-banner.png" alt="Personalised gift creation" loading="lazy" onerror="this.style.display='none'">
        <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,1)" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
        </svg>
        <span style="color:white;font-size:14px;font-weight:600;letter-spacing:.5px">Personalisation Preview</span>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════
     OCCASIONS
════════════════════════════════════════════════ -->
<section class="section occasions-section" aria-labelledby="occ-heading">
  <div class="section-inner">
    <div class="section-header reveal">
      <div class="section-tag">For Every Moment</div>
      <h2 class="section-title" id="occ-heading">Shop by <em>Occasion</em></h2>
      <p class="section-sub">Whatever the celebration, we have a gift made just for it.</p>
    </div>
    <div class="occasions-grid-home">
      <?php
      // Use static fallback if no DB occasions
      $occ_list = !empty($occasions) ? $occasions : [
          ['name'=>'Anniversary',     'slug'=>'anniversary',    'gift_count'=>48],
          ['name'=>'Birthday',        'slug'=>'birthday',       'gift_count'=>64],
          ['name'=>"Valentine's Day", 'slug'=>'valentines-day', 'gift_count'=>36],
          ['name'=>'Rakhi',           'slug'=>'rakhi',          'gift_count'=>24],
          ['name'=>"Mother's Day",    'slug'=>'mothers-day',    'gift_count'=>30],
      ];
      foreach ($occ_list as $i => $o):
      ?>
        <a href="occasions.php?occasion=<?= urlencode($o['slug']) ?>"
           class="occ-card reveal"
           style="transition-delay:<?= ($i * 0.07) ?>s"
           aria-label="Shop <?= htmlspecialchars($o['name']) ?> gifts">
          <div class="occ-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><?= occasionIcon($o['slug']) ?></svg>
          </div>
          <div class="occ-name"><?= htmlspecialchars($o['name']) ?></div>
          <div class="occ-count">
            <?= ($o['gift_count'] ?? 0) > 0 ? (int)$o['gift_count'] . ' gifts' : 'Explore gifts' ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════
     TESTIMONIALS
════════════════════════════════════════════════ -->
<section class="section testimonials" aria-labelledby="testi-heading">
  <div class="section-inner">
    <div class="section-header reveal">
      <div class="section-tag">Reviews</div>
      <h2 class="section-title" id="testi-heading">What Our Customers <em>Say</em></h2>
      <p class="section-sub">Real stories from people who found the perfect gift right here.</p>
    </div>
    <div class="testimonials-grid">
      <?php
      // Static fallback
      $testi_list = !empty($testimonials) ? $testimonials : [
          ['name'=>'Priya Malhotra', 'instagram'=>'@priya.m',       'review'=>'Absolutely beautiful! The quality exceeded my expectations. My boyfriend loved the crochet bouquet so much!', 'rating'=>5, 'city'=>'Mumbai'],
          ['name'=>'Ravi Kumar',     'instagram'=>'@ravi_captures', 'review'=>'The magazine was so emotional, we both cried! 100% worth every rupee. Will definitely order again.',          'rating'=>5, 'city'=>'Delhi'],
          ['name'=>'Ananya Singh',   'instagram'=>'@ananya.creates','review'=>'So cute and well-made. The flowers look so real! Great packaging too. Highly recommended.',                   'rating'=>5, 'city'=>'Ahmedabad'],
      ];
      foreach ($testi_list as $i => $t):
      ?>
        <div class="testi-card reveal" style="transition-delay:<?= ($i * 0.1) ?>s">
          <div class="testi-stars" aria-label="Rating: <?= $t['rating'] ?> out of 5">
            <?= renderStars((int)$t['rating']) ?>
          </div>
          <p class="testi-quote">"<?= htmlspecialchars($t['review']) ?>"</p>
          <div class="testi-author">
            <div class="testi-avatar" aria-hidden="true"><?= mb_strtoupper(mb_substr($t['name'], 0, 1)) ?></div>
            <div>
              <div class="testi-name"><?= htmlspecialchars($t['name']) ?></div>
              <?php if (!empty($t['city'])): ?>
                <div class="testi-loc"><?= htmlspecialchars($t['city']) ?></div>
              <?php endif; ?>
              <?php if (!empty($t['instagram'])): ?>
                <div class="testi-handle"><?= htmlspecialchars($t['instagram']) ?></div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════
     NEWSLETTER
════════════════════════════════════════════════ -->
<section class="newsletter" aria-labelledby="nl-heading">
  <div class="newsletter-inner">
    <div class="newsletter-tag">Stay Connected</div>
    <h3 class="newsletter-title" id="nl-heading">Stay in the Loop</h3>
    <p class="newsletter-sub">Get exclusive offers, new arrivals, and gifting inspiration straight to your inbox.</p>
    <div class="newsletter-form" role="form" aria-label="Newsletter subscription">
      <input class="newsletter-input" type="email" id="nlEmail"
             placeholder="Enter your email address" autocomplete="email" required
             aria-label="Email address"/>
      <button class="newsletter-btn" id="nlBtn" type="button" aria-label="Subscribe to newsletter">
        Subscribe
      </button>
    </div>
    <div class="newsletter-msg" id="nlMsg" role="alert" aria-live="polite"></div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

<!-- ═══════════════════════════════════════════════
     FLOATING WHATSAPP
════════════════════════════════════════════════ -->
<a href="https://wa.me/<?= htmlspecialchars($wa_number) ?>?text=<?= rawurlencode("Hello Aakar Creatives 🌸\n\nI'd love to explore your gift collection!") ?>"
   class="wa-float"
   target="_blank"
   rel="noopener"
   aria-label="Chat with us on WhatsApp">
  <div class="wa-float-pulse" aria-hidden="true"></div>
  <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.062.522 4.002 1.44 5.698L0 24l6.435-1.418A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.887 0-3.656-.497-5.193-1.366l-.372-.22-3.821.842.863-3.716-.242-.383A9.975 9.975 0 0 1 2 12c0-5.514 4.486-10 10-10s10 4.486 10 10-4.486 10-10 10z"/>
  </svg>
</a>

<script>
'use strict';

/* ── Scroll reveal ─────────────────────────────────────────── */
var revealEls = document.querySelectorAll('.reveal, .animate');
var ro = new IntersectionObserver(function(entries){
  entries.forEach(function(e){
    if(e.isIntersecting){
      e.target.classList.add('visible');
      if(e.target.classList.contains('animate')) e.target.style.animationPlayState = 'running';
      ro.unobserve(e.target);
    }
  });
}, { threshold: 0.12 });

revealEls.forEach(function(el){
  if(el.classList.contains('animate')) el.style.animationPlayState = 'paused';
  ro.observe(el);
});

/* ── Analytics tracking ────────────────────────────────────── */
function trackView(productId){
  if(!productId) return;
  fetch('track.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'type=product_view&product_id='+productId,keepalive:true}).catch(function(){});
}
function trackWa(productId){
  if(!productId) return;
  fetch('track.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'type=whatsapp_click&product_id='+productId,keepalive:true}).catch(function(){});
}

/* ── Newsletter AJAX ───────────────────────────────────────── */
var nlBtn   = document.getElementById('nlBtn');
var nlEmail = document.getElementById('nlEmail');
var nlMsg   = document.getElementById('nlMsg');

function showNlMsg(text, type){
  nlMsg.textContent = text;
  nlMsg.className   = 'newsletter-msg ' + type;
  setTimeout(function(){ nlMsg.style.display='none'; nlMsg.className='newsletter-msg'; }, 5000);
}

if(nlBtn){
  nlBtn.addEventListener('click', async function(){
    var email = nlEmail ? nlEmail.value.trim() : '';
    if(!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){
      showNlMsg('Please enter a valid email address.','error');
      return;
    }
    nlBtn.disabled = true;
    nlBtn.textContent = 'Subscribing…';
    try{
      var fd = new FormData();
      fd.append('newsletter_email', email);
      var res  = await fetch('index.php',{method:'POST',body:fd});
      var data = await res.json();
      showNlMsg(data.msg, data.ok ? 'success' : 'error');
      if(data.ok && nlEmail) nlEmail.value = '';
    } catch(err){
      showNlMsg('Something went wrong. Please try again.','error');
    } finally {
      nlBtn.disabled = false;
      nlBtn.textContent = 'Subscribe';
    }
  });
}

if(nlEmail){
  nlEmail.addEventListener('keydown', function(e){ if(e.key==='Enter' && nlBtn) nlBtn.click(); });
}
</script>
</body>
</html>