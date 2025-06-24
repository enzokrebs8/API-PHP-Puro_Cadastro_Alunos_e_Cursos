<?php
    function readData() {
        if (!file_exists('data.json')) {
            file_put_contents('data.json', json_encode(['alunos' => [], 'cursos' => []]));
        }
        $data = file_get_contents('data.json');
        if ($data === false) {
            throw new Exception('Erro ao ler arquivo de dados');
        }
        return json_decode($data, true);
    }

    function writeData($data) {
        $result = file_put_contents('data.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        if ($result === false) {
            throw new Exception('Erro ao salvar dados');
        }
    }

    function sendJson($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
?>