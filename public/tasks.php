<?php
session_start();
require_once __DIR__ . '/../src/User.php';
require_once __DIR__ . '/../src/Task.php';

User::checkAuth();
$task = new Task();
$user_id = $_SESSION['user_id'];
$status = $_GET['status'] ?? '';
$tasks = $task->getTasks($user_id, $status);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tasks - Task Manager</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body { background: #f0f2f5; min-height: 100vh; }
        .card { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .table thead { background-color: #343a40; color: #fff; }
        .btn-icon { border:none; background:none; cursor:pointer; font-size:1.2rem; transition: transform 0.2s; }
        .btn-icon:hover { transform: scale(1.2); }
        .modal-content { border-radius: 12px; }
        .modal-header { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; }
        .navbar { background-color: #fff; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<?php $username = $_SESSION['username'] ?? 'User'; ?>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">Task Manager</a>

        <div class="d-flex align-items-center">
            <!-- User initial circle -->
            <div class="dropdown">
                <button class="btn btn-secondary rounded-circle text-white fw-bold"
                        type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                        style="width:40px; height:40px; padding:0; font-size:1.2rem;">
                    <?= strtoupper($_SESSION['username'][0] ?? 'U') ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-5" style="padding-top: 80px;">
    <div class="card p-4">
        <!-- Filter + Add -->
        <div class="d-flex align-items-center mb-3 gap-2 flex-wrap">
            <form method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                <label>Status:</label>
                <select name="status" class="form-select w-auto">
                    <option value="">All</option>
                    <option value="active" <?= $status==='active'?'selected':'' ?>>Active</option>
                    <option value="completed" <?= $status==='completed'?'selected':'' ?>>Completed</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
            <button type="button" class="btn btn-success ms-auto" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                <i class="fa-solid fa-plus"></i> Add Task
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody id="taskTableBody">
                <?php foreach($tasks as $t): ?>
                    <tr id="taskRow<?= $t['id'] ?>">
                        <td><?= htmlspecialchars($t['name']) ?></td>
                        <td><?= htmlspecialchars($t['due_date']) ?></td>
                        <td><?= htmlspecialchars($t['status']) ?></td>
                        <td>
                            <button class="btn-icon text-primary" onclick="openEditModal(<?= $t['id'] ?>,'<?= htmlspecialchars($t['name'], ENT_QUOTES) ?>','<?= $t['due_date'] ?>','<?= $t['status'] ?>')" title="Edit">
                                <i class="fa-solid fa-pencil"></i>
                            </button>
                            <button class="btn-icon text-danger"
                                    onclick="deleteTask(<?= $t['id'] ?>, '<?= htmlspecialchars($t['name'], ENT_QUOTES) ?>')"
                                    title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="addTaskForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Task Name</label><input type="text" name="name" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Due Date</label><input type="date" name="due_date" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active">Active</option><option value="completed">Completed</option></select></div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-success">Add Task</button></div>
        </form>
    </div>
</div>

<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editTaskForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="editId">
                <div class="mb-3"><label class="form-label">Task Name</label><input type="text" name="name" id="editName" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Due Date</label><input type="date" name="due_date" id="editDueDate" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Status</label><select name="status" id="editStatus" class="form-select"><option value="active">Active</option><option value="completed">Completed</option></select></div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Update Task</button></div>
        </form>
    </div>
</div>

<!-- Delete Task Modal -->
<div class="modal fade" id="deleteTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Delete Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this task?</p>
                <p id="deleteTaskName" class="fw-bold"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button id="confirmDeleteBtn" type="button" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const editModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
    const addModal = new bootstrap.Modal(document.getElementById('addTaskModal'));

    // Open Edit Modal
    function openEditModal(id, name, dueDate, status) {
        document.getElementById('editId').value = id;
        document.getElementById('editName').value = name;
        document.getElementById('editDueDate').value = dueDate;
        document.getElementById('editStatus').value = status;
        editModal.show();
    }

    // AJAX: Edit Task
    document.getElementById('editTaskForm').addEventListener('submit', function(e){
        e.preventDefault();
        let formData = new FormData(this);
        fetch('updateTask.php',{method:'POST',body:formData})
            .then(res=>res.json())
            .then(data=>{
                if(data.success){
                    const row=document.getElementById('taskRow'+formData.get('id'));
                    row.children[0].textContent=formData.get('name');
                    row.children[1].textContent=formData.get('due_date');
                    row.children[2].textContent=formData.get('status');
                    editModal.hide();
                } else alert('Update failed');
            });
    });


    document.getElementById('addTaskForm').addEventListener('submit', function(e){
        e.preventDefault();
        let formData=new FormData(this);
        fetch('createTask.php',{method:'POST',body:formData})
            .then(res=>res.json())
            .then(data=>{
                if(data.success){
                    const t=data.task;
                    const tbody=document.getElementById('taskTableBody');
                    const row=document.createElement('tr');
                    row.id='taskRow'+t.id;
                    row.innerHTML=`
                <td>${t.name}</td>
                <td>${t.due_date}</td>
                <td>${t.status}</td>
                <td>
                    <button class="btn-icon text-primary" onclick="openEditModal(${t.id}, '${t.name.replace(/'/g,"\\'")}', '${t.due_date}', '${t.status}')" title="Edit">
                        <i class="fa-solid fa-pencil"></i>
                    </button>
                    <button class="btn-icon text-danger" onclick="deleteTask(${t.id})" title="Delete">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>`;
                    tbody.appendChild(row);
                    this.reset();
                    addModal.hide();
                } else alert('Add failed');
            });
    });

    // AJAX: Delete Task
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteTaskModal'));
    let taskToDeleteId = null;

    // Open Delete Modal
    function deleteTask(id, name) {
        taskToDeleteId = id;
        document.getElementById('deleteTaskName').textContent = name;
        deleteModal.show();
    }

    // Confirm Deletion
    document.getElementById('confirmDeleteBtn').addEventListener('click', function(){
        if (!taskToDeleteId) return;

        fetch('deleteTask.php', {
            method: 'POST',
            body: new URLSearchParams({id: taskToDeleteId})
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('taskRow' + taskToDeleteId).remove();
                    deleteModal.hide();
                    taskToDeleteId = null;
                } else {
                    alert('Delete failed');
                }
            });
    });
</script>
</body>
</html>
