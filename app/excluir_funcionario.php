<?php
session_start();

// Verifica se o ID do livro foi fornecido
if (isset($_POST['funcionario_id'])) {
    // Obtém o ID do livro a ser excluído
    $funcionario_id = $_POST['funcionario_id'];

    // Conecta ao banco de dados (substitua pelos seus detalhes de conexão)
    require_once('../database/conexao.php');

    // Prepara a consulta SQL para excluir o livro
    $sql = "DELETE FROM funcionarios WHERE id = :funcionario_id";
    $stmt = $pdo->prepare($sql);

    // Executa a exclusão
    $stmt->bindParam(':funcionario_id', $funcionario_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        // Retorna uma resposta JSON indicando sucesso
        echo json_encode(['status' => 'success']);
    } else {
        // Retorna uma resposta JSON indicando falha
        echo json_encode(['status' => 'error']);
    }

    // Fecha a conexão com o banco de dados
    $pdo = null;
}
?>