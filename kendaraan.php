<?php
require_once "connection.php";
require_once "utils.php";
header('Content-type: application/json');

if (isset($_GET['api']) && function_exists($_GET['api'])) {
    checkRequestHeader();
    $_GET['api']();
}

function addKendaraan()
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
    $query = "SELECT * FROM customer";

    $result = mysqli_query($connect, $query);

    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    if (isset($_GET['id']) && count($rows) == 0) {
        http_response_code(404);
        echo json_encode(['status' => 0, 'message' => "ID doesn't match"], JSON_NUMERIC_CHECK);
        die();
    }

    http_response_code(200);
    echo json_encode(['status' => 1, 'message' => 'success get data', 'data' => $rows], JSON_NUMERIC_CHECK);
}


function update()
{
    global $connect;
    checkRequestMethod('PUT');

    $data = json_decode(file_get_contents('php://input'), true);

    $requestBody = [
        'new_nama' => '',
        'new_notelp' => '',
        'new_idcard' => ''
    ];
    $match = array_intersect_key($data, $requestBody);
    checkRequestBody($match, 3);

    $studentData = mysqli_query($connect, "SELECT * FROM customer WHERE nama = '$data[new_nama]'");
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

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['email'])) {
        http_response_code(400);
        echo json_encode(['status' => 0, 'message' => 'request body not match'], JSON_NUMERIC_CHECK);
        die();
    }

    $studentData = mysqli_query($connect, "SELECT id_student FROM student WHERE email = '$data[email]'");
    $studentData = mysqli_fetch_row($studentData);
    if (!$studentData) {
        http_response_code(404);
        echo json_encode(['status' => 0, 'message' => 'user not found'], JSON_NUMERIC_CHECK);
        die();
    }

    $token = mysqli_query($connect, "SELECT token FROM student WHERE email = '$data[email]'");
    $token = mysqli_fetch_row($token)[0];
    checkToken($token);

    // get all id_test by id_student
    $speakingTest = mysqli_query($connect, "SELECT id_test FROM speaking_test WHERE id_student = '$studentData[0]'");
    $testIds = [];
    while ($testId = mysqli_fetch_row($speakingTest)) {
        $testIds[] = $testId[0];
    }

    // get all audio from deleted user
    $testAudios = [];
    foreach ($testIds as $testId) {
        $detailIds = mysqli_query($connect, "SELECT answer FROM st_detail WHERE id_test = '$testId'");
        while ($detailId = mysqli_fetch_row($detailIds)) {
            if ($detailId[0] != '') {
                $testAudios[] = $detailId[0];
            }
        }
    }

    // delete audio
    foreach ($testAudios as $filename) {
        if (file_exists('./answers/' . $filename)) {
            unlink('./answers/' . $filename);
        }
    }

    // delete st_detail from db
    foreach ($testIds as $testId) {
        mysqli_query($connect, "DELETE FROM st_detail WHERE id_test = '$testId'");
    }
    $query1 = "DELETE FROM speaking_test WHERE id_student = '$studentData[0]'";
    $query2 = "DELETE FROM student WHERE email = '$data[email]'";

    $result1 = mysqli_query($connect, $query1);
    $result2 = mysqli_query($connect, $query2);

    if ($result1 && $result2) {
        http_response_code(200);
        echo json_encode(['status' => 1, 'message' => 'data deleted'], JSON_NUMERIC_CHECK);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 0, 'message' => 'request failed, server error'], JSON_NUMERIC_CHECK);
    }
}
