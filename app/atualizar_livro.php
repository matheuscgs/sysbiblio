<?php
session_start();

require_once('../database/conexao.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Certifique-se de que está usando "livro_id" para recuperar o ID do livro
        $livroId = $_POST['livro_id'];

        $stmt = $pdo->prepare("UPDATE livros 
                      SET edicao = :edicao, 
                          editora = :editora, 
                          aquisicao = :aquisicao, 
                          pais = :pais, 
                          estante = :estante, 
                          autor = :autor, 
                          traducao = :traducao, 
                          data = :data, 
                          isbn = :isbn, 
                          ano = :ano, 
                          prateleira = :prateleira, 
                          estado = :estado, 
                          emprestado = :emprestado, 
                          venda = :venda
                      WHERE id = :livro_id");  // Certifique-se de usar "id" para o campo ID

        $params = array(
            ':edicao' => $_POST['edicao'],
            ':editora' => $_POST['editora'],
            ':aquisicao' => $_POST['aquisicao'],
            ':pais' => $_POST['pais'],
            ':estante' => $_POST['estante'],
            ':autor' => $_POST['autor'],
            ':traducao' => $_POST['traducao'],
            ':data' => $_POST['data'],
            ':isbn' => $_POST['isbn'],
            ':ano' => $_POST['ano'],
            ':prateleira' => $_POST['prateleira'],
            ':estado' => $_POST['estado'],
            ':emprestado' => $_POST['emprestado'],
            ':venda' => $_POST['venda'],
            ':livro_id' => $livroId  // Certifique-se de incluir o ID do livro
        );

        $stmt->execute($params);
        $_SESSION['msg'] = "Livro atualizado com sucesso!";
        // Redirecione para a página de edição com o ID do livro
        header("Location: ../view/pagina_de_edicao.php?id=" . $livroId);
        exit();
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}
?>