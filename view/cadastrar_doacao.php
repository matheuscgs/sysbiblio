<?php
session_start();
require_once('../database/conexao.php');

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['msg'] = "Usuário não logado";
    header("Location: ../index.php");
    exit;
}

// Coletar o ID do usuário logado
$idUsuario = $_SESSION['user_id'];

// Verificar se o método de requisição é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coletar e validar os dados do formulário
    $titulo = validarEntrada($_POST['titulo']);
    $edicao = validarEntrada($_POST['edicao']);
    $editora = validarEntrada($_POST['editora']);
    $aquisicao = validarEntrada($_POST['aquisicao']);
    $pais = validarEntrada($_POST['pais']);
    $autor = validarEntrada($_POST['autor']);
    $traducao = validarEntrada($_POST['traducao']);
    $dataLancamento = validarEntrada($_POST['data']);
    $isbn = validarEntrada($_POST['isbn']);
    $ano = validarEntrada($_POST['ano']);
    $estado = validarEntrada($_POST['estado']);

    // Preparar a consulta SQL para inserir na tabela "doacoes"
    $sql = "INSERT INTO doacoes (titulo, edicao, editora, aquisicao, pais, autor, traducao,  isbn, ano, estado, id_usuario) 
            VALUES (:titulo, :edicao, :editora, :aquisicao, :pais, :autor, :traducao,  :isbn, :ano, :estado, :idUsuario)";
    
    // Preparar e executar a consulta
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':edicao', $edicao);
    $stmt->bindParam(':editora', $editora);
    $stmt->bindParam(':aquisicao', $aquisicao);
    $stmt->bindParam(':pais', $pais);
    $stmt->bindParam(':autor', $autor);
    $stmt->bindParam(':traducao', $traducao);
    $stmt->bindParam(':isbn', $isbn);
    $stmt->bindParam(':ano', $ano);
    $stmt->bindParam(':estado', $estado);
    $stmt->bindParam(':idUsuario', $idUsuario);

    // Executar a consulta
    if ($stmt->execute()) {
        // Livro cadastrado com sucesso, agora vamos atualizar o status
        $livroInseridoId = $pdo->lastInsertId(); // Obtém o ID do livro inserido
    
        // Atualiza o status para "Em Análise"
        $sqlUpdateStatus = "UPDATE doacoes SET status = 'Em Análise' WHERE id = :livroId";
        $stmtUpdate = $pdo->prepare($sqlUpdateStatus);
        $stmtUpdate->bindParam(':livroId', $livroInseridoId);
        $stmtUpdate->execute();
    
        $_SESSION['msg'] = "Livro cadastrado com sucesso.";
    } else {
        $_SESSION['msg'] = "Erro ao cadastrar o livro.";
    }

    // Redirecionar de volta para a página de cadastro de livro
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
    <link rel="stylesheet" href="..\style\styles.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"  crossorigin="anonymous" />
    <title>Cadastrar Doação de Livro</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../src/jquery.mask.min.js"></script>

</head>
<body>

    <?php include('../componentes/menu_superior.php'); ?>

    <div class="container mt-4">
        <h2>Cadastrar Doação de Livro</h2>
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
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="titulo"><i class="fas fa-book"></i> Título:</label>
                    <input type="text" class="form-control" name="titulo" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="edicao"><i class="fas fa-pencil-alt"></i> Edição:</label>
                    <input type="text" class="form-control" name="edicao" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="editora"><i class="fas fa-building"></i> Editora:</label>
                    <input type="text" class="form-control" name="editora" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="aquisicao"><i class="far fa-calendar-alt"></i> Data de Aquisição:</label>
                    <input type="text" class="form-control" name="aquisicao" id="aquisicao" placeholder="AAAA-MM-DD" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="pais"><i class="fas fa-globe"></i> País:</label>
                    <input type="text" class="form-control" name="pais" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="autor"><i class="fas fa-user"></i> Autor:</label>
                    <input type="text" class="form-control" name="autor" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="traducao"><i class="fas fa-language"></i> Tradução:</label>
                    <input type="text" class="form-control" name="traducao" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="isbn"><i class="fas fa-barcode"></i> ISBN:</label>
                    <input type="text" class="form-control" name="isbn" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="ano"><i class="far fa-calendar-alt"></i> Ano:</label>
                    <input type="text" class="form-control" name="ano" id="ano" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="estado"><i class="fas fa-tools"></i> Estado de Conservação:</label>
                    <input type="text" class="form-control" name="estado" required>
                </div>
            </div>
            <br><br>
            <button type="submit" class="btn btn-primary">Cadastrar Livro</button>
            <br><br><br>
        </form>
    </div>

    <?php include('../componentes/rodape.php'); ?>

    <script>
    $(document).ready(function() {
        // Máscara para a data de aquisição
        $('#aquisicao').mask('0000-00-00');
        // Máscara para o ano
        $('#ano').mask('0000');
    });
    </script>

</body>
</html>
