<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="..\style\styles.css">
    <!-- Inclua a biblioteca Font Awesome se ainda não tiver incluído -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"  crossorigin="anonymous" />
    <title>Listar Livros Disponiveis</title>
</head>
<body>

    <!-- Inclua o menu usando include -->
    <?php include('../componentes/menu_superior.php'); ?>
</br></br>
</br></br>
    <h2 align="center">Você não possui permissão para acessar essa página.</h2>
    </br></br>
</br></br>
    <!-- Inclua o rodapé usando include -->
    <?php include('../componentes/rodape.php'); ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>