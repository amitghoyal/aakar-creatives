<?php
/**
 * search-api.php
 * GET ?q=keyword
 * Returns JSON array of matching active products with primary image.
 * Place in your project root (same level as index.php).
 */

require_once 'includes/db.php';   // your existing PDO $pdo connection

header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

$q = trim($_GET['q'] ?? '');
if(strlen($q) < 2){
    echo json_encode([]);
    exit;
}

// Sanitise — allow letters, numbers, spaces, hyphens
$q = preg_replace('/[^\p{L}\p{N}\s\-]/u', '', $q);
$like = '%'.$q.'%';

try {
    $stmt = $pdo->prepare("
        SELECT
            p.id,
            p.name,
            p.slug,
            p.price,
            p.discount_price,
            p.short_description,
            c.name   AS category,
            c.slug   AS category_slug,
            pm.file_url AS image_url
        FROM products p
        JOIN categories c ON c.id = p.category_id
        LEFT JOIN product_media pm
            ON pm.product_id = p.id AND pm.is_primary = 1
        WHERE p.status = 'active'
          AND (
              p.name             LIKE :q1
           OR p.tags             LIKE :q2
           OR p.short_description LIKE :q3
           OR c.name             LIKE :q4
          )
        ORDER BY
            CASE WHEN p.name LIKE :q5 THEN 0 ELSE 1 END,
            p.is_featured DESC,
            p.views DESC
        LIMIT 12
    ");
    $stmt->execute([
        ':q1' => $like, ':q2' => $like,
        ':q3' => $like, ':q4' => $like,
        ':q5' => $like,
    ]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Cast types for JSON
    foreach($rows as &$r){
        $r['id']             = (int)$r['id'];
        $r['price']          = (float)$r['price'];
        $r['discount_price'] = $r['discount_price'] !== null ? (float)$r['discount_price'] : null;
    }
    unset($r);

    echo json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch(PDOException $e){
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}