<?php
require_once __DIR__ . '/Database.php';
class User
{
    private $db;

    /**
     * Constructor
     *
     * Initializes the database connection using the singleton Database class.
     */
    public function __construct()
    {
        $this->db = Database::getInstance()->db;
    }

    /**
     * Register a new user.
     *
     * Check if the user is already exist, hash the password
     *
     * @param string $username The username of the user
     * @param string $user_email The user email of the user tha is unique
     * @param string $password the password of the user
     * @return bool
     */
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

        // inser a new user
        $stmt = $this->db->prepare("INSERT INTO users (username, user_email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $user_email, $hashedPassword);

        return $stmt->execute();
    }

    /**
     * Log in as user
     *
     * @param string $user_email
     * @param string $password
     * @return bool
     */
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

    /**
     * Check if the user is Logged in
     * 
     * @return bool
     */
    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}