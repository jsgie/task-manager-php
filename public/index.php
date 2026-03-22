<?php
session_start();
require_once __DIR__ . '/../src/User.php';

$user = new User();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_email = trim($_POST['user_email']);
    $password = trim($_POST['password']);

    if ($user->login($user_email, $password)) {
        header("Location: tasks.php");
        exit();
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Task Manager</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
<div class="auth-box">
    <h1>Login</h1>
    <?php if($error) echo "<div class='error'>{$error}</div>"; ?>
    <form method="POST">
        <input type="email" name="user_email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <a href="register.php">Register</a>
</div>
</body>
</html>