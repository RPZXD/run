<?php
header('Content-Type: application/json');
require_once '../app/config/database.php';
require_once '../app/models/Registration.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $cid = isset($data->citizen_id) ? $data->citizen_id : '';

    if (!empty($cid)) {
        $database = new Database();
        $db = $database->connect();
        $registration = new Registration($db);

        if ($registration->checkCitizenId($cid)) {
            echo json_encode(['exists' => true]);
        } else {
            echo json_encode(['exists' => false]);
        }
    } else {
        echo json_encode(['error' => 'No citizen_id provided']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
