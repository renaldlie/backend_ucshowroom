<?php
require_once "connection.php";
require_once "utils.php";
header('Content-type: application/json');

if (isset($_GET['api']) && function_exists($_GET['api'])) {
    checkRequestHeader();
    $_GET['api']();
}

function signup()
{
    global $connect;
    checkRequestMethod('POST');

    $data = json_decode(file_get_contents('php://input'), true);

    $requestBody = [
        'nama' => '',
        'notelp' => '',
        'id_card' => ''
    ];
    $match = array_intersect_key($data, $requestBody);
    checkRequestBody($match, 3);



    $query = "INSERT INTO customer SET
                    nama = '$data[nama]',
                    notelp = '$data[notelp]',
                    id_card = '$data[id_card]'";

    $result = mysqli_query($connect, $query);
    $lastId = mysqli_insert_id($connect);

    if ($result) {
        http_response_code(201);
        echo json_encode(['status' => 1, 'message' => 'Create Customer Success', 'data' => [
            'id_customer' => $lastId,
            'nama' => $data['nama'],
            'notelp' => $data['notelp'],
            'id_card' => $data['id_card'],
        ]], JSON_NUMERIC_CHECK);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 1, 'message' => 'Request failed, server error '], JSON_NUMERIC_CHECK);
    }
}

function read()
{
    global $connect;
    checkRequestMethod('GET');

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $query = "SELECT * FROM customer WHERE id_customer = '$id'";
    } else {
        $query = "SELECT * FROM customer";
    }

    $result = mysqli_query($connect, $query);

    if (!$result) {
        http_response_code(500);
        echo json_encode(['status' => 0, 'message' => 'Internal Server Error'], JSON_NUMERIC_CHECK);
        die();
    }

    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    if (isset($_GET['id']) && count($rows) == 0) {
        http_response_code(404);
        echo json_encode(['status' => 0, 'message' => 'No matching records found'], JSON_NUMERIC_CHECK);
        die();
    }

    http_response_code(200);
    echo json_encode(['status' => 1, 'message' => 'Success', 'data' => $rows], JSON_NUMERIC_CHECK);
}




function update()
{
    global $connect;
    checkRequestMethod('PUT');

    $data = json_decode(file_get_contents('php://input'), true);

    $requestBody = [
        'nama' => '',
        'new_nama' => '',
        'new_notelp' => '',
        'new_idcard' => ''
    ];
    $match = array_intersect_key($data, $requestBody);
    checkRequestBody($match, 4);

    $studentData = mysqli_query($connect, "SELECT * FROM customer WHERE nama = '$data[nama]'");
    if (!mysqli_num_rows($studentData)) {
        http_response_code(404);
        echo json_encode(['status' => 0, 'message' => 'user not found'], JSON_NUMERIC_CHECK);
        die();
    }


    $query = "UPDATE customer SET
            nama = '$data[new_nama]',
            notelp = '$data[new_notelp]',
            id_card = '$data[new_idcard]'
    WHERE nama = '$data[nama]'";

    $result = mysqli_query($connect, $query);
    if ($result) {
        http_response_code(200);
        echo json_encode(['status' => 1, 'message' => 'data updated'], JSON_NUMERIC_CHECK);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 0, 'message' => 'request failed, server error'], JSON_NUMERIC_CHECK);
    }
}

function delete()
{
    global $connect;
    checkRequestMethod('DELETE');

    // Check if 'id' parameter is set
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['status' => 0, 'message' => 'Missing ID parameter'], JSON_NUMERIC_CHECK);
        die();
    }

    $id = $_GET['id'];

    // Check if the customer with the specified ID exists
    $existingUserQuery = mysqli_query($connect, "SELECT * FROM customer WHERE id_customer = '$id'");
    if (!mysqli_num_rows($existingUserQuery)) {
        http_response_code(404);
        echo json_encode(['status' => 0, 'message' => 'User not found'], JSON_NUMERIC_CHECK);
        die();
    }

    // Delete the customer with the specified ID
    $deleteQuery = "DELETE FROM customer WHERE id_customer = '$id'";
    $result = mysqli_query($connect, $deleteQuery);

    if ($result) {
        http_response_code(200);
        echo json_encode(['status' => 1, 'message' => 'Customer deleted successfully'], JSON_NUMERIC_CHECK);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 0, 'message' => 'Failed to delete customer'], JSON_NUMERIC_CHECK);
    }
}
