<?php
session_start();

require_once('../database/conexao.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Certifique-se de que está usando "funcionario_id" para recuperar o ID do funcionário
        $funcionarioId = $_POST['funcionario_id'];

        $stmt = $pdo->prepare("UPDATE funcionarios 
            SET nome = :nome, 
                cpf = :cpf, 
                telefone = :telefone, 
                cargo = :cargo, 
                admissao = :admissao, 
                licenca = :licenca 
            WHERE id = :funcionario_id");

        $params = array(
            ':nome' => $_POST['nome'],
            ':cpf' => $_POST['cpf'],
            ':telefone' => $_POST['telefone'],
            ':cargo' => $_POST['cargo'],
            ':admissao' => $_POST['admissao'],
            ':licenca' => $_POST['licenca'],
            ':funcionario_id' => $funcionarioId  // Certifique-se de incluir o ID do funcionário
        );

        $stmt->execute($params);
        $_SESSION['msg'] = "Funcionário atualizado com sucesso!";
        // Redirecione para a página de edição com o ID do funcionário
        header("Location: ../view/pagina_de_edicao_func.php?id=" . $funcionarioId);
        exit();
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}
?>