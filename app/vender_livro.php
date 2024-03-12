<?php
session_start();

// Verifique se o método de requisição é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifique se os dados necessários foram enviados
    if (isset($_POST['venderLivro']) && $_POST['venderLivro'] == 'true' && isset($_POST['livroId']) && isset($_POST['valorVenda'])) {
        // Capturar os dados do formulário
        $livroId = $_POST['livroId'];
        $valorVenda = $_POST['valorVenda'];
        $dataVenda = date("Y-m-d H:i:s"); // Captura a data atual

        // Conecte-se ao seu banco de dados (certifique-se de incluir seu arquivo de conexão)
        require_once('../database/conexao.php');

        // Atualize o campo 'venda' na tabela de livros para indicar que o livro foi vendido
        // Insira os detalhes da venda na tabela de vendas
        $sql = "UPDATE livros SET venda = 1 WHERE id = :livroId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':livroId', $livroId);
        $stmt->execute();

        // Insira os detalhes da venda na tabela de vendas
        $sql = "INSERT INTO vendas (id_livro, valor, data_cadastro) VALUES (:livroId, :valorVenda, :dataVenda)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':livroId', $livroId);
        $stmt->bindParam(':valorVenda', $valorVenda);
        $stmt->bindParam(':dataVenda', $dataVenda);
        $stmt->execute();

        // Feche a conexão com o banco de dados
        $pdo = null;

        // Envie uma mensagem de sucesso de volta para o JavaScript
        echo "Livro vendido com sucesso!";
        exit;
    } else {
        // Se os dados necessários não foram enviados, envie uma mensagem de erro
        echo "Erro: Dados ausentes ou inválidos.";
        exit;
    }
} else {
    // Se a requisição não for do tipo POST, envie uma mensagem de erro
    echo "Erro: Método de requisição inválido.";
    exit;
}
?>