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

// Conecte-se ao banco de dados
require_once('../database/conexao.php');

// Consulta para obter os dados da tabela vendas, juntamente com informações relevantes da tabela livros
$sql = "SELECT v.id, v.id_livro, v.valor, l.autor, l.titulo, l.ano FROM vendas v INNER JOIN livros l ON v.id_livro = l.id";
$result = $pdo->query($sql);

// Verificar se a consulta foi bem-sucedida antes de fechar a conexão com o banco de dados
if (!$result) {
    die('Erro ao executar a consulta: ' . $pdo->errorInfo()[2]);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.3/xlsx.full.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" crossorigin="anonymous" />
    <title>Exibir Livros para venda</title>
</head>
<body>

    <?php include('../componentes/menu_superior.php'); ?>

    <div class="container mt-4">
        <h2>Livros para venda</h2>

        <?php
        // Exibir mensagem de sucesso ou erro, se houver
        if(isset($_SESSION['msg'])) {
            echo '<div class="alert alert-success" role="alert">' . $_SESSION['msg'] . '</div>';
            unset($_SESSION['msg']);
        }
        ?>
        <!-- Campo de pesquisa -->
        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Pesquisar por título">

        <!-- Tabela para exibir os livros -->
        <table class="table" id="livrosTable">
            <thead>
                <tr>
                    <th>ID Venda</th>
                    <th>Autor</th>
                    <th>Título</th>
                    <th>Ano</th>
                    <th>Valor</th>
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
                        echo "<td>" . $row["autor"] . "</td>";
                        echo "<td>" . $row["titulo"] . "</td>";
                        echo "<td>" . $row["ano"] . "</td>";
                        echo "<td>" . $row["valor"] . "</td>";
                        // Botão para vender (excluir) a linha correspondente
                        echo "<td><button type='button' class='btn btn-danger btn-excluir' data-id='" . $row["id"] . "'>Vendido</button></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Nenhum livro cadastrado.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        </br></br>
        <button id="btn-download-excel" class="btn btn-primary">Baixar Excel</button>
        </br></br>
    </div>

    <?php include('../componentes/rodape.php'); ?>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
    $(document).ready(function() {
        // Adiciona um manipulador de eventos para o clique do botão "Vendido"
        $(document).on('click', '.btn-excluir', function() {
            // Obtém o ID do livro a partir do atributo data-id
            var livroId = $(this).data('id');

            // Faz uma requisição AJAX para vender o livro
            $.ajax({
                type: "POST",
                url: "../app/excluir_venda.php",
                data: { 
                    venderLivro: 'true', 
                    livroId: livroId,
                    valorVenda: 0 // Você pode precisar adicionar a variável para o valor da venda
                },
                success: function(response) {
                    
                    // Recarrega a página para atualizar a tabela de livros
                    location.reload();
                },
                error: function(xhr, status, error) {
                    // Exibe uma mensagem de erro
                    alert("Erro ao vender o livro: " + error);
                }
            });
        });

        // Função para atualizar a tabela com base no filtro de pesquisa
        function atualizarTabela(filtro) {
            var table = document.getElementById('livrosTable');
            var rows = table.getElementsByTagName('tr');
            for (var i = 0; i < rows.length; i++) {
                var title = rows[i].getElementsByTagName('td')[2]; // A terceira coluna (índice 2) contém o título do livro
                if (title) {
                    var titleText = title.textContent || title.innerText;
                    if (titleText.toLowerCase().includes(filtro.toLowerCase())) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
        }

        // Captura eventos de digitação no campo de pesquisa
        document.getElementById('searchInput').addEventListener('input', function() {
            var filtro = this.value.trim(); // Remove espaços em branco extras no início e no final
            atualizarTabela(filtro);
        });
    });
    </script>

</body>
</html>
