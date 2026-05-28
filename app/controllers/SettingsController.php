<?php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Middleware.php';
require_once __DIR__ . '/../models/Settings.php';

class SettingsController {

    // Any authenticated user can read settings
    public function get() {
        Middleware::requireAuth();
        return (new Settings(Database::connect()))->get();
    }

    public function save($input) {
        Middleware::requireRole(['teacher', 'admin']);

        $errors = [];
        if (isset($input['time_limit'])   && (int)$input['time_limit']   < 1)   $errors[] = 'time_limit must be at least 1';
        if (isset($input['max_attempts']) && (int)$input['max_attempts'] < 0)   $errors[] = 'max_attempts cannot be negative';
        if (isset($input['passing_score'])) {
            $s = (int)$input['passing_score'];
            if ($s < 0 || $s > 100) $errors[] = 'passing_score must be 0–100';
        }

        if (!empty($errors))
            return ['success' => false, 'message' => implode(', ', $errors)];

        return (new Settings(Database::connect()))->save($input);
    }
}
