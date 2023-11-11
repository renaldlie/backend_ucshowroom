<?php
require_once "connection.php";
require_once "utils.php";
header('Content-type: application/json');

if (isset($_GET['api']) && function_exists($_GET['api'])) {
    checkRequestHeader();
    $_GET['api']();
}

function create()
{
    global $connect;
    checkRequestMethod('POST');

    $data = json_decode(file_get_contents('php://input'), true);

    $requestBody = [
        'id_customer' => '',
        'id_kendaraan' => '',
        'jumlah' => '',
        'total' => ''
    ];
    $match = array_intersect_key($data, $requestBody);
    checkRequestBody($match, 4);



    $query = "INSERT INTO pesanan SET
                    id_customer = '$data[id_customer]',
                    id_kendaraan = '$data[id_kendaraan]',
                    jumlah = '$data[jumlah]',
                    total = '$data[total]'";

    $result = mysqli_query($connect, $query);
    $lastId = mysqli_insert_id($connect);

    if ($result) {
        http_response_code(201);
        echo json_encode(['status' => 1, 'message' => 'Create Pesanan Success', 'data' => [
            'id_pesanan' => $lastId,
            'id_customer' => $data['id_customer'],
            'id_kendaraan' => $data['id_kendaraan'],
            'jumlah' => $data['jumlah'],
            'total' => $data['total'],
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
        $query = "SELECT * FROM pesanan WHERE id_pesanan = '$id'";
    } else {
        $query = "SELECT * FROM pesanan";
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
        'id_customer' => '',
        'new_kendaraan' => '',
        'new_jumlah' => '',
        'new_total' => ''
    ];
    $match = array_intersect_key($data, $requestBody);
    checkRequestBody($match, 4);

    $studentData = mysqli_query($connect, "SELECT * FROM pesanan WHERE id_customer = '$data[id_customer]'");
    if (!mysqli_num_rows($studentData)) {
        http_response_code(404);
        echo json_encode(['status' => 0, 'message' => 'user not found'], JSON_NUMERIC_CHECK);
        die();
    }


    $query = "UPDATE pesanan SET
            id_customer = '$data[id_customer]',
            kendaraan = '$data[new_kendaraan]',
            jumlah = '$data[new_jumlah]',
            total = '$data[new_total]'
    WHERE id_customer = '$data[id_customer]'";

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
    $existingUserQuery = mysqli_query($connect, "SELECT * FROM pesanan WHERE id_pesanan = '$id'");
    if (!mysqli_num_rows($existingUserQuery)) {
        http_response_code(404);
        echo json_encode(['status' => 0, 'message' => 'User not found'], JSON_NUMERIC_CHECK);
        die();
    }

    // Delete the customer with the specified ID
    $deleteQuery = "DELETE FROM pesanan WHERE id_pesanan = '$id'";
    $result = mysqli_query($connect, $deleteQuery);

    if ($result) {
        http_response_code(200);
        echo json_encode(['status' => 1, 'message' => 'Pesanan deleted successfully'], JSON_NUMERIC_CHECK);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 0, 'message' => 'Failed to delete pesanan'], JSON_NUMERIC_CHECK);
    }
}
