<?php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Middleware.php';
require_once __DIR__ . '/../models/User.php';

class AdminController {

    public function getUsers() {
        Middleware::requireRole('admin');
        return (new User(Database::connect()))->getAll();
    }

    public function deleteUser($input) {
        Middleware::requireRole('admin');

        if (empty($input['id']))
            return ['success' => false, 'message' => 'User ID is required'];

        return (new User(Database::connect()))->delete($input['id']);
    }

    public function approveTeacher($input) {
        Middleware::requireRole('admin');

        if (!isset($input['id'], $input['approved']))
            return ['success' => false, 'message' => 'Teacher ID and approved status are required'];

        return (new User(Database::connect()))->approveTeacher($input['id'], $input['approved']);
    }
}
