<?php
require("helpers/handler.php");

$handler = new Handler();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $listId = $_POST['list_id'] ?? null;
    $listName = $_POST['list_name'] ?? null;

    if ($listId !== null && $listName !== null) {
        $pdo = $handler->db->PDO();
        $query = "UPDATE todolists SET list_name = :list_name WHERE list_id = :list_id";
        $statement = $pdo->prepare($query);
        $statement->execute([':list_name' => $listName, ':list_id' => $listId]);

        if ($statement->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update list name']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
