<?php
session_start();

// Conecte-se ao banco de dados
require_once('../database/conexao.php');

if(isset($_POST['livroId'])) {
    // Sanitize e recupere o ID da venda
    $idVenda = filter_var($_POST['livroId'], FILTER_SANITIZE_NUMBER_INT);

    try {
        // Exclua a venda da tabela de vendas
        $sqlDeleteVenda = "DELETE FROM vendas WHERE id = :idVenda";
        $stmtDeleteVenda = $pdo->prepare($sqlDeleteVenda);
        $stmtDeleteVenda->bindParam(':idVenda', $idVenda, PDO::PARAM_INT);
        $stmtDeleteVenda->execute();

        // Verifica se a exclusão da venda foi bem-sucedida
        if ($stmtDeleteVenda->rowCount() > 0) {
            // Obtém o ID do livro associado à venda
            $sqlGetLivroId = "SELECT id_livro FROM vendas WHERE id = :idVenda";
            $stmtGetLivroId = $pdo->prepare($sqlGetLivroId);
            $stmtGetLivroId->bindParam(':idVenda', $idVenda, PDO::PARAM_INT);
            $stmtGetLivroId->execute();

            $livroIdRow = $stmtGetLivroId->fetch(PDO::FETCH_ASSOC);

            // Verifica se o ID do livro foi encontrado
            if ($livroIdRow) {
                $livroId = $livroIdRow['id_livro'];

                // Exclua a entrada correspondente na tabela de livros
                $sqlDeleteLivro = "DELETE FROM livros WHERE id = :livroId";
                $stmtDeleteLivro = $pdo->prepare($sqlDeleteLivro);
                $stmtDeleteLivro->bindParam(':livroId', $livroId, PDO::PARAM_INT);
                $stmtDeleteLivro->execute();
            } else {
                // Lidar com a situação se o ID do livro não foi encontrado
                throw new Exception("ID do livro não encontrado para a venda ID: $idVenda");
            }

            // Redireciona de volta para a página principal com uma mensagem de sucesso
            $_SESSION['msg'] = "Venda excluída com sucesso.";
            header("Location: ../view/listar_vendas.php");
            exit();
        } else {
            throw new Exception("Falha ao excluir a venda.");
        }
    } catch (Exception $e) {
        // Lidar com exceções e mensagens de erro
        $_SESSION['msg'] = "Erro: " . $e->getMessage();
        header("Location: ../view/listar_vendas.php");
        exit();
    }
} else {
    // Se o ID da venda não foi fornecido, redirecione de volta para a página principal com uma mensagem de erro
    $_SESSION['msg'] = "ID da venda não fornecido.";
    header("Location: ../view/listar_vendas.php");
    exit();
}
?>
