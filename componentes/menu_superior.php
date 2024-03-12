<!-- cabeçalho.php -->
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-info text-white">
        <div class="container">
            <!-- Logotipo à esquerda -->
            <a class="navbar-brand" href="#"><img src="../imagens/logo.jpg" alt="Logo" height="30"></a>
            <!-- Botão para dispositivos móveis -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Itens do menu -->
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <!-- Item Livros -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="livrosDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Livros
                        </a>
                        <div class="dropdown-menu" aria-labelledby="livrosDropdown">
                            <a class="dropdown-item" href="../view/cadastrar_livros.php">Cadastrar Livros</a>
                            <a class="dropdown-item" href="../view/editar_livros.php">Editar Livros</a>
                            <a class="dropdown-item" href="../view/listarlivros.php">Listar Livros</a>
                        </div>
                    </li>
                    <!-- Item Empréstimos -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="emprestimosDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Empréstimos
                        </a>
                        <div class="dropdown-menu" aria-labelledby="emprestimosDropdown">
                            <a class="dropdown-item" href="../view/emprestimo.php">Emprestar um Livro</a>
                            <a class="dropdown-item" href="../view/listar_emprestimos.php">Ver Todos Empréstimos</a>
                            <a class="dropdown-item" href="../view/valor_multa.php">Ajustar Valor da Multa</a>
                            <a class="dropdown-item" href="../view/listar_meusemprestimos.php">Meus Empréstimos</a>
                            <a class="dropdown-item" href="../view/minhas_multas.php">Minhas Multas</a>
                            <a class="dropdown-item" href="../view/listar_multas.php">Todas Multas</a>
                        </div>
                    </li>
                    <!-- Item Funcionários -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="funcionariosDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Funcionários
                        </a>
                        <div class="dropdown-menu" aria-labelledby="funcionariosDropdown">
                            <a class="dropdown-item" href="../view/cadastrar_funcionarios.php">Cadastrar Funcionários</a>
                            <a class="dropdown-item" href="../view/editar_funcionarios.php">Listar/Alterar Funcionários</a>
                        </div>
                    </li>
                    <!-- Item Doações -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="doacoesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Doações
                        </a>
                        <div class="dropdown-menu" aria-labelledby="doacoesDropdown">
                            <a class="dropdown-item" href="../view/cadastrar_doacao.php">Doar um Livro</a>
                            <a class="dropdown-item" href="../view/listar_doacoes.php">Todas Doações</a>
                            <a class="dropdown-item" href="../view/minhas_doacoes.php">Minhas Doações</a>
                        </div>
                    </li>
                    <!-- Item Listar Vendas -->
                    <li class="nav-item">
                        <a class="nav-link" href="../view/listar_vendas.php">Listar Vendas</a>
                    </li>
                    <!-- Item Contato -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="contatoDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Contato
                        </a>
                        <div class="dropdown-menu" aria-labelledby="doacoesDropdown">
                            <a class="dropdown-item" href="../view/contato.php">Entrar em Contato</a>
                            <a class="dropdown-item" href="../view/listar_contato.php">Listar Contatos</a>
                        </div>
                    </li>
                    <!-- Item Usuarios -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="usuariosDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Usuários
                        </a>
                        <div class="dropdown-menu" aria-labelledby="doacoesDropdown">
                            <a class="dropdown-item" href="../view/cadastrar_usuario.php">Cadastrar novo usuário</a>
                            <a class="dropdown-item" href="../view/listar_usuarios.php">Listar Usuários</a>
                            <a class="dropdown-item" href="../view/atualizar_usuario.php">Alterar meus dados</a>
                        </div>
                    </li>
                </ul>
            </div>
            <!-- Botão de sair com ícone à direita -->
            <form class="form-inline">
                <button class="btn btn-outline-light" type="button" onclick="window.location.href='../app/logout.php'">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </button>
            </form>
        </div>
    </nav>
</header>
