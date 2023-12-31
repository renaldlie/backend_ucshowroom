<?php
require_once "connection.php";

function checkRequestHeader() {
    // Check if the "CONTENT_TYPE" key exists before accessing it
    if (isset($_SERVER['CONTENT_TYPE'])) {
        $contentType = $_SERVER['CONTENT_TYPE'];
        // Your existing code related to content type handling
    } else {
        // Handle the case when "CONTENT_TYPE" key is not set
        // You can log a message, set a default value, or take appropriate action
        
    }

    // Other code in your function
}

function checkRequestMethod($requestMethod) {
    if($_SERVER['REQUEST_METHOD'] != $requestMethod) {
        header('Content-type: application/json');
        http_response_code(405);
        echo json_encode(['status' => 0, 'message' => 'method not allowed'], JSON_NUMERIC_CHECK);
        die();
    };
}

function checkRequestBody($match, $count) {
    if(count($match) != $count) {
        header('Content-type: application/json');
        http_response_code(400);
        echo json_encode(['status' => 0, 'message' => 'request body not match'], JSON_NUMERIC_CHECK);
        die();
    }
}

function checkToken($token) {
    if(!isset($_SERVER['HTTP_TOKEN']) || $_SERVER['HTTP_TOKEN'] != $token) {
        http_response_code(403);
        echo json_encode(['status' => 0, 'message' => 'invalid token']);
        die();
    }
}

function checkOperationToken($table) {
    global $connect;

    if(isset($_SERVER['HTTP_TOKEN'])) {
        $token = mysqli_query($connect, "SELECT token FROM $table WHERE token = '$_SERVER[HTTP_TOKEN]'");
        if(mysqli_num_rows($token) != 1) {
            return false;
        }
    } else {
        return false;
    }

    return true;
}

function tokenErrorMessage() {
    http_response_code(403);
    echo json_encode(['status' => 0, 'message' => 'invalid token']);
    die();
}

function generateToken() {
    $token = openssl_random_pseudo_bytes(32);
    return bin2hex($token);
}