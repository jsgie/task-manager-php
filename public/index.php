<?php
require_once __DIR__ . '/../src/User.php';
$user = new User();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($user->login($username, $password)) {
        header("Location: tasks.php"); // Redirect to tasks page
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Task Manager</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
<div class="auth-box">
    <h1>Login</h1>
    <?php if($error) echo "<div class='error'>{$error}</div>"; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <a href="register.php">Register</a>
</div>
</body>
</html>