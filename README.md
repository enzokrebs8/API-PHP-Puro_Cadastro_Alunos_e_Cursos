# 📚 API PHP Puro: Alunos e Cursos

API RESTful desenvolvida em PHP puro (sem frameworks), com funcionalidades completas de cadastro, consulta, atualização e remoção de **alunos** e **cursos**, utilizando persistência em arquivo JSON.

---

## 🚀 Instalação e Execução

### Pré-requisitos

- PHP 7+ instalado
- Servidor Apache (recomendado: [XAMPP](https://www.apachefriends.org/index.html) ou [WAMP](https://www.wampserver.com/))
- Postman (opcional, mas recomendado para testes)

### Passo a passo

1. Instale o Git em: [git-scm.com](https://git-scm.com/downloads)
2. Clone o repositório:

```bash
cd /c/xampp/htdocs/ # ou /c/wamp64/www/
git clone https://github.com/enzokrebs8/API-PHP-Puro_Cadastro_Alunos_e_Cursos.git
cd API-PHP-Puro_Cadastro_Alunos_e_Cursos
```

3. Inicie o Apache no XAMPP/WAMP
4. Acesse no navegador:

```
http://localhost/API-PHP-Puro_Cadastro_Alunos_e_Cursos/api/api.php/cursos
```

---

## 🧱 Estrutura do Projeto

```
/api
├── api.php         # Roteador principal
├── alunos.php      # Lógica da entidade Aluno
├── cursos.php      # Lógica da entidade Curso
├── utils.php       # Funções auxiliares
└── data.json       # Banco de dados em formato JSON
```

---

## 🧪 Testes com Postman

1. Baixe o [Postman](https://www.postman.com/downloads/)
2. Importe a coleção:
   👉 [Link para coleção Postman](https://documenter.getpostman.com/view/34111277/2sB2xCh9ef)
3. Configure o ambiente com a URL base:

```
http://localhost/API-PHP-Puro_Cadastro_Alunos_e_Cursos/api/api.php
```

---

## 🔌 Endpoints Disponíveis

### 📁 Alunos

| Método | Rota                | Ação                        |
|--------|---------------------|-----------------------------|
| GET    | `/alunos`           | Listar todos os alunos      |
| GET    | `/alunos?id={id}`   | Buscar aluno por ID         |
| GET    | `/alunos?nome={}`   | Buscar aluno por nome       |
| POST   | `/alunos`           | Criar novo aluno            |
| PUT    | `/alunos`           | Atualizar aluno existente   |
| DELETE | `/alunos?id={id}`   | Remover aluno               |

### 📁 Cursos

| Método | Rota                 | Ação                        |
|--------|----------------------|-----------------------------|
| GET    | `/cursos`            | Listar todos os cursos      |
| GET    | `/cursos?id={id}`    | Buscar curso por ID         |
| GET    | `/cursos?nome={}`    | Buscar curso por nome       |
| POST   | `/cursos`            | Criar novo curso            |
| PUT    | `/cursos`            | Atualizar curso existente   |
| DELETE | `/cursos?id={id}`    | Remover curso               |

---

## ✅ Validações

### Alunos
- `nome`, `email` e `curso_id` são obrigatórios
- `email` deve estar em formato válido
- `curso_id` deve existir
- `id` é uma string de 13 caracteres

### Cursos
- `nome` é obrigatório
- `carga_horaria` e `vagas` devem ser números positivos
- Um curso não pode ser excluído se houver alunos matriculados

---

## 📦 Formato dos Dados

### Exemplo de Aluno

```json
{
  "id": "6859dce32a02c",
  "nome": "Letícia Almeida",
  "email": "lele2@email.com",
  "matricula": "3453454534",
  "curso_id": "6859d3dc41b3d",
  "data_criacao": "2025-06-23 23:01:55"
}
```

### Exemplo de Curso

```json
{
  "id": "6859d3dc41b3d",
  "nome": "Banco de Dados - MYSQL",
  "descricao": "Curso completo de Banco de Dados em MYSQL",
  "carga_horaria": 80,
  "vagas": 20,
  "data_criacao": "2025-06-23 22:23:24"
}
```

---

## 💾 Banco de Dados

O armazenamento é feito em `api/data.json`:

```json
{
  "alunos": [],
  "cursos": []
}
```

---

## 📡 Códigos de Resposta

| Código | Status                 | Descrição                        |
|--------|------------------------|----------------------------------|
| 200    | OK                     | Requisição bem-sucedida          |
| 201    | Created                | Recurso criado                   |
| 400    | Bad Request            | Requisição mal formada           |
| 404    | Not Found              | Recurso não encontrado           |
| 405    | Method Not Allowed     | Método HTTP não permitido        |
| 500    | Internal Server Error  | Erro interno do servidor         |

---

## 🛠️ Solução de Problemas

| Problema                          | Solução                                                                 |
|----------------------------------|--------------------------------------------------------------------------|
| Erro 500                         | Verifique permissões no diretório e existência de `data.json`           |
| Recurso não encontrado (404)     | Confirme se o Apache está rodando e URLs estão corretas                 |
| Dados não persistem              | Verifique se `data.json` está sendo salvo corretamente                  |

---

## 📚 Recursos Úteis

- [Documentação Oficial do PHP](https://www.php.net/docs.php)
- [Guia do Postman](https://learning.postman.com/docs/introduction/overview/)
- [Instalação XAMPP](https://www.apachefriends.org/)

---

## 👨‍💻 Autor

Enzo Krebs - [github.com/enzokrebs8](https://github.com/enzokrebs8)
