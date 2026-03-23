<?php

header("Content-Type: application/json");

require_once "../config/db.php";

if (!isset($_POST['id']) || !isset($_POST['action'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing parameters"
    ]);
    exit;
}

$id = $_POST['id'];
$action = $_POST['action'];

if ($action == "approve") {
    $approved = 1;
} elseif ($action == "block") {
    $approved = 0;
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid action"
    ]);
    exit;
}

$sql = "UPDATE users SET approved = $approved WHERE id = $id";

$result = mysqli_query($conn, $sql);

if ($result) {
    echo json_encode([
        "status" => "success",
        "message" => "Student status updated"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => mysqli_error($conn)
    ]);
}

?>