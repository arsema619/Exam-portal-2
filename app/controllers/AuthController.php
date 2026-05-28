<?php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {

    public function login($input) {

        $email    = strtolower(trim($input['email']    ?? ''));
        $password = $input['password'] ?? '';
        $role     = trim($input['role'] ?? '');

        if (!$email || !$password || !$role)
            return ['success' => false, 'message' => 'Email, password and role are required'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            return ['success' => false, 'message' => 'Invalid email address'];

        $user = (new User(Database::connect()))->findByEmailAndRole($email, $role);

        if (!$user || !password_verify($password, $user['password']))
            return ['success' => false, 'message' => 'Invalid credentials'];

        // Block unapproved teachers and students
        if (in_array($user['role'], ['teacher', 'student'], true) && !$user['approved'])
            return ['success' => false, 'message' => 'Your account is pending approval by an administrator'];

        if (session_status() === PHP_SESSION_NONE) session_start();

        $_SESSION['user_id']  = $user['id'];
        $_SESSION['role']     = $user['role'];
        $_SESSION['approved'] = (bool) $user['approved'];

        unset($user['password']);
        return ['success' => true, 'user' => $user];
    }

    public function register($input) {

        $result = (new User(Database::connect()))->create($input);

        // Auto-login only for admin (auto-approved); teachers/students wait for approval
        if ($result['success'] && !empty($result['user']['approved'])) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['user_id']  = $result['user']['id'];
            $_SESSION['role']     = $result['user']['role'];
            $_SESSION['approved'] = true;
        }

        return $result;
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        return ['success' => true];
    }
}
