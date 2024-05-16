<?php
require("helpers/handler.php");

$handler = new Handler();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['task_id'] ?? null;

    if ($taskId !== null) {
        $pdo = $handler->db->PDO();
        $query = "DELETE FROM tasks WHERE task_id = :task_id";
        $statement = $pdo->prepare($query);
        $statement->execute([':task_id' => $taskId]);

        if ($statement->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete task']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
