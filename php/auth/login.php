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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Exam Portal</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<button class="back-btn" onclick="window.location.href='index.html'">← Back to Home</button>

<div class="container">
  <h2>Login</h2>


  <form method="POST" action="">
    
    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <select name="role">
      <option value="student">Student</option>
      <option value="teacher">Teacher</option>
      <option value="admin">Admin</option>
    </select>

    <button type="submit">Login</button>
  </form>

  <p class="toggle">
    Don't have an account?
    <a href="signup.php">Sign Up</a>
  </p>

 
  <p class="error">
    <?php echo $error; ?>
  </p>

</div>

</body>
</html>
