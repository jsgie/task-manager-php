<?php
require_once __DIR__ . '/Database.php';

class Task
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
     * Create a new task
     *
     * @param string $name The name/title of the task
     * @param string $due_date The date in  'YYYY-MM-DD' format
     * @param string $status Task status ('active' or 'completed')
     * @param int $user_id ID of the user who owns the task
     * @return bool
     */
    public function create($name, $due_date, $status, $user_id)
    {
        $stmt = $this->db->prepare("INSERT INTO tasks (name, due_date, status, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $due_date, $status, $user_id);
        return $stmt->execute();
    }

    /**
     * Update an existing task
     *
     * @param int $id Task ID
     * @param string $name title of the task
     * @param int $user_id
     * @param string $due_date the date in in 'YYYY-MM-DD' format
     * @param string $status Task status ('active' or 'completed')
     * @return bool t
     */
    public function updateTask($id, $name, $due_date, $status, $user_id)
    {
        $stmt = $this->db->prepare("UPDATE tasks SET name=?, due_date=?, status=? WHERE id=? AND user_id=?");
        $stmt->bind_param("sssii", $name, $due_date, $status, $id, $user_id);
        return $stmt->execute();
    }
    /**
     * Delete a task
     * @param  int $task_id id of the task
     * @return bool
     */
    public function delete($task_id)
    {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id=?");
        $stmt->bind_param("i", $task_id);
        return $stmt->execute();
    }

    /**
     * Get tasks for a user
     * @param int $user_id id of the user
     * @param String $status
     * @return array
     */
    public function getTasks($user_id, $status = null)
    {
        if ($status) {
            $stmt = $this->db->prepare("SELECT * FROM tasks WHERE user_id=? AND status=?");
            $stmt->bind_param("is", $user_id, $status);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM tasks WHERE user_id=?");
            $stmt->bind_param("i", $user_id);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}