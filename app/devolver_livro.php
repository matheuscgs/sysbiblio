<?php
session_start();
require_once('../database/conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém o ID do livro a ser devolvido a partir da requisição POST
    $livroId = isset($_POST['livro_id']) ? $_POST['livro_id'] : null;

    // Verifica se o ID do livro é válido
    if ($livroId !== null) {
        // Atualiza o campo emprestado para 0 na tabela livros
        $updateSql = "UPDATE livros SET emprestado = 0 WHERE id = :livro_id";
        $updateStatement = $pdo->prepare($updateSql);
        $updateStatement->bindParam(':livro_id', $livroId, PDO::PARAM_INT);

        if ($updateStatement->execute()) {
            // Atualização bem-sucedida
            $response = ['status' => 'success'];
            echo json_encode($response);
            exit;
        } else {
            // Erro na atualização
            $response = ['status' => 'error'];
            echo json_encode($response);
            exit;
        }
    }
}

// Se a requisição não for POST ou se o ID do livro não for fornecido, retorna um erro
$response = ['status' => 'error'];
echo json_encode($response);
exit;
?>