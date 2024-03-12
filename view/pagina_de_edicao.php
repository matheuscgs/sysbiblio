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
    $livroId = $_GET['id'];

    // Aqui, você pode consultar o banco de dados para obter as informações do livro com base no ID
    // e preencher os campos do formulário
    require_once('../database/conexao.php');

    // Consulta para obter os dados do livro pelo ID
    $sql = "SELECT * FROM livros WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $livroId, PDO::PARAM_INT);
    $stmt->execute();

    if ($livro = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Os dados do livro foram obtidos com sucesso
    } else {
        // Se o livro não for encontrado, redireciona para a página de listar livros
        header('Location: listar_livros.php');
        exit();
    }

    // Fecha a conexão com o banco de dados após obter as informações do livro
    $pdo = null;

} else {
    // Se o ID não estiver presente na URL, redireciona para a página de listar livros
    header('Location: listar_livros.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Editar Livro</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="../src/jquery.mask.min.js"></script>
</head>
<body>

    <?php include('../componentes/menu_superior.php'); ?>

    <div class="container mt-4">
        <h2><i class="fas fa-book"></i> Editar Livro</h2>
</br></br>
        <?php
            // Exibir caixa de sucesso
            if (isset($_SESSION['msg'])) {
                echo '<div class="alert alert-success" role="alert">' . $_SESSION['msg'] . '</div>';
                unset($_SESSION['msg']);
            } 
        ?>

        <!-- Formulário para editar livros -->
        <form method="post" action="../app/atualizar_livro.php">
            <!-- Campos do formulário com os valores preenchidos -->
            <div class="form-row">
                <input type="hidden" name="livro_id" value="<?php echo $livro['id']; ?>">
                <div class="form-group col-md-6">
                    <label for="titulo"><i class="fas fa-heading"></i> Título:</label>
                    <input type="text" class="form-control" name="titulo" value="<?php echo $livro['titulo']; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="edicao"><i class="fas fa-bookmark"></i> Edição:</label>
                    <input type="text" class="form-control" name="edicao" value="<?php echo $livro['edicao']; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="editora"><i class="fas fa-building"></i> Editora:</label>
                    <input type="text" class="form-control" name="editora" value="<?php echo $livro['editora']; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="aquisicao"><i class="fas fa-calendar-day"></i> Data de Aquisição:</label>
                    <input type="text" class="form-control" name="aquisicao" id="aquisicao" placeholder="AAAA-MM-DD" value="<?php echo $livro['aquisicao']; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="pais"><i class="fas fa-globe"></i> País:</label>
                    <input type="text" class="form-control" name="pais" value="<?php echo $livro['pais']; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="estante"><i class="fas fa-bookshelf"></i> Estante:</label>
                    <input type="text" class="form-control" name="estante" value="<?php echo $livro['estante']; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="autor"><i class="fas fa-user"></i> Autor:</label>
                    <input type="text" class="form-control" name="autor" value="<?php echo $livro['autor']; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="traducao"><i class="fas fa-language"></i> Tradução:</label>
                    <input type="text" class="form-control" name="traducao" value="<?php echo $livro['traducao']; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="data"><i class="fas fa-calendar-alt"></i> Data de Lançamento:</label>
                    <input type="text" class="form-control" name="data" id="data" placeholder="AAAA-MM-DD" value="<?php echo $livro['data']; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="isbn"><i class="fas fa-barcode"></i> ISBN:</label>
                    <input type="text" class="form-control" name="isbn" value="<?php echo $livro['isbn']; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="ano"><i class="fas fa-calendar"></i> Ano:</label>
                    <input type="text" class="form-control" name="ano" id="ano" value="<?php echo $livro['ano']; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="prateleira"><i class="fas fa-archive"></i> Prateleira:</label>
                    <input type="text" class="form-control" name="prateleira"  value="<?php echo $livro['prateleira']; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="estado"><i class="fas fa-tools"></i> Estado de conservação:</label>
                    <input type="text" class="form-control" name="estado" value="<?php echo $livro['estado']; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="emprestado"><i class="fas fa-handshake"></i> Emprestado:</label>
                    <select class="form-control" name="emprestado" value="<?php echo $livro['emprestado']; ?>" required>
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="venda"><i class="fas fa-shopping-cart"></i> Venda:</label>
                    <select class="form-control" name="venda" value="<?php echo $livro['venda']; ?>" required>
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Atualizar Livro</button>
        </br></br></br>
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