<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';

$stmt = $conn->query('SELECT id, quiz_id, student_id, student_name, quiz_title, score, submitted_at FROM results ORDER BY submitted_at DESC, id DESC');
$results = $stmt->fetchAll();

echo json_encode([
    'success' => true,
    'results' => $results,
]);
