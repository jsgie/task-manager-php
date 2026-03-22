<?php
class Database
{
    private static $instance = null;
    public $db;

    private function __construct()
    {
        $this->db = new mysqli('localhost', 'taskuser', 'password', 'task_manager');

        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }

        $this->setup();
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    private function setup()
    {
        // Users table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL
            )
        ");

        // Tasks table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS tasks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                due_date DATE NOT NULL,
                status ENUM('active','completed') NOT NULL,
                user_id INT NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");

        // Seed admin if not exists
        $stmt = $this->db->prepare("INSERT IGNORE INTO users (id, username, password) VALUES (1, ?, ?)");
        $username = "admin";
        $password = password_hash("password", PASSWORD_DEFAULT);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
    }
}