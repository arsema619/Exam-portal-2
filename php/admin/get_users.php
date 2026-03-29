<?php

require_once __DIR__ . '/../config/db.php';

header("Content-Type: application/json");

// Only allow GET request
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method"
    ]);
    exit;
}

$sql = "SELECT id, name, email, role, approved, created_at FROM users";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => "Database query failed"
    ]);
    exit;
}

$users = [];

while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

echo json_encode([
    "success" => true,
    "data" => $users
]);

?>