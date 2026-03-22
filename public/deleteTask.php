<?php
header('Content-Type: application/json');
session_start();
require_once('../src/User.php');
require_once('../src/Task.php');

User::checkAuth();
$task = new Task();

$id = $_POST['id'] ?? 0;
$success = false;

if($id){
    $success = $task->delete($id);
}

echo json_encode(['success' => $success]);
