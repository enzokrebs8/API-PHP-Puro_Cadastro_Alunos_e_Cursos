<?php
    require 'utils.php';

    $data = readData();
    $method = $_SERVER['REQUEST_METHOD'];
    $id = $_GET['id'] ?? null;

    switch ($method) {
        case 'GET':
            if ($id) {
                foreach ($data['cursos'] as $curso) {
                    if ($curso['id'] == $id) {
                        sendJson($curso);
                    }
                }
                sendJson(['error' => 'Curso não encontrado'], 404);
            } else {
                sendJson($data['cursos']);
            }

        case 'POST':
            $body = json_decode(file_get_contents('php://input'), true);
            if (!$body) {
                sendJson(['error' => 'Dados inválidos'], 400);
            }

            $body['id'] = uniqid();
            $data['cursos'][] = $body;
            writeData($data);
            sendJson($body, 201);

        case 'PUT':
            $body = json_decode(file_get_contents('php://input'), true);
            if (!$body || !isset($body['id'])) {
                sendJson(['error' => 'ID não informado no corpo da requisição'], 400);
            }

            foreach ($data['cursos'] as &$curso) {
                if ($curso['id'] == $body['id']) {
                    $curso = array_merge($curso, $body);
                    writeData($data);
                    sendJson($curso);
                }
            }

            sendJson(['error' => 'Curso não encontrado'], 404);

        case 'DELETE':
            if (!$id) {
                sendJson(['error' => 'ID não informado'], 400);
            }

            foreach ($data['cursos'] as $index => $curso) {
                if ($curso['id'] == $id) {
                    array_splice($data['cursos'], $index, 1);
                    writeData($data);
                    sendJson(['success' => 'Curso removido']);
                }
            }

            sendJson(['error' => 'Curso não encontrado'], 404);

        default:
            sendJson(['error' => 'Método não permitido'], 405);
    }
?>