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
        $stmt = $pdo->prepare("INSERT INTO livros (titulo, edicao, editora, aquisicao, pais, estante, autor, traducao, data, isbn, ano, prateleira, estado) 
                              VALUES (:titulo, :edicao, :editora, :aquisicao, :pais, :estante, :autor, :traducao, :data, :isbn, :ano, :prateleira, :estado)");

        $params = array(
            ':titulo' => $_POST['titulo'],
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
        );

        $stmt->execute($params);
        $_SESSION['msg'] = "Livro cadastrado com sucesso!";
        header("Location: ../view/cadastrar_livros.php");
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
    <title>Cadastrar Livro</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="..\style\styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../src/jquery.mask.min.js"></script>
</head>
<body>

<?php include('../componentes/menu_superior.php'); ?>

<div class="container mt-4">
    <h2><i class="fas fa-book"></i> Cadastrar Livro</h2>
    <?php
    // Exibir caixa de sucesso
    if (isset($_SESSION['msg'])) {
        echo '<div class="alert alert-success" role="alert">' . $_SESSION['msg'] . '</div>';
        unset($_SESSION['msg']);
    }
    ?>
    <br>
    <!-- Formulário para cadastrar livros -->
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="titulo"><i class="fas fa-heading"></i> Título:</label>
                    <input type="text" class="form-control" name="titulo" required>
                </div>
                <div class="form-group">
                    <label for="edicao"><i class="fas fa-pen"></i> Edição:</label>
                    <input type="text" class="form-control" name="edicao" required>
                </div>
                <div class="form-group">
                    <label for="editora"><i class="fas fa-building"></i> Editora:</label>
                    <input type="text" class="form-control" name="editora" required>
                </div>
                <div class="form-group">
                    <label for="aquisicao"><i class="fas fa-calendar-day"></i> Data de Aquisição:</label>
                    <input type="text" class="form-control" name="aquisicao" placeholder="AAAA-MM-DD" id="aquisicao" required>
                </div>
                <div class="form-group">
                    <label for="pais"><i class="fas fa-globe-americas"></i> País:</label>
                    <input type="text" class="form-control" name="pais" required>
                </div>
                <div class="form-group">
                    <label for="estante"><i class="fas fa-bookshelf"></i> Estante:</label>
                    <input type="text" class="form-control" name="estante" required>
                </div>
                <div class="form-group">
                    <label for="prateleira"><i class="fas fa-box"></i> Prateleira:</label>
                    <input type="text" class="form-control" name="prateleira" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="autor"><i class="fas fa-user"></i> Autor:</label>
                    <input type="text" class="form-control" name="autor" required>
                </div>
                <div class="form-group">
                    <label for="traducao"><i class="fas fa-language"></i> Tradução:</label>
                    <input type="text" class="form-control" name="traducao" required>
                </div>
                <div class="form-group">
                    <label for="data"><i class="fas fa-calendar-alt"></i> Data de Lançamento:</label>
                    <input type="text" class="form-control" name="data" placeholder="AAAA-MM-DD" id="data" required>
                </div>
                <div class="form-group">
                    <label for="isbn"><i class="fas fa-barcode"></i> ISBN:</label>
                    <input type="text" class="form-control" name="isbn" required>
                </div>
                <div class="form-group">
                    <label for="ano"><i class="fas fa-calendar"></i> Ano:</label>
                    <input type="text" class="form-control" name="ano" id="ano" required>
                </div>
                <div class="form-group">
                    <label for="estado"><i class="fas fa-bookmark"></i> Estado de conservação:</label>
                    <input type="text" class="form-control" name="estado" required>
                </div>
            </div>
        </div>
        <br><br>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cadastrar Livro</button>
        <br><br><br>
    </form>
</div>

<?php include('../componentes/rodape.php'); ?>

<script>
    $(document).ready(function() {
        // Máscara para a data de aquisição
        $('#aquisicao').mask('0000-00-00');
        // Máscara para a data de lançamento
        $('#data').mask('0000-00-00');
        // Máscara para o ano
        $('#ano').mask('0000');
    });
</script>

</body>
</html>