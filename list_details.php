<?php
require("helpers/handler.php");

$handler = new Handler();
$list_id = $_GET['list_id'] ?? null;

if (!$list_id) {
    die("Invalid list ID");
}

function getTasksForList($handler, $list_id) {
    $pdo = $handler->db->PDO();
    $query = "SELECT * FROM tasks WHERE list_id = :list_id";
    $statement = $pdo->prepare($query);
    $statement->execute([':list_id' => $list_id]);
    return $statement->fetchAll();
}

// Handle form submission for adding a new task
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskDescription = $_POST['task_description'] ?? '';
    if ($taskDescription) {
        $pdo = $handler->db->PDO();
        $query = "INSERT INTO tasks (list_id, task_description) VALUES (:list_id, :task_description)";
        $statement = $pdo->prepare($query);
        $statement->execute([':list_id' => $list_id, ':task_description' => $taskDescription]);
    }
}

$tasks = getTasksForList($handler, $list_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List Details</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to bottom, #e9ecef, #dee2e6);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            width: 95%;
            max-width: 800px;
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 40px;
            border-radius: 10px;
            margin: 20px;
        }

        header, footer {
            text-align: center;
            margin: 20px 0;
        }

        h1 {
            color: #007BFF;
            font-size: 32px;
        }

        h2 {
            color: #343a40;
            margin-bottom: 20px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        li:last-child {
            border-bottom: none;
        }

        .completed {
            text-decoration: line-through;
            color: #6c757d;
        }

        form {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        input[type="text"] {
            flex-grow: 1;
            margin-right: 10px;
            padding: 10px;
        }

        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const checkboxes = document.querySelectorAll('.task-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const taskId = this.dataset.taskId;
                    const isComplete = this.checked ? 1 : 0;

                    fetch('update_task.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `task_id=${taskId}&is_complete=${isComplete}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const taskItem = this.parentElement;
                            if (isComplete) {
                                taskItem.classList.add('completed');
                            } else {
                                taskItem.classList.remove('completed');
                            }
                        } else {
                            console.error('Error updating task:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                });
            });

            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const taskId = this.dataset.taskId;

                    fetch('delete_task.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `task_id=${taskId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const taskItem = this.parentElement;
                            taskItem.remove();
                        } else {
                            console.error('Error deleting task:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                });
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <header>
            <h1>List Details</h1>
        </header>
        <section>
            <h2>Tasks</h2>
            <form action="" method="POST">
                <input type="text" name="task_description" placeholder="New task description">
                <button type="submit">Add Task</button>
            </form>
            <ul>
                <?php foreach ($tasks as $task): ?>
                    <li class="<?= $task['is_complete'] ? 'completed' : '' ?>">
                        <input type="checkbox" class="task-checkbox" data-task-id="<?= $task['task_id'] ?>" <?= $task['is_complete'] ? 'checked' : '' ?>>
                        <?= htmlspecialchars($task['task_description']) ?>
                        <button class="delete-btn" data-task-id="<?= $task['task_id'] ?>">Delete</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <footer>
            <a href="ToDoCPSC431.html">Back to All Lists</a>
        </footer>
    </div>
</body>
</html>
