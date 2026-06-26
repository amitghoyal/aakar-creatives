<?php
/**
 * track.php — lightweight analytics beacon receiver
 * Called via navigator.sendBeacon() from the frontend.
 */

require_once 'includes/db.php';

$event_type  = $_POST['event'] ?? '';
$product_id  = isset($_POST['product_id'])  ? (int)$_POST['product_id']  : null;
$category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;

$allowed = ['product_view','whatsapp_click','category_view','homepage_visit'];
if (!in_array($event_type, $allowed, true)) {
    http_response_code(204);
    exit;
}

$ip_hash   = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '');
$ua        = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 300);
$referrer  = substr($_SERVER['HTTP_REFERER'] ?? '', 0, 300);

try {
    $stmt = $pdo->prepare(
        "INSERT INTO analytics_events
            (event_type, product_id, category_id, ip_hash, user_agent, referrer)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([$event_type, $product_id ?: null, $category_id ?: null, $ip_hash, $ua, $referrer]);

    // Increment product views
    if ($event_type === 'product_view' && $product_id) {
        $pdo->prepare("UPDATE products SET views = views + 1 WHERE id = ?")->execute([$product_id]);
    }
    if ($event_type === 'whatsapp_click' && $product_id) {
        $pdo->prepare("UPDATE products SET whatsapp_clicks = whatsapp_clicks + 1 WHERE id = ?")->execute([$product_id]);
    }
} catch (PDOException $e) {
    error_log('[Track] ' . $e->getMessage());
}

http_response_code(204);