<?php
require_once __DIR__ . '/Database.php';
class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->db;
    }

    public function register($username, $user_email, $password)
    {
        // Check if email already exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE user_email = ? LIMIT 1");
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            return false; // Email already exists
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Default role can be omitted if not needed
        $stmt = $this->db->prepare("INSERT INTO users (username, user_email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $user_email, $hashedPassword);

        return $stmt->execute();
    }

    public function login($user_email, $password)
    {
        $stmt = $this->db->prepare("SELECT id, username, password FROM users WHERE user_email = ? LIMIT 1");
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) return false;

        $stmt->bind_result($id, $username, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['user_email'] = $user_email;
            return true;
        }

        return false;
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}