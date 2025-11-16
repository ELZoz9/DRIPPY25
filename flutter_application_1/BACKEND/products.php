<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/response.php';

$categoryId = $_GET['category_id'] ?? null;
$search     = $_GET['q']           ?? null;

$sql = "
    SELECT 
        p.id,
        p.name,
        p.description,
        p.base_price,
        p.brand,
        p.category_id,
        p.avg_rating,
        p.review_count,
        img.image_url
    FROM products p
    LEFT JOIN product_images img
        ON img.product_id = p.id AND img.is_main = 1
";
$params = [];
$conditions = [];

if ($categoryId !== null && $categoryId !== '') {
    $conditions[] = "p.category_id = ?";
    $params[] = $categoryId;
}

if ($search !== null && $search !== '') {
    $conditions[] = "p.name LIKE ?";
    $params[] = '%' . $search . '%';
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY p.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

json_response([
    "success"  => true,
    "products" => $products
]);
