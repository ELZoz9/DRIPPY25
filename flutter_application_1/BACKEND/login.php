<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/auth.php';

$input = json_decode(file_get_contents("php://input"), true);

$email    = $input['email']    ?? null;
$password = $input['password'] ?? null;

if (!$email || !$password) {
    json_response([
        "success" => false,
        "message" => "email and password are required"
    ], 400);
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    json_response([
        "success" => false,
        "message" => "Invalid email or password"
    ], 401);
}

if (!password_verify($password, $user['password_hash'])) {
    json_response([
        "success" => false,
        "message" => "Invalid email or password"
    ], 401);
}

$token = generateToken();

$update = $pdo->prepare("UPDATE users SET api_token = ? WHERE id = ?");
$update->execute([$token, $user['id']]);

json_response([
    "success" => true,
    "token"   => $token,
    "user"    => [
        "id"     => $user['id'],
        "name"   => $user['name'],
        "email"  => $user['email'],
        "role"   => $user['role']   ?? null,
        "status" => $user['status'] ?? null
    ]
]);
