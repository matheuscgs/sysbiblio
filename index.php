<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href=".\style\styles.css">
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <title>SysBiblio</title>
</head>
<body>

    <div class="background-image"></div>

    <div class="container-index">
        <div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
            <div class="login-container">
                <img src="./imagens/logo.jpg" alt="Logo" class="logo">
                <?php
                // Exibir caixa de alerta se houver um erro
                if(isset($_SESSION['msg'])) {
                    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['msg'] . '</div>';
                    unset($_SESSION['msg']);
                }
                ?>
                <form method="post" action="./app/login.php">
                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="text" name="email" class="form-control" id="email" placeholder="Digite seu email">
                    </div>
                    <div class="form-group">
                        <label for="password">Senha:</label>
                        <input type="password" name="senha" class="form-control" id="password" placeholder="Digite sua senha">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Entrar</button>
                    <div class="text-center mt-3">
                        <a href="./view/cadastro.php" class="btn btn-link">Cadastre-se</a>
                    </div>
                    <div class="text-center mt-3">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
