<?php

require_once __DIR__ . '/db.php';

function generateToken() {
    return bin2hex(random_bytes(32));
}

function getUserFromToken(PDO $pdo) {
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Missing Authorization header"
        ]);
        exit;
    }

    $auth = trim($headers['Authorization']);
    if (stripos($auth, 'Bearer ') === 0) {
        $token = substr($auth, 7);
    } else {
        $token = $auth;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE api_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Invalid or expired token"
        ]);
        exit;
    }

    return $user;
}
