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

require_once('../database/conexao.php');

// Consulta para obter os dados da tabela livros
$sql = "SELECT * FROM livros";
$result = $pdo->query($sql);

// Check if the query was successful before closing the connection
if (!$result) {
    die('Error executing the query: ' . $pdo->errorInfo()[2]);
}

// Close the connection with the database
$pdo = null;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="..\style\styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" crossorigin="anonymous" />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>Editar Livros</title>
</head>
<body>

    <?php include('../componentes/menu_superior.php'); ?>

    <div class="container mt-4">
        <h2>Editar Livros</h2>
        
        <?php
        // Exibir mensagem de sucesso ou erro, se houver
        if(isset($_SESSION['msg'])) {
            echo '<div class="alert alert-success" role="alert">' . $_SESSION['msg'] . '</div>';
            unset($_SESSION['msg']);
        }
        ?>
        <!-- Tabela para exibir os livros -->
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Edição</th>
                    <th>Autor</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop para exibir os dados da tabela livros
                if ($result->rowCount() > 0) {
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["titulo"] . "</td>";
                        echo "<td>" . $row["edicao"] . "</td>";
                        echo "<td>" . $row["autor"] . "</td>";
                        echo "<td>";
                        
                        // Verifica se o livro está marcado como vendido
                        if ($row["venda"] == 1) {
                            // Se estiver vendido, desabilite o botão de vender
                            echo "<button type='button' class='btn btn-primary' disabled>Vender</button>";
                        } else {
                            // Se não estiver vendido, exiba o botão de vender normalmente
                            echo "<button type='button' class='btn btn-primary btn-vender-livro' data-toggle='modal' data-target='#venderLivroModal' data-livro-id='" . $row["id"] . "'>Vender</button>";
                        }
                        
                        echo "<button class='btn btn-danger excluir-btn' data-toggle='modal' data-target='#confirmacaoExclusaoModal' data-id='{$row['id']}'>Excluir</button>";
                        echo "<button class='btn btn-warning editar-btn' data-id='{$row['id']}'>Editar</button>". "</td>";
                        
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Nenhum livro cadastrado.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="modal fade" id="confirmacaoExclusaoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Tem certeza de que deseja excluir este livro?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarExclusao">Confirmar Exclusão</button>
                    
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para venda do livro -->
<div class="modal fade" id="venderLivroModal" tabindex="-1" role="dialog" aria-labelledby="venderLivroModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="venderLivroModalLabel">Vender Livro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="venderLivroForm">
                    <div class="form-group">
                        <label for="valorVenda">Valor da Venda</label>
                        <input type="text" class="form-control" id="valorVenda" name="valorVenda" required>
                    </div>
                    <input type="hidden" id="livroId" name="livroId">
                    <input type="hidden" name="venderLivro" value="true">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmarVenda">Vender</button>
            </div>
        </div>
    </div>
</div>

    

    <script>
    // Armazena o ID do livro a ser excluído
    var livroIdParaExclusao;

    // Configura o ID do livro ao abrir o modal de confirmação
    $('.excluir-btn').on('click', function () {
        livroIdParaExclusao = $(this).data('id');
    });

   // Configura o botão de confirmação dentro do modal
$('#confirmarExclusao').on('click', function () {
    // Envia uma solicitação AJAX para excluir o livro
    $.ajax({
        type: 'POST',
        url: '../app/excluir_livro.php',
        data: { livro_id: livroIdParaExclusao },
        dataType: 'json', // Indica que esperamos uma resposta JSON
        success: function (response) {
            // Verifica se a exclusão foi bem-sucedida
            if (response.status === 'success') {
                // Fecha o modal
                $('#confirmacaoExclusaoModal').modal('hide');
                
                // Remove a linha da tabela
                $('tr[data-id="' + livroIdParaExclusao + '"]').remove();

                // Redireciona de volta à página principal após a exclusão bem-sucedida
                window.location.href = 'editar_livros.php';
            } else {
                // Trata o erro (pode exibir uma mensagem de erro, por exemplo)
                console.error('Erro ao excluir o livro.');
            }
        },
        error: function (error) {
            console.error(error);
        }
    });
});

</script>
<script>
    // Armazena o ID do livro a ser excluído
    var livroIdParaExclusao;

    // Configura o ID do livro ao abrir o modal de confirmação
    $('.excluir-btn').on('click', function () {
        livroIdParaExclusao = $(this).data('id');
    });

    // Configura o botão de confirmação dentro do modal
    $('#confirmarExclusao').on('click', function () {
        // Envia uma solicitação AJAX para excluir o livro
        $.ajax({
            type: 'POST',
            url: '../app/excluir_livro.php',
            data: { livro_id: livroIdParaExclusao },
            dataType: 'json',
            success: function (response) {
                // Verifica se a exclusão foi bem-sucedida
                if (response.status === 'success') {
                    // Fecha o modal
                    $('#confirmacaoExclusaoModal').modal('hide');
                    
                    // Remove a linha da tabela
                    $('tr[data-id="' + livroIdParaExclusao + '"]').remove();

                    // Redireciona de volta à página principal após a exclusão bem-sucedida
                    window.location.href = 'editar_livros.php';
                } else {
                    // Trata o erro (pode exibir uma mensagem de erro, por exemplo)
                    console.error('Erro ao excluir o livro.');
                }
            },
            error: function (error) {
                console.error(error);
            }
        });
    });

    // Configura o botão de edição
    $('.editar-btn').on('click', function () {
        // Obtém o ID do livro a ser editado
        var livroIdParaEdicao = $(this).data('id');

        // Redireciona para a página de edição com o ID do livro
        window.location.href = '../view/pagina_de_edicao.php?id=' + livroIdParaEdicao;
    });
</script>
<script>
    $(document).ready(function(){
        $('.btn-vender-livro').click(function(){
            var livroId = $(this).data('livro-id');
            $('#livroId').val(livroId);
        });

        $('#confirmarVenda').click(function(){
            var livroId = $('#livroId').val();
            var valorVenda = $('#valorVenda').val();

            $.ajax({
                type: 'POST',
                url: '../app/vender_livro.php', // Página PHP para processar a venda
                data: $('#venderLivroForm').serialize(), // Serializa o formulário para enviar os dados
                success: function(response){
                    alert(response); // Exibir mensagem de sucesso ou erro
                    location.reload(); // Recarregar a página após a venda
                }
            });
        });
    });
</script>

    <?php include('../componentes/rodape.php'); ?>

   
</body>
</html>
