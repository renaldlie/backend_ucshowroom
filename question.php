<?php
require_once "connection.php";
require_once "utils.php";
header('Content-type: application/json');

    if(isset($_GET['api']) && function_exists($_GET['api'])) {
        $_GET['api']();
    }

    function insert() {
        global $connect;
        checkRequestHeader();
        checkRequestMethod('POST');
        if(!checkOperationToken('teacher')) {
            tokenErrorMessage();
        };

        $questionCount = mysqli_query($connect, "SELECT COUNT(id_soal) FROM soal");
        $questionCount = mysqli_fetch_row($questionCount)[0];

        // // If question is fix to 10
        // if($questionCount >= 10) {
        //     http_response_code(409);
        //     echo json_encode(['status' => 0, 'message' => 'question has reached maximum limit'], JSON_NUMERIC_CHECK);
        //     die();
        // }

        $data = json_decode(file_get_contents('php://input'), true);

        $requestBody = [
            'question' => '',
            'difficulty' => '',
            'timer' => ''
        ];
        $match = array_intersect_key($data, $requestBody);
        checkRequestBody($match, 3);

        $query = "INSERT INTO soal SET
                     question = '$data[question]',
                     difficulty = '$data[difficulty]',
                     timer = '$data[timer]'";
                     //add difficulty
        $result = mysqli_query($connect, $query);

        if($result) {
            http_response_code(201);
            echo json_encode(['status' => 1, 'message' => 'new question created'], JSON_NUMERIC_CHECK);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 0, 'message' => 'request failed, server error'], JSON_NUMERIC_CHECK);


        }
    }

    function read() {
        global $connect;
        checkRequestMethod('GET');
        if(!checkOperationToken('teacher') && !checkOperationToken('student')) {
            tokenErrorMessage();
        };

        if(isset($_GET['id'])) {
            $query = "SELECT * FROM soal WHERE id_soal = '$_GET[id]'";
        } else if(count($_GET) == 1) {
            $query = "SELECT * FROM soal";
        } else {
            http_response_code(400);
            echo json_encode(['status' => 0, 'message' => 'wrong parameter(s)'], JSON_NUMERIC_CHECK);
            die();
        }

        $result = mysqli_query($connect, $query);

        $rows = [];
        while($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        if(isset($_GET['id']) && count($rows) == 0) {
            http_response_code(404);
            echo json_encode(['status' => 0, 'message' => "ID doesn't match"], JSON_NUMERIC_CHECK);
            die();
        }

        http_response_code(200);
        echo json_encode(['status' => 1, 'message' => 'success get data', 'data' => $rows], JSON_NUMERIC_CHECK);
    }

    function update() {
        global $connect;
        checkRequestHeader();
        checkRequestMethod('PUT');
        if(!checkOperationToken('teacher')) {
            tokenErrorMessage();
        };

        $data = json_decode(file_get_contents('php://input'), true);

        $requestBody = [
            'id' => '',
            'new_question' => '',
            'new_timer' => '',
            'new_difficulty' => ''
        ];
        $match = array_intersect_key($data, $requestBody);
        checkRequestBody($match, 3);

        $questionData = mysqli_query($connect, "SELECT * FROM soal WHERE id_soal = '$data[id]'");
        if(!mysqli_num_rows($questionData)) {
            http_response_code(404);
            echo json_encode(['status' => 0, 'message' => "ID doesn't match"], JSON_NUMERIC_CHECK);
            die();
        }

        $query = "UPDATE soal SET 
                question = '$data[new_question]',
                timer = '$data[new_timer]',
                difficulty = '$data[new_difficulty],        
                WHERE id_soal = '$data[id]'";

        $result = mysqli_query($connect, $query);
        if($result) {
            http_response_code(200);
            echo json_encode(['status' => 1, 'message' => 'question have been updated'], JSON_NUMERIC_CHECK);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 0, 'message' => 'request failed, server error'], JSON_NUMERIC_CHECK);
        }
    }

    function delete() {
        global $connect;
        checkRequestHeader();
        checkRequestMethod('DELETE');
        if(!checkOperationToken('teacher')) {
            tokenErrorMessage();
        };

        $data = json_decode(file_get_contents('php://input'), true);

        if(!isset($data['id'])) {
            http_response_code(400);
            echo json_encode(['status' => 0, 'message' => 'request body not match'], JSON_NUMERIC_CHECK);
            die();
        }

        $questionData = mysqli_query($connect, "SELECT * FROM soal WHERE id_soal = '$data[id]'");
        if(!mysqli_num_rows($questionData)) {
            http_response_code(404);
            echo json_encode(['status' => 0, 'message' => 'data not found'], JSON_NUMERIC_CHECK);
            die();
        }

        $query = "DELETE FROM soal WHERE id_soal = '$data[id]'";

        $result = mysqli_query($connect, $query);
        if($result) {
            http_response_code(200);
            echo json_encode(['status' => 1, 'message' => 'data deleted'], JSON_NUMERIC_CHECK);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 0, 'message' => 'request failed, server error'], JSON_NUMERIC_CHECK);
        }
    }