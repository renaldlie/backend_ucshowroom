<?php
require_once "connection.php";
require_once "utils.php";
header('Content-type: application/json');

    if(isset($_GET['api']) && function_exists($_GET['api'])) {
        $_GET['api']();
    }

    function start() {
        global $connect;
        checkRequestHeader();
        checkRequestMethod('POST');
        if(!checkOperationToken('student')) {
            tokenErrorMessage();
        };

        $data = json_decode(file_get_contents('php://input'), true);

        $requestBody = [
            'datetime' => '',
            'id_student' => ''
            
        ];
        $match = array_intersect_key($data, $requestBody);
        checkRequestBody($match, 2);

        $studentData = mysqli_query($connect, "SELECT * FROM student WHERE id_student = '$data[id_student]'");
        if(!mysqli_num_rows($studentData)) {
            http_response_code(404);
            echo json_encode(['status' => 0, 'message' => 'student not found'], JSON_NUMERIC_CHECK);
            die();
        }

        $query = "INSERT INTO speaking_test SET 
                              datetime = '$data[datetime]',
                              id_student = '$data[id_student]'";

        $result = mysqli_query($connect, $query);
        $lastId = mysqli_insert_id($connect);

        if($result) {
            // create array of question id
            $questionData = mysqli_query($connect, "SELECT id_soal FROM soal");
            while($questionDatum = mysqli_fetch_row($questionData)) {
                mysqli_query($connect, "INSERT INTO st_detail SET id_test = '$lastId', id_soal = '$questionDatum[0]'");
            }
            

            http_response_code(201);
            echo json_encode(['status' => 1, 'message' => 'data inserted', 'data' => ['id_test' => $lastId]], JSON_NUMERIC_CHECK);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 0, 'message' => 'request failed, server error'], JSON_NUMERIC_CHECK);
        }
    }

    function save_result() {
        global $connect;
        checkRequestHeader();
        checkRequestMethod('POST');
        if(!checkOperationToken('student')) {
            tokenErrorMessage();
        };

        // Add Request Difficulty
        $requestBody = [
            'id_test' => '',
            'question' => '',
            'difficulty' => ''
        ];
        $match = array_intersect_key($_POST, $requestBody);
        checkRequestBody($match, 2);

        $testId = mysqli_query($connect, "SELECT id_test FROM speaking_test WHERE id_test = '$_POST[id_test]'");
        $questionId = mysqli_query($connect, "SELECT id_soal FROM soal WHERE id_soal = '$_POST[id_soal]'");
        if(!mysqli_num_rows($testId) || !mysqli_num_rows($questionId)) {
            http_response_code(404);
            echo json_encode(['status' => 0, 'message' => 'test ID or question ID not found'], JSON_NUMERIC_CHECK);
            die();
        }

        if(isset($_FILES['answer']) && $_FILES['answer']['name'] != null) {
            $tmp_name = $_FILES['answer']['tmp_name'];
            $name = $_FILES['answer']['name'];

            $extension = explode('.', $name);
            $extension = strtolower(end($extension));
            $newName = uniqid();
            $newName .= '.';
            $newName .= $extension;

            if(!file_exists('./answers')) {
                mkdir('./answers', 0755, true);
            }
            $uploadStatus = move_uploaded_file($tmp_name, './answers/'.$newName);
        } else {
            $newName = 'NULL';
            $uploadStatus = false;
        }

        $newName = $newName != 'NULL' ? "'$newName'" : 'NULL';
        $query = "UPDATE st_detail SET 
                  answer = $newName
                  WHERE id_test = '$_POST[id_test]'
                  AND id_soal = '$_POST[id_soal]'";

        $result = mysqli_query($connect, $query);

        if($result) {
            http_response_code(201);
            if(!$uploadStatus) {
                echo json_encode(['status' => 1, 'message' => 'speaking test saved but no audio included'], JSON_NUMERIC_CHECK);
            } else {
                echo json_encode(['status' => 1, 'message' => 'speaking test answer saved'], JSON_NUMERIC_CHECK);
            }
        } else {
            http_response_code(500);
            echo json_encode(['status' => 0, 'message' => 'request error, server error'], JSON_NUMERIC_CHECK);
        }
    }

    function read_test() {
        global $connect;
        checkRequestMethod('GET');

        if(isset($_GET['id_student'])) {
            if(!checkOperationToken('student')) {
                tokenErrorMessage();
            }
            $query = "SELECT * FROM speaking_test WHERE id_student = '$_GET[id_student]'";

            if(isset($_GET['date'])) {
                $query .= " AND DATE(datetime) = '$_GET[date]'";
            }
        } else if(count($_GET) == 1 || count($_GET) == 2) {
            if(!checkOperationToken('teacher')) {
                tokenErrorMessage();
            }
            $query = "SELECT speaking_test.*, student.name AS student_name 
                    FROM speaking_test
                    INNER JOIN student ON speaking_test.id_student = student.id_student";

            if(isset($_GET['date'])) {
                $query .= " WHERE DATE(speaking_test.datetime) = '$_GET[date]'";
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 0, 'message' => 'invalid parameter(s)'], JSON_NUMERIC_CHECK);
            die();
        }

        $result = mysqli_query($connect, $query);

        $rows = [];
        while($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        if(count($rows) != 0) {
            foreach($rows as $row => $data) {
                $totalScore = mysqli_query($connect, "SELECT SUM(score) AS total_score FROM st_detail WHERE id_test='$data[id_test]'");
                $totalScore = mysqli_fetch_row($totalScore)[0];
                $rows[$row]['total_score'] = $totalScore;
            }
        }

        http_response_code(200);
        echo json_encode(['status' => 1, 'message' => 'success get data', 'data' => $rows], JSON_NUMERIC_CHECK);
    }


    // Add difficulty detail 
    function test_detail() {
        global $connect;
        checkRequestMethod('GET');

        if(!checkOperationToken('teacher') && !checkOperationToken('student')) {
            tokenErrorMessage();
        }
        // Query the difficulty
        if(isset($_GET['id_test'])) {
            $query = "SELECT st_detail.id_std, st_detail.id_test, st_detail.question, st_detail.difficulty, st_detail.answer, st_detail.score 
                FROM st_detail
                INNER JOIN soal ON st_detail.question = soal.question
                INNER JOIN soal ON st_detail.difficulty = soal.difficulty
                WHERE id_test = '$_GET[id_test]'";
        } else {
            http_response_code(400);
            echo json_encode(['status' => 0, 'message' => 'invalid parameter(s)'], JSON_NUMERIC_CHECK);
            die();
        }

        $result = mysqli_query($connect, $query);
        if(!mysqli_num_rows($result)) {
            http_response_code(404);
            echo json_encode(['status' => 0, 'message' => 'test not found'], JSON_NUMERIC_CHECK);
            die();
        }

        $rows = [];
        while($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        $startUrl = 'https://localhost/speakingtest/answers/';
        foreach($rows as $row => $data) {
            $filename = $data['answer'];
            if($filename != null) {
                $rows[$row]['answer'] = $startUrl;
                $rows[$row]['answer'] .= $filename;
            } else {
                $rows[$row]['answer'] = null;
            }
        }

        http_response_code(200);
        echo json_encode(['status' => 1, 'message' => 'get data success', 'data' => $rows], JSON_NUMERIC_CHECK);
    }

    function update_score() {
        global $connect;
        checkRequestHeader();
        checkRequestMethod('PATCH');

        if(!checkOperationToken('teacher')) {
            tokenErrorMessage();
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $requestBody = [
            'id_std' => '',
            'score' => ''
        ];
        $match = array_intersect_key($data, $requestBody);
        checkRequestBody($match, 2);

        $stdData = mysqli_query($connect, "SELECT * FROM st_detail WHERE id_std = '$data[id_std]'");
        if(!mysqli_num_rows($stdData)) {
            http_response_code(404);
            echo json_encode(['status' => 0, 'message' => 'data not found'], JSON_NUMERIC_CHECK);
            die();
        }

        $query = "UPDATE st_detail SET score = '$data[score]' WHERE id_std = '$data[id_std]'";

        $result = mysqli_query($connect, $query);
        if($result) {
            http_response_code(200);
            echo json_encode(['status' => 1, 'message' => 'score updated'], JSON_NUMERIC_CHECK);
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
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if(!isset($data['id_test'])) {
            http_response_code(400);
            echo json_encode(['status' => 0, 'message' => 'invalid request body'], JSON_NUMERIC_CHECK);
            die();
        }

        $testData = mysqli_query($connect, "SELECT * FROM speaking_test WHERE id_test = '$data[id_test]'");
        if(!mysqli_num_rows($testData)) {
            http_response_code(404);
            echo json_encode(['status' => 0, 'message' => 'data not found'], JSON_NUMERIC_CHECK);
            die();
        }

        $deletedAudio = mysqli_query($connect, "SELECT answer FROM st_detail WHERE id_test = '$data[id_test]'");
        $answers = [];
        while($answer = mysqli_fetch_assoc($deletedAudio)) {
            $answers[] = $answer;
        }
        foreach($answers as $file) {
            $filename = $file['answer'];
            if(file_exists('./answers/'.$filename)) {
                unlink('./answers/'.$filename);
            }
        }

        $query1 = "DELETE FROM st_detail WHERE id_test = '$data[id_test]'";
        $query2 = "DELETE FROM speaking_test WHERE id_test = '$data[id_test]'";

        $result1 = mysqli_query($connect, $query1);
        $result2 = mysqli_query($connect, $query2);

        if($result1 && $result2) {
            http_response_code(200);
            echo json_encode(['status' => 1, 'message' => 'test deleted'], JSON_NUMERIC_CHECK);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 0, 'message' => 'request failed, server error'], JSON_NUMERIC_CHECK);
        }
    }