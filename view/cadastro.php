<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/styles.css"> <!-- Seu arquivo CSS externo -->
    <title>Cadastro</title>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
</head>
<body>

<div class="background-image"></div>

<div class="container-index">
    <div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="col-md-6">
            <div class="login-container">
                <img src="../imagens/logo.jpg" alt="Logo" class="logo">
                <?php
                // Exibir caixa de alerta se houver um erro
                if(isset($_SESSION['msg']) && $_SESSION['msg'] === "Preencha todos campos corretamente.") {
                    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['msg'] . '</div>';
                } elseif(isset($_SESSION['msg'])) {
                    echo '<div class="alert alert-success" role="alert">' . $_SESSION['msg'] . '</div>';
                }
                ?>
                <!-- Formulário de cadastro -->
                <form method="post" action="../app/inserir_usuario.php">
                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" name="nome" class="form-control" id="nome" placeholder="Digite seu nome" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" class="form-control" id="email" placeholder="Digite seu email" required>
                    </div>
                    <div class="form-group">
                        <label for="telefone">Telefone:</label>
                        <input type="tel" name="telefone" class="form-control" id="telefone" placeholder="(11) 11111-1111" required>
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha:</label>
                        <input type="password" name="senha" class="form-control" id="senha" placeholder="Digite sua senha" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Cadastrar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#telefone').mask('(00) 00000-0000');
    });
</script>

</body>
</html>