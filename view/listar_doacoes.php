<?php
session_start();
require_once('../database/conexao.php');

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['msg'] = "Usuário não logado";
    header("Location: ../index.php");
    exit;
}

// Verifique se o nível do usuário é administrador (ou o nível desejado para acessar a página)
if ($_SESSION['user_level'] != 'administrador') {
    // Se o nível do usuário não for administrador, redirecione para outra página ou exiba uma mensagem de erro
    header("Location: ../view/permissao.php");
}

// Verificar se o formulário de alteração de status foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_doacao'])) {
    $id_doacao = $_POST['id_doacao'];

    if (isset($_POST['recusar'])) {
        alterarStatusDoacao($id_doacao, 'recusado');
    } elseif (isset($_POST['aceitar'])) {
        alterarStatusDoacao($id_doacao, 'aceito');
    } elseif (isset($_POST['recebido'])) {
        alterarStatusDoacao($id_doacao, 'recebido');
    }
}

// Função para alterar o status de uma doação
function alterarStatusDoacao($id_doacao, $novo_status) {
    global $pdo;

    $sql = "UPDATE doacoes SET status = :novo_status WHERE id = :id_doacao";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':novo_status', $novo_status);
    $stmt->bindParam(':id_doacao', $id_doacao);

    if ($stmt->execute()) {
        $_SESSION['msg'] = "Status da doação alterado com sucesso.";
    } else {
        $_SESSION['msg'] = "Erro ao alterar o status da doação.";
    }
}

// Consulta SQL para selecionar os dados de todas as doações
$sql = "SELECT * FROM doacoes";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$doacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.3/xlsx.full.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="..\style\styles.css">
    <title>Listar Todas as Doações</title>
</head>

<body>
    <?php include('../componentes/menu_superior.php'); ?>

    <div class="container mt-4">
        <h2>Lista de Todas as Doações</h2>
        <?php
        // Exibir mensagem de sucesso ou erro, se houver
        if (isset($_SESSION['msg'])) {
            echo '<div class="alert alert-success" role="alert">' . $_SESSION['msg'] . '</div>';
            unset($_SESSION['msg']);
        }
        ?>
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
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($doacoes as $doacao): ?>
                    <tr>
                        <td><?php echo $doacao['titulo']; ?></td>
                        <td><?php echo $doacao['edicao']; ?></td>
                        <td><?php echo $doacao['editora']; ?></td>
                        <td><?php echo $doacao['aquisicao']; ?></td>
                        <td><?php echo $doacao['pais']; ?></td>
                        <td><?php echo $doacao['autor']; ?></td>
                        <td><?php echo $doacao['traducao']; ?></td>
                        <td><?php echo $doacao['isbn']; ?></td>
                        <td><?php echo $doacao['ano']; ?></td>
                        <td><?php echo $doacao['estado']; ?></td>
                        <td><?php echo $doacao['status']; ?></td>
                        <td>
                            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <input type="hidden" name="id_doacao" value="<?php echo $doacao['id']; ?>">
                                <button type="submit" name="recusar" class="btn btn-danger">Recusar</button>
                                <button type="submit" name="aceitar" class="btn btn-success">Aceitar</button>
                                <button type="submit" name="recebido" class="btn btn-info">Recebido</button>
                            </form>
                        </td>
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
            a.download = 'doacoes.xls';
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