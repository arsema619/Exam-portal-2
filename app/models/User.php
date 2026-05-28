<?php

class User {

    private $conn;

    public function __construct($conn) { $this->conn = $conn; }

    // --- Admin ---

    public function getAll() {
        $stmt = $this->conn->query("SELECT id, name, email, role, approved FROM users ORDER BY id DESC");
        return ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id=:id AND role <> 'admin'");
        return ['success' => (bool) $stmt->execute([':id' => $id])];
    }

    public function approveTeacher($id, $approved) {
        $stmt = $this->conn->prepare("UPDATE users SET approved=:approved WHERE id=:id AND role='teacher'");
        return ['success' => (bool) $stmt->execute([':approved' => (int)$approved, ':id' => $id])];
    }

    // --- Teacher ---

    // Returns students who submitted results for this teacher's quizzes; null = all students
    public function getStudents($teacherId = null) {
        if ($teacherId) {
            $stmt = $this->conn->prepare(
                "SELECT DISTINCT u.id, u.name, u.email, u.approved FROM users u
                 INNER JOIN results r ON r.student_id = u.id
                 INNER JOIN quizzes q ON q.id = r.quiz_id AND q.teacher_id = :tid
                 WHERE u.role = 'student' ORDER BY u.id DESC"
            );
            $stmt->execute([':tid' => $teacherId]);
        } else {
            $stmt = $this->conn->query("SELECT id, name, email, approved FROM users WHERE role='student' ORDER BY id DESC");
        }
        return $stmt->fetchAll();
    }

    public function approveStudent($id, $approved) {
        $stmt = $this->conn->prepare("UPDATE users SET approved=:approved WHERE id=:id AND role='student'");
        return ['success' => (bool) $stmt->execute([':approved' => (int)$approved, ':id' => $id])];
    }

    // --- Auth ---

    public function findByEmailAndRole($email, $role) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email=:email AND role=:role LIMIT 1");
        $stmt->execute([':email' => $email, ':role' => $role]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT id, name, email, role, approved FROM users WHERE id=:id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create($data) {
        foreach (['name', 'email', 'password', 'role'] as $field) {
            if (empty($data[$field]))
                return ['success' => false, 'message' => "Missing required field: $field"];
        }

        if (!in_array($data['role'], ['student', 'teacher', 'admin'], true))
            return ['success' => false, 'message' => 'Invalid role'];

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
            return ['success' => false, 'message' => 'Invalid email address'];

        if (strlen($data['password']) < 6)
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];

        $check = $this->conn->prepare("SELECT id FROM users WHERE email=:email");
        $check->execute([':email' => $data['email']]);
        if ($check->fetch())
            return ['success' => false, 'message' => 'Email already exists'];

        $approved = $data['role'] === 'admin' ? 1 : 0;
        $name     = strip_tags(trim($data['name']));
        $email    = strtolower(trim($data['email']));

        $this->conn->prepare(
            "INSERT INTO users (name, email, password, role, approved) VALUES (:name, :email, :password, :role, :approved)"
        )->execute([
            ':name'     => $name,
            ':email'    => $email,
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':role'     => $data['role'],
            ':approved' => $approved,
        ]);

        return [
            'success' => true,
            'user'    => ['id' => (int)$this->conn->lastInsertId(), 'name' => $name, 'email' => $email, 'role' => $data['role'], 'approved' => $approved]
        ];
    }
}
