<?php
class Database
{
    private static $instance = null;
    public $db;

    /**
     * Private constructor to prevent multiple instances
     * Intiatise the database connection and set  up table
     */
    private function __construct()
    {
        $this->db = new mysqli('localhost', 'taskuser', 'password', 'task_manager');

        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }

        $this->setup();
    }

    /**
     * Function to get Instance
     * @return Database|null
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Sets up the database tables if they do not exist .
     * Create the table user and tasks
     * @return void
     */
    private function setup()
    {
        // Users table with username and user_email
        $this->db->query("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL,
                user_email VARCHAR(100) UNIQUE NOT NULL,
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
        $stmt = $this->db->prepare("INSERT IGNORE INTO users (id, username, user_email, password) VALUES (1, ?, ?, ?)");
        $username = "Admin";
        $user_email = "admin@example.com";
        $password = password_hash("password", PASSWORD_DEFAULT);
        $stmt->bind_param("sss", $username, $user_email, $password);
        $stmt->execute();
    }
}
?>