<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/auth.php';

$user = getUserFromToken($pdo);

$input = json_decode(file_get_contents("php://input"), true);

$product_id = $input['product_id'] ?? null;
$variant_id = $input['variant_id'] ?? null;
$quantity   = $input['quantity']   ?? 1;

if (!$product_id || !$variant_id) {
    json_response([
        "success" => false,
        "message" => "product_id and variant_id are required"
    ], 400);
}

$cartStmt = $pdo->prepare("SELECT id FROM carts WHERE user_id = ?");
$cartStmt->execute([$user['id']]);
$cart = $cartStmt->fetch(PDO::FETCH_ASSOC);

if (!$cart) {
    $createCart = $pdo->prepare("INSERT INTO carts (user_id) VALUES (?)");
    $createCart->execute([$user['id']]);
    $cart_id = $pdo->lastInsertId();
} else {
    $cart_id = $cart['id'];
}

$itemStmt = $pdo->prepare("
    SELECT id, quantity 
    FROM cart_items 
    WHERE cart_id = ? AND product_id = ? AND variant_id = ?
");
$itemStmt->execute([$cart_id, $product_id, $variant_id]);
$item = $itemStmt->fetch(PDO::FETCH_ASSOC);

if ($item) {
    $newQty = $item['quantity'] + $quantity;
    $update = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
    $update->execute([$newQty, $item['id']]);
} else {
    $insert = $pdo->prepare("
        INSERT INTO cart_items (cart_id, product_id, variant_id, quantity)
        VALUES (?, ?, ?, ?)
    ");
    $insert->execute([$cart_id, $product_id, $variant_id, $quantity]);
}

json_response([
    "success" => true,
    "message" => "Item added to cart successfully"
]);
