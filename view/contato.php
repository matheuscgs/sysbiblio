<?php
session_start();
require_once('../database/conexao.php');

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['msg'] = "Usuário não logado";
    header("Location: ../index.php");
    exit;
}

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coletar e validar os dados do formulário
    $mensagem = validarEntrada($_POST['mensagem']);

    // Recuperar informações do usuário da tabela 'usuarios'
    $sql_usuario = "SELECT nome, email, telefone FROM usuarios WHERE id = :idUsuario";
    $stmt_usuario = $pdo->prepare($sql_usuario);
    $stmt_usuario->bindParam(':idUsuario', $_SESSION['user_id']);
    $stmt_usuario->execute();
    $usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

    // Verificar se o usuário foi encontrado na tabela 'usuarios'
    if ($usuario) {
        // Inserir os dados na tabela 'contato'
        $sql_contato = "INSERT INTO contato (nome, email, telefone, mensagem) VALUES (:nome, :email, :telefone, :mensagem)";
        $stmt_contato = $pdo->prepare($sql_contato);
        $stmt_contato->bindParam(':nome', $usuario['nome']);
        $stmt_contato->bindParam(':email', $usuario['email']);
        $stmt_contato->bindParam(':telefone', $usuario['telefone']);
        $stmt_contato->bindParam(':mensagem', $mensagem);

        // Executar a consulta
        if ($stmt_contato->execute()) {
            $_SESSION['success_msg'] = "Mensagem enviada com sucesso.";
        } else {
            $_SESSION['error_msg'] = "Erro ao enviar a mensagem.";
        }
    } else {
        $_SESSION['error_msg'] = "Usuário não encontrado.";
    }

    // Redirecionar de volta para a página de contato
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Função para validar entrada de dados
function validarEntrada($dados) {
    $dados = trim($dados);
    $dados = stripslashes($dados);
    $dados = htmlspecialchars($dados);
    return $dados;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/styles.css">
    <title>Enviar Mensagem de Contato</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include('../componentes/menu_superior.php'); ?>

    <div class="container mt-4">
        <h2><i class="fas fa-envelope"></i> Enviar Mensagem de Contato</h2>
        <?php
            // Exibir mensagem de sucesso ou erro
            if (isset($_SESSION['success_msg'])) {
                echo '<div class="alert alert-success" role="alert">' . $_SESSION['success_msg'] . '</div>';
                unset($_SESSION['success_msg']);
            } elseif (isset($_SESSION['error_msg'])) {
                echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error_msg'] . '</div>';
                unset($_SESSION['error_msg']);
            }
        ?>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="form-group">
                <label for="mensagem"><i class="fas fa-comment"></i> Mensagem (máximo 500 caracteres):</label>
                <textarea class="form-control" id="mensagem" name="mensagem" rows="5" maxlength="500" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Enviar</button>
        </form>
    </div>

    <?php include('../componentes/rodape.php'); ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>