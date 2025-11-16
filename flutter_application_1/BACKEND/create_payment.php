<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/auth.php';

$user = getUserFromToken($pdo);

$input = json_decode(file_get_contents("php://input"), true);

$order_id        = $input['order_id']        ?? null;
$amount          = $input['amount']          ?? null;
$payment_method  = $input['payment_method']  ?? 'card';
$status          = $input['status']          ?? 'success';
$transaction_ref = $input['transaction_ref'] ?? null;

if (!$order_id) {
    json_response([
        "success" => false,
        "message" => "order_id is required"
    ], 400);
}

$orderStmt = $pdo->prepare("
    SELECT id, total_amount 
    FROM orders 
    WHERE id = ? AND user_id = ?
");
$orderStmt->execute([$order_id, $user['id']]);
$order = $orderStmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    json_response([
        "success" => false,
        "message" => "Order not found"
    ], 404);
}

if ($amount === null) {
    $amount = (float)$order['total_amount'];
}

$stmt = $pdo->prepare("
    INSERT INTO payments (order_id, user_id, amount, payment_method, status, transaction_ref)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $order_id,
    $user['id'],
    $amount,
    $payment_method,
    $status,
    $transaction_ref
]);

$payment_id = $pdo->lastInsertId();

json_response([
    "success"     => true,
    "message"     => "Payment created successfully",
    "payment_id"  => $payment_id,
    "order_id"    => $order_id,
    "amount"      => $amount,
    "status"      => $status,
    "method"      => $payment_method
]);
