<?php
// Porject name: CPSC 431 Final Project - ToDo List 
// File name:  item.php
// Date : May 19 2023
// Author: Jiu Lin 

// Uncomment below if you need to have detailed error reporting
// error_reporting(E_ALL);

// Include the Handler class so we can use it. 
require("helpers/handler.php");

// Create a new request handler. 
$handler = new Handler();

// Process the request (Will execute one of the GET/PUT/POST/DELETE functions below)
$handler->process();

// This function executes if you create a fetch() request to api/example.php and use "GET" as the method
// By default you need to use api/example.php?id=123 to send an ID value. 
// You can use .htaccess to clean up the request URL to something like api/example/123 instead. It's optional. 
// function GET (Handler $h)  // GOOD //
// {
//     // Get the "id" provided in the URL
//     $id = $h->request->get["id"] ?? false; 

//     if ($id !== false)
//     {
//         // Use the ID to get a single record from the data source
//         $h->response->json(getRecord($h, $id));
//     }else{
//         // No ID was provided, just get all of the records
//         $h->response->json(getAllRecords($h));
//     }
// }

// This secondary function is for getting 1 record from our DB

//  function getRecord (Handler $handler, $id)
// {
//     // Create a new PDO object with a special function from our DataSource class
//     // This allows us to keep the MySQL credentials in 1 place and not rewrite them here
//     $pdo = $handler->db->PDO();

//     // This "query" is just a simple call to a stored procedure in our MySQL Database
//     // The question mark is a special placeholder for parameters the pecedure takes
//     $query = "CALL get_one_item(?)";

//     // Preparing the query sets up the parameter placeholders and helps prevent SQL injection
//     $statement = $pdo->prepare($query);

//     // This executes the query. THIS is where your parameters go, as an array (even if only 1). 
//     $statement->execute([$id]); 

//     // The fetchAll() function returns an array. Each item contains 1 record. 
//     // Each record in the array is a PHP associative array (e.g. ["id" => 123, "name" => "Bob"]). 
//     $result = $statement->fetchAll();

//     // The response->json() function accepts a PHP array or object as input
//     // It then automatically converts it to JSON for you and outputs appropriate headers. 
//     $handler->response->json($result);
// }

    //This function does the same as getRecord but uses a different stored procedure
    function GET (Handler $handler) {
        $pdo = $handler->db->PDO();
        $sort = $handler->request->get['sort'] ?? 'name'; // Default sorting by name
        $order = 'ASC';
    
        if ($sort === 'name') {
            $query = "SELECT * FROM todolists ORDER BY list_name $order";
        } elseif ($sort === 'created') {
            $query = "SELECT * FROM todolists ORDER BY created_at $order";
        } else {
            $query = "SELECT * FROM todolists";
        }
    
        $statement = $pdo->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();
        $handler->response->json($result);
    }
// This function executes if you create a fetch() request to api/example.php and use "DELETE" as the method
function DELETE(Handler $handler) {
    $pdo = $handler->db->PDO();
    $listId = $handler->request->post['list_id'] ?? null;

    if ($listId !== null) {
        // Delete  tasks associated with the list
        $deleteTasksQuery = "DELETE FROM tasks WHERE list_id = :list_id";
        $deleteTasksStatement = $pdo->prepare($deleteTasksQuery);
        $deleteTasksStatement->execute([':list_id' => $listId]);

        // Delete the list itself
        $deleteListQuery = "DELETE FROM todolists WHERE list_id = :list_id";
        $deleteListStatement = $pdo->prepare($deleteListQuery);
        $deleteListStatement->execute([':list_id' => $listId]);

        if ($deleteListStatement->rowCount() > 0) {
            $handler->response->json(['success' => true]);
        } else {
            $handler->response->json(['success' => false, 'message' => 'Failed to delete list']);
        }
    } else {
        $handler->response->json(['success' => false, 'message' => 'Invalid request']);
    }
}



// This function executes if you create a fetch() request to api/example.php and use "POST" as the method
function POST(Handler $handler) {
    $pdo = $handler->db->PDO();
    $listName = $handler->request->post['listName'] ?? null;
    $taskDescription = $handler->request->post['task_description'] ?? null;
    $listId = $handler->request->post['list_id'] ?? null;

    if ($handler->request->post['delete']) {
        DELETE($handler);
    } elseif ($listName) {
        $query = "INSERT INTO todolists (list_name) VALUES (:list_name)";
        $statement = $pdo->prepare($query);
        $statement->execute([':list_name' => $listName]);

        if ($statement->rowCount() > 0) {
            $newId = $pdo->lastInsertId();
            $handler->response->json(['success' => true, 'message' => 'List created successfully', 'list_id' => $newId]);
        } else {
            $handler->response->json(['success' => false, 'message' => 'Failed to create list']);
        }
    } elseif ($taskDescription && $listId) {
        $query = "INSERT INTO tasks (list_id, task_description) VALUES (:list_id, :task_description)";
        $statement = $pdo->prepare($query);
        $statement->execute([':list_id' => $listId, ':task_description' => $taskDescription]);

        if ($statement->rowCount() > 0) {
            $newId = $pdo->lastInsertId();
            $handler->response->json(['success' => true, 'message' => 'Task added successfully', 'task_id' => $newId]);
        } else {
            $handler->response->json(['success' => false, 'message' => 'Failed to add task']);
        }
    } else {
        $handler->response->json(['success' => false, 'message' => 'Invalid request']);
    }
}
