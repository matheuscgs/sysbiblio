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
$sql = "SELECT e.id AS emprestimos_id, l.titulo AS titulo_livro, l.autor AS autor_livro, u.nome AS nome_usuario, u.email AS email_usuario, u.id AS usuario_id, l.id AS livro_id, e.data_emprestimo, e.data_devolucao FROM emprestimos e INNER JOIN livros l ON e.id_livro = l.id INNER JOIN usuarios u ON e.id_usuario = u.id";

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.3/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"  crossorigin="anonymous" />
    <title>Visualizar empréstimos de livros</title>
</head>
<body>

    <?php include('../componentes/menu_superior.php'); ?>

    <div class="container mt-4">
        <h2>Visualizar Empréstimos de Livros</h2>

        <?php
        // Exibir mensagem de sucesso ou erro, se houver
        if(isset($_SESSION['msg'])) {
            echo '<div class="alert alert-success" role="alert">' . $_SESSION['msg'] . '</div>';
            unset($_SESSION['msg']);
        }
        ?>

        <!-- Formulário de pesquisa por título -->
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Pesquisar por título" id="searchInput">
        </div>

        <!-- Tabela para exibir os empréstimos -->
        <table class="table" id="emprestimosTable">
            <thead>
                <tr>
                    <th>ID do Empréstimo</th>
                    <th>Título do Livro</th>
                    <th>Autor do Livro</th>
                    <th>Nome do Usuário</th>
                    <th>E-mail do Usuário</th>
                    <th>ID do Usuário</th>
                    <th>ID do Livro</th>
                    <th>Data do Empréstimo</th>
                    <th>Data para Devolução</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop para exibir os dados da tabela empréstimos
                if ($result->rowCount() > 0) {
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . $row["emprestimos_id"] . "</td>";
                        echo "<td>" . $row["titulo_livro"] . "</td>";
                        echo "<td>" . $row["autor_livro"] . "</td>";
                        echo "<td>" . $row["nome_usuario"] . "</td>";
                        echo "<td>" . $row["email_usuario"] . "</td>";
                        echo "<td>" . $row["usuario_id"] . "</td>";
                        echo "<td>" . $row["livro_id"] . "</td>";
                        echo "<td>" . $row["data_emprestimo"] . "</td>";
                        echo "<td>" . $row["data_devolucao"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Nenhum livro cadastrado.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        </br></br>
        <button id="btn-download-excel" class="btn btn-primary">Baixar Excel</button>
        </br></br>
    </div>

    <?php include('../componentes/rodape.php'); ?>

    <script>
        document.getElementById('btn-download-excel').addEventListener('click', function() {
            // Seleciona a tabela
            var table = document.querySelector('table');

            // Obtém os dados da tabela e converte para uma matriz
            var dataArray = [];
            var rows = table.querySelectorAll('tr');
            rows.forEach(function(row) {
                var rowData = [];
                var cells = row.querySelectorAll('th, td');
                cells.forEach(function(cell) {
                    rowData.push(cell.textContent.trim());
                });
                dataArray.push(rowData);
            });

            // Cria uma planilha
            var worksheet = XLSX.utils.aoa_to_sheet(dataArray);

            // Cria um livro de trabalho e adiciona a planilha
            var workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Tabela');

            // Converte o livro de trabalho em um arquivo binário
            var wbout = XLSX.write(workbook, { bookType: 'xlsx', type: 'binary' });

            // Função para criar um arquivo e iniciar o download
            function s2ab(s) {
                var buf = new ArrayBuffer(s.length);
                var view = new Uint8Array(buf);
                for (var i = 0; i != s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
                return buf;
            }

            // Cria um blob a partir do arquivo binário
            var blob = new Blob([s2ab(wbout)], { type: 'application/octet-stream' });

            // Cria um URL para o blob
            var url = URL.createObjectURL(blob);

            // Cria um link para o URL do blob e dispara o download
            var a = document.createElement('a');
            a.href = url;
            a.download = 'emprestimos.xls';
            a.click();

            // Libera o URL do blob
            setTimeout(function() {
                window.URL.revokeObjectURL(url);
            }, 0);
        });

        // Função para pesquisar dinamicamente enquanto o usuário digita
        document.getElementById('searchInput').addEventListener('input', function() {
            var searchText = this.value.toLowerCase();
            var table = document.getElementById('emprestimosTable');
            var rows = table.getElementsByTagName('tr');

            // Loop através de todas as linhas da tabela e oculta aquelas que não correspondem à pesquisa
            for (var i = 0; i < rows.length; i++) {
                var title = rows[i].getElementsByTagName('td')[1];
                if (title) {
                    var titleText = title.textContent || title.innerText;
                    if (titleText.toLowerCase().indexOf(searchText) > -1) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
