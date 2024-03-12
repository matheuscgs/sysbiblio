<?php
session_start();
require_once('../database/conexao.php');

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['msg'] = "Usuário não logado";
    header("Location: ../index.php");
    exit;
}

// Consulta SQL para selecionar os dados da tabela 'doacoes'
$sql = "SELECT * FROM doacoes WHERE id_usuario = :idUsuario";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':idUsuario', $_SESSION['user_id']);
$stmt->execute();
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="..\style\styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"  crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.3/xlsx.full.min.js"></script>

    <title>Listar Livros Minhas Doações</title>
</head>
<body>
    <?php include('../componentes/menu_superior.php'); ?>

    <div class="container mt-4">
        <h2>Listar Minhas Doações</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Edição</th>
                    <th>Editora</th>
                    <th>Data de Aquisição</th>
                    <th>País</th>
                    <th>Autor</th>
                    <th>Tradução</th>
                    <th>ISBN</th>
                    <th>Ano</th>
                    <th>Estado de Conservação</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($livros as $livro): ?>
                    <tr>
                        <td><?php echo $livro['titulo']; ?></td>
                        <td><?php echo $livro['edicao']; ?></td>
                        <td><?php echo $livro['editora']; ?></td>
                        <td><?php echo $livro['aquisicao']; ?></td>
                        <td><?php echo $livro['pais']; ?></td>
                        <td><?php echo $livro['autor']; ?></td>
                        <td><?php echo $livro['traducao']; ?></td>
                        <td><?php echo $livro['isbn']; ?></td>
                        <td><?php echo $livro['ano']; ?></td>
                        <td><?php echo $livro['estado']; ?></td>
                        <td><?php echo $livro['status']; ?></td>
                    </tr>
                <?php endforeach; ?>
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
        a.download = 'minhas_doacoes.xls';
        a.click();

        // Libera o URL do blob
        setTimeout(function() {
            window.URL.revokeObjectURL(url);
        }, 0);
    });
</script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
