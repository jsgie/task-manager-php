<?php
session_start();
require_once __DIR__ . '/../src/User.php';

$user = new User();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $user_email = trim($_POST['user_email']);
    $password = trim($_POST['password']);

    if (empty($username)) {
        $error = "Username is required";
    } elseif (empty($user_email)) {
        $error = "Email is required";
    } elseif (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        if ($user->register($username, $user_email, $password)) {
            if ($user->login($user_email, $password)) {
                header("Location: tasks.php");
                exit();
            } else {
                $error = "Registration succeeded but login failed!";
            }
        } else {
            $error = "Email already exists";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Task Manager</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
<div class="auth-box">
    <h1>Register</h1>
    <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="user_email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password (min 6 chars)" required>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="index.php">Login here</a></p>
</div>
</body>
</html>