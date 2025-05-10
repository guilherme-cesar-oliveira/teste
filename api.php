<?php
// Configurações do MySQL
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "nome_do_banco"; // Substitua pelo nome correto

// Cabeçalhos comuns
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Rota GET simples de verificação
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(["status" => "ok", "mensagem" => "API funcionando"]);
    exit;
}

// Rota POST para executar query
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conecta ao MySQL
    $conn = new mysqli($host, $usuario, $senha, $banco);
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(["erro" => "Falha na conexão: " . $conn->connect_error]);
        exit;
    }

    // Lê o corpo da requisição
    $input = json_decode(file_get_contents("php://input"), true);
    $sql = isset($input['query']) ? trim($input['query']) : null;

    if (!$sql) {
        http_response_code(400);
        echo json_encode(["erro" => "Nenhuma query fornecida."]);
        exit;
    }

    // Executa a query
    $result = $conn->query($sql);

    if ($result === TRUE) {
        echo json_encode(["sucesso" => true, "mensagem" => "Query executada com sucesso."]);
    } elseif ($result instanceof mysqli_result) {
        $dados = [];
        while ($row = $result->fetch_assoc()) {
            $dados[] = $row;
        }
        echo json_encode(["sucesso" => true, "dados" => $dados]);
    } else {
        http_response_code(400);
        echo json_encode(["erro" => "Erro na query: " . $conn->error]);
    }

    $conn->close();
    exit;
}

// Método não permitido
http_response_code(405);
echo json_encode(["erro" => "Método não permitido. Use GET ou POST."]);
