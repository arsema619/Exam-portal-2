<?php
session_start();
require '../config/db.php'; // include the MySQLi connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Basic validation
    if (!$name || !$email || !$password) {
        echo json_encode(['error' => 'Please fill all fields']);
        exit;
    }

    if (strlen($password) < 6) {
        echo json_encode(['error' => 'Password must be at least 6 characters']);
        exit;
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode(['error' => 'Email already registered']);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, approved) VALUES (?, ?, ?, ?, ?)");
    $approved = ($role === 'student') ? 0 : 1; // students need approval
    $stmt->bind_param("ssssi", $name, $email, $hashedPassword, $role, $approved);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Account created']);
    } else {
        echo json_encode(['error' => 'Signup failed']);
    }
}