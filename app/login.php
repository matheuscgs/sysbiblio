<?php
session_start();

if (isset($_SESSION['user_id'])) {
    // Se o usuário já estiver logado, redirecione para a página protegida
    header("Location: listarlivros.php");
    exit;
}

require_once('../database/conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar se os campos de usuário e senha estão preenchidos
    if (isset($_POST['email']) && isset($_POST['senha'])) {
        // Processar o formulário de login
        $email = $_POST['email'];
        $password = $_POST['senha'];

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email AND senha = :senha");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $password);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Login bem-sucedido, armazenar informações na sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['email'];
            $_SESSION['user_level'] = $user['nivel'];

            // Redirecionar para a página protegida
            header("Location:../view/listarlivros.php");
            exit;
        } else {
            $_SESSION['msg'] = "E-mail ou senha incorretos.";
            // Se ocorrer um erro, redirecione para a página de login com um parâmetro de erro
            header("Location:../index.php");
            exit;
        }
    } else {
        $_SESSION['msg'] = "Por favor, preencha todos os campos.";
        // Se campos estiverem vazios, redirecione para a página de login com um parâmetro de erro
        header("Location:../index.php");
        exit;
    }
}
?>