<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/response.php';

$input = json_decode(file_get_contents("php://input"), true);

$name     = $input['name']     ?? null;
$email    = $input['email']    ?? null;
$password = $input['password'] ?? null;

$gender   = $input['gender']    ?? null;
$height   = $input['height_cm'] ?? null;
$weight   = $input['weight_kg'] ?? null;

if (!$name || !$email || !$password) {
    json_response([
        "success" => false,
        "message" => "name, email and password are required"
    ], 400);
}

$password_hash = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password_hash, gender, height_cm, weight_kg)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $name,
        $email,
        $password_hash,
        $gender,
        $height,
        $weight
    ]);

    json_response([
        "success" => true,
        "message" => "User registered successfully"
    ], 201);

} catch (PDOException $e) {
    if ($e->getCode() === "23000") {
        json_response([
            "success" => false,
            "message" => "Email already exists"
        ], 409);
    }

    json_response([
        "success" => false,
        "message" => "Server error"
    ], 500);
}
