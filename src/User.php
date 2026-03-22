<?php
require_once 'Database.php';

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
        if ($stmt->num_rows > 0) return false;

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
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
        $stmt->bind_result($id, $username, $hashedPassword);

        if ($stmt->num_rows == 1) {
            $stmt->fetch();
            if (password_verify($password, $hashedPassword)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['user_email'] = $user_email;
                $_SESSION['username'] = $username;
                return true;
            }
        }
        return false;
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public function logout()
    {
        session_unset();
        session_destroy();
    }
}
?>