<?php
    function handleCursos($method, $id, $query, &$data) {
        switch ($method) {
            case 'GET':
                if ($id) {
                    // Busca por ID (exata)
                    $cursoEncontrado = null;
                    foreach ($data['cursos'] as $curso) {
                        if ($curso['id'] === $id) {
                            $cursoEncontrado = $curso;
                            break;
                        }
                    }
                    
                    if ($cursoEncontrado) {
                        sendJson($cursoEncontrado);
                    } else {
                        sendJson(['error' => 'Curso não encontrado'], 404);
                    }
                } elseif (isset($query['nome'])) {
                    // Busca parcial por nome (case-insensitive)
                    $termoBusca = strtolower(trim($query['nome']));
                    $result = array_filter($data['cursos'], function($curso) use ($termoBusca) {
                        return strpos(strtolower($curso['nome']), $termoBusca) !== false;
                    });
                    
                    if (empty($result)) {
                        sendJson(['error' => 'Nenhum curso encontrado com este termo'], 404);
                    }
                    
                    sendJson(array_values($result));
                } else {
                    // Lista todos
                    sendJson($data['cursos']);
                }
                break;

            case 'POST':
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!$input) {
                    sendJson(['error' => 'Dados inválidos'], 400);
                }
                
                if (empty($input['nome'])) {
                    sendJson(['error' => 'Nome do curso é obrigatório'], 400);
                }
                
                $curso = [
                    'id' => uniqid(),
                    'nome' => trim($input['nome']),
                    'descricao' => isset($input['descricao']) ? trim($input['descricao']) : '',
                    'carga_horaria' => $input['carga_horaria'] ?? 0,
                    'vagas' => $input['vagas'] ?? 0,
                    'data_criacao' => date('Y-m-d H:i:s')
                ];
                
                $data['cursos'][] = $curso;
                writeData($data);
                sendJson(['success' => 'Curso criado', 'data' => $curso], 201);
                break;

            case 'PUT':
                // Obter dados da requisição
                $input = json_decode(file_get_contents('php://input'), true);
                
                // Validação básica dos dados
                if (!$input) {
                    sendJson(['error' => 'Dados inválidos ou corpo da requisição vazio', 
                            'details' => 'O corpo da requisição deve conter um JSON válido'], 400);
                }
                
                // Verifica se o ID foi fornecido no corpo
                if (!isset($input['id'])) {
                    sendJson(['error' => 'Identificação do curso necessária',
                            'details' => 'O campo "id" é obrigatório no corpo da requisição',
                            'required_fields' => ['id' => 'string']], 400);
                }
                
                $id = $input['id'];
                
                // Validação do formato do ID
                if (!preg_match('/^[a-f0-9]{13}$/', $id)) {
                    sendJson(['error' => 'Formato de ID inválido',
                            'details' => 'O ID deve conter 13 caracteres alfanuméricos',
                            'example' => '6859d3dc41b3d'], 400);
                }
                
                // Busca o curso
                $cursoEncontrado = false;
                foreach ($data['cursos'] as &$curso) {
                    if ($curso['id'] === $id) {
                        $cursoEncontrado = true;
                        
                        // Validação do nome
                        if (isset($input['nome'])) {
                            $nome = trim($input['nome']);
                            if (empty($nome)) {
                                sendJson(['error' => 'Nome inválido',
                                        'details' => 'O nome não pode estar vazio'], 400);
                            }
                            $curso['nome'] = $nome;
                        }
                        
                        // Atualização da descrição
                        if (isset($input['descricao'])) {
                            $curso['descricao'] = trim($input['descricao']);
                        }
                        
                        // Validação da carga horária
                        if (isset($input['carga_horaria'])) {
                            if (!is_numeric($input['carga_horaria']) || $input['carga_horaria'] <= 0) {
                                sendJson(['error' => 'Carga horária inválida',
                                        'details' => 'A carga horária deve ser um número positivo',
                                        'received' => $input['carga_horaria']], 400);
                            }
                            $curso['carga_horaria'] = (int)$input['carga_horaria'];
                        }
                        
                        // Validação das vagas
                        if (isset($input['vagas'])) {
                            if (!is_numeric($input['vagas']) || $input['vagas'] < 0) {
                                sendJson(['error' => 'Vagas inválidas',
                                        'details' => 'O número de vagas deve ser zero ou positivo',
                                        'received' => $input['vagas']], 400);
                            }
                            $curso['vagas'] = (int)$input['vagas'];
                        }
                        
                        // Registro da data de atualização
                        $curso['data_atualizacao'] = date('Y-m-d H:i:s');
                        
                        // Salva as alterações
                        writeData($data);
                        
                        // Resposta de sucesso
                        sendJson([
                            'success' => 'Curso atualizado com sucesso',
                            'data' => $curso,
                            'updated_fields' => array_keys($input),
                            'timestamp' => $curso['data_atualizacao']
                        ]);
                    }
                }
                
                // Se não encontrou o curso
                if (!$cursoEncontrado) {
                    sendJson(['error' => 'Curso não encontrado',
                            'details' => 'Nenhum curso encontrado com o ID fornecido',
                            'received_id' => $id,
                            'available_ids' => array_map(function($c) { return $c['id']; }, $data['cursos'])], 404);
                }
                break;

            case 'DELETE':
                if (!$id) {
                    sendJson(['error' => 'ID não informado'], 400);
                }
                
                // Verifica se há alunos matriculados
                $alunosMatriculados = array_filter($data['alunos'], function($aluno) use ($id) {
                    return $aluno['curso_id'] === $id;
                });
                
                if (count($alunosMatriculados) > 0) {
                    sendJson(['error' => 'Não é possível excluir curso com alunos matriculados'], 400);
                }
                
                foreach ($data['cursos'] as $index => $curso) {
                    if ($curso['id'] === $id) {
                        array_splice($data['cursos'], $index, 1);
                        writeData($data);
                        sendJson(['success' => 'Curso removido com sucesso']);
                    }
                }
                
                sendJson(['error' => 'Curso não encontrado'], 404);
                break;

            default:
                sendJson(['error' => 'Método não permitido'], 405);
        }
    }
?>