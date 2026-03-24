<?php
header('Content-Type: application/json');
session_start();

require_once('../src/User.php');
require_once('../src/Task.php');

$user = new User();

if (!$user->isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$task = new Task();

$id = $_POST['id'] ?? 0;
$name = trim($_POST['name'] ?? '');
$due_date = $_POST['due_date'] ?? '';
$status = $_POST['status'] ?? '';

if (!$id || empty($name) || empty($due_date) || empty($status)) {
    echo json_encode(['success' => false, 'message' => 'All fields required']);
    exit();
}

if (!strtotime($due_date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid date']);
    exit();
}

$allowedStatus = ['active', 'completed'];
if (!in_array($status, $allowedStatus)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

$user_id = $_SESSION['user_id'];

$success = $task->updateTask($id, $name, $due_date, $status, $user_id);

echo json_encode([
    'success' => $success,
    'task' => [
        'id' => $id,
        'name' => $name,
        'due_date' => $due_date,
        'status' => $status
    ]
]);