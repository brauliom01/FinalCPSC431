<?php
require("helpers/handler.php");

$handler = new Handler();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['task_id'] ?? null;
    $isComplete = $_POST['is_complete'] ?? null;

    if ($taskId !== null && $isComplete !== null) {
        $pdo = $handler->db->PDO();
        $query = "UPDATE tasks SET is_complete = :is_complete WHERE task_id = :task_id";
        $statement = $pdo->prepare($query);
        $statement->execute([':is_complete' => $isComplete, ':task_id' => $taskId]);

        if ($statement->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update task']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>