<?php
    function handleAlunos($method, $id, $query, &$data) {
        switch ($method) {
            case 'GET':
                if ($id) {
                    // Busca por ID (exata) - CORRIGIDO
                    $alunoEncontrado = null;
                    foreach ($data['alunos'] as $aluno) {
                        if ($aluno['id'] === $id) {
                            $alunoEncontrado = $aluno;
                            break;
                        }
                    }
                    
                    if ($alunoEncontrado) {
                        sendJson($alunoEncontrado);
                    } else {
                        sendJson(['error' => 'Aluno não encontrado'], 404);
                    }
                } elseif (isset($query['nome'])) {
                    // Busca parcial por nome (case-insensitive)
                    $termoBusca = strtolower(trim($query['nome']));
                    $result = array_filter($data['alunos'], function($aluno) use ($termoBusca) {
                        return strpos(strtolower($aluno['nome']), $termoBusca) !== false;
                    });
                    
                    if (empty($result)) {
                        sendJson(['error' => 'Nenhum aluno encontrado com este termo'], 404);
                    }
                    
                    sendJson(array_values($result));
                } else {
                    // Lista todos
                    sendJson($data['alunos']);
                }
                break;

            case 'POST':
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!$input) {
                    sendJson(['error' => 'Dados inválidos'], 400);
                }
                
                // Validação
                $required = ['nome', 'email', 'curso_id'];
                foreach ($required as $field) {
                    if (empty($input[$field])) {
                        sendJson(['error' => "Campo obrigatório faltando: $field"], 400);
                    }
                }
                
                if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                    sendJson(['error' => 'Email inválido'], 400);
                }
                
                // Verifica se curso existe
                $cursoExists = false;
                foreach ($data['cursos'] as $curso) {
                    if ($curso['id'] === $input['curso_id']) {
                        $cursoExists = true;
                        break;
                    }
                }
                if (!$cursoExists) {
                    sendJson(['error' => 'Curso não encontrado'], 404);
                }
                
                // Cria aluno
                $aluno = [
                    'id' => uniqid(),
                    'nome' => trim($input['nome']),
                    'email' => trim($input['email']),
                    'matricula' => $input['matricula'] ?? '',
                    'curso_id' => $input['curso_id'],
                    'data_criacao' => date('Y-m-d H:i:s')
                ];
                
                $data['alunos'][] = $aluno;
                writeData($data);
                sendJson(['success' => 'Aluno criado', 'data' => $aluno], 201);
                break;

            case 'PUT':
                // Obter dados da requisição
                $input = json_decode(file_get_contents('php://input'), true);
                
                // Validação básica dos dados
                if (!$input) {
                    sendJson(['error' => 'Dados inválidos ou corpo da requisição vazio', 
                            'details' => 'O corpo da requisição deve conter um JSON válido'], 400);
                }
                
                // Verifica se o ID foi fornecido
                if (!isset($input['id'])) {
                    sendJson(['error' => 'Identificação do aluno necessária',
                            'details' => 'O campo "id" é obrigatório no corpo da requisição',
                            'required_fields' => ['id' => 'string']], 400);
                }
                
                $id = $input['id'];
                
                // Validação do formato do ID (opcional)
                if (!preg_match('/^[a-f0-9]{13}$/', $id)) {
                    sendJson(['error' => 'Formato de ID inválido',
                            'details' => 'O ID deve conter 13 caracteres alfanuméricos',
                            'example' => '6859d7f6cce84'], 400);
                }
                
                // Busca o aluno
                $alunoEncontrado = false;
                foreach ($data['alunos'] as &$aluno) {
                    if ($aluno['id'] === $id) {
                        $alunoEncontrado = true;
                        
                        // Validação de email
                        if (isset($input['email'])) {
                            $email = trim($input['email']);
                            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                sendJson(['error' => 'Email inválido',
                                        'details' => 'O email fornecido não é válido',
                                        'received' => $input['email'],
                                        'example' => 'usuario@dominio.com'], 400);
                            }
                            $aluno['email'] = $email;
                        }
                        
                        // Validação do curso_id
                        if (isset($input['curso_id'])) {
                            $cursoExists = false;
                            foreach ($data['cursos'] as $curso) {
                                if ($curso['id'] === $input['curso_id']) {
                                    $cursoExists = true;
                                    break;
                                }
                            }
                            if (!$cursoExists) {
                                sendJson(['error' => 'Curso não encontrado',
                                        'details' => 'O curso_id fornecido não existe na base de dados',
                                        'received' => $input['curso_id'],
                                        'available_courses' => array_map(function($c) { return $c['id']; }, $data['cursos'])], 404);
                            }
                            $aluno['curso_id'] = $input['curso_id'];
                        }
                        
                        // Atualização do nome (com sanitização)
                        if (isset($input['nome'])) {
                            $aluno['nome'] = trim($input['nome']);
                            if (empty($aluno['nome'])) {
                                sendJson(['error' => 'Nome inválido',
                                        'details' => 'O nome não pode estar vazio'], 400);
                            }
                        }
                        
                        // Atualização da matrícula (se fornecida)
                        if (isset($input['matricula'])) {
                            $aluno['matricula'] = trim($input['matricula']);
                        }
                        
                        // Registro da data de atualização
                        $aluno['data_atualizacao'] = date('Y-m-d H:i:s');
                        
                        // Salva as alterações
                        writeData($data);
                        
                        // Resposta de sucesso
                        sendJson([
                            'success' => 'Aluno atualizado com sucesso',
                            'data' => $aluno,
                            'updated_fields' => array_keys($input),
                            'timestamp' => $aluno['data_atualizacao']
                        ]);
                    }
                }
                
                // Se não encontrou o aluno
                if (!$alunoEncontrado) {
                    sendJson(['error' => 'Aluno não encontrado',
                            'details' => 'Nenhum aluno encontrado com o ID fornecido',
                            'received_id' => $id,
                            'available_ids' => array_map(function($a) { return $a['id']; }, $data['alunos'])], 404);
                }
                break;

            case 'DELETE':
                if (!$id) {
                    sendJson(['error' => 'ID não informado'], 400);
                }
                
                foreach ($data['alunos'] as $index => $aluno) {
                    if ($aluno['id'] === $id) {
                        array_splice($data['alunos'], $index, 1);
                        writeData($data);
                        sendJson(['success' => 'Aluno removido com sucesso']);
                    }
                }
                
                sendJson(['error' => 'Aluno não encontrado'], 404);
                break;

            default:
                sendJson(['error' => 'Método não permitido'], 405);
        }
    }
?>