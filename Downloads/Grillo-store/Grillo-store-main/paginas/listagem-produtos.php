<?php
session_start();
// Necessário para conectar ao banco de dados (BD)
require_once('conexao.php'); 

// --- 1. BUSCA DINÂMICA DE PRODUTOS NO BANCO DE DADOS ---
$produtos_bd = [];
$sql = "SELECT id, nome, preco, categoria, imagem, descricao FROM produtos ORDER BY id DESC";
$resultado = $conexao->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    while ($linha = $resultado->fetch_assoc()) {
        $produtos_bd[] = $linha; // Popula o array com dados do BD
    }
}
// ---------------------------------------------------------


$abrir_carrinho = false;
if (isset($_SESSION['carrinho_adicionado']) && $_SESSION['carrinho_adicionado']) {
    $abrir_carrinho = true;
    unset($_SESSION['carrinho_adicionado']);
}

function formatar_preco($preco)
{
    // Garante que o preço seja um número para evitar avisos
    $preco = is_numeric($preco) ? $preco : 0; 
    return number_format($preco, 2, ',', '.');
}

function calcular_total_carrinho()
{
    $total = 0;
    if (isset($_SESSION['carrinho']) && is_array($_SESSION['carrinho'])) {
        foreach ($_SESSION['carrinho'] as $item) {
            $total += ($item['preco'] * $item['quantidade']);
        }
    }
    return $total;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - Grilo Store</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="../estilo/style-listagem.css">
    <link rel="icon" href="../imagem-grilo/grilo.png" type="image/x-icon">

    <script>
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'block';
            }
        }
    </script>
</head>

<body>

    <header>
        <div class="top-bar">
            <div class="top-bar-content">
                <div class="left-text">
                    <p>Entrega rápida para todo o Brasil!</p>
                </div>
                <div class="right-text">
                    <p>Fale Conosco <i class="fas fa-phone"></i></p>
                </div>
            </div>
        </div>
        <nav class="navbar">
            <div class="nav-container">
                <div class="grilo-logo">
                    <img src="../imagem-grilo/grilo.png"> Grillo Store
                </div>
                <a href="Principal.php" class="btn btn-secondary"> Voltar </a>

                <div class="search-bar">
                    <input type="text" placeholder="Pesquisar produtos..." id="searchInput">
                    <i class="fas fa-search"></i>
                </div>

                <ul class="nav-links">
                    <?php if (isset($_SESSION['usuario_nome'])): ?>
                        <li><a href="#"><i class="fas fa-user"></i> Olá, <?= $_SESSION['usuario_nome']; ?></a></li>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                    <?php else: ?>
                        <li><a href="#" rel="account" id="login-btn"><i class="fas fa-user"></i> Minha Conta</a></li>
                        <li><a href="cadastro.php" class="btn btn-primary">Cadastro</a></li>
                        <li><a href="#" class="btn btn-secondary" id="login-btn">Login</a></li>
                    <?php endif; ?>

                    <?php
                    $quantidade_carrinho = 0;
                    if (isset($_SESSION['carrinho'])) {
                        foreach ($_SESSION['carrinho'] as $item) {
                            $quantidade_carrinho += $item['quantidade'];
                        }
                    }
                    ?>
                    <li class="cart-link">
                        <a href="#" id="cart-icon" onclick="openModal('cartModal')">
                            <i class="fas fa-shopping-cart"></i> Carrinho
                            <?php if ($quantidade_carrinho > 0): ?>
                                <span class="cart-badge" id="cart-badge-count"><?= $quantidade_carrinho; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="cep-link"><a href="#" id="header-cep-btn" onclick="openModal('cepModal')"><i class="fas fa-map-marker-alt"></i> Inserir CEP</a></li>

                    <li class="darkmode-container">
                        <button id="darkModeToggle" class="btn-dark-mode"><i class="fas fa-moon"></i></button>
                    </li>
                </ul>
            </div>
        </nav>
    </header>


    <main>
        <section class="product-listing">
            <div class="product-listing-header">
                <h2>Todos os Produtos</h2>
                <p>Confira nossa seleção de produtos de alta qualidade.</p>
            </div>

            <div class="product-grid" id="productGrid">
                
                <?php if (empty($produtos_bd)): ?>
                    <p style="text-align: center; width: 100%; margin-top: 50px;">
                        Nenhum produto encontrado. Por favor, adicione produtos no painel de administrador.
                    </p>
                <?php else: ?>
                    <?php foreach ($produtos_bd as $produto): 
                        $caminho_imagem = 'uploads/' . htmlspecialchars($produto['imagem'] ?? 'placeholder.jpg');
                    ?>
                        <a href="detalhe_produto.php?id=<?= $produto['id']; ?>" class="product-card-link" data-id="<?= $produto['id']; ?>">
                            <div class="product-card" data-category="<?= htmlspecialchars($produto['categoria']); ?>">
                                <button class="wishlist-btn"><i class="far fa-heart"></i></button>
                                <img src="<?= $caminho_imagem; ?>" alt="<?= htmlspecialchars($produto['nome']); ?>" class="product-image">
                                <div class="product-info">
                                    <p class="product-category"><?= htmlspecialchars($produto['categoria']); ?></p>
                                    <h3 class="product-title"><?= htmlspecialchars($produto['nome']); ?></h3>
                                    <div class="product-rating">
                                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i>
                                        <span>(4.0)</span>
                                    </div>
                                    <p class="product-price">R$ <?= formatar_preco($produto['preco']); ?></p>

                                    <form action="adicionar_carrinho.php" method="POST" onclick="event.stopPropagation();">
                                        <input type="hidden" name="produto_id" value="<?= $produto['id']; ?>">
                                        <input type="hidden" name="produto_nome" value="<?= htmlspecialchars($produto['nome']); ?>">
                                        <input type="hidden" name="produto_preco" value="<?= $produto['preco']; ?>">
                                        <input type="hidden" name="produto_quantidade" value="1">
                                        <button type="submit" class="btn-add-to-cart">Adicionar ao Carrinho</button>
                                    </form>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div> 
        </section>
    </main>

    <div id="cep-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="document.getElementById('cep-modal').style.display='none'">&times;</span>
            <h2>Calcular Frete</h2>
            <form>
                <input type="text" id="cepInput" placeholder="Digite seu CEP" maxlength="9" required>
                <button type="submit" id="checkCepBtn">Verificar</button>
            </form>
        </div>
    </div>

    <div id="login-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="document.getElementById('login-modal').style.display='none'">&times;</span>
            <h2>Entrar na Conta</h2>
            <form id="login-form">
                <input type="email" id="email" placeholder="Seu E-mail" required>
                <input type="password" id="password" placeholder="Sua Senha" required>
                <button type="submit">Entrar</button>
            </form>
        </div>
    </div>

    <div id="cartModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="document.getElementById('cartModal').style.display='none'">&times;</span>
            <h2>Seu Carrinho</h2>
            <div id="cart-items-container">
                </div>
            <p>Total: <span id="cart-total">R$ <?= formatar_preco(calcular_total_carrinho()); ?></span></p>
            <button class="btn-checkout">Finalizar Compra</button>
        </div>
    </div>
    
    <?php include "../componentes/footer.php"; ?>
    <script src="../script/script-listagem.js"></script>


</body>

</html>