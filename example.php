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
function GET (Handler $h)  // GOOD //
{
    // Get the "id" provided in the URL
    $id = $h->request->get["id"] ?? false; 

    if ($id !== false)
    {
        // Use the ID to get a single record from the data source
        $h->response->json(getRecord($h, $id));
    }else{
        // No ID was provided, just get all of the records
        $h->response->json(getAllRecords($h));
    }
}

// This secondary function is for getting 1 record from our DB
function getRecord (Handler $handler, $id)
{
    // Create a new PDO object with a special function from our DataSource class
    // This allows us to keep the MySQL credentials in 1 place and not rewrite them here
    $pdo = $handler->db->PDO();

    // This "query" is just a simple call to a stored procedure in our MySQL Database
    // The question mark is a special placeholder for parameters the pecedure takes
    $query = "CALL get_one_item(?)";

    // Preparing the query sets up the parameter placeholders and helps prevent SQL injection
    $statement = $pdo->prepare($query);

    // This executes the query. THIS is where your parameters go, as an array (even if only 1). 
    $statement->execute([$id]); 

    // The fetchAll() function returns an array. Each item contains 1 record. 
    // Each record in the array is a PHP associative array (e.g. ["id" => 123, "name" => "Bob"]). 
    $result = $statement->fetchAll();

    // The response->json() function accepts a PHP array or object as input
    // It then automatically converts it to JSON for you and outputs appropriate headers. 
    $handler->response->json($result);
}

    //This function does the same as getRecord but uses a different stored procedure
function getAllRecords (Handler $handler)
{
    // Create a new PDO object with our DataSource class
    $pdo = $handler->db->PDO();

    // This simple "query" is just a stored procedure call—one that does NOT need parameters
    $query = "CALL get_list_items()";  

    // We prepare the statement anyway because it still helps optimize the query
    $statement = $pdo->prepare($query);

    // We execute the query on the MySQL server. No parameters needed. 
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
    // Create the PDO object
    
    // Write a SQL query to use with placeholders for the data
    // The query will have MULTIPLE parameters. You may want to NAME the parameters to avoid confusion. 
    // Use a special syntax. Instead of question marks, use column (key) names with a colon in front: 
    $myQuery = "CALL create_record_procedure(:name,:age)";
    
    // Use a special PHP Associative Array for named parameters. The key names should start with a colon too. 
    // Obviously you'll need to get the values you need ($handler->request should have them somewhere)
    $parameters = [
        ":name" => "bob",
        ":age" => 23
    ];

    // NOTE: We do NOT have an ID. You shouldn't need an ID to create a record, your MySQL DB can do that. 
    // The exception is if you want to generate your IDs in PHP for some reason. I don't recommend it. 

    // Prepare the query

    // Execute the query, use the $parameters variable as the parameter. 

    // Fetch the query results

    // Return JSON (or some type of "success" response)
    // You're creating a record. It would be wise to return the record itself so you're 100% sure it worked. 
    // You could also just assume it worked and return a success message. 
}



// This function executes if you create a fetch() request to api/example.php and use "PUT" as the method
function PUT(Handler $handler)  
{
    // The PUT function is identical to the POST function with one difference: The ID. 
    // With PUT we are updating a record that should exist so you NEED to have the ID for this one. 
    // It can be part of the URL or it can just be part of the payload (data) sent from the client.     
}

