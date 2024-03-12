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
    // Se o nível do usuário não for administrador, redirecione para uma mensagem de erro
    header("Location: ../view/permissao.php");
}

require_once('../database/conexao.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, telefone, senha, nivel) 
                              VALUES (:nome, :email, :telefone, :senha, :nivel)");

        $params = array(
            ':nome' => $_POST['nome'],
            ':email' => $_POST['email'],
            ':telefone' => $_POST['telefone'],
            ':senha' => $_POST['senha'],
            ':nivel' => $_POST['nivel'],
        );

        $stmt->execute($params);
        $_SESSION['msg'] = "Usuário cadastrado com sucesso!";
        header("Location: ../view/cadastrar_usuario.php");
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
    <title>Cadastrar Usuário</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<?php include('../componentes/menu_superior.php'); ?>

<div class="container mt-4">
    <h2><i class="fas fa-user-plus"></i> Cadastrar Usuário</h2>
    <?php
    // Exibir caixa de sucesso
    if (isset($_SESSION['msg'])) {
        echo '<div class="alert alert-success" role="alert">' . $_SESSION['msg'] . '</div>';
        unset($_SESSION['msg']);
    }
    ?>
    <br>
    <!-- Formulário para cadastrar usuários -->
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div class="form-group">
            <label for="nome"><i class="fas fa-user"></i> Nome:</label>
            <input type="text" class="form-control" name="nome" required>
        </div>
        <div class="form-group">
            <label for="email"><i class="fas fa-envelope"></i> E-mail:</label>
            <input type="email" class="form-control" name="email" required>
        </div>
        <div class="form-group">
            <label for="telefone"><i class="fas fa-phone"></i> Telefone:</label>
            <input type="tel" name="telefone" class="form-control" id="telefone" placeholder="(11) 11111-1111" required>
        </div>
        <div class="form-group">
            <label for="senha"><i class="fas fa-lock"></i> Senha:</label>
            <input type="password" class="form-control" name="senha" required>
        </div>
        <div class="form-group">
            <label for="nivel"><i class="fas fa-user-shield"></i> Nível:</label>
            <select class="form-control" name="nivel" required>
                <option value="leitor">Leitor</option>
                <option value="administrador">Administrador</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus"></i> Cadastrar Usuário</button>
    </form>
</div>
</br></br></br>
<?php include('../componentes/rodape.php'); ?>
<script>
      $(document).ready(function() {
      $('#telefone').mask('(00) 00000-0000');
      });
    </script>
</body>
</html>