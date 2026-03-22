<?php
header('Content-Type: application/json');
session_start();
require_once('../src/User.php');
require_once('../src/Task.php');

$user = new User();
// redirect if not logged in
if (!$user->isLoggedIn()) {
    echo json_encode(['success'=>false]);
    exit();
}
$task = new Task();

$id = $_POST['id'] ?? 0;
$name = $_POST['name'] ?? '';
$due_date = $_POST['due_date'] ?? '';
$status = $_POST['status'] ?? '';

$success = false;
if ($id && $name && $due_date && $status) {
    $success = $task->updateTask($id, $name, $due_date, $status);
}

echo json_encode(['success' => $success, 'task' => ['id' => $id, 'name' => $name, 'due_date' => $due_date, 'status' => $status]]);
