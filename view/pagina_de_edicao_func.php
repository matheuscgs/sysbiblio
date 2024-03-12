<?php

session_start();

// Verifique se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se o usuário não estiver logado, redirecione para a página de login
    $_SESSION['msg'] = "Usuário não logado";
    header("Location: ../index.php");
    exit;
}

// Verifique se o nível do usuário é administrador (ou o nível desejado para acessar a página)
if ($_SESSION['user_level'] != 'administrador') {
    // Se o nível do usuário não for administrador, redirecione para outra página ou exiba uma mensagem de erro
    header("Location: ../view/permissao.php");
}

// Recupera o ID do livro da URL
if (isset($_GET['id'])) {
    $funcionarioId = $_GET['id'];

    // Aqui, você pode consultar o banco de dados para obter as informações do livro com base no ID
    // e preencher os campos do formulário
    require_once('../database/conexao.php');

    // Consulta para obter os dados do livro pelo ID
    $sql = "SELECT * FROM funcionarios WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $funcionarioId, PDO::PARAM_INT);
    $stmt->execute();

    if ($funcionario = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Os dados do livro foram obtidos com sucesso
    } else {
        // Se o livro não for encontrado, redireciona para a página de listar livros
        header('Location: editar_funcionarios.php');
        exit();
    }

    // Fecha a conexão com o banco de dados após obter as informações do funcionario
    $pdo = null;

} else {
    // Se o ID não estiver presente na URL, redireciona para a página de listar funcionarios
    header('Location: editar_funcionarios.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Atualizar Funcionário</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="../src/jquery.mask.min.js"></script>
</head>
<body>

    <?php include('../componentes/menu_superior.php'); ?>

    <div class="container mt-4">
        <h2><i class="fas fa-user-edit"></i> Atualizar Funcionário</h2>
        </br></br>
        <?php
        // Exibir mensagem de sucesso
        if (isset($_SESSION['msg'])) {
            echo '<div class="alert alert-success" role="alert">' . $_SESSION['msg'] . '</div>';
            unset($_SESSION['msg']);
        }
        ?>

        <!-- Formulário para editar funcionário -->
        <form method="post" action="../app/atualizar_funcionario.php">
            <!-- Campos do formulário preenchidos com os dados do funcionário -->
            <div class="form-row">
                <input type="hidden" name="funcionario_id" value="<?php echo $funcionario['id']; ?>">
                <div class="form-group col-md-6">
                    <label for="nome"><i class="fas fa-user"></i> Nome:</label>
                    <input type="text" class="form-control" name="nome" value="<?php echo $funcionario['nome']; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="cpf"><i class="fas fa-id-card"></i> CPF:</label>
                    <input type="text" class="form-control" name="cpf" id="cpf" value="<?php echo $funcionario['cpf']; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="telefone"><i class="fas fa-phone"></i> Telefone:</label>
                    <input type="text" class="form-control" name="telefone" id="telefone" value="<?php echo $funcionario['telefone']; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="cargo"><i class="fas fa-user-tag"></i> Cargo:</label>
                    <input type="text" class="form-control" name="cargo" value="<?php echo $funcionario['cargo']; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="admissao"><i class="fas fa-calendar-alt"></i> Admissão:</label>
                    <input type="text" class="form-control" name="admissao" id="admissao" value="<?php echo $funcionario['admissao']; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="licenca"><i class="fas fa-check-circle"></i> Licença:</label>
                    <select class="form-control" name="licenca" required>
                        <option value="1" <?php if ($funcionario['licenca'] == 1) echo 'selected'; ?>>Sim</option>
                        <option value="0" <?php if ($funcionario['licenca'] == 0) echo 'selected'; ?>>Não</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Atualizar Funcionário</button>
    </br></br></br>
        </form>
    </div>

    <?php include('../componentes/rodape.php'); ?>

    <script>
    $(document).ready(function() {
        $('#telefone').mask('(00)00000-0000');
        $('#admissao').mask('0000-00-00');
        $('#cpf').mask('000.000.000-00');
    });
</script>
</body>
</html>