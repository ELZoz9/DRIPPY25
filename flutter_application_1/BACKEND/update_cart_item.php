<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/auth.php';

$user = getUserFromToken($pdo);

$input = json_decode(file_get_contents("php://input"), true);

$cart_item_id = $input['cart_item_id'] ?? null;
$quantity     = $input['quantity']     ?? null;

if (!$cart_item_id || $quantity === null) {
    json_response([
        "success" => false,
        "message" => "cart_item_id and quantity are required"
    ], 400);
}

if ($quantity < 1) {
    json_response([
        "success" => false,
        "message" => "Quantity must be at least 1"
    ], 400);
}

$stmt = $pdo->prepare("
    UPDATE cart_items 
    SET quantity = ? 
    WHERE id = ? 
    AND cart_id = (SELECT id FROM carts WHERE user_id = ?)
");
$stmt->execute([$quantity, $cart_item_id, $user['id']]);

json_response([
    "success" => true,
    "message" => "Cart item updated successfully"
]);
