<?php
    function readData() {
        return json_decode(file_get_contents('data.json'), true);
    }

    function writeData($data) {
        file_put_contents('data.json', json_encode($data, JSON_PRETTY_PRINT));
    }

    function sendJson($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
?>
