<?php
session_start();

// Verifique se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se o usuário não estiver logado, redirecione para a página de login
    $_SESSION['msg'] = "Usuário não logado";
    header("Location: ../index.php");
    exit;
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="../style/styles.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" crossorigin="anonymous" />
    <title>Exibir Livros Cadastrados</title>
</head>
<body>

<?php include('../componentes/menu_superior.php'); ?>

<div class="container mt-4">
    <h2>Livros Cadastrados</h2>

    <!-- Campo de pesquisa -->
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Pesquisar por título">

    <!-- Tabela para exibir os livros -->
    <table class="table" id="livrosTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Edição</th>
                <th>Editora</th>
                <th>Aquisição</th>
                <th>País</th>
                <th>Autor</th>
                <th>Tradução</th>
                <th>Data</th>
                <th>ISBN</th>
                <th>Ano</th>
                <th>Estante</th>
                <th>Prateleira</th>
                <th>Estado</th>
                <th>Disponível?</th>
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
                    echo "<td>" . $row["editora"] . "</td>";
                    echo "<td>" . $row["aquisicao"] . "</td>";
                    echo "<td>" . $row["pais"] . "</td>";
                    echo "<td>" . $row["autor"] . "</td>";
                    echo "<td>" . $row["traducao"] . "</td>";
                    echo "<td>" . $row["data"] . "</td>";
                    echo "<td>" . $row["isbn"] . "</td>";
                    echo "<td>" . $row["ano"] . "</td>";
                    echo "<td>" . $row["estante"] . "</td>";
                    echo "<td>" . $row["prateleira"] . "</td>";
                    echo "<td>" . $row["estado"] . "</td>";
                    echo "<td>" . ($row["emprestado"] == 1 ? 'Não' : 'Sim') . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Nenhum livro cadastrado.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <br/><br/>
    <button id="btn-download-excel" class="btn btn-primary">Baixar Excel</button>
    <br/><br/>
</div>

<?php include('../componentes/rodape.php'); ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
        a.download = 'livros.xls';
        a.click();

        // Libera o URL do blob
        setTimeout(function() {
            window.URL.revokeObjectURL(url);
        }, 0);
    });
// Função para atualizar a tabela com base no filtro de pesquisa
function atualizarTabela(filtro) {
        var table = document.getElementById('livrosTable');
        var rows = table.getElementsByTagName('tr');

        // Loop pelas linhas da tabela, exceto pela primeira linha de cabeçalho
        for (var i = 1; i < rows.length; i++) {
            var title = rows[i].getElementsByTagName('td')[1]; // A segunda coluna (índice 1) contém o título do livro
            if (title) {
                var titleText = title.textContent || title.innerText;
                if (titleText.toLowerCase().indexOf(filtro.toLowerCase()) > -1) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }
    }

    // Captura eventos de digitação no campo de pesquisa
    document.getElementById('searchInput').addEventListener('input', function() {
        var filtro = this.value;
        atualizarTabela(filtro);
    });

</script>
</body>
</html>
