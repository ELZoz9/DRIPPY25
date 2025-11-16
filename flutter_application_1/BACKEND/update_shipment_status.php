<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/auth.php';

$user = getUserFromToken($pdo);

$input = json_decode(file_get_contents("php://input"), true);

$shipment_id = $input['shipment_id'] ?? null;
$status      = $input['status']      ?? null;

if (!$shipment_id || !$status) {
    json_response([
        "success" => false,
        "message" => "shipment_id and status are required"
    ], 400);
}

if (!in_array($status, ['pending', 'shipped', 'in_transit', 'delivered', 'returned'])) {
    json_response([
        "success" => false,
        "message" => "Invalid status"
    ], 400);
}

$sql = "UPDATE shipments SET status = ?";

$params = [$status];

if ($status === 'shipped') {
    $sql .= ", shipped_at = NOW()";
} elseif ($status === 'delivered') {
    $sql .= ", delivered_at = NOW()";
}

$sql .= " WHERE id = ?";

$params[] = $shipment_id;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

json_response([
    "success" => true,
    "message" => "Shipment status updated successfully"
]);
