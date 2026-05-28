<?php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Middleware.php';
require_once __DIR__ . '/../models/Result.php';
require_once __DIR__ . '/../models/Settings.php';

class ResultController {

    // Students only — injects student_id from session
    public function save($input) {
        $user            = Middleware::requireRole('student');
        $input['student_id'] = $user['id'];

        $conn        = Database::connect();
        $settings    = (new Settings($conn))->get();
        $maxAttempts = (int) ($settings['settings']['max_attempts'] ?? 0);

        return (new Result($conn))->save($input, $maxAttempts);
    }

    // Students only — lets the frontend check before even starting the exam
    public function checkAttempts($input) {
        $user = Middleware::requireRole('student');

        if (empty($input['quiz_id']))
            return ['success' => false, 'message' => 'quiz_id is required'];

        $conn        = Database::connect();
        $settings    = (new Settings($conn))->get();
        $maxAttempts = (int) ($settings['settings']['max_attempts'] ?? 0);

        return (new Result($conn))->getAttemptStatus($user['id'], (int) $input['quiz_id'], $maxAttempts);
    }

    // Teachers see only their quizzes' results; admins see all
    public function getAll() {
        $user      = Middleware::requireRole(['teacher', 'admin']);
        $teacherId = $user['role'] === 'teacher' ? $user['id'] : null;
        return (new Result(Database::connect()))->getAll($teacherId);
    }
}
