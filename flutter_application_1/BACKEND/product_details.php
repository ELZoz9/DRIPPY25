<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/response.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    json_response([
        "success" => false,
        "message" => "Product id is required"
    ], 400);
}

$stmt = $pdo->prepare("
    SELECT 
        p.id,
        p.name,
        p.description,
        p.base_price,
        p.brand,
        p.category_id,
        p.avg_rating,
        p.review_count,
        p.seller_id
    FROM products p
    WHERE p.id = ?
");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    json_response([
        "success" => false,
        "message" => "Product not found"
    ], 404);
}

$stmtImg = $pdo->prepare("
    SELECT 
        id,
        image_url,
        is_main
    FROM product_images
    WHERE product_id = ?
    ORDER BY is_main DESC, id ASC
");
$stmtImg->execute([$id]);
$images = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

$stmtVar = $pdo->prepare("
    SELECT 
        id,
        size,
        color,
        price,
        stock
    FROM product_variants
    WHERE product_id = ?
    ORDER BY id ASC
");
$stmtVar->execute([$id]);
$variants = $stmtVar->fetchAll(PDO::FETCH_ASSOC);

$product['images']   = $images;
$product['variants'] = $variants;

json_response([
    "success" => true,
    "product" => $product
]);

