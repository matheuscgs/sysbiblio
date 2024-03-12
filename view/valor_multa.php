<?php
session_start();
require_once('../database/conexao.php'); // Supondo que você tenha um arquivo para a conexão com o banco de dados

if (!isset($_SESSION['user_id'])) {
    $_SESSION['msg'] = "Usuário não logado";
    header("Location: ../index.php");
    exit;
}

if ($_SESSION['user_level'] != 'administrador') {
    header("Location: ../view/permissao.php");
}

// Recuperar o valor atual da multa diária
$sql = "SELECT valor_multa FROM valor_multa";
$stmt = $pdo->query($sql);
$valorMulta = $stmt->fetchColumn();

// Processar o formulário se for submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novoValorMulta = $_POST['valor_multa'];

    // Atualizar o valor da multa diária na tabela valor_multa
    $sqlUpdate = "UPDATE valor_multa SET valor_multa = :novoValorMulta";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->bindParam(':novoValorMulta', $novoValorMulta);
    $stmtUpdate->execute();

    // Redirecionar com mensagem de sucesso
    $_SESSION['msg'] = "Valor da multa diária atualizado com sucesso.";
    header("Location: ../view/valor_multa.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Atualizar Valor da Multa Diária</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../style/styles.css">
</head>
<body>

    <?php include('../componentes/menu_superior.php'); ?>

    <div class="container mt-4">
        <h2><i class="fas fa-dollar-sign"></i> Atualizar Valor da Multa Diária</h2>
       
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="valor_multa"><i class="fas fa-exclamation-circle"></i> Valor da multa diária:</label>
                <input type="text" class="form-control" name="valor_multa" value="<?php echo $valorMulta; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-sync-alt"></i> Atualizar multa</button>
            <br><br>
        </form>
    </div>

    <?php include('../componentes/rodape.php'); ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Script para exibir mensagem de sucesso em um alert -->
    <script>
        // Verifica se a mensagem de sucesso está presente e exibe um alerta
        $(document).ready(function() {
            var successMessage = "<?php echo isset($_SESSION['msg']) ? $_SESSION['msg'] : ''; ?>";
            if (successMessage !== "") {
                alert(successMessage);
            }
        });
    </script>
</body>
</html>
