<?php
session_start();
if (!isset($_SESSION['usuario_nome'])) {
    header("Location: login.php");
    exit();
}
$usuario_nome = $_SESSION['usuario_nome'];
$usuario_email = $_SESSION['usuario_email'] ?? '';
$usuario_cpf = $_SESSION['usuario_cpf'] ?? '';
$usuario_telefone = $_SESSION['usuario_telefone'] ?? '';
$usuario_nascimento = $_SESSION['usuario_data_nascimento'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta - Grillo Store</title>
     <link rel="stylesheet" href="../estilo/estilo-minha-conta.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
        <h2>Olá, <?php echo htmlspecialchars($usuario_nome); ?> 👋</h2>
        <p>Gerencie suas informações e acompanhe seus pedidos.</p>

        <div class="account-buttons">
            <button class="btn-section active" data-section="dados"><i class="fas fa-user"></i> Meus Dados</button>
            <button class="btn-section" data-section="compras"><i class="fas fa-shopping-bag"></i> Minhas Compras</button>
            <button class="btn-section" data-section="enderecos"><i class="fas fa-map-marker-alt"></i> Endereços</button>
        </div>

        <div class="account-content">
            <!-- Seção: Meus Dados -->
            <div id="dados" class="section active">
                <h3>Meus Dados</h3>
                <form id="formDados" method="post" action="atualiza_dados.php">
                    <div class="form-group">
                        <label for="nome">Nome Completo:</label>
                        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario_nome); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario_email); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="cpf">CPF:</label>
                        <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($usuario_cpf); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="telefone">Telefone:</label>
                        <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($usuario_telefone); ?>">
                    </div>
                    <div class="form-group">
                        <label for="data_nascimento">Data de Nascimento:</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($usuario_nascimento); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </form>
            </div>

            <!-- Seção: Minhas Compras -->
            <div id="compras" class="section">
                <h3>Minhas Compras</h3>
                <p>Aqui aparecerão todos os seus pedidos recentes.</p>
                <div class="placeholder">
                    <i class="fas fa-box-open"></i>
                    <p>Você ainda não realizou nenhuma compra.</p>
                </div>
            </div>

            <!-- Seção: Endereços -->
            <div id="enderecos" class="section">
                <h3>Endereços</h3>
                <p>Gerencie seus endereços de entrega.</p>
                <div class="placeholder">
                    <i class="fas fa-map-marked-alt"></i>
                    <p>Nenhum endereço cadastrado.</p>
                    <button class="btn btn-primary">Adicionar Endereço</button>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="../script/script-minhaconta.js"></script>
   <?php include "../componentes/footer.php"; ?>

</body>
</html>
