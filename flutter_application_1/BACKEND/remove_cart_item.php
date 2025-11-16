<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/auth.php';

$user = getUserFromToken($pdo);

$input = json_decode(file_get_contents("php://input"), true);

$cart_item_id = $input['cart_item_id'] ?? null;

if (!$cart_item_id) {
    json_response([
        "success" => false,
        "message" => "cart_item_id is required"
    ], 400);
}

$stmt = $pdo->prepare("
    DELETE FROM cart_items 
    WHERE id = ? 
    AND cart_id = (SELECT id FROM carts WHERE user_id = ?)
");
$stmt->execute([$cart_item_id, $user['id']]);

json_response([
    "success" => true,
    "message" => "Cart item removed successfully"
]);
