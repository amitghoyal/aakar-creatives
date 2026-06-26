<?php
/**
 * includes/header.php — Aakar Creatives
 * Production-ready shared header.
 * Fixes: desktop search fully functional, add-to-cart from search cards,
 *        product detail page links, all panels wired correctly.
 * Requires: $pdo set before include.
 */

$current_page = basename($_SERVER['PHP_SELF']);

/* ── WhatsApp number ─────────────────────────────────────── */
try {
    $wa_stmt   = $pdo->query("SELECT phone_number FROM whatsapp_settings WHERE is_primary = 1 LIMIT 1");
    $wa_row    = $wa_stmt->fetch(PDO::FETCH_ASSOC);
    $wa_number = $wa_row ? preg_replace('/\D/', '', $wa_row['phone_number']) : '919510360227';
} catch (PDOException $e) {
    $wa_number = '919510360227';
}

/* ── Active nav helper ───────────────────────────────────── */
function navActive(string $page, string $current): string {
    return $page === $current ? ' class="active"' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Aakar Creatives – Gifts That Speak Hearts</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400;1,600&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="public/css/global.css"/>

<style>
/* ═══════════════════════════════════════════════════
   CSS CUSTOM PROPERTIES
═══════════════════════════════════════════════════ */
:root {
  --rose:         #b85c6e;
  --rose-hover:   #a3505f;
  --rose-light:   #d4849a;
  --rose-bg:      #fdf0f3;
  --rose-pale:    #fdf0f3;
  --gold:         #c9a96e;
  --cream:        #fdf8f5;
  --cream-deep:   #f5ece8;
  --white:        #ffffff;
  --text-dark:    #1e1519;
  --text-mid:     #5a3d44;
  --text-light:   #a0828a;
  --border:       #e8d8dc;
  --border-light: #f0e8ea;
  --shadow-xs:    0 1px 4px rgba(30,21,25,.06);
  --shadow-sm:    0 2px 8px rgba(30,21,25,.08);
  --shadow-md:    0 6px 24px rgba(30,21,25,.12);
  --shadow-lg:    0 12px 40px rgba(30,21,25,.18);
  --radius:       14px;
  --max-w:        1320px;
  --transition:   .18s ease;
  --ff-body:      'Quicksand', sans-serif;
  --ff-serif:     'Cormorant Garamond', serif;
  --header-h:     76px;
}
*, *::before, *::after { box-sizing: border-box; }
body { font-family: var(--ff-body); margin: 0; background: var(--cream); color: var(--text-dark); }

/* ═══════════════════════════════════════════════════
   ANNOUNCEMENT BAR
═══════════════════════════════════════════════════ */
.ann-bar {
  background: var(--text-dark);
  color: rgba(255,255,255,.88);
  display: flex; align-items: center; justify-content: center;
  gap: 40px; padding: 10px 24px;
  font-size: 11px; font-weight: 600; letter-spacing: .9px; text-transform: uppercase; overflow: hidden;
}
.ann-item { display: flex; align-items: center; gap: 8px; white-space: nowrap; }
.ann-dot  { width: 5px; height: 5px; border-radius: 50%; background: var(--rose-light); flex-shrink: 0; }
@media (max-width: 768px) {
  .ann-bar { gap: 0; font-size: 10px; padding: 9px 16px; }
  .ann-item:not(:first-child) { display: none; }
}

/* ═══════════════════════════════════════════════════
   HEADER
═══════════════════════════════════════════════════ */
.header {
  background: rgba(255,255,255,.97);
  backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
  border-bottom: 1px solid var(--border-light);
  position: sticky; top: 0; z-index: 200;
  transition: box-shadow var(--transition);
}
.header.scrolled { box-shadow: var(--shadow-sm); }
.header-inner {
  max-width: var(--max-w); margin: 0 auto;
  padding: 0 40px; height: var(--header-h);
  display: grid; grid-template-columns: 1fr auto 1fr;
  align-items: center; gap: 20px;
}

/* Logo */
.logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
.logo-mark {
  width: 44px; height: 44px; flex-shrink: 0;
  background: linear-gradient(140deg, var(--rose) 0%, #8b3a4c 100%);
  border-radius: 12px; display: flex; align-items: center; justify-content: center;
  box-shadow: 0 4px 16px rgba(184,92,110,.28);
}
.logo-mark svg { width: 22px; height: 22px; stroke: #fff; fill: none; stroke-width: 1.6; stroke-linecap: round; stroke-linejoin: round; }
.logo-text { line-height: 1; }
.logo-name { font-size: 19px; font-weight: 700; color: var(--text-dark); letter-spacing: -.3px; display: block; }
.logo-tag  { font-size: 9.5px; font-weight: 600; color: var(--rose); letter-spacing: 2.2px; text-transform: uppercase; display: block; margin-top: 2px; }

/* Desktop Nav */
.nav { display: flex; align-items: center; gap: 2px; }
.nav a {
  font-size: 13px; font-weight: 600; color: var(--text-mid);
  padding: 8px 15px; border-radius: 8px;
  transition: all var(--transition); text-decoration: none; white-space: nowrap;
}
.nav a:hover, .nav a.active { color: var(--rose); background: var(--rose-bg); }

/* Header Actions */
.header-actions { display: flex; align-items: center; justify-content: flex-end; gap: 4px; }
.icon-btn {
  width: 40px; height: 40px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  color: var(--text-mid); cursor: pointer;
  background: none; border: none; font-family: inherit;
  transition: all var(--transition); position: relative;
}
.icon-btn:hover { background: var(--rose-bg); color: var(--rose); }
.icon-btn svg { width: 19px; height: 19px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
.menu-btn { display: none; }

/* Badges */
.cart-badge, .wish-badge {
  position: absolute; top: -4px; right: -4px;
  background: var(--rose); color: #fff;
  font-size: 10px; font-weight: 700;
  width: 17px; height: 17px; border-radius: 50%;
  display: none; align-items: center; justify-content: center;
  pointer-events: none; border: 2px solid #fff;
}

/* ═══════════════════════════════════════════════════
   MOBILE NAV DRAWER
═══════════════════════════════════════════════════ */
.mobile-nav { display: none; position: fixed; inset: 0; z-index: 300; }
.mobile-nav.open { display: block; }
.mobile-nav-overlay { position: absolute; inset: 0; background: rgba(30,21,25,.45); backdrop-filter: blur(4px); }
.mobile-nav-panel {
  position: absolute; top: 0; left: 0; bottom: 0; width: 310px;
  background: var(--white); overflow-y: auto;
  transform: translateX(-100%);
  transition: transform .36s cubic-bezier(.4,0,.2,1);
  display: flex; flex-direction: column;
}
.mobile-nav.open .mobile-nav-panel { transform: translateX(0); }

.mnp-head {
  padding: 20px 20px 16px; display: flex; align-items: center;
  justify-content: space-between; border-bottom: 1px solid var(--border-light); flex-shrink: 0;
}
.mnp-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
.mnp-logo .logo-name { font-size: 16px; }
.mnp-logo .logo-tag  { font-size: 9px; }
.mnp-close {
  width: 36px; height: 36px; border-radius: 8px; border: none; background: none;
  cursor: pointer; display: flex; align-items: center; justify-content: center;
  color: var(--text-mid); transition: all var(--transition);
}
.mnp-close:hover { background: var(--rose-bg); color: var(--rose); }
.mnp-close svg { width: 20px; height: 20px; stroke: currentColor; fill: none; stroke-width: 2; }

.mnp-links { padding: 12px 12px 8px; display: flex; flex-direction: column; gap: 2px; }
.mnp-links a {
  display: flex; align-items: center; gap: 12px;
  padding: 12px 14px; border-radius: 10px;
  font-size: 14px; font-weight: 600; color: var(--text-mid);
  text-decoration: none; transition: all var(--transition);
}
.mnp-links a:hover, .mnp-links a.active { color: var(--rose); background: var(--rose-bg); }
.mnp-links a svg { width: 17px; height: 17px; stroke: currentColor; fill: none; stroke-width: 1.8; flex-shrink: 0; }
.mnp-divider { height: 1px; background: var(--border-light); margin: 8px 20px; }

.mnp-actions { padding: 8px 12px; display: flex; gap: 8px; }
.mnp-action-btn {
  flex: 1; display: flex; flex-direction: column; align-items: center;
  gap: 5px; padding: 12px 8px; border-radius: 10px;
  border: 1.5px solid var(--border-light); background: none; cursor: pointer;
  font-family: inherit; font-size: 10.5px; font-weight: 700; color: var(--text-mid);
  text-transform: uppercase; letter-spacing: .8px; transition: all var(--transition); position: relative;
}
.mnp-action-btn:hover  { background: var(--rose-bg); border-color: var(--rose-light); color: var(--rose); }
.mnp-action-btn.active { background: var(--rose); border-color: var(--rose); color: #fff; }
.mnp-action-btn svg { width: 20px; height: 20px; stroke: currentColor; fill: none; stroke-width: 1.8; }
.mnp-action-btn .mnp-badge {
  position: absolute; top: 6px; right: 6px;
  background: var(--rose); color: #fff;
  font-size: 9px; font-weight: 700;
  width: 15px; height: 15px; border-radius: 50%;
  display: none; align-items: center; justify-content: center;
}
.mnp-action-btn.active .mnp-badge { background: rgba(255,255,255,.3); }

.mnp-auth-state {
  margin: 8px 12px 0; padding: 14px 16px;
  background: var(--rose-bg); border-radius: 12px; border: 1px solid var(--border);
}
.mnp-auth-loggedout { text-align: center; }
.mnp-auth-loggedout p { font-size: 12.5px; color: var(--text-mid); margin: 0 0 10px; font-weight: 500; }
.mnp-auth-btns { display: flex; gap: 8px; }
.mnp-btn-signin {
  flex: 1; padding: 9px; border: 1.5px solid var(--rose); border-radius: 8px;
  background: none; color: var(--rose); font-family: inherit; font-size: 12px;
  font-weight: 700; cursor: pointer; transition: all var(--transition);
}
.mnp-btn-signin:hover { background: var(--rose); color: #fff; }
.mnp-btn-signup {
  flex: 1; padding: 9px; border: none; border-radius: 8px;
  background: var(--rose); color: #fff; font-family: inherit; font-size: 12px;
  font-weight: 700; cursor: pointer; transition: background var(--transition);
}
.mnp-btn-signup:hover { background: var(--rose-hover); }
.mnp-auth-loggedin { display: none; align-items: center; gap: 12px; }
.mnp-auth-loggedin.show { display: flex; }
.mnp-user-avatar {
  width: 40px; height: 40px; border-radius: 50%; background: var(--rose); color: #fff;
  display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; flex-shrink: 0;
}
.mnp-user-info { flex: 1; min-width: 0; }
.mnp-user-name  { font-size: 13px; font-weight: 700; color: var(--text-dark); margin: 0; }
.mnp-user-label { font-size: 11px; color: var(--text-light); margin: 2px 0 0; }
.mnp-signout-btn {
  background: none; border: 1.5px solid var(--border); border-radius: 7px; padding: 5px 10px;
  font-family: inherit; font-size: 11px; font-weight: 700; color: var(--text-mid); cursor: pointer; transition: all var(--transition);
}
.mnp-signout-btn:hover { border-color: var(--rose); color: var(--rose); }

.mnp-wa-btn {
  margin: 12px 12px 20px; display: flex; align-items: center; justify-content: center; gap: 8px;
  padding: 13px; border-radius: 50px; background: #25d366; color: #fff; border: none;
  font-family: inherit; font-size: 13px; font-weight: 700; cursor: pointer; text-decoration: none;
  transition: background var(--transition);
}
.mnp-wa-btn:hover { background: #1fb958; }
.mnp-wa-btn svg { width: 18px; height: 18px; fill: #fff; }

/* ═══════════════════════════════════════════════════
   OVERLAY / PANEL SYSTEM
═══════════════════════════════════════════════════ */
.ac-overlay {
  position: fixed; inset: 0; z-index: 2000;
  background: rgba(0,0,0,.45);
  display: none; opacity: 0; transition: opacity .22s ease;
}
.ac-overlay.is-open    { display: flex; align-items: flex-start; justify-content: center; }
.ac-overlay.is-visible { opacity: 1; }
.ac-panel-overlay { align-items: stretch; justify-content: flex-end; }

/* Side panel */
.ac-panel {
  background: #fff; width: min(420px,100vw);
  display: flex; flex-direction: column;
  transform: translateX(100%); transition: transform .28s cubic-bezier(.4,0,.2,1);
  height: 100%; overflow: hidden;
}
.ac-overlay.is-visible .ac-panel { transform: translateX(0); }
.ac-panel-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 1.1rem 1.25rem; border-bottom: 1px solid #f0e8e8; flex-shrink: 0;
}
.ac-panel-title { font-size: 1rem; font-weight: 700; color: #2d1a22; margin: 0; }
.ac-panel-body  { flex: 1; overflow-y: auto; padding: 1rem 1.25rem; }
.ac-panel-footer { padding: 1rem 1.25rem; border-top: 1px solid #f0e8e8; flex-shrink: 0; background: #fff; }

/* ═══════════════════════════════════════════════════
   SEARCH MODAL — FIXED & FULL-FEATURED
═══════════════════════════════════════════════════ */
.ac-search-modal {
  background: #fff; width: min(720px, 96vw);
  border-radius: 18px; margin-top: 5vh;
  max-height: 84vh; display: flex; flex-direction: column;
  transform: translateY(-20px) scale(.97);
  transition: transform .22s ease; overflow: hidden;
  /* Critical: pointer events must work */
  pointer-events: auto;
}
.ac-overlay.is-visible .ac-search-modal { transform: translateY(0) scale(1); }

.ac-search-bar {
  display: flex; align-items: center; gap: .75rem;
  padding: 1rem 1.25rem; border-bottom: 1px solid #f0e8e8; flex-shrink: 0;
}
.ac-search-icon { width: 18px; height: 18px; stroke: #b85c6e; fill: none; stroke-width: 2; flex-shrink: 0; }
.ac-search-input {
  flex: 1; border: none; outline: none;
  font-size: 1rem; font-family: inherit; color: #2d1a22; background: transparent;
  /* Critical: ensure input is interactive */
  pointer-events: auto; cursor: text;
}
.ac-search-input::placeholder { color: #c0a0a8; }
.ac-search-clear-btn {
  display: none; background: none; border: none; cursor: pointer;
  color: #c0a0a8; font-size: 1.4rem; line-height: 1; padding: 0 4px; transition: color .15s;
}
.ac-search-clear-btn:hover { color: #b85c6e; }
.ac-icon-close {
  background: none; border: none; cursor: pointer;
  width: 34px; height: 34px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  color: #b85c6e; transition: background .15s; flex-shrink: 0;
}
.ac-icon-close svg { width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2; }
.ac-icon-close:hover { background: #fdf0f3; }

.ac-search-tags {
  display: flex; gap: .45rem; flex-wrap: wrap;
  padding: .7rem 1.25rem; border-bottom: 1px solid #f0e8e8; flex-shrink: 0;
}
.ac-tag {
  font-size: .75rem; padding: .28rem .7rem; border-radius: 20px;
  border: 1px solid #e8d0d6; color: #b85c6e; cursor: pointer;
  transition: background .15s, color .15s; white-space: nowrap; user-select: none;
}
.ac-tag:hover { background: #b85c6e; color: #fff; }

.ac-search-filters {
  display: flex; align-items: center; gap: .5rem; flex-wrap: wrap;
  padding: .65rem 1.25rem; border-bottom: 1px solid #f0e8e8;
  flex-shrink: 0; background: #fdfafb;
}
.ac-filter-label { font-size: 11px; font-weight: 700; color: #a08890; text-transform: uppercase; letter-spacing: .8px; margin-right: 4px; }
.ac-filter-chip {
  font-size: 11.5px; font-weight: 600; padding: .3rem .7rem; border-radius: 20px;
  border: 1.5px solid #e8d0d6; color: #b85c6e; background: #fff; cursor: pointer; transition: all .15s;
}
.ac-filter-chip:hover, .ac-filter-chip.active { background: #b85c6e; color: #fff; border-color: #b85c6e; }
.ac-sort-select {
  margin-left: auto; border: 1.5px solid #e8d0d6; border-radius: 8px;
  padding: .28rem .7rem; font-family: inherit; font-size: 11.5px;
  font-weight: 600; color: #2d1a22; background: #fff; outline: none; cursor: pointer;
}
.ac-sort-select:focus { border-color: #b85c6e; }

.ac-search-results { flex: 1; overflow-y: auto; padding: .75rem 1.25rem 1.25rem; }
.ac-search-hint { color: #c0a0a8; font-size: .9rem; text-align: center; padding: 2.5rem 0; margin: 0; }
.ac-search-hint strong { display: block; font-size: 2rem; margin-bottom: .5rem; }
.ac-result-stats {
  font-size: 12px; font-weight: 600; color: #a08890;
  margin-bottom: .75rem; padding-bottom: .6rem; border-bottom: 1px solid #f5ecee;
  display: flex; align-items: center; justify-content: space-between;
}

/* Product grid in search */
.ac-search-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(155px, 1fr)); gap: .85rem; }
.ac-search-card {
  border-radius: 12px; border: 1px solid #f0e8e8; overflow: hidden; cursor: pointer;
  transition: box-shadow .18s, transform .18s; text-decoration: none; display: block; background: #fff; position: relative;
}
.ac-search-card:hover { box-shadow: 0 6px 20px rgba(184,92,110,.15); transform: translateY(-2px); }
.ac-search-card-img {
  width: 100%; aspect-ratio: 1; background: #fdf0f3;
  display: flex; align-items: center; justify-content: center; font-size: 2.2rem; overflow: hidden; position: relative;
}
.ac-search-card-img img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s ease; }
.ac-search-card:hover .ac-search-card-img img { transform: scale(1.06); }

.ac-card-actions {
  position: absolute; inset: 0; background: rgba(30,21,25,.32); backdrop-filter: blur(2px);
  display: flex; align-items: center; justify-content: center; gap: 6px;
  opacity: 0; transition: opacity .2s ease;
}
.ac-search-card:hover .ac-card-actions { opacity: 1; }
.ac-card-action-btn {
  width: 32px; height: 32px; border-radius: 50%; background: #fff; border: none; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transform: translateY(8px); transition: all .2s ease; color: #b85c6e;
}
.ac-search-card:hover .ac-card-action-btn { transform: translateY(0); }
.ac-card-action-btn:hover { background: #b85c6e; color: #fff; }
.ac-card-action-btn svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2; }
.ac-card-action-btn.wa-btn svg { fill: currentColor; stroke: none; width: 14px; height: 14px; }
.ac-card-action-btn.cart-btn svg { width: 13px; height: 13px; }
.ac-card-action-btn.wish-btn.wishlisted svg { fill: #b85c6e; }
.ac-card-action-btn.wish-btn.wishlisted { background: #fdf0f3; }

.ac-search-badge {
  position: absolute; top: 7px; left: 7px; padding: 3px 9px; border-radius: 20px;
  font-size: 9px; font-weight: 700; letter-spacing: .5px; text-transform: uppercase; color: #fff; z-index: 1;
}
.ac-search-card-info { padding: .6rem .7rem .75rem; }
.ac-search-card-cat  { font-size: 9.5px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: rgba(184,92,110,.55); margin: 0 0 3px; }
.ac-search-card-name { font-size: .8rem; font-weight: 700; color: #2d1a22; margin: 0 0 .4rem; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.ac-search-card-footer { display: flex; align-items: center; justify-content: space-between; gap: 4px; }
.ac-search-card-price { font-size: .85rem; color: #b85c6e; font-weight: 700; }
.ac-search-card-orig  { font-size: .72rem; color: #c0a0a8; text-decoration: line-through; }
.ac-search-card-pct   { font-size: .7rem; font-weight: 700; color: #2e8b57; background: #e8f5ef; padding: 2px 6px; border-radius: 10px; }

.ac-no-results { color: #c0a0a8; font-size: .9rem; text-align: center; padding: 2.5rem 0; }
.ac-no-results strong { color: #b85c6e; }
.ac-spinner {
  display: block; width: 24px; height: 24px; border: 2.5px solid #f0e8e8; border-top-color: #b85c6e;
  border-radius: 50%; animation: ac-spin .7s linear infinite; margin: 2.5rem auto;
}
@keyframes ac-spin { to { transform: rotate(360deg); } }

/* ═══════════════════════════════════════════════════
   CART & WISHLIST ITEMS
═══════════════════════════════════════════════════ */
.ac-item { display: flex; gap: .9rem; align-items: flex-start; padding: .9rem 0; border-bottom: 1px solid #f5ecee; }
.ac-item:last-child { border-bottom: none; }
.ac-item-img {
  width: 72px; height: 72px; border-radius: 10px; overflow: hidden; flex-shrink: 0;
  background: #fdf0f3; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;
}
.ac-item-img img { width: 100%; height: 100%; object-fit: cover; }
.ac-item-info { flex: 1; min-width: 0; }
.ac-item-name  { font-size: .88rem; font-weight: 700; color: #2d1a22; margin: 0 0 .2rem; line-height: 1.3; }
.ac-item-meta  { font-size: .76rem; color: #c0a0a8; margin: 0 0 .35rem; }
.ac-item-price { font-size: .88rem; color: #b85c6e; font-weight: 700; }
.ac-item-orig  { font-size: .74rem; color: #c0a0a8; text-decoration: line-through; margin-left: .3rem; }
.ac-item-actions { display: flex; align-items: center; gap: .5rem; margin-top: .5rem; }
.ac-qty { display: flex; align-items: center; gap: .3rem; }
.ac-qty-btn {
  width: 24px; height: 24px; border-radius: 50%; border: 1px solid #e8d0d6; background: none;
  color: #b85c6e; cursor: pointer; font-size: .9rem; font-weight: 700;
  display: flex; align-items: center; justify-content: center; transition: background .12s;
}
.ac-qty-btn:hover { background: #fdf0f3; }
.ac-qty-num { font-size: .85rem; font-weight: 700; color: #2d1a22; min-width: 1.4rem; text-align: center; }
.ac-remove {
  background: none; border: none; cursor: pointer; color: #e8a0a8;
  font-size: .76rem; text-decoration: underline; padding: 0; transition: color .12s;
}
.ac-remove:hover { color: #e05050; }
.ac-add-cart-btn {
  background: none; border: 1px solid #e8d0d6; border-radius: 8px; color: #b85c6e;
  font-size: .76rem; font-weight: 700; padding: .28rem .65rem; cursor: pointer;
  font-family: inherit; transition: background .12s, color .12s;
}
.ac-add-cart-btn:hover { background: #b85c6e; color: #fff; }

.ac-cart-total { display: flex; justify-content: space-between; font-size: .95rem; font-weight: 700; color: #2d1a22; margin-bottom: .6rem; }
.ac-cart-note  { font-size: .77rem; color: #c0a0a8; margin: .3rem 0 .8rem; text-align: center; }
.ac-empty { display: flex; flex-direction: column; align-items: center; padding: 3rem 1rem; color: #c0a0a8; text-align: center; gap: .4rem; }
.ac-empty-icon  { font-size: 3rem; line-height: 1; }
.ac-empty-title { color: #b85c6e; font-weight: 700; font-size: .95rem; margin: 0; }
.ac-empty p { margin: 0; font-size: .87rem; }

/* ═══════════════════════════════════════════════════
   AUTH MODAL
═══════════════════════════════════════════════════ */
.ac-auth-modal {
  background: #fff; width: min(420px, 96vw); border-radius: 18px; margin-top: 6vh;
  padding: 2rem 1.75rem; position: relative;
  transform: translateY(-20px) scale(.97); transition: transform .22s ease;
  max-height: 90vh; overflow-y: auto;
}
.ac-overlay.is-visible .ac-auth-modal { transform: translateY(0) scale(1); }
.ac-auth-close { position: absolute; top: 1rem; right: 1rem; }
.ac-auth-logo { display: flex; align-items: center; gap: .6rem; margin-bottom: 1.5rem; color: #b85c6e; font-weight: 700; font-size: 1.05rem; }
.ac-auth-tabs { display: flex; margin-bottom: 1.5rem; border-bottom: 2px solid #f0e8e8; }
.ac-auth-tab {
  flex: 1; padding: .65rem; border: none; background: none; font-family: inherit; font-size: .9rem;
  color: #c0a0a8; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: color .15s, border-color .15s;
}
.ac-auth-tab.active { color: #b85c6e; border-bottom-color: #b85c6e; font-weight: 700; }
.ac-field { margin-bottom: 1rem; }
.ac-field label { display: block; font-size: .82rem; font-weight: 700; color: #6b4050; margin-bottom: .35rem; }
.ac-optional { font-weight: 400; color: #c0a0a8; }
.ac-field input {
  width: 100%; padding: .65rem .9rem; border: 1.5px solid #e8d0d6; border-radius: 10px;
  font-family: inherit; font-size: .92rem; color: #2d1a22; transition: border-color .15s; outline: none; background: #fff;
}
.ac-field input:focus { border-color: #b85c6e; }
.ac-input-group { display: flex; align-items: center; }
.ac-input-group input { border-radius: 0 10px 10px 0; border-left: none; flex: 1; }
.ac-input-prefix {
  padding: .65rem .75rem; background: #fdf0f3; border: 1.5px solid #e8d0d6; border-right: none;
  border-radius: 10px 0 0 10px; color: #b85c6e; font-size: .88rem; font-weight: 700; white-space: nowrap;
}
.ac-pw-toggle {
  background: none; border: 1.5px solid #e8d0d6; border-left: none; border-radius: 0 10px 10px 0;
  cursor: pointer; padding: .65rem .7rem; color: #c0a0a8; display: flex; align-items: center; transition: color .12s;
}
.ac-pw-toggle:hover { color: #b85c6e; }
.ac-auth-msg { font-size: .82rem; min-height: 1.1rem; margin: .2rem 0 .5rem; }
.ac-auth-msg.error   { color: #e05050; }
.ac-auth-msg.success { color: #4a9e6e; }
.ac-auth-footer-note { font-size: .82rem; color: #c0a0a8; text-align: center; margin-top: .75rem; }
.ac-auth-footer-note a { color: #b85c6e; text-decoration: none; }
.ac-auth-welcome { font-size: 1rem; color: #2d1a22; margin: .75rem 0; }
.ac-avatar {
  width: 56px; height: 56px; border-radius: 50%; background: #b85c6e; color: #fff;
  display: flex; align-items: center; justify-content: center; font-size: 1.4rem; font-weight: 700; margin: 0 auto;
}

/* ═══════════════════════════════════════════════════
   SHARED BUTTONS
═══════════════════════════════════════════════════ */
.ac-btn-primary {
  width: 100%; padding: .75rem; background: #b85c6e; color: #fff;
  border: none; border-radius: 10px; font-family: inherit; font-size: .92rem; font-weight: 700;
  cursor: pointer; transition: background .15s, transform .1s;
  display: flex; align-items: center; justify-content: center; gap: 6px;
}
.ac-btn-primary:hover   { background: #a3505f; }
.ac-btn-primary:active  { transform: scale(.98); }
.ac-btn-primary:disabled { opacity: .6; cursor: not-allowed; }
.ac-btn-outline {
  width: 100%; padding: .65rem; background: transparent; color: #b85c6e;
  border: 1.5px solid #b85c6e; border-radius: 10px; font-family: inherit;
  font-size: .88rem; font-weight: 700; cursor: pointer; margin-top: .5rem; transition: background .15s;
}
.ac-btn-outline:hover { background: #fdf0f3; }

/* ═══════════════════════════════════════════════════
   TOAST
═══════════════════════════════════════════════════ */
.ac-toast {
  position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 4000;
  background: #2d1a22; color: #fff; padding: .65rem 1.1rem; border-radius: 10px;
  font-size: .85rem; font-weight: 600; transform: translateY(80px); opacity: 0;
  transition: transform .25s ease, opacity .25s ease; pointer-events: none;
  max-width: 290px; border-left: 3px solid #b85c6e;
}
.ac-toast.show    { transform: translateY(0); opacity: 1; }
.ac-toast.success { background: #4a9e6e; border-left-color: #3a8e5e; }
.ac-toast.error   { background: #e05050; border-left-color: #c04040; }

/* ═══════════════════════════════════════════════════
   RESPONSIVE HEADER
═══════════════════════════════════════════════════ */
@media (max-width: 900px) {
  .header-inner { grid-template-columns: 1fr auto; padding: 0 20px; height: 66px; --header-h: 66px; }
  .nav { display: none; }
  .menu-btn { display: flex; }
  #authOpenBtn, #wishlistOpenBtn { display: none; }
}
@media (max-width: 480px) {
  .ac-search-grid { grid-template-columns: repeat(2, 1fr); }
  .ac-search-modal { border-radius: 14px; margin-top: 3vh; max-height: 90vh; }
  .ac-search-filters { display: none; }
}
</style>

<!-- ANNOUNCEMENT BAR -->
<div class="ann-bar" role="banner">
  <div class="ann-item"><div class="ann-dot"></div>Free Shipping on Orders Above ₹999</div>
  <div class="ann-item"><div class="ann-dot"></div>Handcrafted Gifts, Made With Love</div>
  <div class="ann-item"><div class="ann-dot"></div>Easy Returns &amp; Secure Checkout</div>
</div>

<!-- HEADER -->
<header class="header" id="mainHeader">
  <div class="header-inner">

    <a href="index.php" class="logo" aria-label="Aakar Creatives Home">
      <div class="logo-mark" aria-hidden="true">
        <svg viewBox="0 0 24 24"><path d="M20 12v10H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
      </div>
      <div class="logo-text">
        <span class="logo-name">Aakar Creatives</span>
        <span class="logo-tag">Gifts That Speak Hearts</span>
      </div>
    </a>

    <nav class="nav" aria-label="Primary navigation">
      <a href="index.php"<?= navActive('index.php', $current_page) ?>>Home</a>
      <a href="shop.php"<?= navActive('shop.php', $current_page) ?>>Shop</a>
      <a href="occasions.php"<?= navActive('occasions.php', $current_page) ?>>Occasions</a>
      <a href="about.php"<?= navActive('about.php', $current_page) ?>>About</a>
    </nav>

    <div class="header-actions">
      <button class="icon-btn" id="searchOpenBtn" title="Search" aria-label="Search products">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </button>
      <button class="icon-btn" id="authOpenBtn" title="Account" aria-label="My account">
        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </button>
      <button class="icon-btn" id="wishlistOpenBtn" title="Wishlist" aria-label="My wishlist" style="position:relative">
        <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        <span class="wish-badge" id="wishBadgeHeader"></span>
      </button>
      <button class="icon-btn" id="cartOpenBtn" title="Cart" aria-label="Shopping cart" style="position:relative">
        <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        <span class="cart-badge" id="cartBadgeHeader"></span>
      </button>
      <button class="menu-btn icon-btn" id="menuBtn" aria-label="Open menu" aria-expanded="false">
        <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
    </div>

  </div>
</header>

<!-- MOBILE NAV DRAWER -->
<div class="mobile-nav" id="mobileNav" role="dialog" aria-modal="true" aria-label="Mobile navigation">
  <div class="mobile-nav-overlay" id="navOverlay"></div>
  <div class="mobile-nav-panel">
    <div class="mnp-head">
      <a href="index.php" class="mnp-logo">
        <div class="logo-mark" style="width:36px;height:36px;border-radius:9px;" aria-hidden="true">
          <svg viewBox="0 0 24 24" style="width:18px;height:18px;"><path d="M20 12v10H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
        </div>
        <div class="logo-text">
          <span class="logo-name">Aakar Creatives</span>
          <span class="logo-tag">Gifts That Speak Hearts</span>
        </div>
      </a>
      <button class="mnp-close" id="navClose" aria-label="Close menu">
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <div class="mnp-auth-state">
      <div class="mnp-auth-loggedout" id="mnpLoggedOut">
        <p>Sign in to save your wishlist &amp; track orders</p>
        <div class="mnp-auth-btns">
          <button class="mnp-btn-signin" id="mnpSigninBtn">Sign In</button>
          <button class="mnp-btn-signup" id="mnpSignupBtn">Create Account</button>
        </div>
      </div>
      <div class="mnp-auth-loggedin" id="mnpLoggedIn">
        <div class="mnp-user-avatar" id="mnpAvatar">A</div>
        <div class="mnp-user-info">
          <p class="mnp-user-name" id="mnpUserName">User</p>
          <p class="mnp-user-label">Member</p>
        </div>
        <button class="mnp-signout-btn" id="mnpSignoutBtn">Sign Out</button>
      </div>
    </div>

    <div class="mnp-actions">
      <button class="mnp-action-btn" id="mnpSearchBtn" aria-label="Search">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        Search
      </button>
      <button class="mnp-action-btn" id="mnpWishBtn" aria-label="Wishlist">
        <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        Wishlist
        <span class="mnp-badge" id="mnpWishBadge"></span>
      </button>
      <button class="mnp-action-btn" id="mnpCartBtn" aria-label="Cart">
        <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        Cart
        <span class="mnp-badge" id="mnpCartBadge"></span>
      </button>
    </div>

    <div class="mnp-divider"></div>

    <nav class="mnp-links" aria-label="Mobile navigation">
      <a href="index.php"<?= navActive('index.php', $current_page) ?>>
        <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Home
      </a>
      <a href="shop.php"<?= navActive('shop.php', $current_page) ?>>
        <svg viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        Shop All Gifts
      </a>
      <a href="occasions.php"<?= navActive('occasions.php', $current_page) ?>>
        <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        Occasions
      </a>
      <a href="about.php"<?= navActive('about.php', $current_page) ?>>
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        About Us
      </a>
    </nav>

    <a class="mnp-wa-btn" href="https://wa.me/<?= htmlspecialchars($wa_number) ?>" target="_blank" rel="noopener noreferrer">
      <svg viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6z"/></svg>
      Chat on WhatsApp
    </a>
  </div>
</div>

<!-- SEARCH MODAL -->
<div class="ac-overlay" id="searchOverlay" role="dialog" aria-modal="true" aria-label="Search products">
  <div class="ac-search-modal" id="searchModal">
    <div class="ac-search-bar">
      <svg class="ac-search-icon" viewBox="0 0 24 24" aria-hidden="true">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <input type="text" id="headerSearchInput" class="ac-search-input"
             placeholder="Search gifts, categories, occasions…" autocomplete="off"
             aria-label="Search products"/>
      <button class="ac-search-clear-btn" id="headerSearchClear" aria-label="Clear search">×</button>
      <button class="ac-icon-close" id="searchClose" aria-label="Close search">
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <div class="ac-search-tags" id="searchQuickTags">
      <span class="ac-tag" data-q="crochet bouquet">Crochet Bouquet</span>
      <span class="ac-tag" data-q="photo frame">Photo Frames</span>
      <span class="ac-tag" data-q="anniversary">Anniversary</span>
      <span class="ac-tag" data-q="birthday">Birthday</span>
      <span class="ac-tag" data-q="gift box">Gift Boxes</span>
      <span class="ac-tag" data-q="shirt">Shirts</span>
    </div>

    <div class="ac-search-filters" id="headerSearchFilterBar">
      <span class="ac-filter-label">Filter:</span>
      <button class="ac-filter-chip active" data-hcat="all">All</button>
      <div id="headerCatChips" style="display:contents;"></div>
      <select class="ac-sort-select" id="headerSearchSort" aria-label="Sort results">
        <option value="featured">Featured</option>
        <option value="popular">Most Popular</option>
        <option value="price-low">Price ↑</option>
        <option value="price-high">Price ↓</option>
        <option value="discount">Best Discount</option>
        <option value="new">Newest</option>
      </select>
    </div>

    <div class="ac-search-results" id="headerSearchResults">
      <p class="ac-search-hint"><strong>🔍</strong>Start typing to discover gifts…</p>
    </div>
  </div>
</div>

<!-- CART PANEL -->
<div class="ac-overlay ac-panel-overlay" id="cartOverlay" role="dialog" aria-modal="true" aria-label="Shopping cart">
  <div class="ac-panel" id="cartPanel">
    <div class="ac-panel-header">
      <h2 class="ac-panel-title">
        <svg viewBox="0 0 24 24" width="18" height="18" style="stroke:currentColor;fill:none;stroke-width:1.8;vertical-align:-3px;margin-right:6px;"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        Your Cart
      </h2>
      <button class="ac-icon-close" id="cartClose" aria-label="Close cart">
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="ac-panel-body" id="cartBody"></div>
    <div class="ac-panel-footer" id="cartFooter" style="display:none;">
      <div class="ac-cart-total"><span>Total</span><span id="cartTotal">₹0</span></div>
      <p class="ac-cart-note">We process orders via WhatsApp — tap below to enquire!</p>
      <button class="ac-btn-primary" id="cartWhatsapp">
        <svg viewBox="0 0 448 512" width="17" height="17" style="fill:white;flex-shrink:0;"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157z"/></svg>
        Order via WhatsApp
      </button>
    </div>
  </div>
</div>

<!-- WISHLIST PANEL -->
<div class="ac-overlay ac-panel-overlay" id="wishlistOverlay" role="dialog" aria-modal="true" aria-label="Wishlist">
  <div class="ac-panel" id="wishlistPanel">
    <div class="ac-panel-header">
      <h2 class="ac-panel-title">
        <svg viewBox="0 0 24 24" width="18" height="18" style="stroke:#b85c6e;fill:none;stroke-width:1.8;vertical-align:-3px;margin-right:6px;"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        Wishlist
      </h2>
      <button class="ac-icon-close" id="wishlistClose" aria-label="Close wishlist">
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="ac-panel-body" id="wishlistBody"></div>
    <div class="ac-panel-footer" id="wishlistFooter" style="display:none;">
      <button class="ac-btn-primary" id="wishlistToCart">Move All to Cart</button>
    </div>
  </div>
</div>

<!-- AUTH MODAL -->
<div class="ac-overlay" id="authOverlay" role="dialog" aria-modal="true" aria-label="Sign in or create account">
  <div class="ac-auth-modal">
    <button class="ac-icon-close ac-auth-close" id="authClose" aria-label="Close">
      <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <div class="ac-auth-logo">
      <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#b85c6e" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M20 12v10H4V12"/><path d="M22 7H2v5h20V7z"/>
        <path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/>
        <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>
      </svg>
      <span>Aakar Creatives</span>
    </div>
    <div class="ac-auth-tabs">
      <button class="ac-auth-tab active" data-tab="signin">Sign In</button>
      <button class="ac-auth-tab" data-tab="signup">Create Account</button>
    </div>
    <form class="ac-auth-form" id="signinForm" novalidate>
      <div class="ac-field">
        <label for="siPhone">Mobile Number</label>
        <div class="ac-input-group">
          <span class="ac-input-prefix">+91</span>
          <input type="tel" id="siPhone" name="phone" placeholder="9876543210" maxlength="10" pattern="\d{10}" required/>
        </div>
      </div>
      <div class="ac-field">
        <label for="siPassword">Password</label>
        <div class="ac-input-group">
          <input type="password" id="siPassword" name="password" placeholder="Your password" required/>
          <button type="button" class="ac-pw-toggle" data-target="siPassword" aria-label="Toggle password">
            <svg viewBox="0 0 24 24" width="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>
      <p class="ac-auth-msg" id="signinMsg"></p>
      <button type="submit" class="ac-btn-primary">Sign In</button>
      <p class="ac-auth-footer-note">Don't have an account? <a href="#" class="ac-tab-switch" data-tab="signup">Create one</a></p>
    </form>
    <form class="ac-auth-form" id="signupForm" novalidate style="display:none;">
      <div class="ac-field">
        <label for="suName">Full Name</label>
        <input type="text" id="suName" name="name" placeholder="Your name" required/>
      </div>
      <div class="ac-field">
        <label for="suPhone">Mobile Number</label>
        <div class="ac-input-group">
          <span class="ac-input-prefix">+91</span>
          <input type="tel" id="suPhone" name="phone" placeholder="9876543210" maxlength="10" pattern="\d{10}" required/>
        </div>
      </div>
      <div class="ac-field">
        <label for="suEmail">Email <span class="ac-optional">(optional)</span></label>
        <input type="email" id="suEmail" name="email" placeholder="you@example.com"/>
      </div>
      <div class="ac-field">
        <label for="suCity">City <span class="ac-optional">(optional)</span></label>
        <input type="text" id="suCity" name="city" placeholder="Surat"/>
      </div>
      <div class="ac-field">
        <label for="suPassword">Password</label>
        <div class="ac-input-group">
          <input type="password" id="suPassword" name="password" placeholder="Min. 6 characters" required minlength="6"/>
          <button type="button" class="ac-pw-toggle" data-target="suPassword" aria-label="Toggle password">
            <svg viewBox="0 0 24 24" width="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>
      <p class="ac-auth-msg" id="signupMsg"></p>
      <button type="submit" class="ac-btn-primary">Create Account</button>
      <p class="ac-auth-footer-note">Already registered? <a href="#" class="ac-tab-switch" data-tab="signin">Sign in</a></p>
    </form>
    <div id="authLoggedIn" style="display:none;text-align:center;padding:1rem 0;">
      <div class="ac-avatar" id="authAvatar">A</div>
      <p class="ac-auth-welcome" id="authWelcomeMsg">Welcome back!</p>
      <button class="ac-btn-outline" id="authSignout">Sign Out</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="ac-toast" id="acToast" role="alert" aria-live="polite"></div>

<script>
(function(){
'use strict';

/* ── Constants ─────────────────────────────────────── */
var WA_NUMBER   = '<?= htmlspecialchars($wa_number, ENT_QUOTES) ?>';
var CART_KEY    = 'aakar_cart';
var WISH_KEY    = 'aakar_wishlist';
var USER_KEY    = 'aakar_user';

/* ── Toast ─────────────────────────────────────────── */
function toast(msg, type, dur) {
  dur = dur || 3000;
  var t = document.getElementById('acToast');
  t.textContent = msg;
  t.className = 'ac-toast show' + (type ? ' ' + type : '');
  clearTimeout(t._tm);
  t._tm = setTimeout(function() { t.className = 'ac-toast'; }, dur);
}
window.acToast = toast;

/* ── Format price ──────────────────────────────────── */
function fmtPrice(n) { return '₹' + Number(n).toLocaleString('en-IN'); }

/* ── Escape HTML ───────────────────────────────────── */
function esc(s) {
  if (!s) return '';
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ── Overlay helpers ───────────────────────────────── */
function openOverlay(id) {
  var el = document.getElementById(id);
  if (!el) return;
  el.style.display = 'flex';
  requestAnimationFrame(function() {
    el.classList.add('is-open');
    requestAnimationFrame(function() { el.classList.add('is-visible'); });
  });
  document.body.style.overflow = 'hidden';
}
function closeOverlay(id) {
  var el = document.getElementById(id);
  if (!el) return;
  el.classList.remove('is-visible');
  setTimeout(function() {
    el.classList.remove('is-open');
    el.style.display = 'none';
    var anyOpen = document.querySelector('.ac-overlay.is-open');
    if (!anyOpen && !document.querySelector('.mobile-nav.open')) {
      document.body.style.overflow = '';
    }
  }, 280);
}
window.openOverlay  = openOverlay;
window.closeOverlay = closeOverlay;

/* Backdrop click to close */
document.querySelectorAll('.ac-overlay').forEach(function(ov) {
  ov.addEventListener('click', function(e) {
    if (e.target === ov) closeOverlay(ov.id);
  });
});

/* ESC key */
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    ['searchOverlay','cartOverlay','wishlistOverlay','authOverlay'].forEach(closeOverlay);
  }
});

/* ── Sticky header ─────────────────────────────────── */
var mainHeader = document.getElementById('mainHeader');
if (mainHeader) {
  window.addEventListener('scroll', function() {
    mainHeader.classList.toggle('scrolled', window.scrollY > 10);
  }, { passive: true });
}

/* ── MOBILE NAV ────────────────────────────────────── */
var menuBtn    = document.getElementById('menuBtn');
var mobileNav  = document.getElementById('mobileNav');
var navClose   = document.getElementById('navClose');
var navOverlay = document.getElementById('navOverlay');

function openNav()  { if (!mobileNav) return; mobileNav.classList.add('open'); document.body.style.overflow = 'hidden'; if (menuBtn) menuBtn.setAttribute('aria-expanded','true'); }
function closeNav() { if (!mobileNav) return; mobileNav.classList.remove('open'); var anyOpen = document.querySelector('.ac-overlay.is-open'); if (!anyOpen) document.body.style.overflow = ''; if (menuBtn) menuBtn.setAttribute('aria-expanded','false'); }

if (menuBtn)    menuBtn.addEventListener('click', openNav);
if (navClose)   navClose.addEventListener('click', closeNav);
if (navOverlay) navOverlay.addEventListener('click', closeNav);

function mobileAction(fn) { closeNav(); setTimeout(fn, 320); }

/* Mobile quick-action buttons */
var mnpSearchBtn = document.getElementById('mnpSearchBtn');
var mnpWishBtn   = document.getElementById('mnpWishBtn');
var mnpCartBtn   = document.getElementById('mnpCartBtn');
var mnpSigninBtn  = document.getElementById('mnpSigninBtn');
var mnpSignupBtn  = document.getElementById('mnpSignupBtn');
var mnpSignoutBtn = document.getElementById('mnpSignoutBtn');

if (mnpSearchBtn) mnpSearchBtn.addEventListener('click', function() {
  mobileAction(function() {
    openOverlay('searchOverlay');
    setTimeout(function() { var si = document.getElementById('headerSearchInput'); if (si) si.focus(); }, 250);
  });
});
if (mnpWishBtn) mnpWishBtn.addEventListener('click', function() {
  mobileAction(function() { renderWishlist(); openOverlay('wishlistOverlay'); });
});
if (mnpCartBtn) mnpCartBtn.addEventListener('click', function() {
  mobileAction(function() { renderCart(); openOverlay('cartOverlay'); });
});
if (mnpSigninBtn) mnpSigninBtn.addEventListener('click', function() {
  mobileAction(function() { renderAuthModal(getUser()); switchAuthTab('signin'); openOverlay('authOverlay'); });
});
if (mnpSignupBtn) mnpSignupBtn.addEventListener('click', function() {
  mobileAction(function() { renderAuthModal(getUser()); switchAuthTab('signup'); openOverlay('authOverlay'); });
});
if (mnpSignoutBtn) mnpSignoutBtn.addEventListener('click', doSignout);

/* ── Desktop header buttons ────────────────────────── */
var searchOpenBtn   = document.getElementById('searchOpenBtn');
var cartOpenBtn     = document.getElementById('cartOpenBtn');
var wishlistOpenBtn = document.getElementById('wishlistOpenBtn');
var authOpenBtn     = document.getElementById('authOpenBtn');

if (searchOpenBtn) searchOpenBtn.addEventListener('click', function() {
  openOverlay('searchOverlay');
  setTimeout(function() {
    var si = document.getElementById('headerSearchInput');
    if (si) { si.focus(); si.select(); }
  }, 260);
});
if (cartOpenBtn)     cartOpenBtn.addEventListener('click',     function() { renderCart();     openOverlay('cartOverlay');     });
if (wishlistOpenBtn) wishlistOpenBtn.addEventListener('click', function() { renderWishlist(); openOverlay('wishlistOverlay'); });
if (authOpenBtn)     authOpenBtn.addEventListener('click',     function() { renderAuthModal(getUser()); openOverlay('authOverlay'); });

document.getElementById('searchClose').onclick   = function() { closeOverlay('searchOverlay'); };
document.getElementById('cartClose').onclick     = function() { closeOverlay('cartOverlay');   };
document.getElementById('wishlistClose').onclick = function() { closeOverlay('wishlistOverlay'); };
document.getElementById('authClose').onclick     = function() { closeOverlay('authOverlay');   };

/* ════════════════════════════════════════════════════
   SEARCH — fully fixed
   ════════════════════════════════════════════════════ */
var searchTimer;
var searchInput   = document.getElementById('headerSearchInput');
var searchClear   = document.getElementById('headerSearchClear');
var searchResults = document.getElementById('headerSearchResults');
var searchSort    = document.getElementById('headerSearchSort');
var activeSrchCat = 'all';
var lastSrchData  = [];
var lastSrchQuery = '';

/* Build category chips if PRODUCTS available */
function buildSearchCatChips() {
  var wrap = document.getElementById('headerCatChips');
  if (!wrap) return;
  if (typeof PRODUCTS === 'undefined' || !PRODUCTS.length) return;
  var cats = {}, seen = {};
  PRODUCTS.forEach(function(p) {
    if (p.catSlug && !seen[p.catSlug]) { seen[p.catSlug] = 1; cats[p.catSlug] = p.catName; }
  });
  Object.keys(cats).forEach(function(slug) {
    var chip = document.createElement('button');
    chip.className = 'ac-filter-chip';
    chip.dataset.hcat = slug;
    chip.textContent = cats[slug];
    chip.addEventListener('click', function() { setSearchCat(slug); });
    wrap.appendChild(chip);
  });
}

function setSearchCat(slug) {
  activeSrchCat = slug;
  document.querySelectorAll('#headerSearchFilterBar .ac-filter-chip').forEach(function(c) {
    c.classList.toggle('active', c.dataset.hcat === slug);
  });
  applySearchRender(lastSrchData, lastSrchQuery);
}

/* Wire up search input — CRITICAL: event must fire properly */
if (searchInput) {
  /* Use both input and keyup for maximum compatibility */
  ['input', 'keyup'].forEach(function(ev) {
    searchInput.addEventListener(ev, function() {
      clearTimeout(searchTimer);
      var q = searchInput.value;
      searchClear.style.display = q.trim() ? 'block' : 'none';
      if (!q.trim()) { resetSearch(); return; }
      if (q.trim().length < 2) return;
      searchResults.innerHTML = '<div class="ac-spinner"></div>';
      searchTimer = setTimeout(function() { doHeaderSearch(q.trim()); }, 300);
    });
  });
  /* Also handle paste */
  searchInput.addEventListener('paste', function() {
    setTimeout(function() {
      var q = searchInput.value.trim();
      searchClear.style.display = q ? 'block' : 'none';
      if (q.length >= 2) { searchResults.innerHTML = '<div class="ac-spinner"></div>'; doHeaderSearch(q); }
    }, 50);
  });
}

if (searchClear) {
  searchClear.addEventListener('click', function() {
    searchInput.value = '';
    searchClear.style.display = 'none';
    resetSearch();
    searchInput.focus();
  });
}

if (searchSort) {
  searchSort.addEventListener('change', function() {
    applySearchRender(lastSrchData, lastSrchQuery);
  });
}

/* Quick tag chips */
document.querySelectorAll('#searchQuickTags .ac-tag').forEach(function(tag) {
  tag.addEventListener('click', function() {
    var q = tag.dataset.q;
    searchInput.value = q;
    searchClear.style.display = 'block';
    searchResults.innerHTML = '<div class="ac-spinner"></div>';
    doHeaderSearch(q);
  });
});

function resetSearch() {
  lastSrchData = []; lastSrchQuery = '';
  searchResults.innerHTML = '<p class="ac-search-hint"><strong>🔍</strong>Start typing to discover gifts…</p>';
}

function doHeaderSearch(q) {
  lastSrchQuery = q;
  /* If PRODUCTS array is available on the page (shop.php), use local search */
  if (typeof PRODUCTS !== 'undefined' && PRODUCTS.length) {
    var res = localProductSearch(PRODUCTS, q);
    lastSrchData = res;
    applySearchRender(res, q);
    return;
  }
  /* Otherwise call search-api.php */
  fetch('search-api.php?q=' + encodeURIComponent(q))
    .then(function(r) { if (!r.ok) throw new Error('Network error'); return r.json(); })
    .then(function(data) {
      var norm = (data || []).map(function(p) {
        return {
          id:         p.id,
          name:       p.name,
          slug:       p.slug || '',
          desc:       p.short_description || '',
          price:      parseFloat(p.price) || 0,
          salePrice:  p.discount_price ? parseFloat(p.discount_price) : null,
          catName:    p.category || '',
          catSlug:    p.category_slug || '',
          badgeName:  p.badge_name || null,
          badgeColor: p.badge_color || '#b85c6e',
          primaryImg: p.image_url || null,
          inStock:    true, isFeatured: false, isNew: false, views: 0
        };
      });
      lastSrchData = norm;
      applySearchRender(norm, q);
    })
    .catch(function() {
      searchResults.innerHTML = '<p class="ac-no-results">Search unavailable. <a href="shop.php?q=' + encodeURIComponent(q) + '" style="color:#b85c6e">Browse shop →</a></p>';
    });
}

function localProductSearch(products, q) {
  var ql = q.toLowerCase();
  return products.filter(function(p) {
    return (p.name  && p.name.toLowerCase().includes(ql))   ||
           (p.desc  && p.desc.toLowerCase().includes(ql))   ||
           (p.catName && p.catName.toLowerCase().includes(ql)) ||
           (p.tags  && p.tags.some && p.tags.some(function(t) { return t.toLowerCase().includes(ql); })) ||
           (p.occasions && p.occasions.some && p.occasions.some(function(o) { return o.toLowerCase().includes(ql); }));
  });
}

function applySearchRender(data, q) {
  var list = data.slice();
  if (activeSrchCat && activeSrchCat !== 'all') {
    list = list.filter(function(p) { return p.catSlug === activeSrchCat; });
  }
  var sortVal = searchSort ? searchSort.value : 'featured';
  switch (sortVal) {
    case 'price-low':  list.sort(function(a,b) { return a.price - b.price; }); break;
    case 'price-high': list.sort(function(a,b) { return b.price - a.price; }); break;
    case 'popular':    list.sort(function(a,b) { return (b.views||0) - (a.views||0); }); break;
    case 'new':        list.sort(function(a,b) { return ((b.isNew?1:0) - (a.isNew?1:0)); }); break;
    case 'discount':
      list.sort(function(a,b) {
        var da = (a.salePrice && a.price) ? (1 - a.price/a.salePrice) : 0;
        var db = (b.salePrice && b.price) ? (1 - b.price/b.salePrice) : 0;
        return db - da;
      }); break;
    default: list.sort(function(a,b) { return ((b.isFeatured?1:0) - (a.isFeatured?1:0)); });
  }
  renderSearchCards(list, q);
}

function renderSearchCards(products, q) {
  if (!products.length) {
    searchResults.innerHTML = '<p class="ac-no-results">No results for <strong>"' + esc(q) + '"</strong>. <a href="shop.php?q=' + encodeURIComponent(q) + '" style="color:#b85c6e">Browse all →</a></p>';
    return;
  }
  var wish = getWishlist();
  var html = '<div class="ac-result-stats">' +
    '<span><strong>' + products.length + '</strong> gift' + (products.length !== 1 ? 's' : '') + ' found</span>' +
    '<span style="font-size:11px;color:#c0a0a8;">Click to view details</span>' +
    '</div><div class="ac-search-grid">';

  products.forEach(function(p) {
    var isWished = wish.some(function(w) { return String(w.id) === String(p.id); });
    var pct = (p.salePrice && p.price) ? Math.round((1 - p.price / p.salePrice) * 100) : 0;
    var imgHtml = p.primaryImg
      ? '<img src="' + esc(p.primaryImg) + '" alt="' + esc(p.name) + '" loading="lazy">'
      : '<span style="font-size:2.2rem;">🎁</span>';
    var badgeHtml = p.badgeName
      ? '<span class="ac-search-badge" style="background:' + esc(p.badgeColor || '#b85c6e') + '">' + esc(p.badgeName) + '</span>' : '';

    html += '<div class="ac-search-card" data-pid="' + p.id + '" data-slug="' + esc(p.slug) + '">' +
      '<div class="ac-search-card-img">' + imgHtml + badgeHtml +
        '<div class="ac-card-actions">' +
          '<button class="ac-card-action-btn wish-btn' + (isWished ? ' wishlisted' : '') + '" data-pid="' + p.id + '" title="Wishlist">' +
            '<svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>' +
          '</button>' +
          '<button class="ac-card-action-btn cart-btn" data-pid="' + p.id + '" title="Add to Cart">' +
            '<svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>' +
          '</button>' +
          '<button class="ac-card-action-btn wa-btn" data-pid="' + p.id + '" title="WhatsApp">' +
            '<svg viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157z"/></svg>' +
          '</button>' +
        '</div>' +
      '</div>' +
      '<div class="ac-search-card-info">' +
        '<p class="ac-search-card-cat">' + esc(p.catName) + '</p>' +
        '<p class="ac-search-card-name">' + esc(p.name) + '</p>' +
        '<div class="ac-search-card-footer">' +
          '<div><span class="ac-search-card-price">' + fmtPrice(p.price) + '</span>' +
            (p.salePrice ? ' <span class="ac-search-card-orig">' + fmtPrice(p.salePrice) + '</span>' : '') +
          '</div>' +
          (pct > 0 ? '<span class="ac-search-card-pct">-' + pct + '%</span>' : '') +
        '</div>' +
      '</div>' +
    '</div>';
  });
  html += '</div>';
  searchResults.innerHTML = html;

  /* Wire up action buttons */
  searchResults.querySelectorAll('.wish-btn').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      var pid = parseInt(btn.dataset.pid);
      var p   = findProduct(pid);
      if (!p) return;
      var inList = toggleWishlistItem({ id: p.id, name: p.name, price: p.price, discount_price: p.salePrice, image_url: p.primaryImg, category: p.catName });
      btn.classList.toggle('wishlisted', inList);
    });
  });

  searchResults.querySelectorAll('.cart-btn').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      var pid = parseInt(btn.dataset.pid);
      var p   = findProduct(pid);
      if (!p) return;
      addToCart({ id: p.id, name: p.name, price: p.price, discount_price: p.salePrice, image_url: p.primaryImg, category: p.catName });
    });
  });

  searchResults.querySelectorAll('.wa-btn').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      var pid = parseInt(btn.dataset.pid);
      var p   = findProduct(pid);
      if (!p) return;
      var msg = 'Hello Aakar Creatives! 🌸\n\nI\'d like to know more about:\n\n📦 *' + p.name + '*\n🏷 *Category:* ' + p.catName + '\n💰 *Price:* ' + fmtPrice(p.price) + '\n\nKindly share details. 😊';
      window.open('https://wa.me/' + WA_NUMBER + '?text=' + encodeURIComponent(msg), '_blank');
      toast('Opening WhatsApp…', 'success');
    });
  });

  /* Card click → detail or shop page */
  searchResults.querySelectorAll('.ac-search-card').forEach(function(card) {
    card.addEventListener('click', function(e) {
      if (e.target.closest('.ac-card-action-btn')) return;
      var pid  = parseInt(card.dataset.pid);
      var slug = card.dataset.slug;
      closeOverlay('searchOverlay');
      setTimeout(function() {
        if (typeof openDetail === 'function') {
          openDetail(pid);
        } else if (slug) {
          window.location.href = 'product.php?slug=' + encodeURIComponent(slug);
        } else {
          window.location.href = 'shop.php?product=' + pid;
        }
      }, 300);
    });
  });

  /* Build category chips now that data is loaded */
  buildSearchCatChips();
}

function findProduct(id) {
  if (typeof PRODUCTS !== 'undefined') return PRODUCTS.find(function(p) { return p.id === id; }) || null;
  return lastSrchData.find(function(p) { return p.id === id; }) || null;
}

/* ════════════════════════════════════════════════════
   CART  (localStorage)
   ════════════════════════════════════════════════════ */
function getCart() { try { return JSON.parse(localStorage.getItem(CART_KEY)) || []; } catch(e) { return []; } }
function saveCart(c) { localStorage.setItem(CART_KEY, JSON.stringify(c)); updateAllBadges(); }

function addToCart(product) {
  var cart = getCart();
  var idx  = cart.findIndex(function(i) { return String(i.id) === String(product.id); });
  if (idx > -1) { cart[idx].qty = Math.min((cart[idx].qty || 1) + 1, 10); }
  else { cart.push(Object.assign({}, product, { qty: 1 })); }
  saveCart(cart);
  toast((product.name || 'Item') + ' added to cart!', 'success');
}
window.addToCart = addToCart;

function removeFromCart(id) { saveCart(getCart().filter(function(i) { return String(i.id) !== String(id); })); renderCart(); }
function changeCartQty(id, delta) {
  var cart = getCart();
  var idx  = cart.findIndex(function(i) { return String(i.id) === String(id); });
  if (idx < 0) return;
  cart[idx].qty = Math.max(1, Math.min(10, (cart[idx].qty || 1) + delta));
  saveCart(cart); renderCart();
}

function renderCart() {
  var cart   = getCart();
  var body   = document.getElementById('cartBody');
  var footer = document.getElementById('cartFooter');
  if (!cart.length) {
    body.innerHTML = emptyState('🛒','Your cart is empty','Add some beautiful gifts!');
    footer.style.display = 'none'; return;
  }
  body.innerHTML = '';
  var total = 0;
  cart.forEach(function(item) {
    total += parseFloat(item.price) * (item.qty || 1);
    var div = document.createElement('div');
    div.className = 'ac-item';
    div.innerHTML =
      '<div class="ac-item-img">' + (item.image_url ? '<img src="' + esc(item.image_url) + '" alt="' + esc(item.name||'') + '" loading="lazy">' : '🎁') + '</div>' +
      '<div class="ac-item-info">' +
        '<p class="ac-item-name">' + esc(item.name||'') + '</p>' +
        '<p class="ac-item-meta">' + esc(item.category||'') + '</p>' +
        '<p class="ac-item-price">' + fmtPrice(item.price) + (item.discount_price ? '<span class="ac-item-orig">' + fmtPrice(item.discount_price) + '</span>' : '') + '</p>' +
        '<div class="ac-item-actions">' +
          '<div class="ac-qty">' +
            '<button class="ac-qty-btn" data-id="' + item.id + '" data-d="-1">−</button>' +
            '<span class="ac-qty-num">' + (item.qty||1) + '</span>' +
            '<button class="ac-qty-btn" data-id="' + item.id + '" data-d="1">+</button>' +
          '</div>' +
          '<button class="ac-remove" data-id="' + item.id + '">Remove</button>' +
        '</div>' +
      '</div>';
    body.appendChild(div);
  });
  body.querySelectorAll('.ac-qty-btn').forEach(function(b) { b.onclick = function() { changeCartQty(b.dataset.id, +b.dataset.d); }; });
  body.querySelectorAll('.ac-remove').forEach(function(b) { b.onclick = function() { removeFromCart(b.dataset.id); }; });
  document.getElementById('cartTotal').textContent = fmtPrice(total);
  footer.style.display = 'block';
}

document.getElementById('cartWhatsapp').onclick = function() {
  var cart = getCart();
  if (!cart.length) return;
  var msg = 'Hello Aakar Creatives! 🎁\n\nI would like to order:\n\n';
  var total = 0;
  cart.forEach(function(i) {
    var lineTotal = parseFloat(i.price) * (i.qty || 1);
    total += lineTotal;
    msg += '• *' + i.name + '* × ' + (i.qty||1) + ' — ' + fmtPrice(lineTotal) + '\n';
  });
  msg += '\n*Total: ' + fmtPrice(total) + '*\n\nKindly confirm and guide me. 😊';
  window.open('https://wa.me/' + WA_NUMBER + '?text=' + encodeURIComponent(msg), '_blank');
};

/* ════════════════════════════════════════════════════
   WISHLIST  (localStorage)
   ════════════════════════════════════════════════════ */
function getWishlist() { try { return JSON.parse(localStorage.getItem(WISH_KEY)) || []; } catch(e) { return []; } }
function saveWishlist(w) { localStorage.setItem(WISH_KEY, JSON.stringify(w)); updateAllBadges(); }

function toggleWishlistItem(product) {
  var w   = getWishlist();
  var idx = w.findIndex(function(i) { return String(i.id) === String(product.id); });
  if (idx > -1) { w.splice(idx, 1); saveWishlist(w); toast('Removed from wishlist'); return false; }
  w.push(product); saveWishlist(w); toast('Added to wishlist ♡', 'success'); return true;
}
window.toggleWishlist = toggleWishlistItem;
window.isWishlisted   = function(id) { return getWishlist().some(function(i) { return String(i.id) === String(id); }); };

function removeFromWishlist(id) { saveWishlist(getWishlist().filter(function(i) { return String(i.id) !== String(id); })); renderWishlist(); }

function renderWishlist() {
  var w    = getWishlist();
  var body = document.getElementById('wishlistBody');
  var foot = document.getElementById('wishlistFooter');
  if (!w.length) {
    body.innerHTML = emptyState('♡','Your wishlist is empty','Tap the heart on any gift!');
    foot.style.display = 'none'; return;
  }
  body.innerHTML = '';
  w.forEach(function(item) {
    var div = document.createElement('div');
    div.className = 'ac-item';
    div.innerHTML =
      '<div class="ac-item-img">' + (item.image_url ? '<img src="' + esc(item.image_url) + '" alt="' + esc(item.name||'') + '" loading="lazy">' : '🎁') + '</div>' +
      '<div class="ac-item-info">' +
        '<p class="ac-item-name">' + esc(item.name||'') + '</p>' +
        '<p class="ac-item-meta">' + esc(item.category||'') + '</p>' +
        '<p class="ac-item-price">' + fmtPrice(item.price) + '</p>' +
        '<div class="ac-item-actions">' +
          '<button class="ac-add-cart-btn" data-id="' + item.id + '">+ Add to Cart</button>' +
          '<button class="ac-remove" data-id="' + item.id + '">Remove</button>' +
        '</div>' +
      '</div>';
    body.appendChild(div);
  });
  body.querySelectorAll('.ac-add-cart-btn').forEach(function(b) {
    var it = w.find(function(i) { return String(i.id) === b.dataset.id; });
    b.onclick = function() { if (it) addToCart(it); };
  });
  body.querySelectorAll('.ac-remove').forEach(function(b) { b.onclick = function() { removeFromWishlist(b.dataset.id); }; });
  foot.style.display = 'block';
}

document.getElementById('wishlistToCart').onclick = function() {
  getWishlist().forEach(function(item) { addToCart(item); });
  saveWishlist([]);
  renderWishlist();
  closeOverlay('wishlistOverlay');
  setTimeout(function() { renderCart(); openOverlay('cartOverlay'); }, 350);
};

/* ════════════════════════════════════════════════════
   BADGES
   ════════════════════════════════════════════════════ */
function updateAllBadges() {
  var cartCount = getCart().reduce(function(s,i) { return s + (i.qty||1); }, 0);
  var wishCount = getWishlist().length;

  /* Header cart badge */
  var cb = document.getElementById('cartBadgeHeader');
  if (cb) { cb.textContent = cartCount; cb.style.display = cartCount > 0 ? 'flex' : 'none'; }

  /* Header wish badge */
  var wb = document.getElementById('wishBadgeHeader');
  if (wb) { wb.textContent = wishCount; wb.style.display = wishCount > 0 ? 'flex' : 'none'; }

  /* Mobile badges */
  var mnpCB = document.getElementById('mnpCartBadge');
  if (mnpCB) { mnpCB.textContent = cartCount; mnpCB.style.display = cartCount > 0 ? 'flex' : 'none'; }
  var mnpWB = document.getElementById('mnpWishBadge');
  if (mnpWB) { mnpWB.textContent = wishCount; mnpWB.style.display = wishCount > 0 ? 'flex' : 'none'; }

  var mnpCBtn = document.getElementById('mnpCartBtn');
  var mnpWBtn = document.getElementById('mnpWishBtn');
  if (mnpCBtn) mnpCBtn.classList.toggle('active', cartCount > 0);
  if (mnpWBtn) mnpWBtn.classList.toggle('active', wishCount > 0);
}
window.updateAllBadges = updateAllBadges;

/* ════════════════════════════════════════════════════
   AUTH
   ════════════════════════════════════════════════════ */
function getUser() { try { return JSON.parse(localStorage.getItem(USER_KEY)); } catch(e) { return null; } }
function saveUser(u) { localStorage.setItem(USER_KEY, JSON.stringify(u)); updateMobileAuthUI(u); }
function clearUser() { localStorage.removeItem(USER_KEY); updateMobileAuthUI(null); }

function updateMobileAuthUI(user) {
  var lo = document.getElementById('mnpLoggedOut');
  var li = document.getElementById('mnpLoggedIn');
  if (user) {
    if (lo) lo.style.display = 'none';
    if (li) li.classList.add('show');
    var av = document.getElementById('mnpAvatar');
    var un = document.getElementById('mnpUserName');
    if (av) av.textContent = user.name ? user.name[0].toUpperCase() : 'A';
    if (un) un.textContent = user.name || 'Member';
  } else {
    if (lo) lo.style.display = 'block';
    if (li) li.classList.remove('show');
  }
}

function renderAuthModal(user) {
  var li   = document.getElementById('authLoggedIn');
  var tabs = document.querySelector('.ac-auth-tabs');
  var sf   = document.getElementById('signinForm');
  var sup  = document.getElementById('signupForm');
  if (user) {
    if (sf)   sf.style.display   = 'none';
    if (sup)  sup.style.display  = 'none';
    if (tabs) tabs.style.display = 'none';
    if (li)   li.style.display   = 'block';
    var av = document.getElementById('authAvatar');
    var wm = document.getElementById('authWelcomeMsg');
    if (av) av.textContent = user.name ? user.name[0].toUpperCase() : 'A';
    if (wm) wm.textContent = 'Welcome, ' + user.name + '!';
  } else {
    if (li)   li.style.display   = 'none';
    if (tabs) tabs.style.display = 'flex';
    switchAuthTab('signin');
  }
}

function switchAuthTab(tab) {
  document.querySelectorAll('.ac-auth-tab').forEach(function(t) {
    t.classList.toggle('active', t.dataset.tab === tab);
  });
  var sf  = document.getElementById('signinForm');
  var sup = document.getElementById('signupForm');
  if (sf)  sf.style.display  = tab === 'signin'  ? 'block' : 'none';
  if (sup) sup.style.display = tab === 'signup' ? 'block' : 'none';
  /* Clear messages */
  ['signinMsg','signupMsg'].forEach(function(id) {
    var m = document.getElementById(id); if (m) { m.textContent = ''; m.className = 'ac-auth-msg'; }
  });
}

function showAuthMsg(id, msg, type) {
  var m = document.getElementById(id);
  if (m) { m.textContent = msg; m.className = 'ac-auth-msg ' + (type || 'error'); }
}

document.querySelectorAll('.ac-auth-tab').forEach(function(t) { t.onclick = function() { switchAuthTab(t.dataset.tab); }; });
document.querySelectorAll('.ac-tab-switch').forEach(function(a) {
  a.addEventListener('click', function(e) { e.preventDefault(); switchAuthTab(a.dataset.tab); });
});
document.querySelectorAll('.ac-pw-toggle').forEach(function(btn) {
  btn.onclick = function() {
    var inp = document.getElementById(btn.dataset.target);
    if (inp) inp.type = inp.type === 'password' ? 'text' : 'password';
  };
});

/* Sign In */
document.getElementById('signinForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  var phone = document.getElementById('siPhone').value.trim();
  var pwd   = document.getElementById('siPassword').value;
  if (!phone || !pwd) { showAuthMsg('signinMsg','Please fill in all fields.'); return; }
  var btn = e.target.querySelector('button[type=submit]');
  btn.disabled = true; btn.textContent = 'Signing in…';
  try {
    var res  = await fetch('auth-api.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'signin',phone:'+91'+phone,password:pwd})});
    var data = await res.json();
    if (data.success) { saveUser(data.user); toast('Welcome back, '+data.user.name+'!','success'); closeOverlay('authOverlay'); }
    else { showAuthMsg('signinMsg', data.message || 'Invalid credentials.'); }
  } catch(err) { showAuthMsg('signinMsg','Connection error. Please try again.'); }
  btn.disabled = false; btn.textContent = 'Sign In';
});

/* Sign Up */
document.getElementById('signupForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  var name  = document.getElementById('suName').value.trim();
  var phone = document.getElementById('suPhone').value.trim();
  var email = document.getElementById('suEmail').value.trim();
  var city  = document.getElementById('suCity').value.trim();
  var pwd   = document.getElementById('suPassword').value;
  if (!name || !phone || !pwd) { showAuthMsg('signupMsg','Please fill required fields.'); return; }
  if (phone.length !== 10) { showAuthMsg('signupMsg','Enter a valid 10-digit mobile number.'); return; }
  if (pwd.length < 6) { showAuthMsg('signupMsg','Password must be at least 6 characters.'); return; }
  var btn = e.target.querySelector('button[type=submit]');
  btn.disabled = true; btn.textContent = 'Creating account…';
  try {
    var res  = await fetch('auth-api.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'signup',name,phone:'+91'+phone,email,city,password:pwd})});
    var data = await res.json();
    if (data.success) {
      saveUser(data.user);
      showAuthMsg('signupMsg','Account created! Welcome 🎉','success');
      setTimeout(function() { closeOverlay('authOverlay'); toast('Welcome to Aakar Creatives 🎁','success'); }, 1000);
    } else { showAuthMsg('signupMsg', data.message || 'Something went wrong.'); }
  } catch(err) { showAuthMsg('signupMsg','Connection error. Please try again.'); }
  btn.disabled = false; btn.textContent = 'Create Account';
});

async function doSignout() {
  try { await fetch('auth-api.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'signout'})}); } catch(e) {}
  clearUser(); closeOverlay('authOverlay'); closeNav(); toast('Signed out successfully.');
}
document.getElementById('authSignout').onclick = doSignout;

/* ── Helpers ───────────────────────────────────────── */
function emptyState(icon, title, sub) {
  return '<div class="ac-empty"><div class="ac-empty-icon">' + icon + '</div><p class="ac-empty-title">' + title + '</p><p>' + sub + '</p></div>';
}

/* ── INIT ──────────────────────────────────────────── */
updateAllBadges();
updateMobileAuthUI(getUser());
buildSearchCatChips();

})();
</script>