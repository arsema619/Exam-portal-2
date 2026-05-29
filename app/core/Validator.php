<?php

class Validator {
    public static function requireFields(array $data, array $fields) {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null)
                return ['success' => false, 'message' => "Missing required field: $field"];
        }
        return null;
    }

    public static function isEmail(string $value): bool {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function sanitize(string $value): string {
        return strip_tags(trim($value));
    }
}
