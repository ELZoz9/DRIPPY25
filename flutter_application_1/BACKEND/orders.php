<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo json_encode([
        'status' => 'success',
        'message' => 'Order placed successfully'
    ]);
} else {
    echo json_encode([
        'status' => 'success',
        'orders' => [
            ['id'=>1, 'product'=>'T-Shirt', 'quantity'=>2],
            ['id'=>2, 'product'=>'Jeans', 'quantity'=>1]
        ]
    ]);
}
?>
