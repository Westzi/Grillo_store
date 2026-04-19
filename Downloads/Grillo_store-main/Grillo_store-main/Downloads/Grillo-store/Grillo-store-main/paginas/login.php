<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Grillo Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../estilo/estilo-login.css">
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
            <h2>Faça seu login</h2>

            <!-- MENSAGENS DE ERRO -->
            <?php
            session_start();
            if (isset($_SESSION['erro_login'])) {
                echo '<div class="error-message" style="color: red; background: #ffe6e6; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">' . $_SESSION['erro_login'] . '</div>';
                unset($_SESSION['erro_login']);
            }
            ?>

            <form action="processa_login.php" method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <br><br>
                <div class="password-wrapper" style="position: relative; display: flex; align-items: center; width: 100%;">
                    <input type="password" name="senha" id="senhaLogin" placeholder="Senha" required style="width: 100%; padding-right: 35px; box-sizing: border-box;">
                    <button type="button" class="toggle-senha" id="olhoLogin" aria-label="Mostrar/ocultar senha" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: transparent; border: none; cursor: pointer; font-size: 1rem; color: #555; display: flex; align-items: center; justify-content: center; padding: 0;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <br><br>
                <input class="inputSubmit" type="submit" name="submit" value="Entrar">
            </form>

            <div class="auth-link">
                Não tem uma conta? <a href="cadastro.php">Cadastre-se</a>
            </div>
        </div>
    </main>

    
    <?php include "../componentes/footer.php"; ?>


    <script src="../script/script-login.js"></script>
    <script>window.isUserLoggedIn = <?= isset($_SESSION['usuario_id'])? 'true':'false' ?>;</script>
    <script>
        const form = document.getElementById('cep-form');
        form.addEventListener('submit', (e) => {
            if (!window.isUserLoggedIn) {
                e.preventDefault();
                // Redireciona para login; envia redirect para voltar depois
                window.location.href = 'login.php?redirect=Principal.php';
            }
        });
    </script>
</body>

</html>