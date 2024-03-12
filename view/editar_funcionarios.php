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

// Consulta para obter os dados da tabela funcionarios
$sql = "SELECT * FROM funcionarios";
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
    <link rel="stylesheet" href="../style/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" crossorigin="anonymous" />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>Listar/Alterar Funcionários</title>
</head>
<body>

    <?php include('../componentes/menu_superior.php'); ?>

    <div class="container mt-4">
        <h2>Listar/Alterar Funcionários</h2>
        
        <?php
        // Exibir mensagem de sucesso ou erro, se houver
        if(isset($_SESSION['msg'])) {
            echo '<div class="alert alert-success" role="alert">' . $_SESSION['msg'] . '</div>';
            unset($_SESSION['msg']);
        }
        ?>
        <!-- Tabela para exibir os funcionarios -->
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>Cargo</th>
                    <th>Admissão</th>
                    <th>Licença</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop para exibir os dados da tabela funcionarios
                if ($result->rowCount() > 0) {
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["nome"] . "</td>";
                        echo "<td>" . $row["cpf"] . "</td>";
                        echo "<td>" . $row["telefone"] . "</td>";
                        echo "<td>" . $row["cargo"] . "</td>";
                        echo "<td>" . $row["admissao"] . "</td>";
                        echo "<td>" . $row["licenca"] . "</td>";
                        echo "<td>"."<button class='btn btn-danger excluir-btn' data-toggle='modal' data-target='#confirmacaoExclusaoModal' data-id='{$row['id']}'>Excluir</button>";
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

    <script>
    // Armazena o ID do funcionario a ser excluído
    var funcionarioIdParaExclusao;

    // Configura o ID do funcionario ao abrir o modal de confirmação
    $(document).on('click', '.excluir-btn', function () {
        funcionarioIdParaExclusao = $(this).data('id');
    });

    // Configura o botão de confirmação dentro do modal
    $('#confirmarExclusao').on('click', function () {
        // Envia uma solicitação AJAX para excluir o funcionario
        $.ajax({
            type: 'POST',
            url: '../app/excluir_funcionario.php',
            data: { funcionario_id: funcionarioIdParaExclusao },
            dataType: 'json', // Indica que esperamos uma resposta JSON
            success: function (response) {
                // Verifica se a exclusão foi bem-sucedida
                if (response.status === 'success') {
                    // Fecha o modal
                    $('#confirmacaoExclusaoModal').modal('hide');
                    
                    // Remove a linha da tabela
                    $('tr[data-id="' + funcionarioIdParaExclusao + '"]').remove();

                    // Redireciona de volta à página principal após a exclusão bem-sucedida
                    window.location.href = 'editar_funcionarios.php';
                } else {
                    // Trata o erro (pode exibir uma mensagem de erro, por exemplo)
                    console.error('Erro ao excluir o funcionario.');
                }
            },
            error: function (error) {
                console.error(error);
            }
        });
    });

    // Configura o botão de edição
    $('.editar-btn').on('click', function () {
        // Obtém o ID do funcionario a ser editado
        var funcionarioIdParaEdicao = $(this).data('id');

        // Redireciona para a página de edição com o ID do funcionario
        window.location.href = '../view/pagina_de_edicao_func.php?id=' + funcionarioIdParaEdicao;
    });
    </script>

    <?php include('../componentes/rodape.php'); ?>
</body>
</html>