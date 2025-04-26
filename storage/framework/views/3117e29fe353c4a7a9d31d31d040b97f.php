<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Calendar</title>
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
        
        .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
        }
        
        .calendar-container {
            margin-top: 30px;
            margin-bottom: 50px;
        }
        
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .calendar-title {
            color: var(--primary);
            font-weight: 700;
            margin: 0;
        }
        
        .week-container {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            padding-bottom: 15px;
        }
        
        .day-column {
            min-width: 250px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 15px;
            transition: all 0.3s ease;
        }
        
        .day-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 10px;
            margin-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .day-name {
            font-weight: 600;
            color: var(--primary);
            margin: 0;
        }
        
        .task-count {
            background-color: var(--light);
            color: var(--dark);
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }
        
        .task-card {
            padding: 15px;
            margin-bottom: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border-left: 4px solid;
            transition: all 0.3s ease;
            cursor: grab;
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
        
        .task-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .task-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .task-description {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        .task-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .task-status {
            font-size: 0.75rem;
            padding: 3px 8px;
            border-radius: 10px;
            font-weight: 500;
        }
        
        .status-belum_mulai {
            background-color: rgba(248, 150, 30, 0.1);
            color: var(--warning);
        }
        
        .status-proses {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary);
        }
        
        .status-selesai {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success);
        }
        
        .task-actions {
            display: flex;
            gap: 5px;
        }
        
        .btn-action {
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            padding: 0;
        }
        
        .dragging {
            opacity: 0.5;
            transform: scale(1.02);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2) !important;
        }
        
        .drag-over {
            background-color: rgba(67, 97, 238, 0.05) !important;
        }
        
        @media (max-width: 768px) {
            .week-container {
                flex-direction: column;
            }
            
            .day-column {
                min-width: 100%;
            }
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary);
        }
        
        /* Modal styles */
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            border-bottom: none;
            padding-bottom: 0;
        }
        
        .modal-footer {
            border-top: none;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <i class="fas fa-calendar-alt me-2"></i>Task Calendar
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">  
                    <li class="nav-item">
                        <a class="nav-link" href="/">
                            <i class="fas fa-tasks me-1"></i> Tasks
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container calendar-container">
        <div class="calendar-header">
            <h1 class="calendar-title">Weekly Task Calendar</h1>
            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                    <i class="fas fa-plus me-2"></i> Add Task
                </button>
            </div>
        </div>
        
        <div class="week-container">
            <?php $__currentLoopData = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="day-column" data-day="<?php echo e($day); ?>">
                    <div class="day-header">
                        <h3 class="day-name"><?php echo e($day); ?></h3>
                        <span class="task-count"><?php echo e(count($tasks[$day] ?? [])); ?></span>
                    </div>
                    
                    <?php $__currentLoopData = $tasks[$day] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="task-card" draggable="true" data-id="<?php echo e($task->id); ?>" data-status="<?php echo e($task->status); ?>">
                            <div class="task-title"><?php echo e($task->title); ?></div>
                            <?php if($task->description): ?>
                                <div class="task-description"><?php echo e($task->description); ?></div>
                            <?php endif; ?>
                            <div class="task-footer">
                                <span class="task-status status-<?php echo e($task->status); ?>">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $task->status))); ?>

                                </span>
                                <div class="task-actions">
                                    <button class="btn btn-action btn-sm btn-outline-primary edit-task" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editTaskModal"
                                            data-id="<?php echo e($task->id); ?>"
                                            data-title="<?php echo e($task->title); ?>"
                                            data-description="<?php echo e($task->description); ?>"
                                            data-day="<?php echo e($task->day_of_week); ?>"
                                            data-status="<?php echo e($task->status); ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-action btn-sm btn-outline-danger delete-task" 
                                            data-id="<?php echo e($task->id); ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addTaskForm" action="<?php echo e(route('tasks.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="addTaskModalLabel">Add New Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="addTaskTitle" class="form-label">Title*</label>
                            <input type="text" class="form-control" id="addTaskTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="addTaskDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="addTaskDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="addTaskDay" class="form-label">Day*</label>
                            <select class="form-select" id="addTaskDay" name="day_of_week" required>
                                <option value="" disabled selected>Select Day</option>
                                <?php $__currentLoopData = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($day); ?>"><?php echo e($day); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="addTaskStatus" class="form-label">Status*</label>
                            <select class="form-select" id="addTaskStatus" name="status" required>
                                <option value="belum_mulai" selected>Belum Mulai</option>
                                <option value="proses">Proses</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Task Modal -->
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editTaskForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editTaskTitle" class="form-label">Title*</label>
                            <input type="text" class="form-control" id="editTaskTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="editTaskDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editTaskDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editTaskDay" class="form-label">Day*</label>
                            <select class="form-select" id="editTaskDay" name="day_of_week" required>
                                <?php $__currentLoopData = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($day); ?>"><?php echo e($day); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editTaskStatus" class="form-label">Status*</label>
                            <select class="form-select" id="editTaskStatus" name="status" required>
                                <option value="belum_mulai">Belum Mulai</option>
                                <option value="proses">Proses</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Drag and Drop functionality
            let draggedCard = null;

            document.querySelectorAll('.task-card').forEach(card => {
                card.addEventListener('dragstart', function() {
                    draggedCard = this;
                    setTimeout(() => {
                        this.classList.add('dragging');
                    }, 0);
                });

                card.addEventListener('dragend', function() {
                    this.classList.remove('dragging');
                    draggedCard = null;
                });
            });

            document.querySelectorAll('.day-column').forEach(column => {
                column.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.classList.add('drag-over');
                });

                column.addEventListener('dragleave', function() {
                    this.classList.remove('drag-over');
                });

                column.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.classList.remove('drag-over');
                    
                    if (draggedCard) {
                        if (!this.contains(draggedCard)) {
                            this.appendChild(draggedCard);
                            
                            // Update task count
                            const taskCount = this.querySelector('.task-count');
                            taskCount.textContent = parseInt(taskCount.textContent) + 1;
                            
                            // Find the original column and update its count
                            const originalColumn = draggedCard.closest('.day-column');
                            if (originalColumn && originalColumn !== this) {
                                const originalCount = originalColumn.querySelector('.task-count');
                                originalCount.textContent = parseInt(originalCount.textContent) - 1;
                            }
                            
                            // Send update to server
                            const taskId = draggedCard.getAttribute('data-id');
                            const newDay = this.getAttribute('data-day');
                            
                            fetch(`/tasks/${taskId}/update-day`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({ day_of_week: newDay })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (!data.success) {
                                    alert('Failed to update task day');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Error updating task day');
                            });
                        }
                    }
                });
            });

            // Edit Task Modal
            document.querySelectorAll('.edit-task').forEach(button => {
                button.addEventListener('click', function() {
                    const taskId = this.getAttribute('data-id');
                    const title = this.getAttribute('data-title');
                    const description = this.getAttribute('data-description');
                    const day = this.getAttribute('data-day');
                    const status = this.getAttribute('data-status');
                    
                    document.getElementById('editTaskTitle').value = title;
                    document.getElementById('editTaskDescription').value = description;
                    document.getElementById('editTaskDay').value = day;
                    document.getElementById('editTaskStatus').value = status;
                    
                    document.getElementById('editTaskForm').action = `/tasks/${taskId}`;
                });
            });

            // Delete Task
            document.querySelectorAll('.delete-task').forEach(button => {
                button.addEventListener('click', function() {
                    const taskId = this.getAttribute('data-id');
                    
                    if (confirm('Are you sure you want to delete this task?')) {
                        fetch(`/tasks/${taskId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(response => {
                            if (response.ok) {
                                // Remove task card from DOM
                                const taskCard = document.querySelector(`.task-card[data-id="${taskId}"]`);
                                if (taskCard) {
                                    const dayColumn = taskCard.closest('.day-column');
                                    taskCard.remove();
                                    
                                    // Update task count
                                    const taskCount = dayColumn.querySelector('.task-count');
                                    taskCount.textContent = parseInt(taskCount.textContent) - 1;
                                }
                            } else {
                                alert('Failed to delete task');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error deleting task');
                        });
                    }
                });
            });

            // Add Task Form Submission
            document.getElementById('addTaskForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert('Failed to add task');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error adding task');
                });
            });

            // Edit Task Form Submission
            document.getElementById('editTaskForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-HTTP-Method-Override': 'PUT'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert('Failed to update task');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating task');
                });
            });
        });
    </script>
</body>
</html><?php /**PATH D:\XAMP\htdocs\to-do-list\resources\views/calendar.blade.php ENDPATH**/ ?>