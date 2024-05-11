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
    function GET (Handler $handler)
    {
        // Create a new PDO object with our DataSource class
        $pdo = $handler->db->PDO();
    
        // This direct SQL query fetches all records from the 'todolists' table
        $query = "SELECT * FROM todolists";  
    
        // We prepare the statement as it is best practice to do so even with straightforward queries
        $statement = $pdo->prepare($query);
    
        // We execute the query on the MySQL server. No parameters are needed for this query
        $statement->execute();  
    
        // Output a PHP array of all records found. Each item in the array is a PHP Associative Array.
        $result = $statement->fetchAll();
    
        // Automatically convert our PHP array to JSON and output it for the client. 
        $handler->response->json($result);
    }
// This function executes if you create a fetch() request to api/example.php and use "DELETE" as the method
function DELETE(Handler $handler)
{
    // Create the PDO object

    // Get the ID of the record you want to delete ($handler->request should have it somewhere)

    // Write a SQL query to use with a placeholder for the ID

    // Prepare the query 

    // Execute the query, provide the ID as a parameter

    // Fetch the query results

    // Return JSON (or some type of "success" response)
    // You're deleting so you don't necessarily have results to return!
}



// This function executes if you create a fetch() request to api/example.php and use "POST" as the method
function POST(Handler $handler) // GOOD//
{
    // Create the PDO object from the DataSource
    $pdo = $handler->db->PDO();

    // The SQL query to insert a new list with just the list name. ID is auto-incremented.
    $query = "INSERT INTO todolists (list_name) VALUES (:list_name)";

    // Assume you get this value from the client-side. Adjust the retrieval as necessary.
    $listName = $handler->request->post['listName'] ?? null;

    // Check if the list name is provided
    if (!$listName) {
        $handler->response->json(['success' => false, 'message' => 'List name is required.']);
        return;
    }

    // Prepare the SQL statement with the placeholder for list_name
    $statement = $pdo->prepare($query);

    try {
        // Execute the query with the list name parameter
        $statement->execute([':list_name' => $listName]);

        // Check if the insert was successful
        if ($statement->rowCount() > 0) {
            // Return success message with the ID of the newly created list
            $newId = $pdo->lastInsertId(); // Gets the last inserted ID
            $handler->response->json(['success' => true, 'message' => 'List created successfully', 'list_id' => $newId]);
        } else {
            $handler->response->json(['success' => false, 'message' => 'Failed to create list']);
        }
    } catch (PDOException $e) {
        // Return JSON error message in case of a database error
        $handler->response->json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}



// This function executes if you create a fetch() request to api/example.php and use "PUT" as the method
function PUT(Handler $handler)  
{
    // The PUT function is identical to the POST function with one difference: The ID. 
    // With PUT we are updating a record that should exist so you NEED to have the ID for this one. 
    // It can be part of the URL or it can just be part of the payload (data) sent from the client.     
}

