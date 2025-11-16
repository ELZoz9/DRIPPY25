<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

function json_response($data, int $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data);
    exit;
}
