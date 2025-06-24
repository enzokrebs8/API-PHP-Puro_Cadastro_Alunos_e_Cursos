<?php
    require 'utils.php';

    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");

    $method = $_SERVER['REQUEST_METHOD'];
    $requestUri = $_SERVER['REQUEST_URI'];

    // Remove query string da URI
    $uri = strtok($requestUri, '?');

    // Extrai as partes do caminho
    $pathParts = explode('/', trim(parse_url($uri, PHP_URL_PATH)));

    // Remove itens vazios e reindexa o array
    $pathParts = array_values(array_filter($pathParts));

    // Encontra a posição de api.php no caminho
    $apiPos = array_search('api.php', $pathParts);

    if ($apiPos !== false) {
        // A entidade está na posição seguinte a api.php
        $entity = $pathParts[$apiPos + 1] ?? '';
        
        // O ID pode estar na próxima posição ou na query string
        $id = $pathParts[$apiPos + 2] ?? null;
    } else {
        $entity = '';
        $id = null;
    }

    // Se não encontrou na URL, verifica na query string
    if (!$id) {
        $id = $_GET['id'] ?? null;
    }

    $query = $_GET;

    try {
        $data = readData();
        
        switch ($entity) {
            case 'alunos':
                require 'alunos.php';
                handleAlunos($method, $id, $query, $data);
                break;
                
            case 'cursos':
                require 'cursos.php';
                handleCursos($method, $id, $query, $data);
                break;
                
            default:
                sendJson(['error' => 'Endpoint não encontrado'], 404);
        }
    } catch (Exception $e) {
        sendJson(['error' => 'Erro interno no servidor: ' . $e->getMessage()], 500);
    }
?>