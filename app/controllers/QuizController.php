<?php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Middleware.php';
require_once __DIR__ . '/../models/Quiz.php';
require_once __DIR__ . '/../models/Settings.php';

class QuizController {

    // Teachers only — injects teacher_id from session
    public function create($input) {
        $user            = Middleware::requireRole('teacher');
        $input['teacher_id'] = $user['id'];
        return (new Quiz(Database::connect()))->create($input);
    }

    // Supports single quiz or { quizzes: [...] } batch
    public function upload($input) {
        $user    = Middleware::requireRole('teacher');
        $conn    = Database::connect();
        $model   = new Quiz($conn);
        $quizzes = $input['quizzes'] ?? [$input];

        if (empty($quizzes))
            return ['success' => false, 'message' => 'No quiz data provided'];

        $created = [];
        $errors  = [];

        foreach ($quizzes as $i => $quizData) {
            $quizData['teacher_id'] = $user['id'];
            $result = $model->create($quizData);
            $result['success'] ? $created[] = $result['quiz_id'] : $errors[] = "Quiz " . ($i + 1) . ": " . $result['message'];
        }

        if (!empty($errors) && empty($created))
            return ['success' => false, 'message' => implode('; ', $errors)];

        return ['success' => true, 'created_ids' => $created, 'errors' => $errors];
    }

    // Teachers see own quizzes; students/admins see all. Respects randomize setting.
    public function getAll() {
        $user      = Middleware::requireAuth();
        $conn      = Database::connect();
        $settings  = (new Settings($conn))->get();
        $randomize = !empty($settings['settings']['randomize_questions']);
        $teacherId = $user['role'] === 'teacher' ? $user['id'] : null;
        return (new Quiz($conn))->getAll($teacherId, $randomize);
    }
}
