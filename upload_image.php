<?php
require_once "connection.php";
require_once "utils.php";
header('Content-type: application/json');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the file was sent without errors
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/'; // Set your upload directory
        $uploadFile = $uploadDir . basename($_FILES['image']['name']);

        // Move the uploaded file to the desired location
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $response = [
                'status' => 1,
                'message' => 'Image uploaded successfully',
                'imageUrl' => $uploadFile,
            ];
        } else {
            $response = [
                'status' => 0,
                'message' => 'Failed to move uploaded file',
            ];
        }
    } else {
        $response = [
            'status' => 0,
            'message' => 'Error uploading file',
        ];
    }
} else {
    $response = [
        'status' => 0,
        'message' => 'Invalid request method',
    ];
}

echo json_encode($response);

?>