<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/includes/db.php';

define('APP_NAME', 'Aakar Creatives');
define('APP_VER', '3.2.0');
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', '/uploads/');

function db(): PDO { global $pdo; return $pdo; }

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function csrf_field(): string { return '<input type="hidden" name="_csrf" value="'.csrf_token().'">'; }
function verify_csrf(): void {
    if (!isset($_POST['_csrf']) || !hash_equals(csrf_token(), $_POST['_csrf'])) {
        http_response_code(403); die('CSRF token mismatch.');
    }
}

function h(mixed $v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function redirect(string $url): never { header('Location: '.$url); exit; }
function flash_set(string $msg, string $type = 'success'): void { $_SESSION['flash'] = ['msg'=>$msg,'type'=>$type]; }
function flash_get(): array { $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f; }
function time_ago(string $dt): string {
    $diff = time() - strtotime($dt);
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return round($diff/60).'m ago';
    if ($diff < 86400) return round($diff/3600).'h ago';
    if ($diff < 604800) return round($diff/86400).'d ago';
    return date('d M Y', strtotime($dt));
}
function log_action(string $action, string $type = '', int $id = 0, array $details = []): void {
    if (empty($_SESSION['admin_id'])) return;
    if (!$action) return;
    try { db()->prepare('INSERT INTO admin_activity_log (admin_id,action,target_type,target_id,details,ip_address) VALUES (?,?,?,?,?,?)')->execute([
            $_SESSION['admin_id'], $action, $type ?: null, $id ?: null,
            $details ? json_encode($details) : null, $_SERVER['REMOTE_ADDR'] ?? null,
        ]); }
catch (Exception) {}
}

function statusBadge(string $s): string {
    $map = [
        'active'=>'badge-green','confirmed'=>'badge-green','delivered'=>'badge-green','completed'=>'badge-green',
        'draft'=>'badge-amber','contacted'=>'badge-amber','pending'=>'badge-amber',
        'new_inquiry'=>'badge-rose','in_production'=>'badge-blue','designing'=>'badge-blue',
        'dispatched'=>'badge-blue','upcoming'=>'badge-blue',
        'archived'=>'badge-slate','expired'=>'badge-slate','cancelled'=>'badge-slate','inactive'=>'badge-slate',
    ];
    return $map[strtolower($s)] ?? 'badge-slate';
}
function offer_status(string $start, string $end, int $active): string {
    if (!$active) return 'Inactive';
    $today = date('Y-m-d');
    if ($today < $start) return 'Upcoming';
    if ($today > $end) return 'Expired';
    return 'Active';
}
function make_slug(string $s): string { return strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($s)), '-')); }
function unique_slug(string $table, string $base, int $exclude = 0): string {
    $slug = $base; $n = 1;
    $check = db()->prepare("SELECT COUNT(*) FROM {$table} WHERE slug=? AND id!=?");
    while (true) { $check->execute([$slug, $exclude]); if (!(int)$check->fetchColumn()) return $slug; $slug = $base.'-'.(++$n); }
}
function validate_email(string $email): bool { return filter_var($email, FILTER_VALIDATE_EMAIL) !== false; }
function validate_phone(string $phone): bool { return preg_match('/^\+?[0-9\s\-]{7,15}$/', $phone) === 1; }
function db_exists(string $table, string $col, $val): bool {
    $stmt = db()->prepare("SELECT 1 FROM {$table} WHERE {$col}=?");
    $stmt->execute([$val]);
    return (bool)$stmt->fetchColumn();
}
function handle_upload(string $input_name, string $folder = 'products'): ?string {
    if (empty($_FILES[$input_name]['tmp_name'])) return null;
    $file = $_FILES[$input_name];
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
    if (!in_array(mime_content_type($file['tmp_name']), $allowed)) return null;
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = uniqid($folder.'_', true).'.'.$ext;
    $dir = UPLOAD_DIR . $folder . '/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    if (!move_uploaded_file($file['tmp_name'], $dir.$name)) return null;
    return UPLOAD_URL . $folder . '/' . $name;
}
function handle_multiple_uploads(string $input_name, string $folder = 'products'): array {
    $urls = [];
    if (empty($_FILES[$input_name]['tmp_name'])) return $urls;
    $files = $_FILES[$input_name];
    $count = is_array($files['tmp_name']) ? count($files['tmp_name']) : 1;
    if (!is_array($files['tmp_name'])) {
        $files['tmp_name'] = [$files['tmp_name']];
        $files['name']     = [$files['name']];
        $files['error']    = [$files['error']];
    }
    $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
    $dir = UPLOAD_DIR . $folder . '/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    for ($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
        if (!in_array(mime_content_type($files['tmp_name'][$i]), $allowed)) continue;
        $ext  = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
        $name = uniqid($folder.'_', true).'.'.$ext;
        if (move_uploaded_file($files['tmp_name'][$i], $dir.$name))
            $urls[] = UPLOAD_URL . $folder . '/' . $name;
    }
    return $urls;
}

// ── AUTH ───────────────────────────────────────────────────────
$login_error = '';
if (($_POST['action'] ?? '') === 'login') {
    verify_csrf();
    $stmt = db()->prepare('SELECT * FROM admins WHERE email=? AND is_active=1 LIMIT 1');
    $stmt->execute([trim($_POST['email'] ?? '')]);
    $row = $stmt->fetch(); $pw = $_POST['password'] ?? ''; $ok = false;
    if ($row) {
        if (str_starts_with($row['password_hash'], '$PLACEHOLDER')) {
            $ok = ($pw === 'aakar@2024');
            if ($ok) db()->prepare('UPDATE admins SET password_hash=?,last_login_at=NOW() WHERE id=?')->execute([password_hash($pw, PASSWORD_BCRYPT), $row['id']]);
        } else {
            $ok = password_verify($pw, $row['password_hash']);
            if ($ok) db()->prepare('UPDATE admins SET last_login_at=NOW() WHERE id=?')->execute([$row['id']]);
        }
    }
    if ($ok) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $row['id']; $_SESSION['admin_name'] = $row['name']; $_SESSION['admin_role'] = $row['role'];
        log_action('auth.login'); redirect('?page=dashboard');
    }
    $login_error = 'Invalid email or password.';
}
if (isset($_GET['logout'])) { log_action('auth.logout'); session_destroy(); redirect('?'); }

$logged_in  = isset($_SESSION['admin_id']);
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$admin_role = $_SESSION['admin_role'] ?? 'admin';
$page       = $_GET['page'] ?? ($logged_in ? 'dashboard' : 'login');
if (!$logged_in && $page !== 'login') redirect('?');

