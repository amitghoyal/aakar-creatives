<?php
/**
 * auth-api.php
 * POST JSON body: { action, ...fields }
 * Actions: signin | signup | signout
 *
 * Uses the customers table from your Aakar schema.
 * Place in your project root.
 */

require_once 'includes/db.php';   // your existing PDO $pdo

session_start();

header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

function resp(bool $ok, string $msg, array $extra=[]): void {
    echo json_encode(array_merge(['success'=>$ok,'message'=>$msg], $extra),
        JSON_UNESCAPED_UNICODE);
    exit;
}

// Only accept POST
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    resp(false,'Method not allowed');
}

$body   = file_get_contents('php://input');
$data   = json_decode($body, true);
$action = $data['action'] ?? '';

/* ─────────────────────────────────────────────
   SIGN IN
───────────────────────────────────────────── */
if($action === 'signin'){
    $phone    = trim($data['phone'] ?? '');
    $password = $data['password'] ?? '';

    if(!$phone || !$password){
        resp(false, 'Phone and password are required.');
    }

    try {
        $stmt = $pdo->prepare(
            "SELECT id, name, phone, email, city, password_hash, is_active
               FROM customers WHERE phone = :phone LIMIT 1"
        );
        $stmt->execute([':phone' => $phone]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$customer){
            // Slight delay to slow brute-force
            usleep(250000);
            resp(false, 'No account found with this number.');
        }

        if(!(int)$customer['is_active']){
            resp(false, 'Your account has been deactivated. Please contact us.');
        }

        if(!$customer['password_hash'] || !password_verify($password, $customer['password_hash'])){
            usleep(250000);
            resp(false, 'Incorrect password. Please try again.');
        }

        // Update last login
        $pdo->prepare("UPDATE customers SET last_login_at = NOW() WHERE id = :id")
            ->execute([':id' => $customer['id']]);

        $_SESSION['customer_id'] = $customer['id'];

        $user = [
            'id'    => (int)$customer['id'],
            'name'  => $customer['name'],
            'phone' => $customer['phone'],
            'email' => $customer['email'],
            'city'  => $customer['city'],
        ];
        resp(true, 'Signed in successfully.', ['user' => $user]);

    } catch(PDOException $e){
        resp(false, 'A server error occurred. Please try again.');
    }
}

/* ─────────────────────────────────────────────
   SIGN UP
───────────────────────────────────────────── */
if($action === 'signup'){
    $name     = trim($data['name']     ?? '');
    $phone    = trim($data['phone']    ?? '');
    $email    = trim($data['email']    ?? '') ?: null;
    $city     = trim($data['city']     ?? '') ?: null;
    $password = $data['password']      ?? '';

    // Basic validation
    if(!$name || !$phone || !$password){
        resp(false, 'Name, phone, and password are required.');
    }
    if(!preg_match('/^\+91\d{10}$/', $phone)){
        resp(false, 'Enter a valid Indian mobile number.');
    }
    if(strlen($password) < 6){
        resp(false, 'Password must be at least 6 characters.');
    }
    if($email && !filter_var($email, FILTER_VALIDATE_EMAIL)){
        resp(false, 'Enter a valid email address.');
    }

    try {
        // Check duplicate phone
        $chk = $pdo->prepare("SELECT id FROM customers WHERE phone = :phone LIMIT 1");
        $chk->execute([':phone' => $phone]);
        if($chk->fetch()){
            resp(false, 'An account with this number already exists. Please sign in.');
        }

        // Check duplicate email (if provided)
        if($email){
            $chkE = $pdo->prepare("SELECT id FROM customers WHERE email = :email LIMIT 1");
            $chkE->execute([':email' => $email]);
            if($chkE->fetch()){
                resp(false, 'This email is already registered.');
            }
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost'=>12]);

        $ins = $pdo->prepare(
            "INSERT INTO customers (name, phone, email, city, password_hash, is_active, created_at, updated_at)
             VALUES (:name, :phone, :email, :city, :hash, 1, NOW(), NOW())"
        );
        $ins->execute([
            ':name'  => $name,
            ':phone' => $phone,
            ':email' => $email,
            ':city'  => $city,
            ':hash'  => $hash,
        ]);
        $newId = (int)$pdo->lastInsertId();

        $_SESSION['customer_id'] = $newId;

        $user = ['id'=>$newId,'name'=>$name,'phone'=>$phone,'email'=>$email,'city'=>$city];
        resp(true, 'Account created successfully!', ['user' => $user]);

    } catch(PDOException $e){
        resp(false, 'Could not create account. Please try again.');
    }
}

/* ─────────────────────────────────────────────
   SIGN OUT
───────────────────────────────────────────── */
if($action === 'signout'){
    session_destroy();
    resp(true, 'Signed out.');
}

resp(false, 'Unknown action.');