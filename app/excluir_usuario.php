<?php
session_start();

// Se a exclusão foi bem-sucedida, defina uma variável de sessão para a mensagem de sucesso
$_SESSION['success_message'] = "Usuário excluído com sucesso";

// Verificar se a solicitação veio através de um método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar se o ID do usuário foi enviado
    if (isset($_POST['user_id'])) {
        // Incluir arquivo de conexão com o banco de dados
        require_once('../database/conexao.php');

        // Obter o ID do usuário a ser excluído
        $user_id = $_POST['user_id'];

        // Preparar e executar a consulta SQL para excluir o usuário
        $sql = "DELETE FROM usuarios WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);

        // Vincular o parâmetro do ID do usuário
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        // Verificar se a exclusão foi bem-sucedida
        if ($stmt->execute()) {
            // Retornar uma resposta JSON para indicar o sucesso da exclusão
            echo json_encode(array('status' => 'success'));
            exit;
        } else {
            // Retornar uma resposta JSON para indicar falha na exclusão
            echo json_encode(array('status' => 'error'));
            exit;
        }
    }
}

// Se não veio por método POST ou se não foi fornecido um ID de usuário válido, retornar erro
echo json_encode(array('status' => 'error', 'message' => 'ID de usuário inválido'));
exit;
?>