// ── POST MUTATIONS ─────────────────────────────────────────────
if ($logged_in && $_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'add_product') {
        $name = trim($_POST['name'] ?? '');
        $cat_id = (int)($_POST['category_id'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        if (strlen($name) < 2 || strlen($name) > 200) { flash_set('Product name must be 2–200 chars.', 'error'); redirect('?page=products'); }
        if ($cat_id <= 0 || !db_exists('categories', 'id', $cat_id)) { flash_set('Valid category required.', 'error'); redirect('?page=products'); }
        if ($price < 0) { flash_set('Price cannot be negative.', 'error'); redirect('?page=products'); }
        $slug = unique_slug('products', make_slug($name));
        $img  = handle_upload('primary_image', 'products');
        $id = db()->prepare('INSERT INTO products (category_id,badge_id,name,slug,short_description,full_description,product_story,price,discount_price,delivery_days,tags,whatsapp_message,is_featured,is_new_arrival,is_trending,is_bestseller,in_stock,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')->execute([
            $cat_id, ($_POST['badge_id']??'')!==''?(int)$_POST['badge_id']:null, $name, $slug,
            trim($_POST['short_description']??'')?:null, trim($_POST['full_description']??'')?:null,
            trim($_POST['product_story']??'')?:null, $price,
            ($_POST['discount_price']??'')!==''?(float)$_POST['discount_price']:null,
            trim($_POST['delivery_days']??'3-5 Working Days'),
            trim($_POST['tags']??'')?:null, trim($_POST['whatsapp_message']??'')?:null,
            isset($_POST['is_featured'])?1:0, isset($_POST['is_new_arrival'])?1:0,
            isset($_POST['is_trending'])?1:0, isset($_POST['is_bestseller'])?1:0,
            isset($_POST['in_stock'])?1:0, $_POST['status']??'draft',
        ]) ? (int)db()->lastInsertId() : 0;
        $imgs = handle_multiple_uploads('product_images', 'products');
        if ($img) array_unshift($imgs, $img);
        foreach ($imgs as $i => $url) {
            db()->prepare('INSERT INTO product_media (product_id,file_url,is_primary,sort_order) VALUES (?,?,?,?)')->execute([$id, $url, $i===0?1:0, $i]);
        }
        if (!empty($_POST['occasion_ids']) && is_array($_POST['occasion_ids'])) {
            foreach ($_POST['occasion_ids'] as $oid) {
                if(db_exists('occasions', 'id', $oid)) db()->prepare('INSERT IGNORE INTO product_occasions VALUES (?,?)')->execute([$id, (int)$oid]);
            }
        }
        log_action('product.create','product',$id); flash_set('Product created.'); redirect('?page=products');
    }

    if ($action === 'edit_product' && isset($_POST['id'])) {
        $pid = (int)$_POST['id'];
        $name = trim($_POST['name'] ?? '');
        $cat_id = (int)($_POST['category_id'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        if (strlen($name) < 2 || strlen($name) > 200) { flash_set('Product name must be 2–200 chars.', 'error'); redirect("?page=products&edit={$pid}"); }
        if ($cat_id <= 0 || !db_exists('categories', 'id', $cat_id)) { flash_set('Valid category required.', 'error'); redirect("?page=products&edit={$pid}"); }
        if ($price < 0) { flash_set('Price cannot be negative.', 'error'); redirect("?page=products&edit={$pid}"); }
        $slug = unique_slug('products', make_slug($name), $pid);
        $img = handle_upload('primary_image', 'products');
        db()->prepare('UPDATE products SET category_id=?,badge_id=?,name=?,slug=?,short_description=?,full_description=?,product_story=?,price=?,discount_price=?,delivery_days=?,tags=?,whatsapp_message=?,is_featured=?,is_new_arrival=?,is_trending=?,is_bestseller=?,in_stock=?,status=? WHERE id=?')->execute([
            $cat_id, ($_POST['badge_id']??'')!==''?(int)$_POST['badge_id']:null, $name, $slug,
            trim($_POST['short_description']??'')?:null, trim($_POST['full_description']??'')?:null,
            trim($_POST['product_story']??'')?:null, $price,
            ($_POST['discount_price']??'')!==''?(float)$_POST['discount_price']:null,
            trim($_POST['delivery_days']??'3-5 Working Days'),
            trim($_POST['tags']??'')?:null, trim($_POST['whatsapp_message']??'')?:null,
            isset($_POST['is_featured'])?1:0, isset($_POST['is_new_arrival'])?1:0,
            isset($_POST['is_trending'])?1:0, isset($_POST['is_bestseller'])?1:0,
            isset($_POST['in_stock'])?1:0, $_POST['status']??'draft', $pid,
        ]);
        $imgs = handle_multiple_uploads('product_images', 'products');
        if ($img) array_unshift($imgs, $img);
        if (!empty($imgs)) {
            $sort = (int)db()->query("SELECT COALESCE(MAX(sort_order),0)+1 FROM product_media WHERE product_id=$pid")->fetchColumn();
            foreach ($imgs as $i => $url) {
                db()->prepare('INSERT INTO product_media (product_id,file_url,is_primary,sort_order) VALUES (?,?,0,?)')->execute([$pid, $url, $sort+$i]);
            }
        }
        db()->prepare('DELETE FROM product_occasions WHERE product_id=?')->execute([$pid]);
        if (!empty($_POST['occasion_ids']) && is_array($_POST['occasion_ids'])) {
            foreach ($_POST['occasion_ids'] as $oid) db()->prepare('INSERT IGNORE INTO product_occasions VALUES (?,?)')->execute([$pid,(int)$oid]);
        }
        log_action('product.edit','product',$pid); flash_set('Product updated.'); redirect('?page=products');
    }

    if ($action === 'delete_product_image' && isset($_POST['media_id'])) {
        $mid = (int)$_POST['media_id'];
        $m = db()->prepare('SELECT file_url FROM product_media WHERE id=?'); $m->execute([$mid]); $row = $m->fetch();
        if ($row) { @unlink(str_replace(UPLOAD_URL, UPLOAD_DIR, $row['file_url'])); }
        db()->prepare('DELETE FROM product_media WHERE id=?')->execute([$mid]);
        flash_set('Image removed.'); redirect('?page=products&edit='.($_POST['product_id']??0));
    }

    // VARIANTS
    if ($action === 'add_variant' && isset($_POST['product_id'])) {
        $vpid  = (int)$_POST['product_id'];
        $sid   = ($_POST['size_id']??'')!==''?(int)$_POST['size_id']:null;
        $cid   = ($_POST['color_id']??'')!==''?(int)$_POST['color_id']:null;
        $price = ($_POST['price_override']??'')!==''?(float)$_POST['price_override']:null;
        $disc  = ($_POST['discount_price_override']??'')!==''?(float)$_POST['discount_price_override']:null;
        $stock = (int)($_POST['stock_qty']??-1);
        try {
            db()->prepare('INSERT INTO product_variants (product_id,size_id,color_id,price_override,discount_price_override,stock_qty,is_active,sort_order) VALUES (?,?,?,?,?,?,1,0)')
                ->execute([$vpid,$sid,$cid,$price,$disc,$stock]);
            flash_set('Variant added.');
        } catch (Exception $e) {
            flash_set('That size/color combination already exists.','error');
        }
        redirect('?page=products&variants='.$vpid);
    }

    if ($action === 'update_variant_stock' && isset($_POST['id'])) {
        $vid = (int)$_POST['id'];
        $vpid = (int)$_POST['product_id'];
        $stock = (int)$_POST['stock_qty'];
        db()->prepare('UPDATE product_variants SET stock_qty=? WHERE id=?')->execute([$stock,$vid]);
        flash_set('Stock updated.'); redirect('?page=products&variants='.$vpid);
    }

    // CATEGORIES
    if ($action === 'add_category') {
        $name = trim($_POST['name'] ?? '');
        if (strlen($name) < 2) { flash_set('Category name too short.', 'error'); redirect('?page=categories'); }
        $slug = unique_slug('categories', make_slug($name));
        $img  = handle_upload('image', 'categories');
        $cid = db()->prepare('INSERT INTO categories (name,slug,description,image_url,is_featured,sort_order) VALUES (?,?,?,?,?,?)')->execute([
            $name, $slug, trim($_POST['description']??'')?:null, $img,
            isset($_POST['is_featured'])?1:0, (int)($_POST['sort_order']??0),
        ]) ? (int)db()->lastInsertId() : 0;
        log_action('category.create','category',$cid); flash_set('Category created.'); redirect('?page=categories');
    }
    if ($action === 'edit_category' && isset($_POST['id'])) {
        $cid = (int)$_POST['id'];
        $name = trim($_POST['name'] ?? '');
        if (strlen($name) < 2) { flash_set('Category name too short.', 'error'); redirect("?page=categories&edit={$cid}"); }
        $img = handle_upload('image', 'categories');
        $set = 'name=?,description=?,is_featured=?,sort_order=?';
        $params = [$name, trim($_POST['description']??'')?:null, isset($_POST['is_featured'])?1:0, (int)($_POST['sort_order']??0)];
        if ($img) { $set .= ',image_url=?'; $params[] = $img; }
        $params[] = $cid;
        db()->prepare("UPDATE categories SET {$set} WHERE id=?")->execute($params);
        log_action('category.edit','category',$cid); flash_set('Category updated.'); redirect('?page=categories');
    }

    // BADGES
    if ($action === 'add_badge') {
        $name = trim($_POST['name'] ?? '');
        if (!$name) { flash_set('Badge name required.', 'error'); redirect('?page=badges'); }
        db()->prepare('INSERT INTO badges (name,color_hex,icon) VALUES (?,?,?)')->execute([$name, $_POST['color_hex']??'#b85c6e', trim($_POST['icon']??'')?:null]);
        flash_set('Badge created.'); redirect('?page=badges');
    }
    if ($action === 'edit_badge' && isset($_POST['id'])) {
        $name = trim($_POST['name'] ?? '');
        if (!$name) { flash_set('Badge name required.', 'error'); redirect('?page=badges'); }
        db()->prepare('UPDATE badges SET name=?,color_hex=?,icon=? WHERE id=?')->execute([$name, $_POST['color_hex']??'#b85c6e', trim($_POST['icon']??'')?:null, (int)$_POST['id']]);
        flash_set('Badge updated.'); redirect('?page=badges');
    }

    // INQUIRIES
    if ($action === 'add_inquiry') {
        $phone = preg_replace('/\s+/', '', $_POST['phone'] ?? '');
        if (!validate_phone($phone)) { flash_set('Invalid phone.', 'error'); redirect('?page=inquiries'); }
        $c = db()->prepare('SELECT id FROM customers WHERE phone=? LIMIT 1'); $c->execute([$phone]); $cust = $c->fetch();
        if ($cust) { $cust_id = $cust['id']; }
        else {
            db()->prepare('INSERT INTO customers (name,phone,city) VALUES (?,?,?)')->execute([trim($_POST['customer_name']??'Unknown'),$phone,trim($_POST['city']??'')?:null]);
            $cust_id=(int)db()->lastInsertId();
        }
        $pid = ($_POST['product_id']??'') !== '' ? (int)$_POST['product_id'] : null;
        if ($pid && !db_exists('products', 'id', $pid)) $pid = null;
        $iid = db()->prepare('INSERT INTO inquiries (customer_id,product_id,source,status,notes,followup_date,wa_clicked_at) VALUES (?,?,?,?,?,?,?)')->execute([
            $cust_id, $pid, $_POST['source']??'whatsapp', $_POST['status']??'new_inquiry',
            trim($_POST['notes']??'')?:null,
            ($_POST['followup_date']??'')!==''?$_POST['followup_date']:null,
            ($_POST['wa_clicked_at']??'')!==''?$_POST['wa_clicked_at']:null,
        ]) ? (int)db()->lastInsertId() : 0;
        log_action('inquiry.create','inquiry',$iid); flash_set('Inquiry logged.'); redirect('?page=inquiries');
    }
    if ($action === 'edit_inquiry' && isset($_POST['id'])) {
        $iid = (int)$_POST['id'];
        $pid = ($_POST['product_id']??'') !== '' ? (int)$_POST['product_id'] : null;
        if ($pid && !db_exists('products', 'id', $pid)) $pid = null;
        db()->prepare('UPDATE inquiries SET product_id=?,source=?,status=?,notes=?,followup_date=? WHERE id=?')->execute([
            $pid, $_POST['source']??'whatsapp', $_POST['status']??'new_inquiry',
            trim($_POST['notes']??'')?:null,
            ($_POST['followup_date']??'')!==''?$_POST['followup_date']:null, $iid,
        ]);
        log_action('inquiry.edit','inquiry',$iid); flash_set('Inquiry updated.'); redirect('?page=inquiries');
    }
    if ($action === 'update_inquiry_status' && isset($_POST['id'])) {
        $valid = ['new_inquiry','contacted','confirmed','designing','completed','delivered','cancelled'];
        $st = in_array($_POST['status'],$valid)?$_POST['status']:'new_inquiry';
        db()->prepare('UPDATE inquiries SET status=? WHERE id=?')->execute([$st,(int)$_POST['id']]);
        flash_set('Status updated.'); redirect('?page=inquiries');
    }

    // CUSTOMERS
    if ($action === 'add_customer') {
        $name = trim($_POST['name'] ?? '');
        $phone = preg_replace('/\s+/', '', $_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        if (!$name || !validate_phone($phone)) { flash_set('Valid name and phone required.', 'error'); redirect('?page=customers'); }
        if ($email && !validate_email($email)) { flash_set('Invalid email.', 'error'); redirect('?page=customers'); }
        if (db_exists('customers', 'phone', $phone)) { flash_set('Phone already exists.', 'error'); redirect('?page=customers'); }
        db()->prepare('INSERT INTO customers (name,phone,email,instagram,city,notes) VALUES (?,?,?,?,?,?)')->execute([
            $name, $phone, $email?:null, trim($_POST['instagram']??'')?:null,
            trim($_POST['city']??'')?:null, trim($_POST['notes']??'')?:null,
        ]);
        flash_set('Customer added.'); redirect('?page=customers');
    }
    if ($action === 'edit_customer' && isset($_POST['id'])) {
        $cid = (int)$_POST['id'];
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        if (!$name) { flash_set('Name required.', 'error'); redirect("?page=customers&edit={$cid}"); }
        if ($email && !validate_email($email)) { flash_set('Invalid email.', 'error'); redirect("?page=customers&edit={$cid}"); }
        db()->prepare('UPDATE customers SET name=?,email=?,instagram=?,city=?,notes=? WHERE id=?')->execute([
            $name, $email?:null, trim($_POST['instagram']??'')?:null,
            trim($_POST['city']??'')?:null, trim($_POST['notes']??'')?:null, $cid,
        ]);
        flash_set('Customer updated.'); redirect('?page=customers');
    }

    // ORDERS
    if ($action === 'add_order') {
        $qty=(int)($_POST['quantity']??1); $unit=(float)($_POST['unit_price']??0);
        $disc=(float)($_POST['discount_amount']??0); $final=max(0,($unit*$qty)-$disc);
        if ($qty < 1) { flash_set('Qty must be at least 1.', 'error'); redirect('?page=orders'); }
        if ($unit < 0) { flash_set('Price cannot be negative.', 'error'); redirect('?page=orders'); }
        if (!db_exists('customers', 'id', (int)$_POST['customer_id'])) { flash_set('Valid customer required.', 'error'); redirect('?page=orders'); }
        if (!db_exists('products', 'id', (int)$_POST['product_id'])) { flash_set('Valid product required.', 'error'); redirect('?page=orders'); }
        $oid = db()->prepare('INSERT INTO orders (inquiry_id,customer_id,product_id,quantity,unit_price,discount_amount,final_price,status,customisation,delivery_address,expected_by,admin_notes) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')->execute([
            ($_POST['inquiry_id']??'')!==''?(int)$_POST['inquiry_id']:null,
            (int)$_POST['customer_id'],(int)$_POST['product_id'],$qty,$unit,$disc,$final,
            $_POST['status']??'pending', trim($_POST['customisation']??'')?:null,
            trim($_POST['delivery_address']??'')?:null,
            ($_POST['expected_by']??'')!==''?$_POST['expected_by']:null,
            trim($_POST['admin_notes']??'')?:null,
        ]) ? (int)db()->lastInsertId() : 0;
        // If converted from inquiry, update its status
        if (!empty($_POST['inquiry_id'])) {
            db()->prepare("UPDATE inquiries SET status='confirmed' WHERE id=? AND status='new_inquiry'")->execute([(int)$_POST['inquiry_id']]);
        }
        log_action('order.create','order',$oid); flash_set('Order created.'); redirect('?page=orders');
    }
    if ($action === 'edit_order' && isset($_POST['id'])) {
        $oid=(int)$_POST['id'];
        db()->prepare('UPDATE orders SET status=?,admin_notes=?,expected_by=?,delivered_on=? WHERE id=?')->execute([
            $_POST['status']??'pending', trim($_POST['admin_notes']??'')?:null,
            ($_POST['expected_by']??'')!==''?$_POST['expected_by']:null,
            ($_POST['delivered_on']??'')!==''?$_POST['delivered_on']:null, $oid,
        ]);
        log_action('order.edit','order',$oid); flash_set('Order updated.'); redirect('?page=orders');
    }

    // OFFERS
    if ($action === 'add_offer') {
        $name = trim($_POST['name'] ?? '');
        $start = $_POST['start_date'] ?? date('Y-m-d'); $end = $_POST['end_date'] ?? date('Y-m-d');
        if (!$name) { flash_set('Offer name required.', 'error'); redirect('?page=offers'); }
        if ($end < $start) { flash_set('End date before start date.', 'error'); redirect('?page=offers'); }
        db()->prepare('INSERT INTO offers (name,coupon_code,discount_type,discount_value,min_order_value,max_uses,start_date,end_date,is_active) VALUES (?,?,?,?,?,?,?,?,1)')->execute([
            $name, ($_POST['coupon_code']??'')!==''?strtoupper(trim($_POST['coupon_code'])):null,
            $_POST['discount_type']??'percentage', (float)($_POST['discount_value']??0),
            ($_POST['min_order_value']??'')!==''?(float)$_POST['min_order_value']:null,
            ($_POST['max_uses']??'')!==''?(int)$_POST['max_uses']:null, $start, $end,
        ]);
        flash_set('Offer created.'); redirect('?page=offers');
    }
    if ($action === 'edit_offer' && isset($_POST['id'])) {
        $oid=(int)$_POST['id'];
        $name = trim($_POST['name'] ?? '');
        $start = $_POST['start_date'] ?? date('Y-m-d'); $end = $_POST['end_date'] ?? date('Y-m-d');
        if (!$name) { flash_set('Offer name required.', 'error'); redirect('?page=offers'); }
        if ($end < $start) { flash_set('End date before start date.', 'error'); redirect('?page=offers'); }
        db()->prepare('UPDATE offers SET name=?,coupon_code=?,discount_type=?,discount_value=?,min_order_value=?,max_uses=?,start_date=?,end_date=? WHERE id=?')->execute([
            $name, ($_POST['coupon_code']??'')!==''?strtoupper(trim($_POST['coupon_code'])):null,
            $_POST['discount_type']??'percentage', (float)($_POST['discount_value']??0),
            ($_POST['min_order_value']??'')!==''?(float)$_POST['min_order_value']:null,
            ($_POST['max_uses']??'')!==''?(int)$_POST['max_uses']:null, $start, $end, $oid,
        ]);
        flash_set('Offer updated.'); redirect('?page=offers');
    }

    // TESTIMONIALS
    if ($action === 'add_testimonial') {
        $name = trim($_POST['name'] ?? '');
        $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
        if (!$name) { flash_set('Name required.', 'error'); redirect('?page=testimonials'); }
        db()->prepare('INSERT INTO testimonials (name,instagram,product_id,rating,review,is_approved,is_featured) VALUES (?,?,?,?,?,1,0)')->execute([
            $name, trim($_POST['instagram']??'')?:null,
            ($_POST['product_id']??'')!==''?(int)$_POST['product_id']:null,
            $rating, trim($_POST['review']??''),
        ]);
        flash_set('Review saved.'); redirect('?page=testimonials');
    }
    if ($action === 'edit_testimonial' && isset($_POST['id'])) {
        $tid=(int)$_POST['id'];
        $name = trim($_POST['name'] ?? '');
        $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
        if (!$name) { flash_set('Name required.', 'error'); redirect('?page=testimonials'); }
        db()->prepare('UPDATE testimonials SET name=?,instagram=?,product_id=?,rating=?,review=?,is_approved=?,is_featured=? WHERE id=?')->execute([
            $name, trim($_POST['instagram']??'')?:null,
            ($_POST['product_id']??'')!==''?(int)$_POST['product_id']:null,
            $rating, trim($_POST['review']??''),
            isset($_POST['is_approved'])?1:0, isset($_POST['is_featured'])?1:0, $tid,
        ]);
        flash_set('Review updated.'); redirect('?page=testimonials');
    }

    // WHATSAPP
    if ($action === 'save_wa_number') {
        $phone = preg_replace('/\s+/', '', $_POST['phone_number'] ?? '');
        if (!validate_phone($phone)) { flash_set('Invalid phone.', 'error'); redirect('?page=whatsapp'); }
        db()->prepare('UPDATE whatsapp_settings SET phone_number=? WHERE is_primary=1')->execute([$phone]);
        flash_set('WhatsApp number updated.'); redirect('?page=whatsapp');
    }
    if ($action === 'save_wa_template') {
        db()->prepare('UPDATE whatsapp_templates SET template=?,label=? WHERE id=?')->execute([$_POST['template']??'', trim($_POST['label']??''), (int)($_POST['tmpl_id']??1)]);
        flash_set('Template saved.'); redirect('?page=whatsapp');
    }

    // OCCASIONS
    if ($action === 'add_occasion') {
        $name = trim($_POST['name'] ?? '');
        if (!$name) { flash_set('Name required.', 'error'); redirect('?page=occasions'); }
        $slug = unique_slug('occasions', make_slug($name));
        db()->prepare('INSERT INTO occasions (name,slug,icon_emoji,is_active,sort_order) VALUES (?,?,?,1,?)')->execute([$name, $slug, trim($_POST['icon_emoji']??'')?:null, (int)($_POST['sort_order']??0)]);
        flash_set('Occasion added.'); redirect('?page=occasions');
    }
    if ($action === 'edit_occasion' && isset($_POST['id'])) {
        $oid=(int)$_POST['id'];
        $name = trim($_POST['name'] ?? '');
        if (!$name) { flash_set('Name required.', 'error'); redirect('?page=occasions'); }
        db()->prepare('UPDATE occasions SET name=?,icon_emoji=?,sort_order=?,is_active=? WHERE id=?')->execute([$name, trim($_POST['icon_emoji']??'')?:null, (int)($_POST['sort_order']??0), isset($_POST['is_active'])?1:0, $oid]);
        flash_set('Occasion updated.'); redirect('?page=occasions');
    }

    // HOMEPAGE
    if ($action === 'save_hero') {
        $cfg = json_encode(['headline'=>trim($_POST['headline']??''),'script_line'=>trim($_POST['script_line']??''),'cta_text'=>trim($_POST['cta_text']??''),'cta_url'=>trim($_POST['cta_url']??'#collection')]);
        db()->prepare("UPDATE homepage_sections SET config_json=? WHERE `key`='hero_banner'")->execute([$cfg]);
        flash_set('Hero settings saved.'); redirect('?page=homepage');
    }

    // MEDIA
    if ($action === 'upload_media') {
        $folder = in_array($_POST['folder']??'misc',['products','categories','hero','misc'])?$_POST['folder']:'misc';
        $urls = handle_multiple_uploads('files', $folder);
        foreach ($urls as $url) {
            $fname = basename($url); $fsize = 0;
            if (file_exists(str_replace(UPLOAD_URL,UPLOAD_DIR,$url))) $fsize = round(filesize(str_replace(UPLOAD_URL,UPLOAD_DIR,$url))/1024);
            db()->prepare('INSERT INTO media_library (file_url,file_name,file_type,mime_type,file_size_kb,alt_text,folder,uploaded_by) VALUES (?,?,?,?,?,?,?,?)')->execute([$url,$fname,'image',null,$fsize,null,$folder,$_SESSION['admin_id']]);
        }
        flash_set(count($urls).' file(s) uploaded.'); redirect('?page=media');
    }
    if ($action === 'delete_media' && isset($_POST['id'])) {
        $m = db()->prepare('SELECT file_url FROM media_library WHERE id=?'); $m->execute([(int)$_POST['id']]); $row = $m->fetch();
        if ($row) @unlink(str_replace(UPLOAD_URL,UPLOAD_DIR,$row['file_url']));
        db()->prepare('DELETE FROM media_library WHERE id=?')->execute([(int)$_POST['id']]);
        flash_set('Media deleted.'); redirect('?page=media');
    }

    // ADMINS
    if ($action === 'add_admin' && $admin_role === 'super_admin') {
        $email = strtolower(trim($_POST['email'] ?? ''));
        if (!validate_email($email)) { flash_set('Invalid email.', 'error'); redirect('?page=admins'); }
        if (db_exists('admins', 'email', $email)) { flash_set('Email already exists.', 'error'); redirect('?page=admins'); }
        if (strlen($_POST['password']??'') < 8) { flash_set('Password min 8 chars.', 'error'); redirect('?page=admins'); }
        $hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        db()->prepare('INSERT INTO admins (name,email,password_hash,role,is_active) VALUES (?,?,?,?,1)')->execute([trim($_POST['name']??''),$email,$hash,in_array($_POST['role'],['super_admin','admin','viewer'])?$_POST['role']:'admin']);
        flash_set('Admin created.'); redirect('?page=admins');
    }
    if ($action === 'toggle_admin' && $admin_role === 'super_admin' && isset($_POST['id'])) {
        $aid=(int)$_POST['id'];
        if ($aid !== (int)$_SESSION['admin_id']) db()->prepare('UPDATE admins SET is_active = 1 - is_active WHERE id=?')->execute([$aid]);
        flash_set('Admin status toggled.'); redirect('?page=admins');
    }
}

// ── GET ACTIONS ────────────────────────────────────────────────
if ($logged_in && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $ga = $_GET['action'] ?? '';
    $id = (int)($_GET['id'] ?? 0);

    // CSV EXPORT
    if ($ga === 'export_csv' && !empty($_GET['type'])) {
        $type = $_GET['type'];
        $allowed = ['customers','orders','inquiries'];
        if (!in_array($type, $allowed)) redirect('?page='.$type);
        $queries = [
            'customers' => "SELECT name,phone,email,instagram,city,total_orders,created_at FROM customers ORDER BY created_at DESC",
            'orders'    => "SELECT o.id,cu.name customer,cu.phone,p.name product,o.quantity,o.unit_price,o.final_price,o.status,o.expected_by,o.delivered_on,o.created_at FROM orders o JOIN customers cu ON cu.id=o.customer_id JOIN products p ON p.id=o.product_id ORDER BY o.created_at DESC",
            'inquiries' => "SELECT i.id,cu.name customer,cu.phone,i.source,i.status,i.followup_date,i.created_at FROM inquiries i JOIN customers cu ON cu.id=i.customer_id ORDER BY i.created_at DESC",
        ];
        $rows = db()->query($queries[$type])->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="aakar_'.$type.'_'.date('Y-m-d').'.csv"');
        $out = fopen('php://output','w');
        if (!empty($rows)) { fputcsv($out, array_keys($rows[0])); foreach ($rows as $row) fputcsv($out, $row); }
        fclose($out); exit;
    }

    if ($ga === 'delete_product'    && $id) { db()->prepare('DELETE FROM products WHERE id=?')->execute([$id]); log_action('product.delete','product',$id); flash_set('Product deleted.','info'); redirect('?page=products'); }
    if ($ga === 'delete_category'   && $id) { db()->prepare('DELETE FROM categories WHERE id=?')->execute([$id]); flash_set('Category deleted.','info'); redirect('?page=categories'); }
    if ($ga === 'delete_badge'      && $id) { db()->prepare('DELETE FROM badges WHERE id=?')->execute([$id]); flash_set('Badge deleted.','info'); redirect('?page=badges'); }
    if ($ga === 'delete_inquiry'    && $id) { db()->prepare('DELETE FROM inquiries WHERE id=?')->execute([$id]); flash_set('Inquiry deleted.','info'); redirect('?page=inquiries'); }
    if ($ga === 'delete_customer'   && $id) { db()->prepare('DELETE FROM customers WHERE id=?')->execute([$id]); flash_set('Customer deleted.','info'); redirect('?page=customers'); }
    if ($ga === 'delete_offer'      && $id) { db()->prepare('DELETE FROM offers WHERE id=?')->execute([$id]); flash_set('Offer deleted.','info'); redirect('?page=offers'); }
    if ($ga === 'delete_testimonial'&&$id) { db()->prepare('DELETE FROM testimonials WHERE id=?')->execute([$id]); flash_set('Review deleted.','info'); redirect('?page=testimonials'); }
    if ($ga === 'delete_order'      && $id) { db()->prepare('DELETE FROM orders WHERE id=?')->execute([$id]); flash_set('Order deleted.','info'); redirect('?page=orders'); }
    if ($ga === 'delete_occasion'   && $id) { db()->prepare('DELETE FROM occasions WHERE id=?')->execute([$id]); flash_set('Occasion deleted.','info'); redirect('?page=occasions'); }
    if ($ga === 'delete_variant'    && $id) { $vpid=(int)($_GET['product_id']??0); db()->prepare('DELETE FROM product_variants WHERE id=?')->execute([$id]); flash_set('Variant deleted.','info'); redirect('?page=products&variants='.$vpid); }
    if ($ga === 'toggle_featured'   && $id) { db()->prepare('UPDATE products SET is_featured=1-is_featured WHERE id=?')->execute([$id]); redirect('?page=products'); }
    if ($ga === 'toggle_section'    && $id) { db()->prepare('UPDATE homepage_sections SET is_visible=1-is_visible WHERE id=?')->execute([$id]); redirect('?page=homepage'); }
    if ($ga === 'toggle_testi_featured' && $id) { db()->prepare('UPDATE testimonials SET is_featured=1-is_featured WHERE id=?')->execute([$id]); redirect('?page=testimonials'); }
    if ($ga === 'set_primary_image' && $id) {
        $pid = (int)($_GET['product_id']??0);
        db()->prepare('UPDATE product_media SET is_primary=0 WHERE product_id=?')->execute([$pid]);
        db()->prepare('UPDATE product_media SET is_primary=1 WHERE id=?')->execute([$id]);
        redirect('?page=products&edit='.$pid);
    }
}

$nav_new_inq = $logged_in ? (int)db()->query('SELECT COUNT(*) FROM inquiries WHERE status="new_inquiry"')->fetchColumn() : 0;
$nav_orders  = $logged_in ? (int)db()->query('SELECT COUNT(*) FROM orders WHERE status="pending"')->fetchColumn() : 0;
$flash = flash_get();

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= h($logged_in ? ucfirst($page).' — '.APP_NAME.' Admin' : APP_NAME.' Admin') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&family=Cormorant+Garamond:ital,wght@0,400;0,600;1,300;1,400;1,500&display=swap" rel="stylesheet">
<style>
:root {
  --rose:#b85c6e;--rose-hover:#a34d61;--rose-light:#d4899a;--rose-pale:#f7e8ec;--rose-bg:#fdf5f7;--rose-ghost:#fef9fa;
  --cream:#faf7f4;--cream-deep:#f3ece6;--gold:#c9a96e;--gold-light:#e8d5b0;
  --text-dark:#1e1519;--text-mid:#5c4a52;--text-light:#9d8a93;--white:#ffffff;
  --border:#ecdde3;--border-light:#f4eaee;
  --sidebar-dark:#160f13;--sidebar-mid:#1e1519;--sidebar-line:rgba(255,255,255,0.07);
  --green:#3d8c64;--amber:#c08030;--blue:#3a6fa8;--slate:#7a7080;
  --shadow-xs:0 1px 4px rgba(184,92,110,0.06);--shadow-sm:0 2px 16px rgba(184,92,110,0.09);
  --shadow-md:0 8px 32px rgba(184,92,110,0.13);--shadow-lg:0 20px 60px rgba(184,92,110,0.17);
  --shadow-rose:0 4px 20px rgba(184,92,110,0.28);
  --ff:'Quicksand',sans-serif;--ffs:'Cormorant Garamond',serif;
  --radius:14px;--radius-sm:9px;--radius-xs:6px;
  --sw:260px;--topbar-h:68px;--tr:0.24s cubic-bezier(0.4,0,0.2,1);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{font-size:16px;scroll-behavior:smooth}
body{font-family:var(--ff);background:var(--cream);color:var(--text-dark);line-height:1.6;overflow-x:hidden;-webkit-font-smoothing:antialiased}
a{text-decoration:none;color:inherit}img{display:block;max-width:100%}
button{font-family:var(--ff);cursor:pointer;border:none;background:none}
svg{flex-shrink:0}input,textarea,select{font-family:var(--ff)}
::-webkit-scrollbar{width:5px;height:5px}::-webkit-scrollbar-track{background:transparent}::-webkit-scrollbar-thumb{background:var(--border);border-radius:4px}

/* LOGIN */
.login-wrap{min-height:100vh;display:grid;grid-template-columns:1.15fr 1fr;background:var(--white)}
.login-left{background:var(--sidebar-dark);display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px;position:relative;overflow:hidden}
.login-left::before{content:'';position:absolute;top:-180px;left:-180px;width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,rgba(184,92,110,0.14) 0%,transparent 65%);pointer-events:none}
.login-left::after{content:'';position:absolute;bottom:-80px;right:-80px;width:360px;height:360px;border-radius:50%;background:radial-gradient(circle,rgba(201,169,110,0.07) 0%,transparent 65%);pointer-events:none}
.login-brand{position:relative;z-index:1;text-align:center;max-width:400px}
.login-mark{display:inline-flex;align-items:center;gap:14px;margin-bottom:52px}
.login-mark-icon{width:52px;height:52px;background:linear-gradient(140deg,var(--rose) 0%,#7a2f42 100%);border-radius:15px;display:flex;align-items:center;justify-content:center;box-shadow:var(--shadow-rose)}
.login-mark-icon svg{width:24px;height:24px;stroke:white;fill:none;stroke-width:1.6;stroke-linecap:round;stroke-linejoin:round}
.login-brand-name{font-size:20px;font-weight:700;color:white;display:block}
.login-brand-sub{font-size:9.5px;font-weight:600;letter-spacing:2.5px;text-transform:uppercase;color:var(--rose-light);display:block;margin-top:2px}
.login-heading{font-family:var(--ffs);font-size:48px;font-weight:400;color:white;line-height:1.12;margin-bottom:16px}
.login-heading em{font-style:italic;color:var(--rose-light)}
.login-desc{font-size:14px;font-weight:500;color:rgba(255,255,255,0.4);line-height:1.8;margin-bottom:40px}
.login-pills{display:flex;flex-wrap:wrap;gap:8px;justify-content:center}
.login-pill{padding:5px 13px;border:1px solid rgba(255,255,255,0.1);border-radius:20px;font-size:11px;font-weight:600;color:rgba(255,255,255,0.32)}
.login-right{display:flex;align-items:center;justify-content:center;padding:60px 80px;background:var(--white)}
.login-form-box{width:100%;max-width:380px}
.login-form-title{font-size:28px;font-weight:700;color:var(--text-dark);letter-spacing:-0.4px;margin-bottom:5px}
.login-form-sub{font-size:14px;font-weight:500;color:var(--text-light);margin-bottom:36px}
.login-err{background:var(--rose-pale);border:1px solid var(--rose-light);border-radius:var(--radius-sm);padding:11px 15px;font-size:13px;font-weight:600;color:var(--rose);margin-bottom:18px}
.login-hint{margin-top:18px;text-align:center;font-size:12px;font-weight:500;color:var(--text-light)}
.login-hint strong{color:var(--rose)}

/* FORMS */
.fg{margin-bottom:17px}.fl{display:block;font-size:10.5px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:var(--text-mid);margin-bottom:6px}
.fi,.fta,.fsel{width:100%;padding:10px 13px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13.5px;font-weight:500;color:var(--text-dark);background:var(--white);outline:none;transition:border-color var(--tr),box-shadow var(--tr)}
.fi:focus,.fta:focus,.fsel:focus{border-color:var(--rose);box-shadow:0 0 0 3px rgba(184,92,110,0.1)}
.fi::placeholder,.fta::placeholder{color:var(--text-light);font-weight:400}
.fta{resize:vertical;min-height:90px;line-height:1.65}
.fsel{appearance:none;cursor:pointer;padding-right:32px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%239d8a93' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 11px center}
.frow{display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:17px}
.frow3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:13px;margin-bottom:17px}
.frow1{grid-column:1/-1}
.fcb{display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;font-weight:600;color:var(--text-mid)}
.fcb input[type=checkbox]{accent-color:var(--rose);width:15px;height:15px;cursor:pointer}
.fcb-group{display:flex;flex-wrap:wrap;gap:14px}

/* BUTTONS */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:9px 18px;border-radius:50px;font-family:var(--ff);font-size:12px;font-weight:700;letter-spacing:0.7px;text-transform:uppercase;border:none;cursor:pointer;transition:all var(--tr);white-space:nowrap}
.btn svg{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round}
.btn-primary{background:var(--rose);color:white;box-shadow:var(--shadow-rose)}
.btn-primary:hover{background:var(--rose-hover);transform:translateY(-1px);box-shadow:0 8px 28px rgba(184,92,110,0.38)}
.btn-secondary{background:var(--white);color:var(--text-mid);border:1.5px solid var(--border);box-shadow:var(--shadow-xs)}
.btn-secondary:hover{border-color:var(--rose);color:var(--rose)}
.btn-success{background:rgba(61,140,100,0.1);color:#276048;border:1.5px solid rgba(61,140,100,0.25)}
.btn-success:hover{background:rgba(61,140,100,0.18)}
.btn-danger{background:rgba(220,53,69,0.07);color:#c0394a;border:1.5px solid rgba(220,53,69,0.2)}
.btn-danger:hover{background:rgba(220,53,69,0.14)}
.btn-sm{padding:6px 13px;font-size:10.5px}
.btn-full{width:100%;padding:13px;font-size:13px}
.btn-icon{width:31px;height:31px;border-radius:var(--radius-xs);display:flex;align-items:center;justify-content:center;transition:all var(--tr);background:transparent;border:none;cursor:pointer;color:var(--text-light)}
.btn-icon svg{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.btn-icon:hover{background:var(--rose-pale);color:var(--rose)}
.btn-icon.danger:hover{background:rgba(220,53,69,0.08);color:#c0394a}
.btn-icon.success{color:var(--green)}.btn-icon.success:hover{background:rgba(61,140,100,0.1)}

/* LAYOUT */
.sidebar{width:var(--sw);background:var(--sidebar-dark);position:fixed;top:0;left:0;bottom:0;z-index:200;display:flex;flex-direction:column;transition:transform var(--tr)}
.sb-top{padding:22px 18px 18px;border-bottom:1px solid var(--sidebar-line);flex-shrink:0}
.sb-logo{display:flex;align-items:center;gap:11px}
.sb-logo-icon{width:38px;height:38px;background:linear-gradient(140deg,var(--rose) 0%,#7a2f42 100%);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 3px 10px rgba(184,92,110,0.3)}
.sb-logo-icon svg{width:17px;height:17px;stroke:white;fill:none;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round}
.sb-brand-name{font-size:14.5px;font-weight:700;color:white;display:block}
.sb-brand-sub{font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--rose-light);display:block;margin-top:1px}
.sb-nav{flex:1;overflow-y:auto;padding:14px 10px}
.sb-nav::-webkit-scrollbar{width:3px}
.sb-group{margin-bottom:22px}
.sb-group-label{font-size:9px;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;color:rgba(255,255,255,0.2);padding:0 8px;margin-bottom:5px}
.sb-link{display:flex;align-items:center;gap:9px;padding:9px 10px;border-radius:9px;font-size:13px;font-weight:600;color:rgba(255,255,255,0.42);transition:all var(--tr);cursor:pointer;margin-bottom:1px;position:relative;text-decoration:none}
.sb-link:hover{color:rgba(255,255,255,0.82);background:rgba(255,255,255,0.06)}
.sb-link.on{color:white;background:rgba(184,92,110,0.22)}
.sb-link.on::before{content:'';position:absolute;left:0;top:22%;bottom:22%;width:3px;border-radius:0 2px 2px 0;background:var(--rose)}
.sb-link-ic{width:17px;height:17px;flex-shrink:0}
.sb-link-ic svg{width:17px;height:17px;stroke:currentColor;fill:none;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round}
.sb-badge{margin-left:auto;background:var(--rose);color:white;font-size:9px;font-weight:700;padding:2px 6px;border-radius:10px;min-width:18px;text-align:center}
.sb-foot{padding:12px;border-top:1px solid var(--sidebar-line);flex-shrink:0}
.sb-user{display:flex;align-items:center;gap:9px;padding:9px;border-radius:9px}
.sb-av{width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,var(--rose),#7a2f42);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white;flex-shrink:0}
.sb-uname{font-size:12.5px;font-weight:700;color:rgba(255,255,255,0.82);display:block}
.sb-urole{font-size:10px;font-weight:500;color:rgba(255,255,255,0.28);display:block}
.sb-logout{margin-left:auto;width:26px;height:26px;border-radius:6px;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.3);cursor:pointer;transition:all var(--tr);text-decoration:none}
.sb-logout:hover{background:rgba(255,255,255,0.08);color:var(--rose-light)}
.sb-logout svg{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.sb-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:150}
.mob-menu-btn{display:none;width:40px;height:40px;border-radius:10px;align-items:center;justify-content:center;color:var(--text-dark);cursor:pointer;transition:background var(--tr)}
.mob-menu-btn:hover{background:var(--rose-bg)}
.mob-menu-btn svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2}

.main{margin-left:var(--sw);min-height:100vh;display:flex;flex-direction:column}
.topbar{height:var(--topbar-h);background:rgba(255,255,255,0.97);backdrop-filter:blur(20px);border-bottom:1px solid var(--border-light);display:flex;align-items:center;padding:0 30px;gap:14px;position:sticky;top:0;z-index:100;box-shadow:var(--shadow-xs)}
.topbar-bc{display:flex;align-items:center;gap:6px;flex:1}
.bc-home{font-size:12px;font-weight:500;color:var(--text-light)}
.bc-sep{font-size:12px;color:var(--border)}
.bc-cur{font-size:13.5px;font-weight:700;color:var(--text-dark)}
.topbar-right{display:flex;align-items:center;gap:9px;margin-left:auto}
.topbar-sep{width:1px;height:22px;background:var(--border-light)}
.topbar-uname{font-size:13px;font-weight:700;color:var(--text-dark)}
.topbar-urole{font-size:11px;font-weight:500;color:var(--text-light)}
.topbar-av{width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,var(--rose),#7a2f42);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white}

.content{flex:1;padding:28px 30px}
.pg-head{margin-bottom:26px}
.pg-title{font-family:var(--ffs);font-size:30px;font-weight:400;color:var(--text-dark);letter-spacing:-0.3px;margin-bottom:3px}
.pg-title em{font-style:italic;color:var(--rose)}
.pg-sub{font-size:13px;font-weight:500;color:var(--text-light)}

/* FLASH */
.flash{display:flex;align-items:center;gap:9px;padding:12px 16px;border-radius:var(--radius);font-size:13px;font-weight:600;margin-bottom:22px;animation:slideDown 0.28s ease}
.flash-success{background:rgba(61,140,100,0.1);border:1px solid rgba(61,140,100,0.28);color:#2a7050}
.flash-info{background:rgba(58,111,168,0.1);border:1px solid rgba(58,111,168,0.28);color:#2a5080}
.flash-error{background:var(--rose-pale);border:1px solid var(--rose-light);color:var(--rose)}
@keyframes slideDown{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}

/* CARDS */
.card{background:var(--white);border:1px solid var(--border-light);border-radius:var(--radius);box-shadow:var(--shadow-xs)}
.card-h{padding:18px 20px 14px;border-bottom:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between;gap:12px}
.card-title{font-size:14px;font-weight:700;color:var(--text-dark)}
.card-sub{font-size:11.5px;font-weight:500;color:var(--text-light);margin-top:1px}
.card-body{padding:18px 20px}
.card-link{font-size:12px;font-weight:700;color:var(--rose);cursor:pointer;flex-shrink:0}

/* STAT CARDS */
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:22px}
.stat-card{background:var(--white);border:1px solid var(--border-light);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow-xs);transition:all var(--tr);position:relative;overflow:hidden}
.stat-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-md)}
.stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:var(--radius) var(--radius) 0 0}
.sc-rose::before{background:var(--rose)}.sc-gold::before{background:var(--gold)}.sc-green::before{background:var(--green)}.sc-blue::before{background:var(--blue)}
.stat-ic{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:14px}
.sc-rose .stat-ic{background:var(--rose-pale)}.sc-gold .stat-ic{background:rgba(201,169,110,0.12)}.sc-green .stat-ic{background:rgba(61,140,100,0.1)}.sc-blue .stat-ic{background:rgba(58,111,168,0.1)}
.stat-ic svg{width:18px;height:18px;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.sc-rose .stat-ic svg{stroke:var(--rose)}.sc-gold .stat-ic svg{stroke:var(--gold)}.sc-green .stat-ic svg{stroke:var(--green)}.sc-blue .stat-ic svg{stroke:var(--blue)}
.stat-val{font-size:28px;font-weight:700;color:var(--text-dark);line-height:1;margin-bottom:3px}
.stat-lbl{font-size:10.5px;font-weight:700;letter-spacing:0.3px;color:var(--text-light);margin-bottom:9px}
.stat-note{font-size:11px;font-weight:600;color:var(--text-light);background:var(--cream);padding:2px 8px;border-radius:10px;display:inline-block}

/* BADGES/STATUS */
.badge{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:20px;font-size:10.5px;font-weight:700;white-space:nowrap}
.badge::before{content:'';width:5px;height:5px;border-radius:50%;flex-shrink:0}
.badge-green{background:rgba(61,140,100,0.1);color:#276048}.badge-green::before{background:var(--green)}
.badge-rose{background:var(--rose-pale);color:var(--rose)}.badge-rose::before{background:var(--rose)}
.badge-amber{background:rgba(192,128,48,0.1);color:#7a5018}.badge-amber::before{background:var(--amber)}
.badge-blue{background:rgba(58,111,168,0.1);color:#234882}.badge-blue::before{background:var(--blue)}
.badge-slate{background:var(--cream-deep);color:var(--text-light)}.badge-slate::before{background:var(--slate)}
.badge-gold{background:rgba(201,169,110,0.12);color:#6a4c10}.badge-gold::before{background:var(--gold)}

/* TABLES */
.tbl-wrap{overflow-x:auto}
.tbl{width:100%;border-collapse:collapse}
.tbl th{padding:10px 15px;text-align:left;font-size:10px;font-weight:700;letter-spacing:1.4px;text-transform:uppercase;color:var(--text-light);border-bottom:1px solid var(--border-light);white-space:nowrap}
.tbl td{padding:12px 15px;font-size:13px;font-weight:500;color:var(--text-dark);border-bottom:1px solid var(--border-light);vertical-align:middle}
.tbl tr:last-child td{border-bottom:none}.tbl tr:hover td{background:var(--rose-ghost)}
.tbl-empty{text-align:center;padding:44px 16px !important;color:var(--text-light) !important;font-size:13px !important}

/* GRIDS */
.g2{display:grid;grid-template-columns:1fr 1fr;gap:20px}
.g3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:18px}
.g4{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
.g-auto{display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:18px}
.g-auto3{display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:18px}
.mb24{margin-bottom:24px}.mb16{margin-bottom:16px}.mb32{margin-bottom:32px}
.divider{height:1px;background:var(--border-light);margin:18px 0}
.flex-row{display:flex;align-items:center;gap:9px}
.flex-between{display:flex;align-items:center;justify-content:space-between;gap:12px}
.flex-wrap{flex-wrap:wrap}
.action-row{display:flex;align-items:center;gap:4px}

/* PRODUCT CARDS */
.prod-card{background:var(--white);border:1px solid var(--border-light);border-radius:var(--radius);overflow:hidden;transition:all var(--tr);box-shadow:var(--shadow-xs)}
.prod-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-md);border-color:var(--rose-light)}
.prod-img{height:145px;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden}
.prod-img img{width:100%;height:100%;object-fit:cover}
.prod-img-placeholder{width:100%;height:100%;display:flex;align-items:center;justify-content:center}
.prod-img-badge{position:absolute;top:8px;left:8px}
.prod-body{padding:13px 15px}
.prod-name{font-size:13.5px;font-weight:700;color:var(--text-dark);margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.prod-cat{font-size:11.5px;font-weight:500;color:var(--text-light);margin-bottom:9px}
.prod-flags{display:flex;flex-wrap:wrap;gap:4px;margin-bottom:10px}
.flag{font-size:10px;font-weight:700;padding:2px 7px;border-radius:9px}
.flag-gold{background:rgba(201,169,110,0.12);color:#6a4c10}
.flag-rose{background:var(--rose-pale);color:var(--rose)}
.flag-green{background:rgba(61,140,100,0.1);color:#276048}
.flag-red{background:rgba(220,53,69,0.08);color:#c0394a}
.prod-footer{display:flex;align-items:center;justify-content:space-between}
.prod-price{font-size:15.5px;font-weight:700;color:var(--rose)}
.prod-orig{font-size:11.5px;font-weight:500;color:var(--text-light);text-decoration:line-through;margin-left:4px}

/* IMAGE GALLERY */
.img-gallery{display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:8px;margin-top:12px}
.img-thumb{position:relative;border-radius:var(--radius-sm);overflow:hidden;aspect-ratio:1;border:2px solid var(--border-light);cursor:pointer;transition:border-color var(--tr)}
.img-thumb.primary{border-color:var(--rose)}
.img-thumb img{width:100%;height:100%;object-fit:cover}
.img-thumb-del{position:absolute;top:3px;right:3px;width:20px;height:20px;background:rgba(220,53,69,0.85);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:11px;cursor:pointer;border:none;line-height:1}
.img-thumb-primary-badge{position:absolute;bottom:3px;left:3px;background:var(--rose);color:white;font-size:8px;font-weight:700;padding:1px 5px;border-radius:4px;text-transform:uppercase;letter-spacing:0.5px}
.upload-zone{border:2px dashed var(--border);border-radius:var(--radius);padding:22px;text-align:center;cursor:pointer;transition:all var(--tr)}
.upload-zone:hover,.upload-zone.drag{border-color:var(--rose);background:var(--rose-ghost)}
.upload-zone svg{width:28px;height:28px;stroke:var(--text-light);fill:none;stroke-width:1.5;stroke-linecap:round;stroke-linejoin:round;margin:0 auto 8px}
.upload-zone-text{font-size:13px;font-weight:600;color:var(--text-mid);margin-bottom:3px}
.upload-zone-sub{font-size:11.5px;font-weight:500;color:var(--text-light)}

/* MODALS */
.modal-bg{display:none;position:fixed;inset:0;background:rgba(22,15,19,0.6);backdrop-filter:blur(5px);z-index:500;align-items:center;justify-content:center;padding:20px}
.modal-bg.open{display:flex}
.modal{background:var(--white);border-radius:var(--radius);width:100%;max-width:660px;max-height:92vh;overflow-y:auto;box-shadow:var(--shadow-lg);animation:modalIn 0.24s ease}
.modal-wide{max-width:860px}
@keyframes modalIn{from{opacity:0;transform:scale(0.96) translateY(10px)}to{opacity:1;transform:scale(1) translateY(0)}}
.modal-head{display:flex;align-items:center;justify-content:space-between;padding:18px 22px 14px;border-bottom:1px solid var(--border-light);position:sticky;top:0;background:var(--white);z-index:1}
.modal-title{font-size:15px;font-weight:700;color:var(--text-dark)}
.modal-close{width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background var(--tr);color:var(--text-mid)}
.modal-close:hover{background:var(--cream-deep)}
.modal-close svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2}
.modal-body{padding:20px 22px}
.modal-foot{padding:14px 22px;border-top:1px solid var(--border-light);display:flex;justify-content:flex-end;gap:9px;background:var(--cream);border-radius:0 0 var(--radius) var(--radius)}
.fp-label{font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-mid);margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--border-light)}

/* FILTER BAR */
.filter-bar{display:flex;gap:7px;flex-wrap:wrap;margin-bottom:20px}
.filter-btn{padding:7px 14px;border-radius:50px;font-size:11.5px;font-weight:700;border:1.5px solid var(--border);color:var(--text-light);background:var(--white);cursor:pointer;transition:all var(--tr);text-decoration:none;display:inline-block}
.filter-btn:hover,.filter-btn.on{border-color:var(--rose);color:var(--rose);background:var(--rose-ghost)}

/* MISC */
.ava{width:32px;height:32px;border-radius:8px;background:var(--rose-pale);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--rose);flex-shrink:0}
.tag{display:inline-block;font-size:10.5px;font-weight:700;background:var(--cream-deep);color:var(--text-mid);padding:2px 8px;border-radius:5px}
.mono{font-family:'Courier New',monospace;font-size:12px}
.truncate{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px;display:block}
.text-rose{color:var(--rose)}.text-muted{color:var(--text-light);font-size:12px}.text-bold{font-weight:700}.text-sm{font-size:12px}
.section-row{display:flex;align-items:center;gap:11px;padding:12px 15px;background:var(--white);border:1px solid var(--border-light);border-radius:var(--radius-sm);margin-bottom:7px}
.section-row:hover{border-color:var(--rose-light)}
.section-num{width:24px;height:24px;border-radius:6px;background:var(--rose-pale);display:flex;align-items:center;justify-content:center;font-size:10.5px;font-weight:700;color:var(--rose);flex-shrink:0}
.wa-dark{background:#111b21;border-radius:var(--radius);padding:20px}
.wa-label{font-size:9.5px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,0.25);margin-bottom:12px}
.wa-bubble{background:#005c4b;border-radius:10px 10px 10px 2px;padding:12px 15px;max-width:280px}
.wa-text{font-size:13px;color:rgba(255,255,255,0.9);line-height:1.65;white-space:pre-line}
.wa-tick{font-size:10px;color:rgba(255,255,255,0.38);text-align:right;margin-top:4px}
.bar-track{height:5px;background:var(--cream-deep);border-radius:3px;overflow:hidden}
.bar-fill{height:100%;border-radius:3px;background:linear-gradient(90deg,var(--rose),var(--rose-light))}
.chart-area{display:flex;align-items:flex-end;gap:6px;height:150px;padding:0 2px}
.chart-col{flex:1;display:flex;flex-direction:column;align-items:center;gap:4px}
.chart-bar{width:100%;border-radius:4px 4px 0 0;background:linear-gradient(180deg,var(--rose) 0%,var(--rose-light) 100%);opacity:0.78;transition:opacity var(--tr);cursor:default;min-height:5px}
.chart-bar:hover{opacity:1}
.chart-lbl{font-size:9px;font-weight:700;color:var(--text-light)}
.chart-val{font-size:9.5px;font-weight:700;color:var(--text-mid)}
.offer-card{background:var(--white);border:1px solid var(--border-light);border-radius:var(--radius);padding:18px;transition:all var(--tr);position:relative;overflow:hidden}
.offer-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--rose),var(--gold))}
.offer-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-md)}
.media-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px}
.media-item{border-radius:var(--radius-sm);overflow:hidden;border:1.5px solid var(--border-light);transition:border-color var(--tr);position:relative;aspect-ratio:1}
.media-item:hover{border-color:var(--rose-light)}
.media-item img{width:100%;height:100%;object-fit:cover;display:block}
.media-item-overlay{position:absolute;inset:0;background:rgba(22,15,19,0.5);display:flex;align-items:center;justify-content:center;gap:8px;opacity:0;transition:opacity var(--tr)}
.media-item:hover .media-item-overlay{opacity:1}
.media-item-info{position:absolute;bottom:0;left:0;right:0;background:rgba(22,15,19,0.7);padding:5px 8px;font-size:10px;font-weight:600;color:rgba(255,255,255,0.7);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

/* VARIANT COLOR SWATCH */
.color-swatch{width:18px;height:18px;border-radius:50%;border:1.5px solid rgba(0,0,0,0.12);flex-shrink:0;display:inline-block}
.variant-row{display:flex;align-items:center;gap:10px;padding:11px 14px;background:var(--white);border:1px solid var(--border-light);border-radius:var(--radius-sm);margin-bottom:6px;transition:border-color var(--tr)}
.variant-row:hover{border-color:var(--rose-light)}

/* ANIMATIONS */
@keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
.ani{animation:fadeUp 0.4s ease both}
.d1{animation-delay:.05s}.d2{animation-delay:.1s}.d3{animation-delay:.15s}.d4{animation-delay:.2s}

/* RESPONSIVE */
@media(max-width:1100px){.stats-grid{grid-template-columns:repeat(2,1fr)}.g4{grid-template-columns:repeat(2,1fr)}}
@media(max-width:900px){.g2,.g3{grid-template-columns:1fr}.frow,.frow3{grid-template-columns:1fr}}
@media(max-width:768px){
  .sidebar{transform:translateX(-100%)}.sidebar.open{transform:translateX(0)}
  .sb-overlay.open{display:block}.main{margin-left:0}.mob-menu-btn{display:flex}
  .content{padding:18px 16px}.stats-grid{grid-template-columns:1fr 1fr;gap:12px}
  .topbar{padding:0 16px}.login-wrap{grid-template-columns:1fr}.login-left{display:none}.login-right{padding:40px 24px}
}
@media(max-width:480px){.stats-grid{grid-template-columns:1fr 1fr}.g-auto,.g-auto3{grid-template-columns:1fr}.media-grid{grid-template-columns:repeat(auto-fill,minmax(110px,1fr))}}
</style>
</head>
<body>
<?php if (!$logged_in): ?>
<div class="login-wrap">
  <div class="login-left">
    <div class="login-brand">
      <div class="login-mark">
        <div class="login-mark-icon"><svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></div>
        <div><span class="login-brand-name"><?= APP_NAME ?></span><span class="login-brand-sub">Admin Studio</span></div>
      </div>
      <div class="login-heading">Where every gift<br>becomes a <em>memory</em></div>
      <p class="login-desc">Manage your gifting studio — products, inquiries, orders, and stories — all in one refined workspace.</p>
      <div class="login-pills">
        <?php
        try { foreach (db()->query('SELECT name FROM categories WHERE is_active=1 ORDER BY sort_order LIMIT 6')->fetchAll() as $c) echo '<span class="login-pill">'.h($c['name']).'</span>'; }
        catch (Exception) { foreach (['Photo Frames','Gift Boxes','Bouquets','Memory Sand','Crochet Gifts','Photo Magazines'] as $n) echo '<span class="login-pill">'.$n.'</span>'; }
        ?>
      </div>
    </div>
  </div>
  <div class="login-right">
    <div class="login-form-box">
      <div class="login-form-title">Welcome back</div>
      <p class="login-form-sub">Sign in to your admin panel</p>
      <?php if ($login_error): ?><div class="login-err"><?= h($login_error) ?></div><?php endif; ?>
      <form method="POST" autocomplete="on">
        <?= csrf_field() ?><input type="hidden" name="action" value="login">
        <div class="fg"><label class="fl">Email Address</label><input type="email" name="email" class="fi" placeholder="admin@aakarcreatives.com" autocomplete="email" required></div>
        <div class="fg"><label class="fl">Password</label><input type="password" name="password" class="fi" placeholder="Enter password" autocomplete="current-password" required></div>
        <button type="submit" class="btn btn-primary btn-full" style="margin-top:6px">Sign In</button>
      </form>
    </div>
  </div>
</div>

<?php else: ?>
<div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>
<aside class="sidebar" id="sidebar">
  <div class="sb-top">
    <div class="sb-logo">
      <div class="sb-logo-icon"><svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></div>
      <div><span class="sb-brand-name"><?= APP_NAME ?></span><span class="sb-brand-sub">Admin Panel</span></div>
    </div>
  </div>
  <nav class="sb-nav">
    <?php
    $nav = [
      'Overview' => [
        ['dashboard','Dashboard','<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>'],
        ['analytics','Analytics','<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>'],
      ],
      'Catalogue' => [
        ['products','Products','<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>'],
        ['categories','Categories','<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>'],
        ['badges','Badges','<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>'],
        ['occasions','Occasions','<polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/>'],
        ['media','Media Library','<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>'],
      ],
      'Sales' => [
        ['inquiries','Inquiries','<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>'],
        ['orders','Orders','<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/>'],
        ['customers','Customers','<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>'],
        ['offers','Offers','<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>'],
        ['whatsapp','WhatsApp','<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.22h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>'],
      ],
      'Content' => [
        ['testimonials','Testimonials','<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>'],
        ['homepage','Homepage Builder','<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>'],
      ],
      'System' => [
        ['activity','Activity Log','<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>'],
      ],
    ];
    if ($admin_role === 'super_admin') $nav['System'][] = ['admins','Admin Users','<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>'];
    $cur_page_for_nav = ($page === 'products' && isset($_GET['variants'])) ? 'products' : $page;
    foreach ($nav as $group => $links):
    ?>
    <div class="sb-group">
      <div class="sb-group-label"><?= $group ?></div>
      <?php foreach ($links as [$slug, $label, $svgpath]): ?>
      <a href="?page=<?= $slug ?>" class="sb-link <?= $cur_page_for_nav===$slug?'on':'' ?>">
        <span class="sb-link-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><?= $svgpath ?></svg></span>
        <?= $label ?>
        <?php if ($slug==='inquiries' && $nav_new_inq): ?><span class="sb-badge"><?= $nav_new_inq ?></span><?php endif; ?>
        <?php if ($slug==='orders' && $nav_orders): ?><span class="sb-badge"><?= $nav_orders ?></span><?php endif; ?>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
  </nav>
  <div class="sb-foot">
    <div class="sb-user">
      <div class="sb-av"><?= strtoupper(substr($admin_name,0,1)) ?></div>
      <div><span class="sb-uname"><?= h($admin_name) ?></span><span class="sb-urole"><?= str_replace('_',' ',ucwords(str_replace('_',' ',$admin_role))) ?></span></div>
      <a href="?logout=1" class="sb-logout" title="Sign out"><svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></a>
    </div>
  </div>
</aside>

<div class="main">
  <header class="topbar">
    <button class="mob-menu-btn" onclick="toggleSidebar()"><svg viewBox="0 0 24 24"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
    <div class="topbar-bc">
      <span class="bc-home"><?= APP_NAME ?></span>
      <span class="bc-sep">/</span>
      <?php if ($page === 'products' && isset($_GET['variants'])): ?>
      <a href="?page=products" style="color:var(--text-light);font-size:12px;font-weight:500">Products</a>
      <span class="bc-sep">/</span>
      <span class="bc-cur">Variants</span>
      <?php else: ?>
      <span class="bc-cur"><?= ucfirst(str_replace('_',' ',$page)) ?></span>
      <?php endif; ?>
    </div>
    <div class="topbar-right">
      <div class="topbar-sep"></div>
      <div class="topbar-av"><?= strtoupper(substr($admin_name,0,1)) ?></div>
      <div><div class="topbar-uname"><?= h($admin_name) ?></div><div class="topbar-urole"><?= str_replace('_',' ',ucwords(str_replace('_',' ',$admin_role))) ?></div></div>
    </div>
  </header>

  <div class="content">
    <?php if ($flash): ?>
    <div class="flash flash-<?= in_array($flash['type'],['success','info','error'])?$flash['type']:'success' ?>"><?= h($flash['msg']) ?></div>
    <?php endif; ?>

<?php
// ═══════════════════════════════════════════════════════════
// PAGE ROUTING
// ═══════════════════════════════════════════════════════════

// ── DASHBOARD ───────────────────────────────────────────────
if ($page === 'dashboard'):
    $tp   = (int)db()->query('SELECT COUNT(*) FROM products WHERE status="active"')->fetchColumn();
    $tc   = (int)db()->query('SELECT COUNT(*) FROM categories WHERE is_active=1')->fetchColumn();
    $ti   = (int)db()->query('SELECT COUNT(*) FROM inquiries')->fetchColumn();
    $td   = (int)db()->query('SELECT COUNT(*) FROM inquiries WHERE status="delivered"')->fetchColumn();
    $nt   = (int)db()->query('SELECT COUNT(*) FROM inquiries WHERE DATE(created_at)=CURDATE()')->fetchColumn();
    $to   = (int)db()->query('SELECT COUNT(*) FROM orders')->fetchColumn();
    $rev  = (float)db()->query("SELECT COALESCE(SUM(final_price),0) FROM orders WHERE status='delivered'")->fetchColumn();
    $chart= db()->query('SELECT DATE_FORMAT(created_at,"%a") lbl, COUNT(*) cnt FROM inquiries WHERE created_at>=CURDATE()-INTERVAL 6 DAY GROUP BY DATE(created_at),lbl ORDER BY DATE(created_at)')->fetchAll();
    $top5 = db()->query('SELECT p.name,c.name cat,p.views,p.whatsapp_clicks FROM products p JOIN categories c ON c.id=p.category_id WHERE p.status="active" ORDER BY p.views DESC LIMIT 5')->fetchAll();
    $recent=db()->query('SELECT i.*,cu.name cname,cu.phone,p.name pname FROM inquiries i JOIN customers cu ON cu.id=i.customer_id LEFT JOIN products p ON p.id=i.product_id ORDER BY i.created_at DESC LIMIT 8')->fetchAll();
?>
<div class="pg-head ani">
  <div class="pg-title">Studio <em>Overview</em></div>
  <div class="pg-sub">Live business metrics for <?= date('l, d F Y') ?></div>
</div>
<div class="stats-grid ani d1">
  <div class="stat-card sc-rose"><div class="stat-ic"><svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg></div><div class="stat-val"><?= $tp ?></div><div class="stat-lbl">Active Products</div><span class="stat-note"><?= $tc ?> categories</span></div>
  <div class="stat-card sc-gold"><div class="stat-ic"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div><div class="stat-val"><?= $ti ?></div><div class="stat-lbl">Total Inquiries</div><span class="stat-note">+<?= $nt ?> today</span></div>
  <div class="stat-card sc-green"><div class="stat-ic"><svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></div><div class="stat-val"><?= $td ?></div><div class="stat-lbl">Delivered</div><span class="stat-note"><?= $to ?> orders total</span></div>
  <div class="stat-card sc-blue"><div class="stat-ic"><svg viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div><div class="stat-val">&#8377;<?= number_format($rev) ?></div><div class="stat-lbl">Revenue Delivered</div><span class="stat-note">All time</span></div>
</div>
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:22px" class="ani d2">
  <button class="btn btn-primary" onclick="openModal('addProductModal')"><svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add Product</button>
  <a href="?page=inquiries" class="btn btn-secondary">Inquiries<?php if ($nav_new_inq): ?> <span class="badge badge-rose" style="margin-left:2px"><?= $nav_new_inq ?></span><?php endif; ?></a>
  <a href="?page=orders" class="btn btn-secondary">Orders<?php if ($nav_orders): ?> <span class="badge badge-amber" style="margin-left:2px"><?= $nav_orders ?></span><?php endif; ?></a>
  <a href="?page=analytics" class="btn btn-secondary">Analytics</a>
</div>
<div class="g2 mb24 ani d3">
  <div class="card">
    <div class="card-h"><div><div class="card-title">Inquiry Trend</div><div class="card-sub">Last 7 days</div></div></div>
    <div class="card-body">
      <?php if (!empty($chart)): $mx = max(array_column($chart,'cnt')) ?: 1; ?>
      <div class="chart-area"><?php foreach ($chart as $d): $h2 = round(($d['cnt']/$mx)*140); ?><div class="chart-col"><div class="chart-val"><?= $d['cnt'] ?></div><div class="chart-bar" style="height:<?= $h2 ?>px"></div><div class="chart-lbl"><?= $d['lbl'] ?></div></div><?php endforeach; ?></div>
      <?php else: ?><p class="text-muted" style="text-align:center;padding:40px 0">No data yet</p><?php endif; ?>
    </div>
  </div>
  <div class="card">
    <div class="card-h"><div><div class="card-title">Top Products</div><div class="card-sub">By page views</div></div><a href="?page=analytics" class="card-link">View all</a></div>
    <div class="card-body" style="padding-top:8px">
      <?php foreach ($top5 as $i => $p): $pct = $top5[0]['views'] ? round(((int)$p['views'])/(int)$top5[0]['views']*100) : 0; ?>
      <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--border-light)">
        <div class="ava" style="width:24px;height:24px;border-radius:6px;font-size:10px"><?= $i+1 ?></div>
        <div style="flex:1;min-width:0"><div class="truncate text-bold" style="font-size:13px;max-width:140px"><?= h($p['name']) ?></div><div class="text-muted"><?= h($p['cat']) ?></div></div>
        <div><div class="bar-track" style="width:64px;margin-bottom:2px"><div class="bar-fill" style="width:<?= $pct ?>%"></div></div><div class="text-muted" style="text-align:right"><?= number_format((float)$p['views']) ?></div></div>
      </div>
      <?php endforeach; if (empty($top5)): ?><p class="text-muted text-sm">No products yet.</p><?php endif; ?>
    </div>
  </div>
</div>
<div class="card ani d4">
  <div class="card-h"><div><div class="card-title">Recent Inquiries</div></div><a href="?page=inquiries" class="card-link">Manage all</a></div>
  <div class="tbl-wrap"><table class="tbl">
    <thead><tr><th>Customer</th><th>Product</th><th>Source</th><th>Status</th><th>Time</th></tr></thead>
    <tbody>
      <?php foreach ($recent as $r): ?>
      <tr>
        <td><div class="flex-row"><div class="ava"><?= strtoupper(substr($r['cname'],0,1)) ?></div><div><div class="text-bold" style="font-size:13px"><?= h($r['cname']) ?></div><div class="text-muted"><?= h($r['phone']) ?></div></div></div></td>
        <td><span class="truncate"><?= h($r['pname']??'General') ?></span></td>
        <td><span class="tag"><?= ucfirst($r['source']) ?></span></td>
        <td><span class="badge <?= statusBadge($r['status']) ?>"><?= str_replace('_',' ',ucfirst($r['status'])) ?></span></td>
        <td class="text-muted"><?= time_ago($r['created_at']) ?></td>
      </tr>
      <?php endforeach; if (empty($recent)): ?><tr><td colspan="5" class="tbl-empty">No inquiries yet.</td></tr><?php endif; ?>
    </tbody>
  </table></div>
</div>

<?php
// ── PRODUCTS ────────────────────────────────────────────────
elseif ($page === 'products' && !isset($_GET['variants'])):
    $filter = $_GET['filter'] ?? 'all';
    $wh = match($filter) { 'active'=>'WHERE p.status="active"','draft'=>'WHERE p.status="draft"','archived'=>'WHERE p.status="archived"','featured'=>'WHERE p.is_featured=1', default=>'WHERE 1=1' };
    $search = trim($_GET['q'] ?? '');
if ($search) {
    $wh .= " AND (p.name LIKE ? OR p.tags LIKE ?)";
    $stmt = db()->prepare('SELECT p.*,c.name cat,b.name bname,b.color_hex bcolor FROM products p JOIN categories c ON c.id=p.category_id LEFT JOIN badges b ON b.id=p.badge_id '.$wh.' ORDER BY p.sort_order,p.created_at DESC');
    $stmt->execute(["%$search%", "%$search%"]);
    $products = $stmt->fetchAll();
} else {
    $products = db()->query('SELECT p.*,c.name cat,b.name bname,b.color_hex bcolor FROM products p JOIN categories c ON c.id=p.category_id LEFT JOIN badges b ON b.id=p.badge_id '.$wh.' ORDER BY p.sort_order,p.created_at DESC')->fetchAll();
}
    $cats   = db()->query('SELECT * FROM categories WHERE is_active=1 ORDER BY sort_order')->fetchAll();
    $badges = db()->query('SELECT * FROM badges WHERE is_active=1 ORDER BY name')->fetchAll();
    $occasions_all = db()->query('SELECT * FROM occasions WHERE is_active=1 ORDER BY sort_order')->fetchAll();
    $wa_tpl = db()->query("SELECT template FROM whatsapp_templates WHERE is_default=1 LIMIT 1")->fetchColumn();
    $edit_prod = null; $edit_media = []; $edit_occ_ids = [];
    if (isset($_GET['edit'])) {
        $ep = db()->prepare('SELECT * FROM products WHERE id=?'); $ep->execute([(int)$_GET['edit']]); $edit_prod = $ep->fetch();
        if ($edit_prod) {
            $em = db()->prepare('SELECT * FROM product_media WHERE product_id=? ORDER BY is_primary DESC,sort_order'); $em->execute([$edit_prod['id']]); $edit_media = $em->fetchAll();
            $eo = db()->prepare('SELECT occasion_id FROM product_occasions WHERE product_id=?'); $eo->execute([$edit_prod['id']]); $edit_occ_ids = array_column($eo->fetchAll(), 'occasion_id');
        }
    }
    $cat_bgs = ['Photo Frames'=>'linear-gradient(150deg,#f0e8dc,#deccb4)','Photo Magazines'=>'linear-gradient(150deg,#f6e8d0,#d8b87a)','Gift Boxes'=>'linear-gradient(150deg,#fce8e8,#e4a8a8)','Bouquets'=>'linear-gradient(150deg,#fae8e8,#e0b0b0)'];
?>
 <form method="GET" class="flex-row mb16">
   <input type="hidden" name="page" value="products">
   <input type="text" name="q" class="fi" placeholder="Search products..." value="<?= h($search) ?>" style="max-width:260px">
   <button type="submit" class="btn btn-secondary btn-sm">Search</button>
 </form>

<div class="pg-head ani">
  <div class="flex-between">
    <div><div class="pg-title">Product <em>Management</em></div><div class="pg-sub"><?= count($products) ?> products<?= $filter!=='all'?" · $filter":'' ?></div></div>
    <button class="btn btn-primary" onclick="openModal('addProductModal')"><svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add Product</button>
  </div>
</div>
<div class="filter-bar ani d1">
  <?php foreach (['all'=>'All','active'=>'Active','draft'=>'Drafts','featured'=>'Featured','archived'=>'Archived'] as $k=>$l): ?>
  <a href="?page=products&filter=<?= $k ?>" class="filter-btn <?= $filter===$k?'on':'' ?>"><?= $l ?></a>
  <?php endforeach; ?>
</div>
<div class="g-auto ani d2">
  <?php foreach ($products as $p):
    $bg = $cat_bgs[$p['cat']] ?? 'linear-gradient(150deg,#f5eaee,#e0c0c8)';
    $pimg = db()->prepare('SELECT file_url FROM product_media WHERE product_id=? AND is_primary=1 LIMIT 1'); $pimg->execute([$p['id']]); $primary_img = $pimg->fetchColumn();
    $var_count = (int)db()->query("SELECT COUNT(*) FROM product_variants WHERE product_id={$p['id']}")->fetchColumn();
  ?>
  <div class="prod-card">
    <div class="prod-img" style="<?= $primary_img ? '' : "background:$bg" ?>">
      <?php if ($primary_img): ?><img src="./<?= h($primary_img) ?>" alt="<?= h($p['name']) ?>" loading="lazy">
      <?php else: ?><div class="prod-img-placeholder" style="background:<?= $bg ?>"><svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="rgba(184,92,110,0.4)" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div><?php endif; ?>
      <?php if ($p['bname']): ?><div class="prod-img-badge"><span style="background:<?= h($p['bcolor']) ?>;color:white;font-size:9px;font-weight:700;padding:3px 8px;border-radius:10px;display:inline-block"><?= h($p['bname']) ?></span></div><?php endif; ?>
    </div>
    <div class="prod-body">
      <div class="prod-name"><?= h($p['name']) ?></div>
      <div class="prod-cat"><?= h($p['cat']) ?> <?php if ($var_count): ?><span class="tag" style="margin-left:4px"><?= $var_count ?> variants</span><?php endif; ?></div>
      <div class="prod-flags">
        <span class="badge <?= statusBadge($p['status']) ?>"><?= ucfirst($p['status']) ?></span>
        <?php if ($p['is_featured']): ?><span class="flag flag-gold">Featured</span><?php endif; ?>
        <?php if ($p['is_bestseller']): ?><span class="flag flag-rose">Bestseller</span><?php endif; ?>
        <?php if ($p['is_new_arrival']): ?><span class="flag flag-green">New</span><?php endif; ?>
        <?php if (!$p['in_stock']): ?><span class="flag flag-red">Out of Stock</span><?php endif; ?>
      </div>
      <div class="prod-footer">
        <div><span class="prod-price">&#8377;<?= number_format((float)$p['price']) ?></span><?php if ($p['discount_price']): ?><span class="prod-orig">&#8377;<?= number_format((float)$p['discount_price']) ?></span><?php endif; ?></div>
        <div class="action-row">
          <a href="?page=products&edit=<?= $p['id'] ?>" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
          <a href="?page=products&variants=<?= $p['id'] ?>" class="btn-icon" title="Manage variants" style="color:var(--blue)"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M3 12h6m6 0h6"/><path d="M12 3v6m0 6v6"/></svg></a>
          <a href="?page=products&action=toggle_featured&id=<?= $p['id'] ?>" class="btn-icon" title="Toggle featured"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></a>
          <a href="?page=products&action=delete_product&id=<?= $p['id'] ?>" class="btn-icon danger" onclick="return confirm('Delete this product?')" title="Delete"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg></a>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<?php
$_mc = $cats; $_mb = $badges; $_mo = $occasions_all; $_mw = $wa_tpl;
function product_form_fields(array $cats, array $badges, array $occasions, ?string $wa_tpl, array $ep = [], array $edit_media = [], array $edit_occ_ids = []): void { ?>
  <div class="fp-label">Basic Information</div>
  <div class="frow">
    <div class="fg"><label class="fl">Product Name</label><input type="text" name="name" class="fi" placeholder="Eternal Love Frame" value="<?= h($ep['name']??'') ?>" required></div>
    <div class="fg"><label class="fl">Short Description</label><input type="text" name="short_description" class="fi" placeholder="A memory to cherish forever" value="<?= h($ep['short_description']??'') ?>"></div>
  </div>
  <div class="fg"><label class="fl">Full Description</label><textarea name="full_description" class="fta"><?= h($ep['full_description']??'') ?></textarea></div>
  <div class="fg"><label class="fl">Product Story</label><textarea name="product_story" class="fta" style="min-height:65px"><?= h($ep['product_story']??'') ?></textarea></div>
  <div class="divider"></div>
  <div class="fp-label">Pricing & Catalogue</div>
  <div class="frow3">
    <div class="fg"><label class="fl">Price (INR)</label><input type="number" name="price" class="fi" placeholder="1299" step="0.01" value="<?= h($ep['price']??'') ?>" required></div>
    <div class="fg"><label class="fl">Original Price</label><input type="number" name="discount_price" class="fi" placeholder="Optional" step="0.01" value="<?= h($ep['discount_price']??'') ?>"></div>
    <div class="fg"><label class="fl">Delivery Time</label><input type="text" name="delivery_days" class="fi" value="<?= h($ep['delivery_days']??'3-5 Working Days') ?>"></div>
  </div>
  <div class="frow">
    <div class="fg"><label class="fl">Category</label>
      <select name="category_id" class="fsel" required>
        <option value="">Select category</option>
        <?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>" <?= ($ep['category_id']??'')==$c['id']?'selected':'' ?>><?= h($c['name']) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="fg"><label class="fl">Badge</label>
      <select name="badge_id" class="fsel">
        <option value="">No badge</option>
        <?php foreach ($badges as $b): ?><option value="<?= $b['id'] ?>" <?= ($ep['badge_id']??'')==$b['id']?'selected':'' ?>><?= h($b['name']) ?></option><?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="frow">
    <div class="fg"><label class="fl">Tags (comma-separated)</label><input type="text" name="tags" class="fi" placeholder="romantic, couple, handmade" value="<?= h($ep['tags']??'') ?>"></div>
    <div class="fg"><label class="fl">Status</label>
      <select name="status" class="fsel">
        <?php foreach (['active','draft','archived'] as $s): ?><option value="<?= $s ?>" <?= ($ep['status']??'draft')===$s?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="divider"></div>
  <div class="fp-label">Occasions</div>
  <div class="fcb-group" style="margin-bottom:14px">
    <?php foreach ($occasions as $occ): ?>
    <label class="fcb"><input type="checkbox" name="occasion_ids[]" value="<?= $occ['id'] ?>" <?= in_array($occ['id'],$edit_occ_ids)?'checked':'' ?>> <?= h($occ['name']) ?></label>
    <?php endforeach; ?>
  </div>
  <div class="divider"></div>
  <div class="fp-label">Flags</div>
  <div class="fcb-group" style="margin-bottom:16px">
    <?php foreach (['is_featured'=>'Featured','is_new_arrival'=>'New Arrival','is_trending'=>'Trending','is_bestseller'=>'Bestseller','in_stock'=>'In Stock'] as $k=>$l): ?>
    <label class="fcb"><input type="checkbox" name="<?= $k ?>" <?= ($ep[$k]??($k==='in_stock'?1:0))?'checked':'' ?>> <?= $l ?></label>
    <?php endforeach; ?>
  </div>
  <div class="divider"></div>
  <div class="fp-label">Images</div>
  <?php if (!empty($edit_media)): ?>
  <div class="img-gallery" style="margin-bottom:12px">
    <?php foreach ($edit_media as $m): ?>
    <div class="img-thumb <?= $m['is_primary']?'primary':'' ?>">
      <img src="<?= h($m['file_url']) ?>" alt="">
      <?php if ($m['is_primary']): ?><span class="img-thumb-primary-badge">Main</span><?php endif; ?>
      <div style="position:absolute;top:3px;right:3px;display:flex;gap:3px">
        <?php if (!$m['is_primary']): ?>
        <a href="?page=products&action=set_primary_image&id=<?= $m['id'] ?>&product_id=<?= $ep['id']??0 ?>" style="width:18px;height:18px;background:rgba(184,92,110,0.85);border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;text-decoration:none" title="Set as main"><svg viewBox="0 0 24 24" width="9" height="9" fill="none" stroke="white" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></a>
        <?php endif; ?>
        <form method="POST" style="display:inline;margin:0"><?= csrf_field() ?><input type="hidden" name="action" value="delete_product_image"><input type="hidden" name="media_id" value="<?= $m['id'] ?>"><input type="hidden" name="product_id" value="<?= $ep['id']??0 ?>"><button type="submit" class="img-thumb-del" onclick="return confirm('Remove image?')">×</button></form>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <div class="upload-zone" onclick="this.querySelector('input').click()">
    <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
    <div class="upload-zone-text">Click to upload images</div>
    <div class="upload-zone-sub">PNG, JPG, WebP — multiple files supported</div>
    <input type="file" name="product_images[]" multiple accept="image/*" style="display:none" onchange="showFileCount(this)">
  </div>
  <div id="fileCount" style="font-size:12px;font-weight:600;color:var(--text-mid);margin-top:7px"></div>
  <div class="divider"></div>
  <div class="fp-label">WhatsApp Template</div>
  <div class="fg"><textarea name="whatsapp_message" class="fta" style="font-family:monospace;font-size:12px;min-height:110px"><?= h($ep['whatsapp_message'] ?? ($wa_tpl ?: "Hello Aakar Creatives\n\nI am interested in:\n\n*Product:* {product_name}\n*Price:* {price}\n\nPlease share more details.")) ?></textarea></div>
<?php } ?>

<div class="modal-bg" id="addProductModal">
  <div class="modal modal-wide">
    <div class="modal-head"><div class="modal-title">Add New Product</div><div class="modal-close" onclick="closeModal('addProductModal')"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div></div>
    <form method="POST" enctype="multipart/form-data">
      <?= csrf_field() ?><input type="hidden" name="action" value="add_product">
      <div class="modal-body"><?php product_form_fields($_mc,$_mb,$_mo,$_mw); ?></div>
      <div class="modal-foot"><button type="button" class="btn btn-secondary" onclick="closeModal('addProductModal')">Cancel</button><button type="submit" class="btn btn-primary">Save Product</button></div>
    </form>
  </div>
</div>

<?php if ($edit_prod): ?>
<div class="modal-bg open" id="editProductModal">
  <div class="modal modal-wide">
    <div class="modal-head"><div class="modal-title">Edit — <?= h($edit_prod['name']) ?></div><a href="?page=products" class="modal-close"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></a></div>
    <form method="POST" enctype="multipart/form-data">
      <?= csrf_field() ?><input type="hidden" name="action" value="edit_product"><input type="hidden" name="id" value="<?= $edit_prod['id'] ?>">
      <div class="modal-body"><?php product_form_fields($_mc,$_mb,$_mo,$_mw,$edit_prod,$edit_media,$edit_occ_ids); ?></div>
      <div class="modal-foot"><a href="?page=products" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Update Product</button></div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php
// ── PRODUCT VARIANTS ─────────────────────────────────────────
elseif ($page === 'products' && isset($_GET['variants'])):
    $vpid = (int)$_GET['variants'];
    $vprow = db()->prepare('SELECT p.*,c.name cat FROM products p JOIN categories c ON c.id=p.category_id WHERE p.id=?');
    $vprow->execute([$vpid]); $vprod = $vprow->fetch();
    if (!$vprod) { flash_set('Product not found.','error'); redirect('?page=products'); }
    $variants = db()->query("SELECT pv.*,ps.label size_label,ps.size_type,pc.name color_name,pc.hex_code FROM product_variants pv LEFT JOIN product_sizes ps ON ps.id=pv.size_id LEFT JOIN product_colors pc ON pc.id=pv.color_id WHERE pv.product_id={$vpid} ORDER BY pv.sort_order,pv.id")->fetchAll();
    $all_sizes  = db()->query("SELECT * FROM product_sizes WHERE is_active=1 ORDER BY size_type,sort_order")->fetchAll();
    $all_colors = db()->query("SELECT * FROM product_colors WHERE is_active=1 ORDER BY sort_order")->fetchAll();
    $unlimited = count(array_filter($variants, fn($v)=>$v['stock_qty']===-1));
    $out       = count(array_filter($variants, fn($v)=>$v['stock_qty']===0));
    $instock   = count(array_filter($variants, fn($v)=>$v['stock_qty']>0));
?>
<div class="pg-head ani">
  <div class="flex-between">
    <div>
      <div class="pg-title">Variants — <em><?= h($vprod['name']) ?></em></div>
      <div class="pg-sub"><?= count($variants) ?> combinations · <?= h($vprod['cat']) ?></div>
    </div>
    <div class="flex-row">
      <a href="?page=products&edit=<?= $vpid ?>" class="btn btn-secondary">← Edit Product</a>
      <button class="btn btn-primary" onclick="openModal('addVariantModal')"><svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add Variant</button>
    </div>
  </div>
</div>

<div class="g2 mb24 ani d1">
  <div class="card">
    <div class="card-h"><div class="card-title">Base Pricing</div></div>
    <div class="card-body">
      <div class="flex-row" style="gap:12px">
        <div style="background:var(--rose-pale);border-radius:var(--radius-sm);padding:14px 18px;flex:1;text-align:center">
          <div class="text-muted text-sm">Sale Price</div>
          <div style="font-size:22px;font-weight:700;color:var(--rose)">&#8377;<?= number_format((float)$vprod['price']) ?></div>
        </div>
        <?php if ($vprod['discount_price']): ?>
        <div style="background:var(--cream-deep);border-radius:var(--radius-sm);padding:14px 18px;flex:1;text-align:center">
          <div class="text-muted text-sm">Original Price</div>
          <div style="font-size:22px;font-weight:700;color:var(--text-mid);text-decoration:line-through">&#8377;<?= number_format((float)$vprod['discount_price']) ?></div>
        </div>
        <?php endif; ?>
      </div>
      <p class="text-muted text-sm" style="margin-top:12px">Set price overrides per variant. Leave blank to inherit base price.</p>
    </div>
  </div>
  <div class="card">
    <div class="card-h"><div class="card-title">Stock Summary</div></div>
    <div class="card-body">
      <div class="flex-row flex-wrap" style="gap:10px">
        <div style="background:var(--cream-deep);border-radius:var(--radius-sm);padding:12px 16px;text-align:center;flex:1">
          <div style="font-size:20px;font-weight:700"><?= $unlimited ?></div><div class="text-muted text-sm">Made-to-order</div>
        </div>
        <div style="background:rgba(61,140,100,0.08);border-radius:var(--radius-sm);padding:12px 16px;text-align:center;flex:1">
          <div style="font-size:20px;font-weight:700;color:var(--green)"><?= $instock ?></div><div class="text-muted text-sm">In Stock</div>
        </div>
        <div style="background:rgba(220,53,69,0.06);border-radius:var(--radius-sm);padding:12px 16px;text-align:center;flex:1">
          <div style="font-size:20px;font-weight:700;color:#c0394a"><?= $out ?></div><div class="text-muted text-sm">Out of Stock</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card ani d2">
  <div class="card-h"><div><div class="card-title">All Variants</div><div class="card-sub">Size × Color combinations</div></div></div>
  <div class="tbl-wrap"><table class="tbl">
    <thead><tr><th>Size</th><th>Color</th><th>Price Override</th><th>Orig. Override</th><th>Stock</th><th>Status</th><th>Quick Stock</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($variants as $v): ?>
      <tr>
        <td><?php if ($v['size_label']): ?><span class="tag"><?= h($v['size_label']) ?></span> <span class="text-muted text-sm"><?= h($v['size_type']) ?></span><?php else: ?><span class="text-muted">—</span><?php endif; ?></td>
        <td>
          <?php if ($v['color_name']): ?>
          <div class="flex-row"><span class="color-swatch" style="background:<?= h($v['hex_code']) ?>"></span><?= h($v['color_name']) ?></div>
          <?php else: ?><span class="text-muted">—</span><?php endif; ?>
        </td>
        <td><?php if ($v['price_override'] !== null): ?><span class="text-rose text-bold">&#8377;<?= number_format((float)$v['price_override']) ?></span><?php else: ?><span class="text-muted">Base</span><?php endif; ?></td>
        <td><?php if ($v['discount_price_override'] !== null): ?><span style="text-decoration:line-through;color:var(--text-light)">&#8377;<?= number_format((float)$v['discount_price_override']) ?></span><?php else: ?><span class="text-muted">Base</span><?php endif; ?></td>
        <td><?php if ($v['stock_qty'] === -1): ?><span class="badge badge-blue">Made-to-order</span><?php elseif ($v['stock_qty'] === 0): ?><span class="badge badge-rose">Out of Stock</span><?php else: ?><span class="badge badge-green"><?= $v['stock_qty'] ?> units</span><?php endif; ?></td>
        <td><span class="badge <?= $v['is_active']?'badge-green':'badge-slate' ?>"><?= $v['is_active']?'Active':'Off' ?></span></td>
        <td>
          <form method="POST" style="display:flex;gap:5px;align-items:center">
            <?= csrf_field() ?><input type="hidden" name="action" value="update_variant_stock"><input type="hidden" name="id" value="<?= $v['id'] ?>"><input type="hidden" name="product_id" value="<?= $vpid ?>">
            <input type="number" name="stock_qty" value="<?= $v['stock_qty'] ?>" min="-1" style="width:60px;padding:4px 7px;border:1.5px solid var(--border);border-radius:6px;font-size:12px;font-weight:600">
            <button type="submit" class="btn-icon success" title="Update"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></button>
          </form>
        </td>
        <td><a href="?page=products&action=delete_variant&id=<?= $v['id'] ?>&product_id=<?= $vpid ?>" class="btn-icon danger" onclick="return confirm('Delete this variant?')"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg></a></td>
      </tr>
      <?php endforeach; if (empty($variants)): ?><tr><td colspan="8" class="tbl-empty">No variants yet. Add size/color combinations above.</td></tr><?php endif; ?>
    </tbody>
  </table></div>
</div>

<div class="modal-bg" id="addVariantModal">
  <div class="modal">
    <div class="modal-head"><div class="modal-title">Add Variant</div><div class="modal-close" onclick="closeModal('addVariantModal')"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div></div>
    <form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="add_variant"><input type="hidden" name="product_id" value="<?= $vpid ?>">
    <div class="modal-body">
      <div class="frow">
        <div class="fg"><label class="fl">Size</label>
          <select name="size_id" class="fsel">
            <option value="">No size</option>
            <?php $size_groups = []; foreach ($all_sizes as $s) $size_groups[$s['size_type']][] = $s; foreach ($size_groups as $type => $sizes): ?>
            <optgroup label="<?= ucfirst($type) ?>"><?php foreach ($sizes as $s): ?><option value="<?= $s['id'] ?>"><?= h($s['label']) ?><?= $s['dimension_cm']?' ('.$s['dimension_cm'].')':'' ?></option><?php endforeach; ?></optgroup>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="fg"><label class="fl">Color</label>
          <select name="color_id" class="fsel">
            <option value="">No color</option>
            <?php foreach ($all_colors as $c): ?><option value="<?= $c['id'] ?>"><?= h($c['name']) ?> (<?= h($c['hex_code']) ?>)</option><?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="frow">
        <div class="fg"><label class="fl">Price Override (INR)</label><input type="number" name="price_override" class="fi" placeholder="Leave blank = base price" step="0.01"></div>
        <div class="fg"><label class="fl">Original Price Override</label><input type="number" name="discount_price_override" class="fi" placeholder="Leave blank = base" step="0.01"></div>
      </div>
      <div class="fg"><label class="fl">Stock Quantity</label><input type="number" name="stock_qty" class="fi" value="-1" min="-1"><div class="text-muted text-sm" style="margin-top:5px">-1 = made-to-order · 0 = out of stock · &gt;0 = exact quantity</div></div>
    </div>
    <div class="modal-foot"><button type="button" class="btn btn-secondary" onclick="closeModal('addVariantModal')">Cancel</button><button type="submit" class="btn btn-primary">Add Variant</button></div>
    </form>
  </div>
</div>

<?php
// ── CATEGORIES ───────────────────────────────────────────────
elseif ($page === 'categories'):
    $cats = db()->query('SELECT c.*,(SELECT COUNT(*) FROM products p WHERE p.category_id=c.id AND p.status="active") pc FROM categories c ORDER BY c.sort_order,c.name')->fetchAll();
    $edit_cat = null;
    if (isset($_GET['edit'])) { $s=db()->prepare('SELECT * FROM categories WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit_cat=$s->fetch(); }
?>
<div class="pg-head ani flex-between">
  <div><div class="pg-title">Category <em>Management</em></div><div class="pg-sub"><?= count($cats) ?> categories</div></div>
  <button class="btn btn-primary" onclick="openModal('addCatModal')"><svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add Category</button>
</div>
<div class="card ani d1"><div class="tbl-wrap"><table class="tbl">
  <thead><tr><th>Category</th><th>Products</th><th>Slug</th><th>Featured</th><th>Status</th><th>Sort</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($cats as $c): ?>
    <tr>
      <td><div class="flex-row"><?php if ($c['image_url']): ?><img src="<?= h($c['image_url']) ?>" style="width:36px;height:36px;object-fit:cover;border-radius:7px;flex-shrink:0" alt=""><?php else: ?><div class="ava" style="border-radius:7px"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="var(--rose)" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg></div><?php endif; ?><div><div class="text-bold"><?= h($c['name']) ?></div><div class="text-muted"><?= h($c['description']??'') ?></div></div></div></td>
      <td><span class="badge badge-rose"><?= $c['pc'] ?> products</span></td>
      <td><span class="tag mono">/<?= h($c['slug']) ?></span></td>
      <td><span class="badge <?= $c['is_featured']?'badge-green':'badge-slate' ?>"><?= $c['is_featured']?'Yes':'No' ?></span></td>
      <td><span class="badge <?= $c['is_active']?'badge-green':'badge-slate' ?>"><?= $c['is_active']?'Active':'Hidden' ?></span></td>
      <td class="text-muted"><?= $c['sort_order'] ?></td>
      <td><div class="action-row"><a href="?page=categories&edit=<?= $c['id'] ?>" class="btn-icon"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a><a href="?page=categories&action=delete_category&id=<?= $c['id'] ?>" class="btn-icon danger" onclick="return confirm('Delete category?')"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg></a></div></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table></div></div>

<div class="modal-bg" id="addCatModal"><div class="modal"><div class="modal-head"><div class="modal-title">Add Category</div><div class="modal-close" onclick="closeModal('addCatModal')"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div></div>
  <form method="POST" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="action" value="add_category">
  <div class="modal-body">
    <div class="frow"><div class="fg"><label class="fl">Name</label><input type="text" name="name" class="fi" placeholder="Shadow Boxes" required></div><div class="fg"><label class="fl">Sort Order</label><input type="number" name="sort_order" class="fi" value="<?= count($cats)+1 ?>"></div></div>
    <div class="fg"><label class="fl">Description</label><input type="text" name="description" class="fi"></div>
    <div class="fg"><label class="fl">Category Image</label><input type="file" name="image" class="fi" accept="image/*"></div>
    <label class="fcb"><input type="checkbox" name="is_featured"> Feature on Homepage</label>
  </div>
  <div class="modal-foot"><button type="button" class="btn btn-secondary" onclick="closeModal('addCatModal')">Cancel</button><button type="submit" class="btn btn-primary">Create Category</button></div>
  </form>
</div></div>

<?php if ($edit_cat): ?>
<div class="modal-bg open"><div class="modal"><div class="modal-head"><div class="modal-title">Edit Category</div><a href="?page=categories" class="modal-close"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></a></div>
  <form method="POST" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="action" value="edit_category"><input type="hidden" name="id" value="<?= $edit_cat['id'] ?>">
  <div class="modal-body">
    <?php if ($edit_cat['image_url']): ?><img src="<?= h($edit_cat['image_url']) ?>" style="width:100%;height:120px;object-fit:cover;border-radius:var(--radius-sm);margin-bottom:14px" alt=""><?php endif; ?>
    <div class="frow"><div class="fg"><label class="fl">Name</label><input type="text" name="name" class="fi" value="<?= h($edit_cat['name']) ?>" required></div><div class="fg"><label class="fl">Sort Order</label><input type="number" name="sort_order" class="fi" value="<?= h($edit_cat['sort_order']) ?>"></div></div>
    <div class="fg"><label class="fl">Description</label><input type="text" name="description" class="fi" value="<?= h($edit_cat['description']) ?>"></div>
    <div class="fg"><label class="fl">Replace Image</label><input type="file" name="image" class="fi" accept="image/*"></div>
    <label class="fcb"><input type="checkbox" name="is_featured" <?= $edit_cat['is_featured']?'checked':'' ?>> Feature on Homepage</label>
  </div>
  <div class="modal-foot"><a href="?page=categories" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Update Category</button></div>
  </form>
</div></div>
<?php endif; ?>

<?php
// ── BADGES ───────────────────────────────────────────────────
elseif ($page === 'badges'):
    $blist = db()->query('SELECT * FROM badges ORDER BY name')->fetchAll();
    $edit_b = null;
    if (isset($_GET['edit'])) { $s=db()->prepare('SELECT * FROM badges WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit_b=$s->fetch(); }
?>
<div class="pg-head ani flex-between">
  <div><div class="pg-title">Badge <em>System</em></div></div>
  <button class="btn btn-primary" onclick="openModal('addBadgeModal')"><svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> New Badge</button>
</div>
<div class="g4 ani d1">
  <?php foreach ($blist as $b): ?>
  <div class="card" style="padding:18px;text-align:center">
    <div style="width:14px;height:14px;border-radius:50%;background:<?= h($b['color_hex']) ?>;margin:0 auto 10px;box-shadow:0 2px 6px rgba(0,0,0,.15)"></div>
    <div style="display:inline-block;padding:5px 14px;border-radius:20px;background:<?= h($b['color_hex']) ?>;color:white;font-size:11.5px;font-weight:700;margin-bottom:9px"><?= h($b['name']) ?></div>
    <?php if ($b['icon']): ?><div class="text-muted text-sm" style="margin-bottom:8px"><?= h($b['icon']) ?></div><?php endif; ?>
    <div style="margin-bottom:10px"><span class="badge <?= $b['is_active']?'badge-green':'badge-slate' ?>"><?= $b['is_active']?'Active':'Inactive' ?></span></div>
    <div class="action-row" style="justify-content:center">
      <a href="?page=badges&edit=<?= $b['id'] ?>" class="btn-icon"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
      <a href="?page=badges&action=delete_badge&id=<?= $b['id'] ?>" class="btn-icon danger" onclick="return confirm('Delete?')"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg></a>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<div class="modal-bg" id="addBadgeModal"><div class="modal"><div class="modal-head"><div class="modal-title">Create Badge</div><div class="modal-close" onclick="closeModal('addBadgeModal')"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div></div>
  <form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="add_badge">
  <div class="modal-body"><div class="frow"><div class="fg"><label class="fl">Name</label><input type="text" name="name" class="fi" placeholder="Most Loved" required></div><div class="fg"><label class="fl">Color</label><input type="color" name="color_hex" class="fi" value="#b85c6e" style="height:42px;padding:4px 8px;cursor:pointer"></div></div><div class="fg"><label class="fl">Icon Keyword</label><input type="text" name="icon" class="fi" placeholder="heart, star, flame..."></div></div>
  <div class="modal-foot"><button type="button" class="btn btn-secondary" onclick="closeModal('addBadgeModal')">Cancel</button><button type="submit" class="btn btn-primary">Create Badge</button></div>
  </form>
</div></div>
<?php if ($edit_b): ?>
<div class="modal-bg open"><div class="modal"><div class="modal-head"><div class="modal-title">Edit Badge</div><a href="?page=badges" class="modal-close"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></a></div>
  <form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="edit_badge"><input type="hidden" name="id" value="<?= $edit_b['id'] ?>">
  <div class="modal-body"><div class="frow"><div class="fg"><label class="fl">Name</label><input type="text" name="name" class="fi" value="<?= h($edit_b['name']) ?>" required></div><div class="fg"><label class="fl">Color</label><input type="color" name="color_hex" class="fi" value="<?= h($edit_b['color_hex']) ?>" style="height:42px;padding:4px 8px;cursor:pointer"></div></div><div class="fg"><label class="fl">Icon</label><input type="text" name="icon" class="fi" value="<?= h($edit_b['icon']) ?>"></div></div>
  <div class="modal-foot"><a href="?page=badges" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Update Badge</button></div>
  </form>
</div></div>
<?php endif; ?>

<?php
// ── OCCASIONS ────────────────────────────────────────────────
elseif ($page === 'occasions'):
    $olist = db()->query('SELECT * FROM occasions ORDER BY sort_order,name')->fetchAll();
    $edit_occ = null;
    if (isset($_GET['edit'])) { $s=db()->prepare('SELECT * FROM occasions WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit_occ=$s->fetch(); }
?>
<div class="pg-head ani flex-between">
  <div><div class="pg-title">Occasions <em>Manager</em></div><div class="pg-sub"><?= count($olist) ?> occasions</div></div>
  <button class="btn btn-primary" onclick="openModal('addOccModal')"><svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add Occasion</button>
</div>
<div class="card ani d1"><div class="tbl-wrap"><table class="tbl">
  <thead><tr><th>Name</th><th>Slug</th><th>Sort</th><th>Status</th><th>Actions</th></tr></thead>
  <tbody><?php foreach ($olist as $o): ?>
    <tr><td class="text-bold"><?= h($o['name']) ?></td><td><span class="tag mono"><?= h($o['slug']) ?></span></td><td class="text-muted"><?= $o['sort_order'] ?></td><td><span class="badge <?= $o['is_active']?'badge-green':'badge-slate' ?>"><?= $o['is_active']?'Active':'Inactive' ?></span></td>
    <td><div class="action-row"><a href="?page=occasions&edit=<?= $o['id'] ?>" class="btn-icon"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a><a href="?page=occasions&action=delete_occasion&id=<?= $o['id'] ?>" class="btn-icon danger" onclick="return confirm('Delete?')"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg></a></div></td></tr>
  <?php endforeach; ?></tbody>
</table></div></div>

<div class="modal-bg" id="addOccModal"><div class="modal"><div class="modal-head"><div class="modal-title">Add Occasion</div><div class="modal-close" onclick="closeModal('addOccModal')"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div></div>
  <form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="add_occasion"><div class="modal-body"><div class="frow"><div class="fg"><label class="fl">Name</label><input type="text" name="name" class="fi" placeholder="Father's Day" required></div><div class="fg"><label class="fl">Sort Order</label><input type="number" name="sort_order" class="fi" value="<?= count($olist) ?>"></div></div></div>
  <div class="modal-foot"><button type="button" class="btn btn-secondary" onclick="closeModal('addOccModal')">Cancel</button><button type="submit" class="btn btn-primary">Add Occasion</button></div>
  </form>
</div></div>

<?php if ($edit_occ): ?>
<div class="modal-bg open"><div class="modal"><div class="modal-head"><div class="modal-title">Edit Occasion</div><a href="?page=occasions" class="modal-close"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></a></div>
  <form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="edit_occasion"><input type="hidden" name="id" value="<?= $edit_occ['id'] ?>">
  <div class="modal-body"><div class="frow"><div class="fg"><label class="fl">Name</label><input type="text" name="name" class="fi" value="<?= h($edit_occ['name']) ?>" required></div><div class="fg"><label class="fl">Sort Order</label><input type="number" name="sort_order" class="fi" value="<?= $edit_occ['sort_order'] ?>"></div></div><div class="fg"><label class="fcb"><input type="checkbox" name="is_active" <?= $edit_occ['is_active']?'checked':'' ?>> Active</label></div></div>
  <div class="modal-foot"><a href="?page=occasions" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Update</button></div>
  </form>
</div></div>
<?php endif; ?>

<?php
// ── MEDIA ────────────────────────────────────────────────────
elseif ($page === 'media'):
    $mfolder = $_GET['folder'] ?? 'all';
    $mwh = ($mfolder !== 'all') ? "WHERE folder='" . $mfolder . "'" : '';
    $media_items = db()->query("SELECT m.*,a.name aname FROM media_library m LEFT JOIN admins a ON a.id=m.uploaded_by {$mwh} ORDER BY m.created_at DESC LIMIT 200")->fetchAll();
?>
<div class="pg-head ani flex-between">
  <div><div class="pg-title">Media <em>Library</em></div><div class="pg-sub"><?= count($media_items) ?> files</div></div>
  <button class="btn btn-primary" onclick="openModal('uploadMediaModal')"><svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg> Upload Files</button>
</div>
<div class="filter-bar ani d1">
  <?php foreach (['all'=>'All','products'=>'Products','categories'=>'Categories','hero'=>'Hero','misc'=>'Misc'] as $k=>$l): ?><a href="?page=media&folder=<?= $k ?>" class="filter-btn <?= $mfolder===$k?'on':'' ?>"><?= $l ?></a><?php endforeach; ?>
</div>
<div class="media-grid ani d2">
  <?php foreach ($media_items as $m): ?>
  <div class="media-item"><img src="<?= h($m['file_url']) ?>" alt="" loading="lazy">
    <div class="media-item-overlay">
      <a href="<?= h($m['file_url']) ?>" target="_blank" class="btn-icon" style="background:rgba(255,255,255,0.2);color:white"><svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>
      <form method="POST" style="display:inline;margin:0"><?= csrf_field() ?><input type="hidden" name="action" value="delete_media"><input type="hidden" name="id" value="<?= $m['id'] ?>"><button type="submit" class="btn-icon" style="background:rgba(220,53,69,0.7);color:white" onclick="return confirm('Delete?')"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg></button></form>
    </div>
    <div class="media-item-info"><?= h($m['file_name']) ?></div>
  </div>
  <?php endforeach; ?>
</div>
<div class="modal-bg" id="uploadMediaModal"><div class="modal"><div class="modal-head"><div class="modal-title">Upload Files</div><div class="modal-close" onclick="closeModal('uploadMediaModal')"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div></div>
  <form method="POST" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="action" value="upload_media">
  <div class="modal-body"><div class="fg"><label class="fl">Folder</label><select name="folder" class="fsel"><?php foreach (['products','categories','hero','misc'] as $f): ?><option value="<?= $f ?>"><?= ucfirst($f) ?></option><?php endforeach; ?></select></div>
    <div class="upload-zone" onclick="this.querySelector('input').click()"><svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg><div class="upload-zone-text">Click to select files</div><div class="upload-zone-sub">Multiple images supported</div><input type="file" name="files[]" multiple accept="image/*" style="display:none" onchange="showFileCount(this)"></div>
  </div>
  <div class="modal-foot"><button type="button" class="btn btn-secondary" onclick="closeModal('uploadMediaModal')">Cancel</button><button type="submit" class="btn btn-primary">Upload</button></div>
  </form>
</div></div>

<?php
// ── INQUIRIES ────────────────────────────────────────────────
elseif ($page === 'inquiries'):
    $filt = $_GET['filter'] ?? 'all';
    $valid_st = ['new_inquiry','contacted','confirmed','designing','completed','delivered','cancelled'];
    $inqs = db()->query("SELECT i.*,cu.name cname,cu.phone FROM inquiries i JOIN customers cu ON cu.id=i.customer_id ORDER BY i.created_at DESC")->fetchAll();
    $inqs_hydrated = [];
    foreach ($inqs as $raw) {
        $pname = 'General Inquiry';
        if (!empty($raw['product_id'])) { $pq = db()->prepare("SELECT name FROM products WHERE id=?"); $pq->execute([(int)$raw['product_id']]); $pname = $pq->fetchColumn() ?: 'General'; }
        $raw['pname'] = $pname;
        if ($filt === 'all' || $raw['status'] === $filt) $inqs_hydrated[] = $raw;
    }
    $inqs_all = $inqs_hydrated;
    $prods = db()->query('SELECT id,name FROM products WHERE status="active" ORDER BY name')->fetchAll();
    $scounts = [];
    foreach (db()->query('SELECT status,COUNT(*) c FROM inquiries GROUP BY status')->fetchAll() as $r) $scounts[$r['status']]=$r['c'];
    $edit_inq = null;
    if (isset($_GET['edit'])) { $s=db()->prepare('SELECT i.*,cu.name cname,cu.phone FROM inquiries i JOIN customers cu ON cu.id=i.customer_id WHERE i.id=?');$s->execute([(int)$_GET['edit']]);$edit_inq=$s->fetch(); }
?>
<div class="pg-head ani">
  <div class="flex-between">
    <div><div class="pg-title">Inquiry <em>Management</em></div><div class="pg-sub"><?= count($inqs_all) ?> inquiries · <?= $scounts['new_inquiry']??0 ?> new</div></div>
    <div class="flex-row">
      <a href="?page=inquiries&action=export_csv&type=inquiries" class="btn btn-secondary"><svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Export CSV</a>
      <button class="btn btn-primary" onclick="openModal('addInqModal')"><svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Log Inquiry</button>
    </div>
  </div>
</div>
<div class="stats-grid ani d1" style="grid-template-columns:repeat(7,1fr);gap:9px;margin-bottom:18px">
  <?php $sc_map=['new_inquiry'=>['New','sc-rose'],'contacted'=>['Contacted','sc-gold'],'confirmed'=>['Confirmed','sc-blue'],'designing'=>['Designing','sc-blue'],'completed'=>['Completed','sc-green'],'delivered'=>['Delivered','sc-green'],'cancelled'=>['Cancelled','sc-blue']];
  foreach ($sc_map as $st=>[$lbl,$cls]): ?><div class="stat-card <?= $cls ?>" style="padding:13px"><div class="stat-val" style="font-size:22px"><?= $scounts[$st]??0 ?></div><div class="stat-lbl"><?= $lbl ?></div></div><?php endforeach; ?>
</div>
<div class="filter-bar ani d2">
  <a href="?page=inquiries" class="filter-btn <?= $filt==='all'?'on':'' ?>">All</a>
  <?php foreach (['new_inquiry'=>'New','contacted'=>'Contacted','confirmed'=>'Confirmed','designing'=>'Designing','completed'=>'Completed','delivered'=>'Delivered','cancelled'=>'Cancelled'] as $k=>$l): ?>
  <a href="?page=inquiries&filter=<?= $k ?>" class="filter-btn <?= $filt===$k?'on':'' ?>"><?= $l ?></a>
  <?php endforeach; ?>
</div>
<div class="card ani d3"><div class="tbl-wrap"><table class="tbl">
  <thead><tr><th>#</th><th>Customer</th><th>Product</th><th>Source</th><th>Status</th><th>Follow-up</th><th>Created</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($inqs_all as $r): ?>
    <tr>
      <td class="text-muted" style="font-size:11px">#<?= $r['id'] ?></td>
      <td><div class="flex-row"><div class="ava"><?= strtoupper(substr($r['cname'],0,1)) ?></div><div><div class="text-bold" style="font-size:13px"><?= h($r['cname']) ?></div><div class="text-muted"><?= h($r['phone']) ?></div></div></div></td>
      <td><span class="truncate"><?= h($r['pname']) ?></span></td>
      <td><span class="tag"><?= ucfirst($r['source']) ?></span></td>
      <td>
        <form method="POST" style="display:inline"><?= csrf_field() ?><input type="hidden" name="action" value="update_inquiry_status"><input type="hidden" name="id" value="<?= $r['id'] ?>">
          <select name="status" class="fsel" style="padding:3px 26px 3px 8px;font-size:10.5px;height:26px;border-radius:14px;width:auto" onchange="this.form.submit()">
            <?php foreach ($valid_st as $st): ?><option value="<?= $st ?>" <?= $r['status']===$st?'selected':'' ?>><?= str_replace('_',' ',ucfirst($st)) ?></option><?php endforeach; ?>
          </select>
        </form>
      </td>
      <td class="text-muted"><?= $r['followup_date']?date('d M',strtotime($r['followup_date'])):'' ?></td>
      <td class="text-muted"><?= time_ago($r['created_at']) ?></td>
      <td><div class="action-row">
        <a href="?page=inquiries&edit=<?= $r['id'] ?>" class="btn-icon"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
        <?php if (!in_array($r['status'],['cancelled','delivered'])): ?>
        <a href="?page=orders&from_inquiry=<?= $r['id'] ?>" class="btn-icon success" title="Convert to Order"><svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg></a>
        <?php endif; ?>
        <a href="?page=inquiries&action=delete_inquiry&id=<?= $r['id'] ?>" class="btn-icon danger" onclick="return confirm('Delete inquiry?')"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg></a>
      </div></td>
    </tr>
    <?php endforeach; if (empty($inqs_all)): ?><tr><td colspan="8" class="tbl-empty">No inquiries found.</td></tr><?php endif; ?>
  </tbody>
</table></div></div>

<div class="modal-bg" id="addInqModal"><div class="modal modal-wide"><div class="modal-head"><div class="modal-title">Log New Inquiry</div><div class="modal-close" onclick="closeModal('addInqModal')"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div></div>
  <form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="add_inquiry">
  <div class="modal-body">
    <div class="frow"><div class="fg"><label class="fl">Customer Name</label><input type="text" name="customer_name" class="fi" placeholder="Priya Sharma" required></div><div class="fg"><label class="fl">Phone</label><input type="text" name="phone" class="fi" placeholder="+91 98765 43210" required></div></div>
    <div class="frow"><div class="fg"><label class="fl">City</label><input type="text" name="city" class="fi"></div><div class="fg"><label class="fl">Product</label><select name="product_id" class="fsel"><option value="">General Inquiry</option><?php foreach ($prods as $p): ?><option value="<?= $p['id'] ?>"><?= h($p['name']) ?></option><?php endforeach; ?></select></div></div>
    <div class="frow"><div class="fg"><label class="fl">Source</label><select name="source" class="fsel"><?php foreach (['instagram','whatsapp','facebook','referral','direct','other'] as $s): ?><option><?= $s ?></option><?php endforeach; ?></select></div><div class="fg"><label class="fl">Status</label><select name="status" class="fsel"><?php foreach ($valid_st as $st): ?><option value="<?= $st ?>"><?= str_replace('_',' ',ucfirst($st)) ?></option><?php endforeach; ?></select></div></div>
    <div class="frow"><div class="fg"><label class="fl">Follow-up Date</label><input type="date" name="followup_date" class="fi"></div><div class="fg"><label class="fl">WA Clicked At</label><input type="datetime-local" name="wa_clicked_at" class="fi"></div></div>
    <div class="fg"><label class="fl">Notes</label><textarea name="notes" class="fta" style="min-height:65px"></textarea></div>
  </div>
  <div class="modal-foot"><button type="button" class="btn btn-secondary" onclick="closeModal('addInqModal')">Cancel</button><button type="submit" class="btn btn-primary">Save Inquiry</button></div>
  </form>
</div></div>

<?php if ($edit_inq): ?>
<div class="modal-bg open"><div class="modal modal-wide"><div class="modal-head"><div class="modal-title">Edit Inquiry #<?= $edit_inq['id'] ?></div><a href="?page=inquiries" class="modal-close"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></a></div>
  <form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="edit_inquiry"><input type="hidden" name="id" value="<?= $edit_inq['id'] ?>">
  <div class="modal-body">
    <div class="frow"><div class="fg"><label class="fl">Product</label><select name="product_id" class="fsel"><option value="">General</option><?php foreach ($prods as $p): ?><option value="<?= $p['id'] ?>" <?= $edit_inq['product_id']==$p['id']?'selected':'' ?>><?= h($p['name']) ?></option><?php endforeach; ?></select></div><div class="fg"><label class="fl">Source</label><select name="source" class="fsel"><?php foreach (['instagram','whatsapp','facebook','referral','direct','other'] as $s): ?><option value="<?= $s ?>" <?= $edit_inq['source']===$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?></select></div></div>
    <div class="frow"><div class="fg"><label class="fl">Status</label><select name="status" class="fsel"><?php foreach ($valid_st as $st): ?><option value="<?= $st ?>" <?= $edit_inq['status']===$st?'selected':'' ?>><?= str_replace('_',' ',ucfirst($st)) ?></option><?php endforeach; ?></select></div><div class="fg"><label class="fl">Follow-up</label><input type="date" name="followup_date" class="fi" value="<?= h($edit_inq['followup_date']) ?>"></div></div>
    <div class="fg"><label class="fl">Notes</label><textarea name="notes" class="fta"><?= h($edit_inq['notes']) ?></textarea></div>
  </div>
  <div class="modal-foot"><a href="?page=inquiries" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Update Inquiry</button></div>
  </form>
</div></div>
<?php endif; ?>

<?php
// ── ORDERS ───────────────────────────────────────────────────
elseif ($page === 'orders'):
    $of = $_GET['filter'] ?? 'all';
    $valid_ord = ['pending','confirmed','in_production','dispatched','delivered','cancelled'];
    $owh = ($of!=='all' && in_array($of,$valid_ord)) ? "AND o.status='$of'" : '';
    $orders = db()->query("SELECT o.*,cu.name cname,cu.phone,p.name pname FROM orders o JOIN customers cu ON cu.id=o.customer_id JOIN products p ON p.id=o.product_id WHERE 1=1 {$owh} ORDER BY o.created_at DESC")->fetchAll();
    $all_prods = db()->query('SELECT id,name,price FROM products WHERE status="active" ORDER BY name')->fetchAll();
    $all_custs = db()->query('SELECT id,name,phone FROM customers ORDER BY name')->fetchAll();
    $ord_rev   = (float)db()->query("SELECT COALESCE(SUM(final_price),0) FROM orders WHERE status='delivered'")->fetchColumn();
    $edit_ord  = null;
    if (isset($_GET['edit'])) { $s=db()->prepare('SELECT * FROM orders WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit_ord=$s->fetch(); }
    // Pre-fill from inquiry
    $prefill_inq = null;
    if (isset($_GET['from_inquiry'])) {
        $fi = db()->prepare('SELECT i.*,cu.name cname,cu.phone,cu.id cid,p.price pprice,p.name pname,p.id pid FROM inquiries i JOIN customers cu ON cu.id=i.customer_id LEFT JOIN products p ON p.id=i.product_id WHERE i.id=?');
        $fi->execute([(int)$_GET['from_inquiry']]); $prefill_inq = $fi->fetch();
    }
?>
<div class="pg-head ani">
  <div class="flex-between">
    <div><div class="pg-title">Order <em>Management</em></div><div class="pg-sub"><?= count($orders) ?> orders · &#8377;<?= number_format($ord_rev) ?> revenue</div></div>
    <div class="flex-row">
      <a href="?page=orders&action=export_csv&type=orders" class="btn btn-secondary"><svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Export CSV</a>
      <button class="btn btn-primary" onclick="openModal('addOrderModal')"><svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> New Order</button>
    </div>
  </div>
</div>
<?php if ($prefill_inq): ?>
<div class="flash flash-info" style="margin-bottom:18px">
  Converting inquiry from <strong><?= h($prefill_inq['cname']) ?></strong><?= $prefill_inq['pname']?' for '.h($prefill_inq['pname']):'' ?> — form pre-filled below.
</div>
<?php endif; ?>
<div class="filter-bar ani d1">
  <a href="?page=orders" class="filter-btn <?= $of==='all'?'on':'' ?>">All</a>
  <?php foreach (['pending'=>'Pending','confirmed'=>'Confirmed','in_production'=>'In Production','dispatched'=>'Dispatched','delivered'=>'Delivered','cancelled'=>'Cancelled'] as $k=>$l): ?>
  <a href="?page=orders&filter=<?= $k ?>" class="filter-btn <?= $of===$k?'on':'' ?>"><?= $l ?></a>
  <?php endforeach; ?>
</div>
<div class="card ani d2"><div class="tbl-wrap"><table class="tbl">
  <thead><tr><th>#</th><th>Customer</th><th>Product</th><th>Qty</th><th>Amount</th><th>Status</th><th>Expected</th><th>Created</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($orders as $o): ?>
    <tr>
      <td class="text-muted" style="font-size:11px">#<?= $o['id'] ?></td>
      <td><div class="flex-row"><div class="ava"><?= strtoupper(substr($o['cname'],0,1)) ?></div><div><div class="text-bold" style="font-size:13px"><?= h($o['cname']) ?></div><div class="text-muted"><?= h($o['phone']) ?></div></div></div></td>
      <td><span class="truncate"><?= h($o['pname']) ?></span></td>
      <td class="text-muted"><?= $o['quantity'] ?></td>
      <td><div class="text-bold text-rose">&#8377;<?= number_format((float)$o['final_price']) ?></div><?php if (((float)$o['discount_amount'])>0): ?><div class="text-muted">-&#8377;<?= number_format((float)$o['discount_amount']) ?></div><?php endif; ?></td>
      <td><span class="badge <?= statusBadge($o['status']) ?>"><?= str_replace('_',' ',ucfirst($o['status'])) ?></span></td>
      <td class="text-muted"><?= $o['expected_by']?date('d M Y',strtotime($o['expected_by'])):'' ?></td>
      <td class="text-muted"><?= time_ago($o['created_at']) ?></td>
      <td><div class="action-row">
        <a href="?page=orders&edit=<?= $o['id'] ?>" class="btn-icon"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
        <a href="?page=orders&action=delete_order&id=<?= $o['id'] ?>" class="btn-icon danger" onclick="return confirm('Delete order?')"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg></a>
      </div></td>
    </tr>
    <?php endforeach; if (empty($orders)): ?><tr><td colspan="9" class="tbl-empty">No orders yet.</td></tr><?php endif; ?>
  </tbody>
</table></div></div>

<div class="modal-bg<?= $prefill_inq?' open':'' ?>" id="addOrderModal"><div class="modal modal-wide"><div class="modal-head"><div class="modal-title"><?= $prefill_inq?'Convert Inquiry to Order':'Create New Order' ?></div><div class="modal-close" onclick="closeModal('addOrderModal')"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div></div>
  <form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="add_order">
  <?php if ($prefill_inq): ?><input type="hidden" name="inquiry_id" value="<?= $prefill_inq['id'] ?>"><?php endif; ?>
  <div class="modal-body">
    <div class="frow">
      <div class="fg"><label class="fl">Customer</label><select name="customer_id" class="fsel" required><option value="">Select customer</option><?php foreach ($all_custs as $c): ?><option value="<?= $c['id'] ?>" <?= ($prefill_inq && $prefill_inq['cid']==$c['id'])?'selected':'' ?>><?= h($c['name']) ?> — <?= h($c['phone']) ?></option><?php endforeach; ?></select></div>
      <div class="fg"><label class="fl">Product</label><select name="product_id" class="fsel" required id="ordProd"><option value="">Select product</option><?php foreach ($all_prods as $p): ?><option value="<?= $p['id'] ?>" data-price="<?= $p['price'] ?>" <?= ($prefill_inq && $prefill_inq['pid']==$p['id'])?'selected':'' ?>><?= h($p['name']) ?> — &#8377;<?= number_format((float)$p['price']) ?></option><?php endforeach; ?></select></div>
    </div>
    <div class="frow3"><div class="fg"><label class="fl">Quantity</label><input type="number" name="quantity" id="oqty" class="fi" value="1" min="1" oninput="calcFinal()"></div><div class="fg"><label class="fl">Unit Price (INR)</label><input type="number" name="unit_price" id="ouprice" class="fi" placeholder="1299" step="0.01" value="<?= $prefill_inq?h($prefill_inq['pprice']):'' ?>" oninput="calcFinal()" required></div><div class="fg"><label class="fl">Discount (INR)</label><input type="number" name="discount_amount" id="odisc" class="fi" value="0" step="0.01" oninput="calcFinal()"></div></div>
    <div class="frow"><div class="fg"><label class="fl">Final Price (auto)</label><input type="text" id="ofinal" class="fi" readonly style="background:var(--cream);font-weight:700;color:var(--rose)"></div><div class="fg"><label class="fl">Status</label><select name="status" class="fsel"><?php foreach ($valid_ord as $s): ?><option value="<?= $s ?>"><?= str_replace('_',' ',ucfirst($s)) ?></option><?php endforeach; ?></select></div></div>
    <div class="frow"><div class="fg"><label class="fl">Expected By</label><input type="date" name="expected_by" class="fi"></div><div class="fg"><label class="fl">Linked Inquiry #</label><input type="number" name="inquiry_id" class="fi" placeholder="Optional" value="<?= $prefill_inq?h($prefill_inq['id']):'' ?>"></div></div>
    <div class="fg"><label class="fl">Customisation Notes</label><textarea name="customisation" class="fta" style="min-height:65px" placeholder="Names, dates, colours..."></textarea></div>
    <div class="fg"><label class="fl">Delivery Address</label><textarea name="delivery_address" class="fta" style="min-height:65px"></textarea></div>
    <div class="fg"><label class="fl">Admin Notes</label><textarea name="admin_notes" class="fta" style="min-height:55px"></textarea></div>
  </div>
  <div class="modal-foot"><button type="button" class="btn btn-secondary" onclick="closeModal('addOrderModal')">Cancel</button><button type="submit" class="btn btn-primary"><?= $prefill_inq?'Convert to Order':'Create Order' ?></button></div>
  </form>
</div></div>

<?php if ($edit_ord): ?>
<div class="modal-bg open"><div class="modal"><div class="modal-head"><div class="modal-title">Edit Order #<?= $edit_ord['id'] ?></div><a href="?page=orders" class="modal-close"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></a></div>
  <form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="edit_order"><input type="hidden" name="id" value="<?= $edit_ord['id'] ?>">
  <div class="modal-body">
    <div class="fg"><label class="fl">Status</label><select name="status" class="fsel"><?php foreach ($valid_ord as $s): ?><option value="<?= $s ?>" <?= $edit_ord['status']===$s?'selected':'' ?>><?= str_replace('_',' ',ucfirst($s)) ?></option><?php endforeach; ?></select></div>
    <div class="frow"><div class="fg"><label class="fl">Expected By</label><input type="date" name="expected_by" class="fi" value="<?= h($edit_ord['expected_by']) ?>"></div><div class="fg"><label class="fl">Delivered On</label><input type="date" name="delivered_on" class="fi" value="<?= h($edit_ord['delivered_on']) ?>"></div></div>
    <div class="fg"><label class="fl">Admin Notes</label><textarea name="admin_notes" class="fta"><?= h($edit_ord['admin_notes']) ?></textarea></div>
  </div>
  <div class="modal-foot"><a href="?page=orders" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Update Order</button></div>
  </form>
</div></div>
<?php endif; ?>

<?php
// ── CUSTOMERS ────────────────────────────────────────────────
elseif ($page === 'customers'):
    $custs = db()->query('SELECT c.*,(SELECT COUNT(*) FROM inquiries i WHERE i.customer_id=c.id) inq_c,(SELECT COUNT(*) FROM orders o WHERE o.customer_id=c.id) ord_c FROM customers c ORDER BY c.created_at DESC')->fetchAll();
    $edit_cust = null;
    if (isset($_GET['edit'])) { $s=db()->prepare('SELECT * FROM customers WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit_cust=$s->fetch(); }
?>
<div class="pg-head ani">
  <div class="flex-between">
    <div><div class="pg-title">Customer <em>Directory</em></div><div class="pg-sub"><?= count($custs) ?> customers</div></div>
    <div class="flex-row">
      <a href="?page=customers&action=export_csv&type=customers" class="btn btn-secondary"><svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Export CSV</a>
      <button class="btn btn-primary" onclick="openModal('addCustModal')"><svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add Customer</button>
    </div>
  </div>
</div>
<div class="card ani d1"><div class="tbl-wrap"><table class="tbl">
  <thead><tr><th>Customer</th><th>Phone</th><th>City</th><th>Instagram</th><th>Inquiries</th><th>Orders</th><th>Since</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($custs as $c): ?>
    <tr>
      <td><div class="flex-row"><div class="ava"><?= strtoupper(substr($c['name'],0,1)) ?></div><div><div class="text-bold" style="font-size:13px"><?= h($c['name']) ?></div><?php if ($c['email']): ?><div class="text-muted"><?= h($c['email']) ?></div><?php endif; ?></div></div></td>
      <td class="text-muted"><?= h($c['phone']) ?></td>
      <td class="text-muted"><?= h($c['city']??'—') ?></td>
      <td><?= $c['instagram']?'<span class="text-rose text-sm">'.h($c['instagram']).'</span>':'<span class="text-muted">—</span>' ?></td>
      <td><span class="badge badge-blue"><?= $c['inq_c'] ?></span></td>
      <td><span class="badge badge-green"><?= $c['ord_c'] ?></span></td>
      <td class="text-muted"><?= date('d M Y',strtotime($c['created_at'])) ?></td>
      <td><div class="action-row"><a href="?page=customers&edit=<?= $c['id'] ?>" class="btn-icon"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a><a href="?page=customers&action=delete_customer&id=<?= $c['id'] ?>" class="btn-icon danger" onclick="return confirm('Delete customer?')"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg></a></div></td>
    </tr>
    <?php endforeach; if (empty($custs)): ?><tr><td colspan="8" class="tbl-empty">No customers yet.</td></tr><?php endif; ?>
  </tbody>
</table></div></div>

<div class="modal-bg" id="addCustModal"><div class="modal"><div class="modal-head"><div class="modal-title">Add Customer</div><div class="modal-close" onclick="closeModal('addCustModal')"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div></div>
  <form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="add_customer">
  <div class="modal-body">
    <div class="frow"><div class="fg"><label class="fl">Full Name</label><input type="text" name="name" class="fi" placeholder="Priya Sharma" required></div><div class="fg"><label class="fl">Phone</label><input type="text" name="phone" class="fi" placeholder="+919876543210" required></div></div>
    <div class="frow"><div class="fg"><label class="fl">Email</label><input type="email" name="email" class="fi"></div><div class="fg"><label class="fl">City</label><input type="text" name="city" class="fi"></div></div>
    <div class="fg"><label class="fl">Instagram</label><input type="text" name="instagram" class="fi" placeholder="@handle"></div>
    <div class="fg"><label class="fl">Notes</label><textarea name="notes" class="fta" style="min-height:65px"></textarea></div>
  </div>
  <div class="modal-foot"><button type="button" class="btn btn-secondary" onclick="closeModal('addCustModal')">Cancel</button><button type="submit" class="btn btn-primary">Add Customer</button></div>
  </form>
</div></div>

<?php if ($edit_cust): ?>
<div class="modal-bg open"><div class="modal"><div class="modal-head"><div class="modal-title">Edit Customer</div><a href="?page=customers" class="modal-close"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></a></div>
  <form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="edit_customer"><input type="hidden" name="id" value="<?= $edit_cust['id'] ?>">
  <div class="modal-body">
    <div class="frow"><div class="fg"><label class="fl">Full Name</label><input type="text" name="name" class="fi" value="<?= h($edit_cust['name']) ?>" required></div><div class="fg"><label class="fl">Email</label><input type="email" name="email" class="fi" value="<?= h($edit_cust['email']) ?>"></div></div>
    <div class="frow"><div class="fg"><label class="fl">City</label><input type="text" name="city" class="fi" value="<?= h($edit_cust['city']) ?>"></div><div class="fg"><label class="fl">Instagram</label><input type="text" name="instagram" class="fi" value="<?= h($edit_cust['instagram']) ?>"></div></div>
    <div class="fg"><label class="fl">Notes</label><textarea name="notes" class="fta"><?= h($edit_cust['notes']) ?></textarea></div>
  </div>
  <div class="modal-foot"><a href="?page=customers" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Update Customer</button></div>
  </form>
</div></div>
<?php endif; ?>

<?php
// ── OFFERS ───────────────────────────────────────────────────
elseif ($page === 'offers'):
    $offers = db()->query('SELECT * FROM offers ORDER BY created_at DESC')->fetchAll();
    $edit_offer = null;
    if (isset($_GET['edit'])) { $s=db()->prepare('SELECT * FROM offers WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit_offer=$s->fetch(); }
?>
<div class="pg-head ani flex-between">
  <div><div class="pg-title">Offers <em>and Coupons</em></div></div>
  <button class="btn btn-primary" onclick="openModal('addOfferModal')"><svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Create Offer</button>
</div>
<div class="g-auto3 ani d1">
  <?php foreach ($offers as $o):
    $os = offer_status($o['start_date'],$o['end_date'],(int)$o['is_active']);
    $disp = match($o['discount_type']){'percentage'=>$o['discount_value'].'%','flat'=>'&#8377;'.number_format((float)$o['discount_value']),'free_shipping'=>'Free Shipping',default=>ucfirst($o['discount_type'])};
  ?>
  <div class="offer-card">
    <div style="font-size:14.5px;font-weight:700;color:var(--text-dark);margin-bottom:9px"><?= h($o['name']) ?></div>
    <div class="flex-row flex-wrap" style="gap:5px;margin-bottom:9px"><span class="badge badge-rose"><?= ucfirst(str_replace('_',' ',$o['discount_type'])) ?></span><span class="badge badge-gold"><?= $disp ?></span><span class="badge <?= statusBadge(strtolower($os)) ?>"><?= $os ?></span></div>
    <?php if ($o['coupon_code']): ?><div style="font-family:monospace;font-size:12.5px;font-weight:700;color:var(--rose);background:var(--rose-pale);display:inline-block;padding:2px 9px;border-radius:5px;margin-bottom:7px"><?= h($o['coupon_code']) ?></div><?php endif; ?>
    <div class="text-muted text-sm" style="margin-bottom:5px"><?= date('d M Y',strtotime($o['start_date'])) ?> to <?= date('d M Y',strtotime($o['end_date'])) ?></div>
    <div class="action-row" style="margin-top:13px"><a href="?page=offers&edit=<?= $o['id'] ?>" class="btn btn-sm btn-secondary">Edit</a><a href="?page=offers&action=delete_offer&id=<?= $o['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a></div>
  </div>
  <?php endforeach; if (empty($offers)): ?><p class="text-muted">No offers yet.</p><?php endif; ?>
</div>
<div class="modal-bg" id="addOfferModal"><div class="modal modal-wide"><div class="modal-head"><div class="modal-title">Create Offer</div><div class="modal-close" onclick="closeModal('addOfferModal')"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div></div>
  <form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="add_offer">
  <div class="modal-body">
    <div class="frow"><div class="fg"><label class="fl">Offer Name</label><input type="text" name="name" class="fi" placeholder="Diwali Special" required></div><div class="fg"><label class="fl">Coupon Code</label><input type="text" name="coupon_code" class="fi" placeholder="DIWALI25" style="text-transform:uppercase"></div></div>
    <div class="frow"><div class="fg"><label class="fl">Discount Type</label><select name="discount_type" class="fsel"><option value="percentage">Percentage (%)</option><option value="flat">Flat (INR)</option><option value="free_shipping">Free Shipping</option><option value="combo">Combo Deal</option></select></div><div class="fg"><label class="fl">Discount Value</label><input type="number" name="discount_value" class="fi" placeholder="20" step="0.01" value="0"></div></div>
    <div class="frow3"><div class="fg"><label class="fl">Start Date</label><input type="date" name="start_date" class="fi" value="<?= date('Y-m-d') ?>" required></div><div class="fg"><label class="fl">End Date</label><input type="date" name="end_date" class="fi" value="<?= date('Y-m-d',strtotime('+7 days')) ?>" required></div><div class="fg"><label class="fl">Max Uses</label><input type="number" name="max_uses" class="fi" placeholder="Unlimited"></div></div>
    <div class="fg"><label class="fl">Min Order Value (INR)</label><input type="number" name="min_order_value" class="fi" placeholder="Leave blank for no minimum" step="0.01"></div>
  </div>
  <div class="modal-foot"><button type="button" class="btn btn-secondary" onclick="closeModal('addOfferModal')">Cancel</button><button type="submit" class="btn btn-primary">Create Offer</button></div>
  </form>
</div></div>
<?php if ($edit_offer): ?>
<div class="modal-bg open"><div class="modal modal-wide"><div class="modal-head"><div class="modal-title">Edit Offer</div><a href="?page=offers" class="modal-close"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></a></div>
  <form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="edit_offer"><input type="hidden" name="id" value="<?= $edit_offer['id'] ?>">
  <div class="modal-body">
    <div class="frow"><div class="fg"><label class="fl">Offer Name</label><input type="text" name="name" class="fi" value="<?= h($edit_offer['name']) ?>" required></div><div class="fg"><label class="fl">Coupon Code</label><input type="text" name="coupon_code" class="fi" value="<?= h($edit_offer['coupon_code']) ?>" style="text-transform:uppercase"></div></div>
    <div class="frow"><div class="fg"><label class="fl">Discount Type</label><select name="discount_type" class="fsel"><?php foreach (['percentage','flat','free_shipping','combo'] as $dt): ?><option value="<?= $dt ?>" <?= $edit_offer['discount_type']===$dt?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$dt)) ?></option><?php endforeach; ?></select></div><div class="fg"><label class="fl">Value</label><input type="number" name="discount_value" class="fi" value="<?= h($edit_offer['discount_value']) ?>" step="0.01"></div></div>
    <div class="frow3"><div class="fg"><label class="fl">Start Date</label><input type="date" name="start_date" class="fi" value="<?= h($edit_offer['start_date']) ?>" required></div><div class="fg"><label class="fl">End Date</label><input type="date" name="end_date" class="fi" value="<?= h($edit_offer['end_date']) ?>" required></div><div class="fg"><label class="fl">Max Uses</label><input type="number" name="max_uses" class="fi" value="<?= h($edit_offer['max_uses']) ?>"></div></div>
    <div class="fg"><label class="fl">Min Order Value</label><input type="number" name="min_order_value" class="fi" value="<?= h($edit_offer['min_order_value']) ?>" step="0.01"></div>
  </div>
  <div class="modal-foot"><a href="?page=offers" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Update Offer</button></div>
  </form>
</div></div>
<?php endif; ?>

<?php
// ── WHATSAPP ─────────────────────────────────────────────────
elseif ($page === 'whatsapp'):
    $wa_set  = db()->query('SELECT * FROM whatsapp_settings WHERE is_primary=1 LIMIT 1')->fetch();
    $wa_tmpl = db()->query('SELECT wt.*,o.name oname FROM whatsapp_templates wt LEFT JOIN occasions o ON o.id=wt.occasion_id ORDER BY wt.is_default DESC')->fetchAll();
    $def_tmpl= null; foreach ($wa_tmpl as $t) { if ($t['is_default']) { $def_tmpl=$t; break; } }
    $wa_num  = $wa_set['phone_number'] ?? '';
    $wa_today= (int)db()->query('SELECT COUNT(*) FROM inquiries WHERE DATE(wa_clicked_at)=CURDATE()')->fetchColumn();
    $wa_week = (int)db()->query('SELECT COUNT(*) FROM inquiries WHERE wa_clicked_at>=NOW()-INTERVAL 7 DAY')->fetchColumn();
    $wa_month= (int)db()->query('SELECT COUNT(*) FROM inquiries WHERE wa_clicked_at>=NOW()-INTERVAL 30 DAY')->fetchColumn();
    $wa_all  = (int)db()->query('SELECT COUNT(*) FROM inquiries WHERE wa_clicked_at IS NOT NULL')->fetchColumn();
?>
<div class="pg-head ani"><div class="pg-title">WhatsApp <em>Flow Manager</em></div></div>
<div class="g2 ani d1">
  <div>
    <div class="card mb24"><div class="card-h"><div class="card-title">Primary Phone Number</div></div><div class="card-body">
      <form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="save_wa_number">
        <div class="fg"><label class="fl">Phone (with country code)</label><input type="text" name="phone_number" class="fi" value="<?= h($wa_num) ?>" placeholder="+919876543210"></div>
        <button type="submit" class="btn btn-primary">Update Number</button>
      </form>
    </div></div>
    <?php if ($def_tmpl): ?>
    <div class="card"><div class="card-h"><div><div class="card-title">Default Template</div></div></div><div class="card-body">
      <form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="save_wa_template"><input type="hidden" name="tmpl_id" value="<?= $def_tmpl['id'] ?>">
        <div class="fg"><label class="fl">Label</label><input type="text" name="label" class="fi" value="<?= h($def_tmpl['label']) ?>"></div>
        <div class="fg"><label class="fl">Template</label><textarea name="template" class="fta" style="min-height:150px;font-family:monospace;font-size:12px"><?= h($def_tmpl['template']) ?></textarea></div>
        <div style="font-size:11.5px;color:var(--text-light);margin-bottom:13px">Variables: <code style="background:var(--cream-deep);padding:1px 5px;border-radius:4px">{product_name}</code> <code style="background:var(--cream-deep);padding:1px 5px;border-radius:4px">{price}</code></div>
        <button type="submit" class="btn btn-primary">Save Template</button>
      </form>
    </div></div>
    <?php endif; ?>
  </div>
  <div>
    <div class="wa-dark mb24"><div class="wa-label">Live Preview</div><div class="wa-bubble"><div class="wa-text"><?= nl2br(h(str_replace(['{product_name}','{price}'],['Eternal Love Frame','Rs.999'],$def_tmpl['template']??'Hello! I am interested in your products.'))) ?></div><div class="wa-tick">Sent</div></div></div>
    <div class="card"><div class="card-h"><div><div class="card-title">Click Statistics</div></div></div><div class="card-body">
      <div class="g2" style="gap:11px">
        <?php foreach ([['Today',$wa_today],['This Week',$wa_week],['This Month',$wa_month],['All Time',$wa_all]] as $ws): ?>
        <div style="background:var(--cream);border:1px solid var(--border-light);border-radius:var(--radius-sm);padding:15px;text-align:center"><div style="font-size:24px;font-weight:700"><?= $ws[1] ?></div><div class="text-muted text-sm" style="margin-top:2px"><?= $ws[0] ?></div></div>
        <?php endforeach; ?>
      </div>
    </div></div>
  </div>
</div>

<?php
// ── TESTIMONIALS ─────────────────────────────────────────────
elseif ($page === 'testimonials'):
    $testis = db()->query('SELECT t.*,p.name pname FROM testimonials t LEFT JOIN products p ON p.id=t.product_id ORDER BY t.created_at DESC')->fetchAll();
    $prods  = db()->query('SELECT id,name FROM products WHERE status="active" ORDER BY name')->fetchAll();
    $edit_t = null;
    if (isset($_GET['edit'])) { $s=db()->prepare('SELECT * FROM testimonials WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit_t=$s->fetch(); }
?>
<div class="pg-head ani flex-between">
  <div><div class="pg-title">Testimonial <em>Management</em></div><div class="pg-sub"><?= count($testis) ?> reviews</div></div>
  <button class="btn btn-primary" onclick="openModal('addTestiModal')"><svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add Review</button>
</div>
<div class="g-auto3 ani d1">
  <?php foreach ($testis as $t): ?>
  <div class="card" style="padding:20px">
    <div class="flex-between" style="margin-bottom:8px"><div style="color:var(--gold);font-size:12px"><?= str_repeat('&#9733;',(int)$t['rating']).str_repeat('&#9734;',5-(int)$t['rating']) ?></div><div class="flex-row" style="gap:5px"><?php if (!$t['is_approved']): ?><span class="badge badge-amber">Pending</span><?php endif; ?><?php if ($t['is_featured']): ?><span class="badge badge-gold">Featured</span><?php endif; ?></div></div>
    <div style="font-family:var(--ffs);font-style:italic;font-size:14px;color:var(--text-mid);line-height:1.75;margin-bottom:14px">"<?= h($t['review']) ?>"</div>
    <div class="flex-row" style="margin-bottom:12px"><div class="ava" style="border-radius:50%"><?= strtoupper(substr($t['name'],0,1)) ?></div><div><div class="text-bold" style="font-size:13px"><?= h($t['name']) ?></div><?php if ($t['instagram']): ?><div class="text-rose text-sm"><?= h($t['instagram']) ?></div><?php endif; ?></div></div>
    <div class="flex-between" style="padding-top:11px;border-top:1px solid var(--border-light)">
      <span class="text-muted text-sm"><?= time_ago($t['created_at']) ?></span>
      <div class="action-row"><a href="?page=testimonials&edit=<?= $t['id'] ?>" class="btn-icon"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a><a href="?page=testimonials&action=toggle_testi_featured&id=<?= $t['id'] ?>" class="btn-icon"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></a><a href="?page=testimonials&action=delete_testimonial&id=<?= $t['id'] ?>" class="btn-icon danger" onclick="return confirm('Delete?')"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg></a></div>
    </div>
  </div>
  <?php endforeach; if (empty($testis)): ?><p class="text-muted">No reviews yet.</p><?php endif; ?>
</div>
<div class="modal-bg" id="addTestiModal"><div class="modal"><div class="modal-head"><div class="modal-title">Add Review</div><div class="modal-close" onclick="closeModal('addTestiModal')"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div></div>
  <form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="add_testimonial">
  <div class="modal-body">
    <div class="frow"><div class="fg"><label class="fl">Name</label><input type="text" name="name" class="fi" required></div><div class="fg"><label class="fl">Instagram</label><input type="text" name="instagram" class="fi"></div></div>
    <div class="frow"><div class="fg"><label class="fl">Rating</label><select name="rating" class="fsel"><option value="5">5 Stars</option><option value="4">4 Stars</option><option value="3">3 Stars</option><option value="2">2 Stars</option><option value="1">1 Star</option></select></div><div class="fg"><label class="fl">Product</label><select name="product_id" class="fsel"><option value="">General</option><?php foreach ($prods as $p): ?><option value="<?= $p['id'] ?>"><?= h($p['name']) ?></option><?php endforeach; ?></select></div></div>
    <div class="fg"><label class="fl">Review</label><textarea name="review" class="fta" required></textarea></div>
  </div>
  <div class="modal-foot"><button type="button" class="btn btn-secondary" onclick="closeModal('addTestiModal')">Cancel</button><button type="submit" class="btn btn-primary">Save Review</button></div>
  </form>
</div></div>
<?php if ($edit_t): ?>
<div class="modal-bg open"><div class="modal"><div class="modal-head"><div class="modal-title">Edit Review</div><a href="?page=testimonials" class="modal-close"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></a></div>
  <form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="edit_testimonial"><input type="hidden" name="id" value="<?= $edit_t['id'] ?>">
  <div class="modal-body">
    <div class="frow"><div class="fg"><label class="fl">Name</label><input type="text" name="name" class="fi" value="<?= h($edit_t['name']) ?>" required></div><div class="fg"><label class="fl">Instagram</label><input type="text" name="instagram" class="fi" value="<?= h($edit_t['instagram']) ?>"></div></div>
    <div class="frow"><div class="fg"><label class="fl">Rating</label><select name="rating" class="fsel"><?php for ($i=5;$i>=1;$i--): ?><option value="<?= $i ?>" <?= $edit_t['rating']==$i?'selected':'' ?>><?= $i ?> Stars</option><?php endfor; ?></select></div><div class="fg"><label class="fl">Product</label><select name="product_id" class="fsel"><option value="">General</option><?php foreach ($prods as $p): ?><option value="<?= $p['id'] ?>" <?= $edit_t['product_id']==$p['id']?'selected':'' ?>><?= h($p['name']) ?></option><?php endforeach; ?></select></div></div>
    <div class="fg"><label class="fl">Review</label><textarea name="review" class="fta" required><?= h($edit_t['review']) ?></textarea></div>
    <div class="fcb-group"><label class="fcb"><input type="checkbox" name="is_approved" <?= $edit_t['is_approved']?'checked':'' ?>> Approved</label><label class="fcb"><input type="checkbox" name="is_featured" <?= $edit_t['is_featured']?'checked':'' ?>> Featured</label></div>
  </div>
  <div class="modal-foot"><a href="?page=testimonials" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Update Review</button></div>
  </form>
</div></div>
<?php endif; ?>

<?php
// ── HOMEPAGE BUILDER ─────────────────────────────────────────
elseif ($page === 'homepage'):
    $sections   = db()->query('SELECT * FROM homepage_sections ORDER BY sort_order')->fetchAll();
    $hero_sec   = null; foreach ($sections as $s) { if ($s['key']==='hero_banner') { $hero_sec=$s; break; } }
    $hero_cfg   = $hero_sec ? json_decode($hero_sec['config_json']??'{}',true) : [];
    $feat_prods = db()->query("SELECT id,name,status FROM products WHERE is_featured=1 ORDER BY name")->fetchAll();
?>
<div class="pg-head ani"><div class="pg-title">Homepage <em>Builder</em></div></div>
<div class="g2 ani d1">
  <div>
    <div class="card mb24"><div class="card-h"><div><div class="card-title">Section Visibility</div></div></div><div class="card-body">
      <?php foreach ($sections as $i => $sec): ?>
      <div class="section-row"><div class="section-num"><?= $i+1 ?></div><div style="flex:1"><div class="text-bold" style="font-size:13.5px"><?= h($sec['label']) ?></div><div class="text-muted"><?= h($sec['description']??'') ?></div></div><span class="badge <?= $sec['is_visible']?'badge-green':'badge-slate' ?>"><?= $sec['is_visible']?'Visible':'Hidden' ?></span><a href="?page=homepage&action=toggle_section&id=<?= $sec['id'] ?>" class="btn-icon"><svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a></div>
      <?php endforeach; ?>
    </div></div>
    <div class="card"><div class="card-h"><div class="card-title">Featured Products</div><a href="?page=products" class="card-link">Manage</a></div><div class="card-body">
      <?php foreach ($feat_prods as $fp): ?><div class="flex-row" style="padding:9px 0;border-bottom:1px solid var(--border-light)"><div class="ava" style="width:26px;height:26px;border-radius:6px;font-size:11px"><svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="var(--rose)" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg></div><div style="flex:1;font-size:13px;font-weight:700"><?= h($fp['name']) ?></div><span class="badge <?= statusBadge($fp['status']) ?>"><?= ucfirst($fp['status']) ?></span><a href="?page=products&action=toggle_featured&id=<?= $fp['id'] ?>" class="btn-icon danger"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></a></div><?php endforeach; ?>
      <?php if (empty($feat_prods)): ?><p class="text-muted text-sm">No featured products.</p><?php endif; ?>
    </div></div>
  </div>
  <div><div class="card"><div class="card-h"><div class="card-title">Hero Banner Settings</div></div><div class="card-body">
    <form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="save_hero">
      <div class="fg"><label class="fl">Headline</label><input type="text" name="headline" class="fi" value="<?= h($hero_cfg['headline']??'Gifts that hold') ?>"></div>
      <div class="fg"><label class="fl">Script Line</label><input type="text" name="script_line" class="fi" value="<?= h($hero_cfg['script_line']??'your heart') ?>"></div>
      <div class="fg"><label class="fl">CTA Button Text</label><input type="text" name="cta_text" class="fi" value="<?= h($hero_cfg['cta_text']??'Shop Our Collection') ?>"></div>
      <div class="fg"><label class="fl">CTA URL</label><input type="text" name="cta_url" class="fi" value="<?= h($hero_cfg['cta_url']??'#collection') ?>"></div>
      <button type="submit" class="btn btn-primary">Save Hero Settings</button>
    </form>
  </div></div></div>
</div>

<?php
// ── ANALYTICS ────────────────────────────────────────────────
elseif ($page === 'analytics'):
    $tv   = (int)db()->query('SELECT COALESCE(SUM(views),0) FROM products')->fetchColumn();
    $twc  = (int)db()->query('SELECT COALESCE(SUM(whatsapp_clicks),0) FROM products')->fetchColumn();
    $tinq = (int)db()->query('SELECT COUNT(*) FROM inquiries')->fetchColumn();
    $tord = (int)db()->query('SELECT COUNT(*) FROM orders')->fetchColumn();
    $trev = (float)db()->query("SELECT COALESCE(SUM(final_price),0) FROM orders WHERE status='delivered'")->fetchColumn();
    $tconf= (int)db()->query("SELECT COUNT(*) FROM inquiries WHERE status IN ('confirmed','designing','completed','delivered')")->fetchColumn();
    $conv = $tinq > 0 ? round($tconf/$tinq*100,1) : 0;
    $monthly  = db()->query("SELECT DATE_FORMAT(created_at,'%b %Y') lbl,MONTH(created_at) mn,YEAR(created_at) yr,COUNT(*) cnt FROM inquiries WHERE created_at>=NOW()-INTERVAL 8 MONTH GROUP BY yr,mn,lbl ORDER BY yr,mn")->fetchAll();
    $cat_v    = db()->query('SELECT c.name,COALESCE(SUM(p.views),0) tv FROM products p JOIN categories c ON c.id=p.category_id GROUP BY c.id,c.name ORDER BY tv DESC LIMIT 8')->fetchAll();
    $max_cv   = !empty($cat_v) ? max(array_column($cat_v,'tv')) : 1;
    $src_data = db()->query('SELECT source,COUNT(*) c FROM inquiries GROUP BY source ORDER BY c DESC')->fetchAll();
    $max_src  = !empty($src_data) ? max(array_column($src_data,'c')) : 1;
    $prod_perf= db()->query("SELECT p.name,c.name cat,p.views,p.whatsapp_clicks FROM products p JOIN categories c ON c.id=p.category_id WHERE p.status='active' ORDER BY p.views DESC LIMIT 10")->fetchAll();
    $hydrated_perf = [];
    foreach ($prod_perf as $perf) {
        $views = (int)$perf['views']; $clicks = (int)$perf['whatsapp_clicks'];
        $perf['conv'] = $views > 0 ? round(($clicks / $views) * 100, 1) : 0.0;
        $hydrated_perf[] = $perf;
    }
    $prod_perf = $hydrated_perf;

    // Analytics events data
    $ae_total    = (int)db()->query("SELECT COUNT(*) FROM analytics_events")->fetchColumn();
    $ae_today    = (int)db()->query("SELECT COUNT(*) FROM analytics_events WHERE DATE(created_at)=CURDATE()")->fetchColumn();
    $ae_week     = (int)db()->query("SELECT COUNT(*) FROM analytics_events WHERE created_at>=NOW()-INTERVAL 7 DAY")->fetchColumn();
    $ae_unique   = (int)db()->query("SELECT COUNT(DISTINCT ip_hash) FROM analytics_events WHERE created_at>=NOW()-INTERVAL 30 DAY")->fetchColumn();
    $ae_hp       = (int)db()->query("SELECT COUNT(*) FROM analytics_events WHERE event_type='homepage_visit'")->fetchColumn();
    $ae_pv       = (int)db()->query("SELECT COUNT(*) FROM analytics_events WHERE event_type='product_view'")->fetchColumn();
    $ae_wa       = (int)db()->query("SELECT COUNT(*) FROM analytics_events WHERE event_type='whatsapp_click'")->fetchColumn();
    $ae_wa_rate  = $ae_pv > 0 ? round($ae_wa/$ae_pv*100,1) : 0;
    
    // Daily visitors last 14 days
    $daily_vis = db()->query("SELECT DATE_FORMAT(created_at,'%d %b') lbl, DATE(created_at) dt, COUNT(DISTINCT ip_hash) uv, COUNT(*) pv FROM analytics_events WHERE created_at>=NOW()-INTERVAL 13 DAY GROUP BY dt,lbl ORDER BY dt")->fetchAll();
    
    // Top products by analytics events
    $top_by_events = db()->query("SELECT p.name, p.id, COUNT(*) views, SUM(CASE WHEN ae.event_type='whatsapp_click' THEN 1 ELSE 0 END) wa FROM analytics_events ae JOIN products p ON p.id=ae.product_id WHERE ae.event_type IN ('product_view','whatsapp_click') AND ae.product_id IS NOT NULL GROUP BY ae.product_id,p.name,p.id ORDER BY views DESC LIMIT 8")->fetchAll();
    
    // Device breakdown
    $device_data = db()->query("SELECT 
        SUM(CASE WHEN user_agent LIKE '%Mobile%' OR user_agent LIKE '%Android%' OR user_agent LIKE '%iPhone%' THEN 1 ELSE 0 END) mobile,
        SUM(CASE WHEN user_agent NOT LIKE '%Mobile%' AND user_agent NOT LIKE '%Android%' AND user_agent NOT LIKE '%iPhone%' AND user_agent NOT LIKE '%bot%' THEN 1 ELSE 0 END) desktop
        FROM analytics_events WHERE created_at>=NOW()-INTERVAL 30 DAY")->fetch();
    $dev_total = (($device_data['mobile']??0) + ($device_data['desktop']??0)) ?: 1;
    
    // Traffic sources
    $traffic_src = db()->query("SELECT 
        CASE 
            WHEN referrer LIKE '%instagram%' OR user_agent LIKE '%Instagram%' THEN 'Instagram'
            WHEN referrer LIKE '%facebook%' OR user_agent LIKE '%FBAN%' OR user_agent LIKE '%FB%' THEN 'Facebook'
            WHEN referrer LIKE '%google%' THEN 'Google'
            WHEN referrer='' OR referrer IS NULL THEN 'Direct'
            ELSE 'Other'
        END src,
        COUNT(*) c
        FROM analytics_events WHERE created_at>=NOW()-INTERVAL 30 DAY
        GROUP BY src ORDER BY c DESC")->fetchAll();
    $max_ts = !empty($traffic_src) ? max(array_column($traffic_src,'c')) : 1;
?>
<div class="pg-head ani"><div class="pg-title">Analytics <em>Dashboard</em></div><div class="pg-sub">Live from database — events + performance</div></div>

<div class="stats-grid ani d1">
  <div class="stat-card sc-rose"><div class="stat-ic"><svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></div><div class="stat-val"><?= number_format($ae_total) ?></div><div class="stat-lbl">Total Events</div><span class="stat-note">+<?= $ae_today ?> today</span></div>
  <div class="stat-card sc-gold"><div class="stat-ic"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><div class="stat-val"><?= number_format($ae_unique) ?></div><div class="stat-lbl">Unique Visitors (30d)</div><span class="stat-note"><?= $ae_week ?> this week</span></div>
  <div class="stat-card sc-green"><div class="stat-ic"><svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.22h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg></div><div class="stat-val"><?= $ae_wa ?></div><div class="stat-lbl">WhatsApp Clicks</div><span class="stat-note"><?= $ae_wa_rate ?>% of views</span></div>
  <div class="stat-card sc-blue"><div class="stat-ic"><svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></div><div class="stat-val"><?= $conv ?>%</div><div class="stat-lbl">Inquiry Conversion</div><span class="stat-note">₹<?= number_format($trev) ?> revenue</span></div>
</div>

<div class="g2 mb24 ani d2">
  <div class="card">
    <div class="card-h"><div><div class="card-title">Daily Visitors (14 days)</div><div class="card-sub">Unique IPs per day</div></div></div>
    <div class="card-body">
      <?php if (!empty($daily_vis)): $mx=max(array_column($daily_vis,'uv'))?:1; ?>
      <div class="chart-area">
        <?php foreach ($daily_vis as $d): $h2=round(((int)$d['uv']/$mx)*140); ?>
        <div class="chart-col">
          <div class="chart-val"><?= $d['uv'] ?></div>
          <div class="chart-bar" style="height:<?= $h2 ?>px"></div>
          <div class="chart-lbl" style="font-size:8px"><?= $d['lbl'] ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?><p class="text-muted" style="text-align:center;padding:40px 0">No visitor data yet</p><?php endif; ?>
    </div>
  </div>
  <div class="card">
    <div class="card-h"><div><div class="card-title">Event Breakdown</div><div class="card-sub">Last 30 days</div></div></div>
    <div class="card-body">
      <?php
      $events_breakdown = [
        ['Homepage Visits', $ae_hp, 'badge-blue'],
        ['Product Views', $ae_pv, 'badge-rose'],
        ['WhatsApp Clicks', $ae_wa, 'badge-green'],
      ];
      $max_eb = max([$ae_hp,$ae_pv,$ae_wa]) ?: 1;
      foreach ($events_breakdown as [$lbl,$val,$cls]):
      ?>
      <div style="margin-bottom:14px">
        <div class="flex-between" style="margin-bottom:5px">
          <span style="font-size:13px;font-weight:600"><?= $lbl ?></span>
          <span class="badge <?= $cls ?>"><?= number_format((float)$val) ?></span>
        </div>
        <div class="bar-track"><div class="bar-fill" style="width:<?= round($val/max($max_eb,1)*100) ?>%"></div></div>
      </div>
      <?php endforeach; ?>
      <div class="divider"></div>
      <div class="flex-between" style="margin-top:10px">
        <span class="text-muted text-sm">Mobile visitors (30d)</span>
        <span class="text-bold"><?= round(($device_data['mobile']??0)/$dev_total*100) ?>%</span>
      </div>
      <div class="bar-track" style="margin-top:5px"><div class="bar-fill" style="width:<?= round(($device_data['mobile']??0)/$dev_total*100) ?>%"></div></div>
    </div>
  </div>
</div>

<div class="g2 mb24 ani d3">
  <div class="card">
    <div class="card-h"><div><div class="card-title">Traffic Sources</div><div class="card-sub">Last 30 days</div></div></div>
    <div class="card-body">
      <?php foreach ($traffic_src as $ts): ?>
      <div style="margin-bottom:12px">
        <div class="flex-between" style="margin-bottom:4px">
          <span style="font-size:13px;font-weight:600"><?= h($ts['src']) ?></span>
          <span class="badge badge-rose"><?= number_format((float)$ts['c']) ?></span>
        </div>
        <div class="bar-track"><div class="bar-fill" style="width:<?= round(((int)$ts['c'])/max((int)$max_ts,1)*100) ?>%"></div></div>
      </div>
      <?php endforeach; if (empty($traffic_src)): ?><p class="text-muted text-sm">No traffic data yet.</p><?php endif; ?>
    </div>
  </div>
  <div class="card">
    <div class="card-h"><div><div class="card-title">Top Products by Events</div><div class="card-sub">From analytics_events table</div></div></div>
    <div class="card-body" style="padding-top:8px">
      <?php foreach ($top_by_events as $i => $te): ?>
      <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--border-light)">
        <div class="ava" style="width:24px;height:24px;border-radius:6px;font-size:10px"><?= $i+1 ?></div>
        <div style="flex:1;min-width:0">
          <div class="truncate text-bold" style="font-size:13px;max-width:140px"><?= h($te['name']) ?></div>
        </div>
        <div class="flex-row" style="gap:5px">
          <span class="badge badge-blue"><?= number_format((float)$te['views']) ?> views</span>
          <span class="badge badge-green"><?= number_format((float)$te['wa']) ?> WA</span>
        </div>
      </div>
      <?php endforeach; if (empty($top_by_events)): ?><p class="text-muted text-sm">No event data yet.</p><?php endif; ?>
    </div>
  </div>
</div>

<div class="g2 mb24 ani d3">
  <div class="card">
    <div class="card-h"><div class="card-title">Monthly Inquiries</div></div>
    <div class="card-body">
      <?php if (!empty($monthly)): $mx=max(array_column($monthly,'cnt'))?:1; ?>
      <div class="chart-area"><?php foreach ($monthly as $m): $h2=round(($m['cnt']/$mx)*140); ?><div class="chart-col"><div class="chart-val"><?= $m['cnt'] ?></div><div class="chart-bar" style="height:<?= $h2 ?>px"></div><div class="chart-lbl" style="font-size:8.5px"><?= $m['lbl'] ?></div></div><?php endforeach; ?></div>
      <?php else: ?><p class="text-muted" style="text-align:center;padding:40px 0">No data</p><?php endif; ?>
    </div>
  </div>
  <div class="card">
    <div class="card-h"><div class="card-title">Views by Category</div></div>
    <div class="card-body">
      <?php foreach ($cat_v as $cv): ?><div style="margin-bottom:12px"><div class="flex-between" style="margin-bottom:4px"><span style="font-size:13px;font-weight:600"><?= h($cv['name']) ?></span><span class="text-rose text-bold text-sm"><?= number_format((float)$cv['tv']) ?></span></div><div class="bar-track"><div class="bar-fill" style="width:<?= round(((int)$cv['tv'])/max((int)$max_cv,1)*100) ?>%"></div></div></div><?php endforeach; ?>
    </div>
  </div>
</div>

<div class="card ani d4">
  <div class="card-h"><div><div class="card-title">Product Performance</div></div></div>
  <div class="tbl-wrap"><table class="tbl">
    <thead><tr><th>#</th><th>Product</th><th>Category</th><th>Views</th><th>WA Clicks</th><th>Conversion</th></tr></thead>
    <tbody>
      <?php foreach ($prod_perf as $i => $p): ?>
      <tr><td><span class="text-rose text-bold"><?= $i+1 ?></span></td><td class="text-bold"><?= h($p['name']) ?></td><td class="text-muted"><?= h($p['cat']) ?></td><td><?= number_format((float)$p['views']) ?></td><td><?= number_format((float)$p['whatsapp_clicks']) ?></td><td><span class="badge <?= ($p['conv']??0)>20?'badge-green':'badge-amber' ?>"><?= $p['conv']??0 ?>%</span></td></tr>
      <?php endforeach; if (empty($prod_perf)): ?><tr><td colspan="6" class="tbl-empty">No product data yet.</td></tr><?php endif; ?>
    </tbody>
  </table></div>
</div>

<?php
// ─── ACTIVITY LOG ─────────────────────────────────────────────────────────────
elseif ($page === 'activity'):
    $logs = db()->query('SELECT l.*,a.name aname FROM admin_activity_log l LEFT JOIN admins a ON a.id=l.admin_id ORDER BY l.created_at DESC LIMIT 100')->fetchAll();
?>
<div class="pg-head ani"><div class="pg-title">Activity <em>Log</em></div><div class="pg-sub">Last 100 admin actions — audit trail</div></div>
<div class="card ani d1"><div class="tbl-wrap"><table class="tbl">
  <thead><tr><th>Admin</th><th>Action</th><th>Type</th><th>Target ID</th><th>IP</th><th>Time</th></tr></thead>
  <tbody>
    <?php foreach ($logs as $l): ?>
    <tr>
      <td class="text-bold"><?= h($l['aname']??'System') ?></td>
      <td><span class="tag mono"><?= h($l['action']) ?></span></td>
      <td class="text-muted"><?= h($l['target_type']??'—') ?></td>
      <td class="text-muted"><?= $l['target_id']??'—' ?></td>
      <td class="text-muted mono" style="font-size:11px"><?= h($l['ip_address']??'—') ?></td>
      <td class="text-muted"><?= time_ago($l['created_at']) ?></td>
    </tr>
    <?php endforeach; if (empty($logs)): ?><tr><td colspan="6" class="tbl-empty">No activity logged yet.</td></tr><?php endif; ?>
  </tbody>
</table></div></div>

<?php
// ─── ADMIN USERS ──────────────────────────────────────────────────────────────
elseif ($page === 'admins' && $admin_role === 'super_admin'):
    $admin_list = db()->query('SELECT * FROM admins ORDER BY created_at')->fetchAll();
?>
<div class="pg-head ani flex-between">
  <div><div class="pg-title">Admin <em>Users</em></div><div class="pg-sub"><?= count($admin_list) ?> accounts — super admin only</div></div>
  <button class="btn btn-primary" onclick="openModal('addAdminModal')"><svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add Admin</button>
</div>
<div class="card ani d1"><div class="tbl-wrap"><table class="tbl">
  <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Last Login</th><th>Status</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($admin_list as $a): ?>
    <tr>
      <td><div class="flex-row"><div class="ava"><?= strtoupper(substr($a['name'],0,1)) ?></div><span class="text-bold"><?= h($a['name']) ?></span><?php if ($a['id'] == (int)$_SESSION['admin_id']): ?><span class="badge badge-rose" style="margin-left:4px">You</span><?php endif; ?></div></td>
      <td class="text-muted"><?= h($a['email']) ?></td>
      <td><span class="tag"><?= str_replace('_',' ',ucfirst($a['role'])) ?></span></td>
      <td class="text-muted"><?= $a['last_login_at']?time_ago($a['last_login_at']):'Never' ?></td>
      <td><span class="badge <?= $a['is_active']?'badge-green':'badge-slate' ?>"><?= $a['is_active']?'Active':'Disabled' ?></span></td>
      <td><?php if ($a['id']!==(int)$_SESSION['admin_id']): ?><form method="POST" style="display:inline"><?= csrf_field() ?><input type="hidden" name="action" value="toggle_admin"><input type="hidden" name="id" value="<?= $a['id'] ?>"><button type="submit" class="btn btn-sm <?= $a['is_active']?'btn-danger':'btn-secondary' ?>"><?= $a['is_active']?'Disable':'Enable' ?></button></form><?php else: ?><span class="text-muted text-sm">—</span><?php endif; ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php
if ($action === 'change_password') {
    $old = $_POST['old_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    if (strlen($new) < 8) { flash_set('New password min 8 chars.','error'); redirect('?page=admins'); }
    $row = db()->prepare('SELECT password_hash FROM admins WHERE id=?');
    $row->execute([$_SESSION['admin_id']]); $admin = $row->fetch();
    if (!password_verify($old, $admin['password_hash'])) {
        flash_set('Current password incorrect.','error'); redirect('?page=admins');
    }
    db()->prepare('UPDATE admins SET password_hash=? WHERE id=?')
        ->execute([password_hash($new, PASSWORD_BCRYPT), $_SESSION['admin_id']]);
    flash_set('Password updated successfully.');
    redirect('?page=admins');
}
?>
</div></div>

<div class="modal-bg" id="addAdminModal"><div class="modal"><div class="modal-head"><div class="modal-title">Create Admin</div><div class="modal-close" onclick="closeModal('addAdminModal')"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div></div>
  <form method="POST"><?= csrf_field() ?><input type="hidden" name="action" value="add_admin">
  <div class="modal-body">
    <div class="frow"><div class="fg"><label class="fl">Full Name</label><input type="text" name="name" class="fi" placeholder="Sneha Joshi" required></div><div class="fg"><label class="fl">Email</label><input type="email" name="email" class="fi" required></div></div>
    <div class="frow"><div class="fg"><label class="fl">Password</label><input type="password" name="password" class="fi" minlength="8" required placeholder="Min 8 characters"></div><div class="fg"><label class="fl">Role</label><select name="role" class="fsel"><option value="admin">Admin</option><option value="viewer">Viewer</option><option value="super_admin">Super Admin</option></select></div></div>
  </div>
  <div class="modal-foot"><button type="button" class="btn btn-secondary" onclick="closeModal('addAdminModal')">Cancel</button><button type="submit" class="btn btn-primary">Create Admin</button></div>
  </form>
</div></div>


<?php else: ?>
<div class="pg-head ani"><div class="pg-title">Page <em>Not Found</em></div></div>
<div class="card ani d1"><div class="card-body" style="text-align:center;padding:60px"><p class="text-muted">Return to <a href="?page=dashboard" class="text-rose text-bold">Dashboard</a></p></div></div>
<?php endif; ?>

  </div></div><?php endif; // logged_in ?>

<script>
// ── MODALS ────────────────────────────────────────────────────────────────────
function openModal(id) { const m=document.getElementById(id); if(m) m.classList.add('open'); }
function closeModal(id) { const m=document.getElementById(id); if(m) m.classList.remove('open'); }
document.querySelectorAll('.modal-bg').forEach(bg => bg.addEventListener('click', e => { if(e.target===bg) bg.classList.remove('open'); }));
document.addEventListener('keydown', e => { if(e.key==='Escape') document.querySelectorAll('.modal-bg.open').forEach(m=>m.classList.remove('open')); });

// ── SIDEBAR ───────────────────────────────────────────────────────────────────
function toggleSidebar() { document.getElementById('sidebar').classList.toggle('open'); document.getElementById('sbOverlay').classList.toggle('open'); }
function closeSidebar()  { document.getElementById('sidebar')?.classList.remove('open'); document.getElementById('sbOverlay')?.classList.remove('open'); }

// ── ORDER FINAL PRICE CALC ────────────────────────────────────────────────────
function calcFinal() {
  const qty=parseFloat(document.getElementById('oqty')?.value||1);
  const price=parseFloat(document.getElementById('ouprice')?.value||0);
  const disc=parseFloat(document.getElementById('odisc')?.value||0);
  const f=document.getElementById('ofinal');
  if(f) f.value='Rs.'+Math.max(0,(price*qty)-disc).toFixed(2);
}
document.getElementById('ordProd')?.addEventListener('change', function() {
  const opt=this.options[this.selectedIndex]; const price=opt.dataset.price;
  const up=document.getElementById('ouprice');
  if(up && price) { up.value=price; calcFinal(); }
});

// ── FILE UPLOAD FEEDBACK ──────────────────────────────────────────────────────
function showFileCount(input) {
  const count=input.files.length;
  const msg = count>0 ? count+' file'+(count>1?'s':'')+' selected' : '';
  const el=input.closest('.modal-body')?.querySelector('#fileCount') || input.closest('.modal-body')?.querySelector('#mediaFileCount') || document.getElementById('fileCount');
  if(el) el.textContent=msg;
}

// ── UPLOAD ZONE DRAG & DROP ───────────────────────────────────────────────────
document.querySelectorAll('.upload-zone').forEach(zone => {
  zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('drag'));
  zone.addEventListener('drop', e => {
    e.preventDefault(); zone.classList.remove('drag');
    const inp=zone.querySelector('input[type=file]');
    if(inp && e.dataTransfer.files.length) {
      inp.files=e.dataTransfer.files;
      showFileCount(inp);
    }
  });
});

// ── COUPON UPPERCASE ──────────────────────────────────────────────────────────
document.querySelectorAll('input[name="coupon_code"]').forEach(el => el.addEventListener('input', ()=>el.value=el.value.toUpperCase()));

// ── TOPBAR SCROLL SHADOW ──────────────────────────────────────────────────────
const tb=document.querySelector('.topbar');
if(tb) window.addEventListener('scroll', ()=>{ tb.style.boxShadow=scrollY>8?'0 2px 14px rgba(184,92,110,0.1)':'var(--shadow-xs)'; });

// ── FLASH AUTO-HIDE ───────────────────────────────────────────────────────────
const fl=document.querySelector('.flash');
if(fl) { setTimeout(()=>{ fl.style.transition='opacity 0.5s'; fl.style.opacity='0'; setTimeout(()=>fl.remove(),500); }, 4500); }
</script>
</body>
</html>