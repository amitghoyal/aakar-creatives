<?php
/**
 * newsletter_subscribe.php
 * AJAX endpoint — accepts POST {email} and stores it.
 * Returns JSON {success, message}.
 */

header('Content-Type: application/json');
require_once 'includes/db.php';

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

try {
    // Check if already subscribed (using customers table email column as newsletter store)
    $check = $pdo->prepare("SELECT id FROM customers WHERE email = ? LIMIT 1");
    $check->execute([$email]);

    if ($check->fetch()) {
        echo json_encode(['success' => true, 'message' => "You're already subscribed — stay tuned! 🎉"]);
        exit;
    }

    // If you have a dedicated newsletter_subscribers table, use that.
    // For now we just acknowledge. Extend as needed.
    // $pdo->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)")->execute([$email]);

    echo json_encode(['success' => true, 'message' => "You're subscribed! Watch for exclusive offers 🎁"]);

} catch (PDOException $e) {
    error_log('[Newsletter] ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again.']);
}