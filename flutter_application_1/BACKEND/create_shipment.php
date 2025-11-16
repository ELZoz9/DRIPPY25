<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/auth.php';

$user = getUserFromToken($pdo);

$input = json_decode(file_get_contents("php://input"), true);

$order_id       = $input['order_id']       ?? null;
$carrier        = $input['carrier']        ?? null;
$tracking_number = $input['tracking_number'] ?? null;
$status         = $input['status']         ?? "pending";

if (!$order_id || !$carrier) {
    json_response([
        "success" => false,
        "message" => "order_id and carrier are required"
    ], 400);
}

$stmt = $pdo->prepare("
    INSERT INTO shipments 
    (order_id, carrier, tracking_number, status, shipped_at) 
    VALUES (?, ?, ?, ?, NOW())
");

$stmt->execute([
    $order_id,
    $carrier,
    $tracking_number,
    $status
]);

json_response([
    "success" => true,
    "message" => "Shipment created successfully"
]);
