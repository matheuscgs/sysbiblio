<?php
session_start();
require_once('../database/conexao.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['msg'] = "Usuário não logado";
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Obtém o ID do usuário logado

$sqlValorMulta = "SELECT valor_multa FROM valor_multa";
$stmtValorMulta = $pdo->query($sqlValorMulta);
$valorMultaDiaria = $stmtValorMulta->fetchColumn();

$sql = "SELECT emp.id, emp.id_livro, emp.id_usuario, emp.data_emprestimo, emp.data_devolucao, 
               usr.nome AS nome_usuario, usr.email AS email_usuario, usr.telefone AS telefone_usuario
        FROM emprestimos emp
        INNER JOIN usuarios usr ON emp.id_usuario = usr.id
        WHERE emp.id_usuario = :user_id AND emp.data_devolucao < CURDATE()";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$emprestimosAtraso = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['calcular_multa'])) {
    $sqlDeleteRepetidas = "DELETE FROM multas WHERE id IN (
        SELECT id FROM (
            SELECT id, ROW_NUMBER() OVER (PARTITION BY id_emprestimo ORDER BY id) AS rnum
            FROM multas
        ) t
        WHERE t.rnum > 1
    )";
    $stmtDeleteRepetidas = $pdo->prepare($sqlDeleteRepetidas);
    $stmtDeleteRepetidas->execute();

    foreach ($emprestimosAtraso as $emprestimo) {
        $dataDevolucao = new DateTime($emprestimo['data_devolucao']);
        $dataAtual = new DateTime();
        $atraso = $dataDevolucao->diff($dataAtual)->days;
        $valorMulta = $atraso * $valorMultaDiaria;

        $sqlInsertMulta = "INSERT INTO multas (id_emprestimo, atraso, valor) 
                           VALUES (:id_emprestimo, :atraso, :valor)";
        $stmtInsertMulta = $pdo->prepare($sqlInsertMulta);
        $stmtInsertMulta->bindParam(':id_emprestimo', $emprestimo['id']);
        $stmtInsertMulta->bindParam(':atraso', $atraso);
        $stmtInsertMulta->bindParam(':valor', $valorMulta);
        $stmtInsertMulta->execute();
    }

    $_SESSION['msg'] = "Multas calculadas com sucesso.";
    header("Location: minhas_multas.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Listar Multas do Usuário</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="..\style\styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"  crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.3/xlsx.full.min.js"></script>

</head>
<body>
    <?php include('../componentes/menu_superior.php'); ?>

    <div class="container mt-4">
        <h2>Multas do Usuário</h2>
        <?php if (!empty($emprestimosAtraso)): ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID Empréstimo</th>
                            <th>ID Livro</th>
                            <th>Data Empréstimo</th>
                            <th>Data Devolução</th>
                            <th>Atraso (dias)</th>
                            <th>Valor da Multa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($emprestimosAtraso as $emprestimo): ?>
                            <tr>
                                <td><?php echo $emprestimo['id']; ?></td>
                                <td><?php echo $emprestimo['id_livro']; ?></td>
                                <td><?php echo $emprestimo['data_emprestimo']; ?></td>
                                <td><?php echo $emprestimo['data_devolucao']; ?></td>
                                <?php
                                    $dataDevolucao = new DateTime($emprestimo['data_devolucao']);
                                    $dataAtual = new DateTime();
                                    $atraso = $dataDevolucao->diff($dataAtual)->days;
                                    $valorMulta = $atraso * $valorMultaDiaria;
                                ?>
                                <td><?php echo $atraso; ?></td>
                                <td><?php echo $valorMulta; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" name="calcular_multa" class="btn btn-primary">Calcular Multas</button>
                </br></br>
                <button id="btn-download-excel" class="btn btn-primary">Baixar Excel</button>
                </br></br>

                        </br></br></br>

            </form>
        <?php else: ?>
            <p>Não há empréstimos em atraso para este usuário.</p>
        <?php endif; ?>
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
        a.download = 'minhas_multas.xls';
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
