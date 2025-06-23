# API-PHP-Puro_Cadastro_Alunos_e_Cursos

Requisitos:
- PHP 7.4+
- Permissão de escrita no arquivo data.json

Endpoints disponíveis:

1. ALUNOS:
    GET    - /alunos.php              (Lista todos os alunos)
    GET    - /alunos.php?id=ID        (Busca aluno por ID)
    GET    - /alunos.php?nome=NOME    (Filtra alunos por nome)
    POST   - /alunos.php              (Cria novo aluno)
    PUT    - /alunos.php              (Atualiza aluno existente)
    DELETE - /alunos.php?id=ID        (Remove aluno)

2. CURSOS:
    GET    - /cursos.php              (Lista todos os cursos)
    GET    - /cursos.php?id=ID        (Busca curso por ID)
    GET    - /cursos.php?nome=NOME    (Filtra cursos por nome)
    POST   - /cursos.php              (Cria novo curso)
    PUT    - /cursos.php              (Atualiza curso existente)
    DELETE - /cursos.php?id=ID        (Remove curso)

Exemplos de uso com cURL:

# Criar aluno
curl -X POST http://localhost/api/alunos.php \
-H "Content-Type: application/json" \
-d '{
    "nome": "João Silva",
    "email": "joao@email.com",
    "matricula": "20230001",
    "curso_id": "curso123"
}'

# Atualizar curso
curl -X PUT http://localhost/api/cursos.php \
-H "Content-Type: application/json" \
-d '{
    "id": "curso123",
    "carga_horaria": 80,
    "vagas": 30
}'

# Listar alunos matriculados em um curso
curl -X GET "http://localhost/api/alunos.php?curso_id=curso123"
