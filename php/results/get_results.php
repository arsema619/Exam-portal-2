<?php
include "../config/db.php";

$sql = "SELECT * FROM results ORDER BY date DESC";
$result = mysqli_query($conn, $sql);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
?>