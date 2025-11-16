<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/auth.php';

$user = getUserFromToken($pdo);

$input = json_decode(file_get_contents("php://input"), true);

$shipping_address = $input['shipping_address'] ?? null;
$shipping_city    = $input['shipping_city']    ?? null;
$shipping_postcode = $input['shipping_postcode'] ?? null;
$shipping_phone   = $input['shipping_phone']   ?? null;

if (!$shipping_address || !$shipping_city || !$shipping_phone) {
    json_response([
        "success" => false,
        "message" => "shipping_address, shipping_city, and shipping_phone are required"
    ], 400);
}

$cartStmt = $pdo->prepare("
    SELECT id 
    FROM carts 
    WHERE user_id = ?
");
$cartStmt->execute([$user['id']]);
$cart = $cartStmt->fetch(PDO::FETCH_ASSOC);

if (!$cart) {
    json_response([
        "success" => false,
        "message" => "Cart is empty"
    ], 400);
}

$cart_id = $cart['id'];

$itemStmt = $pdo->prepare("
    SELECT 
        ci.id AS cart_item_id,
        ci.quantity,
        p.id AS product_id,
        COALESCE(v.price, p.base_price) AS unit_price,
        v.id AS variant_id
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    LEFT JOIN product_variants v ON ci.variant_id = v.id
    WHERE ci.cart_id = ?
");
$itemStmt->execute([$cart_id]);
$items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($items)) {
    json_response([
        "success" => false,
        "message" => "Cart is empty"
    ], 400);
}

$total_amount = 0;

foreach ($items as $item) {
    $total_amount += $item['unit_price'] * $item['quantity'];
}

$orderStmt = $pdo->prepare("
    INSERT INTO orders 
    (user_id, total_amount, shipping_address, shipping_city, shipping_postcode, shipping_phone)
    VALUES (?, ?, ?, ?, ?, ?)
");
$orderStmt->execute([
    $user['id'],
    $total_amount,
    $shipping_address,
    $shipping_city,
    $shipping_postcode,
    $shipping_phone
]);

$order_id = $pdo->lastInsertId();

$insertItem = $pdo->prepare("
    INSERT INTO order_items
    (order_id, product_id, variant_id, unit_price, quantity)
    VALUES (?, ?, ?, ?, ?)
");

foreach ($items as $item) {
    $insertItem->execute([
        $order_id,
        $item['product_id'],
        $item['variant_id'],
        $item['unit_price'],
        $item['quantity']
    ]);
}

$clearCart = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?");
$clearCart->execute([$cart_id]);

json_response([
    "success" => true,
    "message" => "Order created successfully",
    "order_id" => $order_id,
    "total_amount" => $total_amount
]);
