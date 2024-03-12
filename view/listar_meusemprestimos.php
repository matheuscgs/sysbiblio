<?php
session_start();

// Verifique se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se o usuário não estiver logado, redirecione para a página de login
    $_SESSION['msg'] = "Usuário não logado";
    header("Location: ../index.php");
    exit;
}

// Obter o ID do usuário da sessão
$userId = $_SESSION['user_id'];

require_once('../database/conexao.php');

// Consulta para obter os dados da tabela livros
$sql = "SELECT e.id AS emprestimos_id, l.titulo AS titulo_livro, l.autor AS autor_livro, u.nome AS nome_usuario, u.email AS email_usuario, u.id AS usuario_id, l.id AS livro_id, e.data_emprestimo, e.data_devolucao 
FROM emprestimos e 
INNER JOIN livros l ON e.id_livro = l.id 
INNER JOIN usuarios u ON e.id_usuario = u.id
WHERE u.id = :userId";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
$stmt->execute();

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.3/xlsx.full.min.js"></script>

    <link rel="stylesheet" href="..\style\styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"  crossorigin="anonymous" />
    <title>Visualizar meus empréstimos de livros</title>
</head>
<body>

    <?php include('../componentes/menu_superior.php'); ?>

    <div class="container mt-4">
        <h2>Visualizar Meus Empréstimos de Livros</h2>

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
              // Loop para exibir os dados da tabela livros
if ($stmt->rowCount() > 0) { // Alterado de $sql para $stmt
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { // Alterado de $sql para $stmt
        // Verificar a data de devolução
        $dataDevolucao = new DateTime($row["data_devolucao"]);
        $hoje = new DateTime();
        
        echo "<tr";
        
        // Adicionar classes de acordo com a condição da data de devolução
        if ($hoje >= $dataDevolucao) {
            echo " class='table-danger'";
        } elseif ($hoje >= $dataDevolucao->modify('-1 day')) {
            echo " class='table-warning'";
        }
        
        echo ">";
        
        // Exibir os dados da linha
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
        a.download = 'meus_emprestimos.xls';
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