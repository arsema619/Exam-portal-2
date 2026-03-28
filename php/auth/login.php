<?php
session_start();

$users = [
    "student@test.com" => ["password" => "123456", "role" => "student"],
    "teacher@test.com" => ["password" => "123456", "role" => "teacher"],
    "admin@test.com"   => ["password" => "admin123", "role" => "admin"]
];

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $role = $_POST["role"];

    
    if (isset($users[$email])) {
        if ($users[$email]["password"] === $password && $users[$email]["role"] === $role) {
            
            
            $_SESSION["user"] = $email;
            $_SESSION["role"] = $role;

          
            if ($role == "student") {
                header("Location: student_dashboard.php");
            } elseif ($role == "teacher") {
                header("Location: teacher_dashboard.php");
            } else {
                header("Location: admin_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid password or role!";
        }
    } else {
        $error = "User not found!";
    }
}
?>
