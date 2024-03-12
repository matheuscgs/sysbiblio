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
        <h2>Emprestar/Devolver Livros</h2>

        <?php
        // Exibir mensagem de sucesso ou erro, se houver
        if (isset($_SESSION['msg'])) {
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

                        // Adiciona a condição para verificar se o livro está disponível (emprestado = 0)
                        if ($row["emprestado"] == 0) {
                            echo "<td>";
                            echo "<button class='btn btn-success emprestar-btn' data-toggle='modal' data-target='#confirmacaoEmprestimoModal' data-id='{$row['id']}'>Emprestar</button>";
                            echo "</td>";
                        } else {
                            echo "<td>";
                            echo "<button class='btn btn-warning devolver-btn' data-toggle='modal' data-target='#confirmacaoDevolucaoModal' data-id='{$row['id']}'>Devolver</button>";
                            echo "</td>";
                        }

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Nenhum livro cadastrado.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="emprestimoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirmar Empréstimo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="emprestimoForm">
                        <div class="form-group">
                            <label for="idUsuario">ID do Usuário:</label>
                            <input type="text" class="form-control" id="idUsuario" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Confirmar Empréstimo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include('../componentes/rodape.php'); ?>

    <script>
        // Configura o ID do livro ao clicar no botão "Devolver"
        $('.devolver-btn').on('click', function() {
            var livroIdParaDevolucao = $(this).data('id');

            // Referência ao objeto jQuery $(this)
            var self = $(this);

            // Envia uma solicitação AJAX para devolver o livro
            $.ajax({
                type: 'POST',
                url: '../app/devolver_livro.php',
                data: {
                    livro_id: livroIdParaDevolucao
                },
                dataType: 'json',
                success: function(response) {
                    // Verifica se a devolução foi bem-sucedida
                    if (response.status === 'success') {
                        // Atualiza o botão na tabela (opcional)
                        self.removeClass('btn-warning').addClass('btn-success');

                        // Exibe uma mensagem de sucesso (opcional)
                        alert('Livro devolvido com sucesso!');

                        // Retorna à página de devolução de livros
                        window.location.href = 'emprestimo.php';

                    } else {
                        // Trata o erro (pode exibir uma mensagem de erro, por exemplo)
                        console.error('Erro ao devolver o livro.');
                    }
                },
                error: function(error) {
                    console.error(error);
                }
            });
        });

        // Configura o ID do livro ao clicar no botão "Emprestar"
        $('.emprestar-btn').on('click', function() {
            var livroIdParaEmprestimo = $(this).data('id');
            $('#emprestimoModal').modal('show');

            // Configura o ID do livro no formulário dentro do modal
            $('#emprestimoForm').data('livro-id', livroIdParaEmprestimo);
        });

        // Configura o envio do formulário dentro do modal
        $('#emprestimoForm').on('submit', function(e) {
            e.preventDefault();
            var livroIdParaEmprestimo = $(this).data('livro-id');
            var idUsuario = $('#idUsuario').val();

            // Envia uma solicitação AJAX para realizar o empréstimo
            $.ajax({
                type: 'POST',
                url: '../app/realizar_emprestimo.php',
                data: {
                    livro_id: livroIdParaEmprestimo,
                    id_usuario: idUsuario
                },
                dataType: 'json',
                success: function(response) {
                    // Verifica se o empréstimo foi bem-sucedido
                    if (response.status === 'success') {
                        // Fecha o modal
                        $('#emprestimoModal').modal('hide');

                        // Atualiza a página (opcional)
                        window.location.reload();
                    } else {
                        // Trata o erro (pode exibir uma mensagem de erro, por exemplo)
                        console.error('Erro ao realizar o empréstimo.');
                    }
                },
                error: function(error) {
                    console.error(error);
                }
            });
        });
    </script>
</body>

</html>