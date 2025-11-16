<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/auth.php';

$user = getUserFromToken($pdo);

$cartStmt = $pdo->prepare("SELECT id FROM carts WHERE user_id = ?");
$cartStmt->execute([$user['id']]);
$cart = $cartStmt->fetch(PDO::FETCH_ASSOC);

if (!$cart) {
    json_response([
        "success"      => true,
        "items"        => [],
        "total_items"  => 0,
        "total_price"  => 0
    ]);
}

$sql = "
    SELECT 
        ci.id AS cart_item_id,
        ci.quantity,
        p.id  AS product_id,
        p.name,
        p.base_price,
        COALESCE(v.price, p.base_price) AS unit_price,
        v.id   AS variant_id,
        v.size,
        v.color,
        img.image_url
    FROM cart_items ci
    JOIN carts c
        ON ci.cart_id = c.id
    JOIN products p
        ON ci.product_id = p.id
    LEFT JOIN product_variants v
        ON ci.variant_id = v.id
    LEFT JOIN product_images img
        ON img.product_id = p.id AND img.is_main = 1
    WHERE c.user_id = ?
    ORDER BY ci.id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user['id']]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalItems = 0;
$totalPrice = 0;

foreach ($items as $item) {
    $qty   = (int)$item['quantity'];
    $price = (float)$item['unit_price'];
    $totalItems += $qty;
    $totalPrice += $qty * $price;
}

json_response([
    "success"      => true,
    "items"        => $items,
    "total_items"  => $totalItems,
    "total_price"  => $totalPrice
]);
