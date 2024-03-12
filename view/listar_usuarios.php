<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['msg'] = "Usuário não logado";
    header("Location: ../index.php");
    exit;
}

// Verificar se o usuário é um administrador
if ($_SESSION['user_level'] != 'administrador') {
    header("Location: ../view/permissao.php");
}

// Incluir arquivo de conexão com o banco de dados
require_once('../database/conexao.php');

// Consulta SQL para obter os dados dos usuários
$sql = "SELECT * FROM usuarios";
$result = $pdo->query($sql);

// Verificar se a consulta foi bem-sucedida antes de fechar a conexão
if (!$result) {
    die('Erro ao executar a consulta: ' . $pdo->errorInfo()[2]);
}

// Fechar a conexão com o banco de dados
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"  crossorigin="anonymous" />
    <title>Editar Usuários</title>
</head>
<body>

<?php include('../componentes/menu_superior.php'); ?>

<div class="container mt-4">
    <h2>Editar Usuários</h2>

    <?php
    // Verificar se a variável de sessão de mensagem de sucesso está definida
    if (isset($_SESSION['success_message'])) {
        // Exiba a mensagem de sucesso
        echo '<div class="alert alert-success" role="alert">' . $_SESSION['success_message'] . '</div>';

        // Limpe a variável de sessão para que a mensagem não seja exibida novamente na próxima visita à página
        unset($_SESSION['success_message']);
    }
    ?>

    <!-- Campo de pesquisa por nome -->
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Pesquisar por nome">

    <!-- Tabela para exibir os usuários -->
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>Nível de Acesso</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Loop para exibir os dados dos usuários
            if ($result->rowCount() > 0) {
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["nome"] . "</td>";
                    echo "<td>" . $row["email"] . "</td>";
                    echo "<td>" . $row["telefone"] . "</td>";
                    echo "<td>" . $row["nivel"] . "</td>";
                    echo "<td>";
                    echo "<button class='btn btn-danger excluir-btn' data-toggle='modal' data-target='#confirmacaoExclusaoModal' data-id='{$row['id']}'>Excluir</button>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Nenhum usuário cadastrado.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal de confirmação de exclusão -->
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
                Tem certeza de que deseja excluir este usuário?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarExclusao">Confirmar Exclusão</button>
            </div>
        </div>
    </div>
</div>

<?php include('../componentes/rodape.php'); ?>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Armazenar o ID do usuário a ser excluído
    var userIdParaExclusao;

    // Configurar o ID do usuário ao abrir o modal de confirmação
    $('.excluir-btn').on('click', function () {
        userIdParaExclusao = $(this).data('id');
    });

    // Configurar o botão de confirmação dentro do modal
    $('#confirmarExclusao').on('click', function () {
        // Enviar uma solicitação AJAX para excluir o usuário
        $.ajax({
            type: 'POST',
            url: '../app/excluir_usuario.php', // Arquivo PHP para processar a exclusão do usuário
            data: { user_id: userIdParaExclusao },
            dataType: 'json',
            success: function (response) {
                // Verificar se a exclusão foi bem-sucedida
                if (response.status === 'success') {
                    // Fechar o modal
                    $('#confirmacaoExclusaoModal').modal('hide');
                    
                    // Remover a linha da tabela
                    $('tr[data-id="' + userIdParaExclusao + '"]').remove();

                    // Redirecionar de volta à página principal após a exclusão bem-sucedida
                    window.location.href = 'listar_usuarios.php';
                } else {
                    // Tratar o erro (pode exibir uma mensagem de erro, por exemplo)
                    console.error('Erro ao excluir o usuário.');
                }
            },
            error: function (error) {
                console.error(error);
            }
        });
    });

    // Função para filtrar os usuários por nome
    $('#searchInput').on('input', function () {
        var nome = $(this).val().toLowerCase();
        $('tbody tr').each(function () {
            var linha = $(this);
            var nomeUsuario = linha.find('td:eq(1)').text().toLowerCase(); // O índice 1 corresponde à coluna do nome do usuário
            if (nomeUsuario.indexOf(nome) === -1) {
                linha.hide();
            } else {
                linha.show();
            }
        });
    });
</script>

</body>
</html>
