<?php
session_start();

// Verifique se o método da requisição é POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtém os dados do formulário
    $livroId = $_POST['livro_id'];
    $idUsuario = $_POST['id_usuario'];

    // Sua lógica de conexão ao banco de dados
    require_once('../database/conexao.php');

    // Verifica se o ID do usuário existe na tabela usuarios
    $sqlVerificaUsuario = "SELECT id FROM usuarios WHERE id = :id_usuario";
    $stmtVerificaUsuario = $pdo->prepare($sqlVerificaUsuario);
    $stmtVerificaUsuario->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
    $stmtVerificaUsuario->execute();

    if ($stmtVerificaUsuario->rowCount() === 0) {
        // O ID do usuário não existe, envie uma resposta JSON com erro
        echo json_encode(['status' => 'error', 'message' => 'ID do usuário não encontrado.']);
        exit;
    }

    // Insira os dados na tabela emprestimos e atualize o campo emprestado na tabela livros
    $dataEmprestimo = date('Y-m-d');
    $dataDevolucao = date('Y-m-d', strtotime('+7 days'));

    try {
        $pdo->beginTransaction();
        
        // Atualize o campo emprestado na tabela livros
        $sqlAtualizarLivro = "UPDATE livros SET emprestado = 1 WHERE id = :livro_id";
        $stmtAtualizarLivro = $pdo->prepare($sqlAtualizarLivro);
        $stmtAtualizarLivro->bindParam(':livro_id', $livroId, PDO::PARAM_INT);
        $stmtAtualizarLivro->execute();
       
        // Insira o empréstimo na tabela emprestimos
        $sqlInserirEmprestimo = "INSERT INTO emprestimos (id_livro, id_usuario, data_emprestimo, data_devolucao) 
                                VALUES (:livro_id, :id_usuario, :data_emprestimo, :data_devolucao)";
        $stmtInserirEmprestimo = $pdo->prepare($sqlInserirEmprestimo);
        $stmtInserirEmprestimo->bindParam(':livro_id', $livroId, PDO::PARAM_INT);
        $stmtInserirEmprestimo->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmtInserirEmprestimo->bindParam(':data_emprestimo', $dataEmprestimo);
        $stmtInserirEmprestimo->bindParam(':data_devolucao', $dataDevolucao);
        $stmtInserirEmprestimo->execute();

        $pdo->commit();

        // Envie uma resposta JSON com sucesso
        echo json_encode(['status' => 'success', 'message' => 'Empréstimo realizado com sucesso.']);
    } catch (Exception $e) {
        // Em caso de erro, reverta a transação e envie uma resposta JSON com erro
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Erro ao realizar o empréstimo.']);
    }

    // Feche a conexão com o banco de dados
    $pdo = null;
} else {
    // Se a requisição não for do tipo POST, envie uma resposta JSON com erro
    echo json_encode(['status' => 'error', 'message' => 'Método de requisição inválido.']);
}
?>
