<?php
session_start();
require_once __DIR__ . '/../src/User.php';

$user = new User();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Server-side validation
    if (empty($username)) {
        $error = "Username is required";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        // Attempt to register
        if ($user->register($username, $password)) {
            // Auto-login after registration
            if ($user->login($username, $password)) {
                header("Location: tasks.php"); // Redirect to main page
                exit();
            } else {
                $error = "Registration succeeded but login failed!";
            }
        } else {
            $error = "Username already exists";
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

    <?php if($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password (min 6 chars)" required>
        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="index.php">Login here</a></p>
</div>
</body>
</html>