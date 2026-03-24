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
$name = $_POST['name'] ?? '';
$due_date = $_POST['due_date'] ?? '';
$status = $_POST['status'] ?? 'active';
$user_id = $_SESSION['user_id'];

if (empty($name) || empty($due_date)) {
    echo json_encode(['success' => false, 'message' => 'Name and due date are required']);
    exit();
}

// Valid date
if (!strtotime($due_date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid date']);
    exit();
}

// Valid status
$allowedStatus = ['active', 'completed'];
if (!in_array($status, $allowedStatus)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

// Create the task and get new ID
$newId = $task->create($name, $due_date, $status, $user_id);
if ($newId) {
    echo json_encode([
        'success' => true,
        'task' => [
            'id' => $newId,
            'name' => $name,
            'due_date' => $due_date,
            'status' => $status
        ]
    ]);
} else {
    echo json_encode(['success'=>false, 'message'=>'Could not create task']);
}