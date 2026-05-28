<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];

$quizId = (int) ($input['quiz_id'] ?? 0);
$studentId = (int) ($input['student_id'] ?? 0);
$studentName = trim($input['student_name'] ?? '');
$quizTitle = trim($input['quiz_title'] ?? '');
$score = (int) ($input['score'] ?? 0);

if ($quizId <= 0 || $studentName === '' || $quizTitle === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Result data is incomplete']);
    exit;
}

$stmt = $conn->prepare(
    'INSERT INTO results (quiz_id, student_id, student_name, quiz_title, score)
     VALUES (:quiz_id, :student_id, :student_name, :quiz_title, :score)'
);
$stmt->execute([
    ':quiz_id' => $quizId,
    ':student_id' => $studentId ?: null,
    ':student_name' => $studentName,
    ':quiz_title' => $quizTitle,
    ':score' => $score,
]);

echo json_encode([
    'success' => true,
    'message' => 'Result saved successfully',
]);
