<?php

class Quiz {

    private $conn;

    public function __construct($conn) { $this->conn = $conn; }

    public function create($data) {
        if (empty($data['title']))
            return ['success' => false, 'message' => 'Quiz title is required'];

        if (empty($data['questions']) || !is_array($data['questions']))
            return ['success' => false, 'message' => 'At least one question is required'];

        $this->conn->beginTransaction();

        $this->conn->prepare(
            "INSERT INTO quizzes (title, description, time_limit, teacher_id)
             VALUES (:title, :description, :time_limit, :teacher_id)"
        )->execute([
            ':title'       => strip_tags(trim($data['title'])),
            ':description' => strip_tags(trim($data['description'] ?? '')),
            ':time_limit'  => (int) ($data['timeLimit'] ?? 10),
            ':teacher_id'  => $data['teacher_id'] ?? null,
        ]);

        $quizId = $this->conn->lastInsertId();
        $qStmt  = $this->conn->prepare(
            "INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer)
             VALUES (:quiz_id, :question, :a, :b, :c, :d, :answer)"
        );

        foreach ($data['questions'] as $i => $q) {
            if (empty($q['text'])) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => "Question " . ($i + 1) . " is missing text"];
            }

            $qStmt->execute([
                ':quiz_id'  => $quizId,
                ':question' => strip_tags(trim($q['text'])),
                ':a'        => $q['options'][0] ?? '',
                ':b'        => $q['options'][1] ?? '',
                ':c'        => $q['options'][2] ?? '',
                ':d'        => $q['options'][3] ?? '',
                ':answer'   => $q['options'][$q['answer']] ?? $q['answer'],
            ]);
        }

        $this->conn->commit();
        return ['success' => true, 'quiz_id' => (int) $quizId];
    }

    // $teacherId = null returns all quizzes (students/admin); set for teacher-scoped view
    public function getAll($teacherId = null, $randomize = false) {
        if ($teacherId !== null) {
            $stmt = $this->conn->prepare("SELECT * FROM quizzes WHERE teacher_id=:tid ORDER BY id ASC");
            $stmt->execute([':tid' => $teacherId]);
        } else {
            $stmt = $this->conn->query("SELECT * FROM quizzes ORDER BY id ASC");
        }

        $quizzes = $stmt->fetchAll();
        $qStmt   = $this->conn->prepare("SELECT * FROM questions WHERE quiz_id=:id");

        foreach ($quizzes as &$quiz) {
            $qStmt->execute([':id' => $quiz['id']]);
            $questions = $qStmt->fetchAll();

            foreach ($questions as &$q) {
                $q['text']    = $q['question'];
                $q['options'] = [$q['option_a'], $q['option_b'], $q['option_c'], $q['option_d']];
                $q['answer']  = $q['correct_answer'];
                unset($q['question'], $q['option_a'], $q['option_b'], $q['option_c'], $q['option_d'], $q['correct_answer']);
            }
            unset($q);

            if ($randomize) shuffle($questions);
            $quiz['questions'] = $questions;
        }
        unset($quiz);

        return ['success' => true, 'quizzes' => $quizzes];
    }
}
