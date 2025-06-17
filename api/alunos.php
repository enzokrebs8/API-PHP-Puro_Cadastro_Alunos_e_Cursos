<?php
    require 'utils.php';

    $data = readData();
    $method = $_SERVER['REQUEST_METHOD'];
    $id = $_GET['id'] ?? null;

    switch ($method) {
        case 'GET':
            if ($id) {
                foreach ($data['alunos'] as $aluno) {
                    if ($aluno['id'] == $id) {
                        sendJson($aluno);
                    }
                }
                sendJson(['error' => 'Aluno não encontrado'], 404);
            } else {
                sendJson($data['alunos']);
            }

        case 'POST':
            $body = json_decode(file_get_contents('php://input'), true);
            if (!$body) {
                sendJson(['error' => 'Dados inválidos'], 400);
            }

            $body['id'] = uniqid();
            $data['alunos'][] = $body;
            writeData($data);
            sendJson($body, 201);

        case 'PUT':
            $body = json_decode(file_get_contents('php://input'), true);
            if (!$body || !isset($body['id'])) {
                sendJson(['error' => 'ID não informado no corpo da requisição'], 400);
            }

            foreach ($data['alunos'] as &$aluno) {
                if ($aluno['id'] == $body['id']) {
                    $aluno = array_merge($aluno, $body);
                    writeData($data);
                    sendJson($aluno);
                }
            }

            sendJson(['error' => 'Aluno não encontrado'], 404);

        case 'DELETE':
            if (!$id) {
                sendJson(['error' => 'ID não informado'], 400);
            }

            foreach ($data['alunos'] as $index => $aluno) {
                if ($aluno['id'] == $id) {
                    array_splice($data['alunos'], $index, 1);
                    writeData($data);
                    sendJson(['success' => 'Aluno removido']);
                }
            }

            sendJson(['error' => 'Aluno não encontrado'], 404);

        default:
            sendJson(['error' => 'Método não permitido'], 405);
    }
?>
