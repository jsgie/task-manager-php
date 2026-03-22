<?php

require_once __DIR__ . '/Database.php';

class Task
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->db;
    }

    public function create($name, $due_date, $status, $user_id)
    {
        $stmt = $this->db->prepare("INSERT INTO tasks (name, due_date, status, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $due_date, $status, $user_id);
        return $stmt->execute();
    }

    public function updateTask($id, $name, $due_date, $status)
    {
        $stmt = $this->db->prepare("UPDATE tasks SET name=?, due_date=?, status=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $due_date, $status, $id);
        return $stmt->execute();
    }

    public function delete($task_id)
    {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id=?");
        $stmt->bind_param("i", $task_id);
        return $stmt->execute();
    }

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