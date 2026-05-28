<?php

class Settings {

    private $conn;

    public function __construct($conn) { $this->conn = $conn; }

    public function get() {
        $row = $this->conn->query("SELECT * FROM settings LIMIT 1")->fetch();

        if ($row) {
            $row['time_limit']              = (int)  $row['time_limit'];
            $row['max_attempts']            = (int)  $row['max_attempts'];
            $row['passing_score']           = (int)  $row['passing_score'];
            $row['randomize_questions']     = (bool) $row['randomize_questions'];
            $row['show_result_immediately'] = (bool) $row['show_result_immediately'];
        }

        return [
            'success'  => true,
            'settings' => $row ?: [
                'time_limit'              => 10,
                'max_attempts'            => 1,
                'passing_score'           => 60,
                'randomize_questions'     => false,
                'show_result_immediately' => true
            ]
        ];
    }

    public function save($data) {
        $exists = $this->conn->query("SELECT id FROM settings LIMIT 1")->fetch();

        $sql = $exists
            ? "UPDATE settings SET time_limit=:time_limit, max_attempts=:max_attempts, passing_score=:passing_score, randomize_questions=:randomize_questions, show_result_immediately=:show_result_immediately WHERE id=1"
            : "INSERT INTO settings (time_limit, max_attempts, passing_score, randomize_questions, show_result_immediately) VALUES (:time_limit, :max_attempts, :passing_score, :randomize_questions, :show_result_immediately)";

        $this->conn->prepare($sql)->execute([
            ':time_limit'              => (int)  ($data['time_limit']              ?? 10),
            ':max_attempts'            => (int)  ($data['max_attempts']            ?? 1),
            ':passing_score'           => (int)  ($data['passing_score']           ?? 60),
            ':randomize_questions'     => (int)  ($data['randomize_questions']     ?? 0),
            ':show_result_immediately' => (int)  ($data['show_result_immediately'] ?? 1),
        ]);

        return ['success' => true];
    }
}
