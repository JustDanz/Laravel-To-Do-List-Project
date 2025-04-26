<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
        }
        
        .task-column {
            min-height: 400px;
            background-color: white;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-top: 4px solid;
        }
        
        .task-column[data-status="belum_mulai"] {
            border-color: var(--warning);
        }
        
        .task-column[data-status="proses"] {
            border-color: var(--primary);
        }
        
        .task-column[data-status="selesai"] {
            border-color: var(--success);
        }
        
        .task-card {
            padding: 15px;
            margin-bottom: 15px;
            background: white;
            border-radius: 8px;
            cursor: grab;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border-left: 4px solid;
            transition: all 0.3s ease;
        }
        
        .task-card[data-status="belum_mulai"] {
            border-left-color: var(--warning);
        }
        
        .task-card[data-status="proses"] {
            border-left-color: var(--primary);
        }
        
        .task-card[data-status="selesai"] {
            border-left-color: var(--success);
        }
        
        .dragging {
            opacity: 0.5;
            transform: scale(1.02);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2) !important;
        }
        
        .drag-over {
            background-color: rgba(67, 97, 238, 0.05) !important;
        }
        
        .task-table {
            margin-top: 40px;
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .table thead th {
            background-color: var(--primary);
            color: white;
            border: none;
        }
        
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        
        .status-belum_mulai {
            background-color: var(--warning);
        }
        
        .status-proses {
            background-color: var(--primary);
        }
        
        .status-selesai {
            background-color: var(--success);
        }
        
        @media (max-width: 768px) {
            .task-column {
                min-height: 200px;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?php echo e(url('/')); ?>">To-Do List</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if(auth()->guard()->check()): ?>
                    <li class="nav-item">
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="nav-link btn btn-link" style="color: white;">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </button>
                        </form>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('login')); ?>">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('register')); ?>">
                            <i class="fas fa-user-plus me-1"></i> Register
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('calendar')); ?>">
                            <i class="fas fa-calendar-alt me-1"></i> Calendar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

<div class="container py-5">
    <h2 class="text-primary mb-4">To-Do List Dashboard</h2>

    <!-- Add Task Form -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form action="<?php echo e(route('tasks.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Task Title*</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Day*</label>
                        <select name="day_of_week" class="form-select" required>
                            <option value="" disabled selected>Select Day</option>
                            <?php $__currentLoopData = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($day); ?>"><?php echo e($day); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i> Add
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Trello Board -->
    <div class="row mb-5">
        <?php $__currentLoopData = ['belum_mulai' => 'Belum Mulai', 'proses' => 'Proses', 'selesai' => 'Selesai']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="m-0">
                    <span class="status-indicator status-<?php echo e($key); ?>"></span>
                    <?php echo e($label); ?>

                </h4>
                <span class="badge bg-light text-dark"><?php echo e(count($tasks[$key] ?? [])); ?></span>
            </div>
            <div class="task-column" data-status="<?php echo e($key); ?>" id="column-<?php echo e($key); ?>">
                <?php $__currentLoopData = $tasks[$key] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="task-card mb-3" data-id="<?php echo e($task->id); ?>" data-status="<?php echo e($key); ?>">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <strong><?php echo e($task->title); ?></strong>
                            <small class="text-muted"><?php echo e($task->day_of_week); ?></small>
                        </div>
                        <?php if($task->description): ?>
                        <p class="text-muted mb-3 small"><?php echo e($task->description); ?></p>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button class="btn btn-sm btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editModal<?php echo e($task->id); ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="<?php echo e(route('tasks.destroy', $task->id)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                            <span class="badge bg-<?php echo e($task->status == 'selesai' ? 'success' : 
                                ($task->status == 'proses' ? 'primary' : 'warning text-dark')); ?>">
                                <?php echo e(ucfirst(str_replace('_', ' ', $task->status))); ?>

                            </span>
                        </div>
                    </div>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?php echo e($task->id); ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="<?php echo e(route('tasks.update', $task->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Task</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Title*</label>
                                            <input type="text" name="title" class="form-control" value="<?php echo e($task->title); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea name="description" class="form-control" rows="3"><?php echo e($task->description); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Day*</label>
                                            <select name="day_of_week" class="form-select" required>
                                                <?php $__currentLoopData = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($day); ?>" <?php echo e($task->day_of_week == $day ? 'selected' : ''); ?>><?php echo e($day); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status*</label>
                                            <select name="status" class="form-select" required>
                                                <option value="belum_mulai" <?php echo e($task->status == 'belum_mulai' ? 'selected' : ''); ?>>Belum Mulai</option>
                                                <option value="proses" <?php echo e($task->status == 'proses' ? 'selected' : ''); ?>>Proses</option>
                                                <option value="selesai" <?php echo e($task->status == 'selesai' ? 'selected' : ''); ?>>Selesai</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Task Table -->
    <div class="task-table">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="m-0">Task Summary</h3>
            <span class="badge bg-primary">
                Total: <?php echo e(array_sum(array_map('count', $tasks))); ?>

            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Task</th>
                        <th width="120">Day</th>
                        <th width="150">Status</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php $__currentLoopData = ['belum_mulai', 'proses', 'selesai']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $tasks[$status] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($no++); ?></td>
                            <td>
                                <strong><?php echo e($task->title); ?></strong>
                                <?php if($task->description): ?>
                                <p class="text-muted small mb-0"><?php echo e($task->description); ?></p>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($task->day_of_week); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($task->status == 'selesai' ? 'success' : 
                                    ($task->status == 'proses' ? 'primary' : 'warning text-dark')); ?>">
                                    <i class="fas fa-<?php echo e($task->status == 'selesai' ? 'check-circle' : 
                                        ($task->status == 'proses' ? 'spinner' : 'clock')); ?> me-1"></i>
                                    <?php echo e(ucfirst(str_replace('_', ' ', $task->status))); ?>

                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editModal<?php echo e($task->id); ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="<?php echo e(route('tasks.destroy', $task->id)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize drag and drop
    const columns = document.querySelectorAll('.task-column');
    columns.forEach(column => {
        new Sortable(column, {
            group: 'tasks',
            animation: 150,
            ghostClass: 'dragging',
            dragClass: 'drag-over',
            onEnd: function(evt) {
                const taskId = evt.item.dataset.id;
                const newStatus = evt.to.dataset.status;
                
                updateTaskStatus(taskId, newStatus);
            }
        });
    });

    function updateTaskStatus(taskId, newStatus) {
        fetch(`/tasks/${taskId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                status: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload(); // Refresh to update both board and table
            } else {
                alert('Failed to update task status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating task status');
        });
    }
});
</script>
</body>
</html><?php /**PATH D:\XAMP\htdocs\to-do-list\resources\views/index.blade.php ENDPATH**/ ?>