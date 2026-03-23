<?php

header("Content-Type: application/json");

require_once "../config/db.php";

$sql = "SELECT id, name, email, approved 
        FROM users 
        WHERE role='student'";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        "status" => "error",
        "message" => mysqli_error($conn)
    ]);
    exit;
}

$students = [];

while ($row = mysqli_fetch_assoc($result)) {
    $students[] = $row;
}

echo json_encode([
    "status" => "success",
    "students" => $students
]);

?>