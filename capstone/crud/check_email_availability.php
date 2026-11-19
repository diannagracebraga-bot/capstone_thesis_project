<?php
require_once __DIR__ . '/../database/database_connection.php';
require_once __DIR__ . '/../database/email_availability.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['checked' => false, 'available' => false]);
    exit;
}

$email = strtolower(trim($_POST['email'] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['checked' => false, 'available' => false]);
    exit;
}

$alreadyUsed = emailIsAlreadyUsed($conn, $email);
if ($alreadyUsed === null) {
    http_response_code(503);
    echo json_encode(['checked' => false, 'available' => false]);
    exit;
}

echo json_encode(['checked' => true, 'available' => !$alreadyUsed]);
