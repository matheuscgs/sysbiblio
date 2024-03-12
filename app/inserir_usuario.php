<?php

require_once('../database/conexao.php');

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Verifica se a conexão PDO está disponível
    if (isset($pdo)) {

        // Coleta os dados do formulário com verificações
        $nome = isset($_POST['nome']) ? $_POST['nome'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $telefone = isset($_POST['telefone']) ? $_POST['telefone'] : '';
        $senha = isset($_POST['senha']) ? $_POST['senha'] : ''; // Recomendado armazenar senhas usando password_hash

        // Verifica se as variáveis foram definidas antes de prosseguir
        if ($nome !== '' && $email !== '' && $telefone !== '' && $senha !== '') {

        

            // Prepara a instrução SQL de inserção usando um prepared statement
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, telefone, senha, nivel) VALUES (?, ?, ?, ?, 'leitor')");
            $stmt->bindParam(1, $nome);
            $stmt->bindParam(2, $email);
            $stmt->bindParam(3, $telefone);
            $stmt->bindParam(4, $senha);

            // Executa a instrução SQL
            if ($stmt->execute()) {
                $_SESSION['msg'] = "Cadastro realizado com sucesso!";
                header("Location:../view/cadastro.php");
                exit;
            } else {
                $_SESSION['msg'] = "Preencha todos campos corretamente.";
                header("Location:../view/cadastro.php");
                exit;
            }

            // Fecha o statement
            $stmt = null;
        } else {
            echo "Erro: Todos os campos devem ser preenchidos.";
        }
    } else {
        echo "Erro: Conexão PDO não disponível.";
    }
}
?>