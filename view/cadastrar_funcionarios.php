<?php
session_start();

// Verifique se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se o usuário não estiver logado, redirecione para a página de login
    $_SESSION['msg'] = "Usuário não logado";
    header("Location: ../index.php");
    exit;
}

// Verifique se o nível do usuário é administrador
if ($_SESSION['user_level'] != 'administrador') {
    // Se o nível do usuário não for administrador, redirecione para página de erro
    header("Location: ../view/permissao.php");
}

require_once('../database/conexao.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $stmt = $pdo->prepare("INSERT INTO funcionarios (nome, cpf, telefone, cargo, admissao, licenca) 
                              VALUES (:nome, :cpf, :telefone, :cargo, :admissao, :licenca)");

        $params = array(
            ':nome' => $_POST['nome'],
            ':cpf' => $_POST['cpf'],
            ':telefone' => $_POST['telefone'],
            ':cargo' => $_POST['cargo'],
            ':admissao' => $_POST['admissao'],
            ':licenca' => $_POST['licenca'],
        );

        $stmt->execute($params);
        $_SESSION['msg'] = "Funcionário cadastrado com sucesso!";
        header("Location: ../view/cadastrar_funcionarios.php");
        exit();
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Cadastrar Funcionários</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../src/jquery.mask.min.js"></script>
</head>
<body>

<?php include('../componentes/menu_superior.php'); ?>

<div class="container mt-4">
    <h2><i class="fas fa-user-plus"></i> Cadastrar Funcionários</h2>
    <?php
    // Exibir caixa de sucesso
    if (isset($_SESSION['msg'])) {
        echo '<div class="alert alert-success" role="alert">' . $_SESSION['msg'] . '</div>';
        unset($_SESSION['msg']);
    }
    ?>
    <br>
    <!-- Formulário para cadastrar funcionários -->
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nome"><i class="fas fa-user"></i> Nome:</label>
                    <input type="text" class="form-control" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="cpf"><i class="fas fa-id-card"></i> CPF:</label>
                    <input type="text" class="form-control" name="cpf" id="cpf" required>
                </div>
                <div class="form-group">
                    <label for="telefone"><i class="fas fa-phone"></i> Telefone:</label>
                    <input type="text" class="form-control" name="telefone" id="telefone" placeholder="(11)11111-1111" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="cargo"><i class="fas fa-briefcase"></i> Cargo:</label>
                    <input type="text" class="form-control" name="cargo" required>
                </div>
                <div class="form-group">
                    <label for="admissao"><i class="fas fa-calendar-alt"></i> Admissão:</label>
                    <input type="text" class="form-control" name="admissao" id="admissao" placeholder="AAAA-MM-DD" required>
                </div>
                <div class="form-group">
                    <label for="licenca"><i class="fas fa-user-clock"></i> Licença:</label>
                    <select class="form-control" name="licenca" required>
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                    </select>
                </div>
            </div>
        </div>
        <br><br>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cadastrar Funcionário</button>
        <br><br><br>
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