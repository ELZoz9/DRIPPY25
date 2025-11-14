<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_GET['action'] ?? '';

    if ($action == 'login') {
        echo json_encode([
            'status' => 'success',
            'token' => 'fake-jwt-token'
        ]);
    } elseif ($action == 'register') {
        echo json_encode([
            'status' => 'success',
            'message' => 'User registered successfully'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
}
?>
