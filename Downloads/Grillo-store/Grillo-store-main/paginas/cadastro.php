<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Grillo Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../estilo/estilo-cadastro.css">
</head>

<body>

    <header class="auth-header">
        <a href="Principal.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <div class="logo-container">
            <i class="fas fa-shopping-cart"></i> Grillo Store
        </div>
        <button id="darkModeToggle" class="btn-dark-mode" aria-label="Alternar modo claro/escuro">
            <i class="fas fa-moon"></i>
        </button>
    </header>

    <main class="auth-page">
        <div class="auth-card">
            <h2>Crie sua conta na DropLoja</h2>
            <p>É rápido e fácil. Preencha seus dados abaixo:</p>
            <form id="cadastroForm" method="post" action="processa_cadastro.php" novalidate>

                <div class="form-group">
                    <label for="nome">Nome Completo:</label>
                    <input type="text" id="nome" name="nome" required placeholder="Seu nome">
                </div>
                <div class="form-group">
                    <label for="dataNascimento">Data de Nascimento:</label>
                    <input type="date" id="dataNascimento" name="dataNascimento" required>
                    <span class="error-message" id="idadeError"></span>
                </div>
                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" required placeholder="seu.email@exemplo.com">
                    <span class="error-message" id="emailError"></span>
                </div>
                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <div class="password-wrapper">
                        <input type="password" id="senha" name="senha" required placeholder="Sua senha">
                        <button type="button" class="toggle-senha" id="olho" aria-label="Mostrar/ocultar senha">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small>A senha deve ter no mínimo 8 caracteres, incluindo letras maiúsculas, minúsculas, números e um caractere especial.</small>
                    <span class="error-message" id="senhaError"></span>
                </div>
                <div class="form-group">
                    <label for="confirmaSenha">Confirmar Senha:</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirmaSenha" name="confirmaSenha" required placeholder="Confirme sua senha">
                        <button type="button" class="toggle-senha" id="olho-confirma" aria-label="Mostrar/ocultar senha">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <span class="error-message" id="confirmaSenhaError"></span>
                </div>
                <div class="form-group">
                    <label for="cpf">CPF:</label>
                    <input type="text" id="cpf" name="cpf" maxlength="14" required placeholder="000.000.000-00">
                    <span class="error-message" id="cpfError"></span>
                </div>
                <div class="form-group">
                    <label for="telefone">Telefone:</label>
                    <input type="tel" id="telefone" name="telefone" maxlength="15" required placeholder="(00) 00000-0000">
                    <span class="error-message" id="telefoneError"></span>
                </div>
                <div class="form-buttons">
                    <button type="button" id="limparBtn" class="btn btn-secondary">Limpar</button>
                    <button type="submit" id="enviarBtn" class="btn btn-primary">Enviar</button>
                </div>
            </form>
            <p class="auth-link">Já tem uma conta? <a href="#">Faça Login</a></p>
        </div>
    </main>

    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-column">
                <h3>Links Úteis</h3>
                <ul>
                    <li><a href="#">Sobre Nós</a></li>
                    <li><a href="#">Contato</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>DropLoja</h3>
                <p>&copy; 2023 DropLoja. Todos os direitos reservados.</p>
            </div>
            <div class="footer-column">
                <h3>Redes Sociais</h3>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="../script/script-cadastro.js"></script>
</body>

</html>