<?php
session_start();
// O caminho deve ser ajustado. Se este arquivo estiver em 'paginas/', use '../conexao.php'
require_once('conexao.php'); 

// --- 1. CAPTURA DO ID E BUSCA NO BANCO DE DADOS ---

$produto_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$produto_db = null;

if ($produto_id > 0) {
    // CONSULTA FINAL CORRIGIDA: Apenas as colunas que existem no BD, buscando pelo ID
    $sql_produto = "SELECT id, nome, preco, descricao, categoria, imagem FROM produtos WHERE id = ?";
    
    // Usando prepared statement para segurança
    if ($stmt = $conexao->prepare($sql_produto)) {
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $produto_db = $resultado->fetch_assoc();
            
            // Definindo valores padrão para campos esperados pelo JavaScript:
            $produto_db['preco_antigo'] = 0.00; 
            $produto_db['especificacoes'] = ['Detalhes não fornecidos', 'Produto de alta qualidade', 'Entrega rápida']; 
            $produto_db['cor'] = 'Cor Padrão';
            $produto_db['vendedor'] = 'Grillo Store';
            $parcelas_count = 12; // Exemplo: 12 parcelas
            $parcelas_valor = (float)$produto_db['preco'] / $parcelas_count;
            
            $produto_db['parcelas_count'] = $parcelas_count;
            $produto_db['parcelas_valor'] = $parcelas_valor;
            
        }
        $stmt->close();
    }
}

// Redireciona se o produto não for encontrado
if (!$produto_db) {
    header('Location: listagem-produtos.php');
    exit;
}

// --- 2. PREPARAÇÃO DOS DADOS PARA O JAVASCRIPT ---

// 🚨 CORREÇÃO AQUI: O caminho é apenas 'uploads/'
$imagem_principal = 'uploads/' . htmlspecialchars($produto_db['imagem'] ?? 'placeholder.jpg'); 

$imagens_galeria = [$imagem_principal];

// Monta o objeto que o JavaScript espera (product)
$dados_js = [
    'id' => $produto_db['id'],
    'name' => $produto_db['nome'],
    'price' => (float)$produto_db['preco'],
    'oldPrice' => (float)$produto_db['preco_antigo'] ?? 0.00,
    'installments' => [
        'count' => (int)$produto_db['parcelas_count'] ?? 1,
        'value' => (float)$produto_db['parcelas_valor'] ?? (float)$produto_db['preco'],
    ],
    'images' => $imagens_galeria, 
    'color' => $produto_db['cor'] ?: 'N/A',
    'specs' => $produto_db['especificacoes'],
    'seller' => $produto_db['vendedor'],
    'slug' => $produto_db['id'] 
];

$produto_json = json_encode($dados_js, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="page-title"><?= htmlspecialchars($produto_db['nome']); ?> - Grillo Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../estilo/style-produto.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="listagem-produtos.php" class="logo">Grillo Store</a>
            <div class="header-actions">
                <button id="darkModeToggle" class="dark-mode-button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-moon"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                </button>
                <div class="cart-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                </div>
            </div>
        </div>
    </header>

    <main class="product-page-container">
                <a href="listagem-produtos.php" class="back-button">
            &larr; Voltar para a página de produtos
        </a>
        
        <div class="product-content-wrapper">
            <section class="product-display">
                <div class="product-images">
                    <div class="thumbnail-gallery" id="thumbnail-gallery">
                                                        </div>
                    <div class="main-image-container">
                        <img src="" alt="Imagem principal do produto" class="main-product-image" id="main-product-image">
                    </div>
                </div>

                <div class="product-info-details">
                    <h1 class="product-title" id="product-title"></h1>
                    <div class="price-section">
                        <p class="price-label">À vista</p>
                        <p class="price-value" id="price-value"></p>
                        <p class="installments" id="installments-text"></p>
                        <a href="#" class="payment-methods-link">ver meios de pagamento</a>
                    </div>

                    <div class="product-specs">
                        <p class="spec-color" id="product-color">Cor: <span class="spec-value" id="product-color-value"></span></p>
                        <h2 class="specs-title">O que você precisa saber sobre este produto</h2>
                        <ul class="specs-list" id="specs-list">
                                                                            </ul>
                    </div>
                </div>
            </section>

            <aside class="purchase-sidebar">
                <div class="sidebar-price-block">
                    <div class="current-price-display">
                        <span class="current-price" id="current-price-sidebar"></span>
                        <label class="price-option-radio">
                            <input type="radio" name="priceOption" checked>
                        </label>
                    </div>
                    <p class="free-shipping">Frete Grátis a cima de R$19</p>
                    <p class="delivery-estimate">Chega entre Quarta-feira e Quinta-feira</p>
                    <div class="installments-display">
                        <span class="installment-amount" id="installment-amount-sidebar"></span>
                        <span class="installment-details" id="installment-details-sidebar"></span>
                        <label class="price-option-radio">
                            <input type="radio" name="priceOption">
                        </label>
                    </div>
                </div>

                <div class="delivery-info-block">
                    <p class="delivery-details-link">Mais detalhes da forma de entrega</p>
                    <p class="stock-status">Estoque Disponível</p>
                    <div class="quantity-selector">
                        <label for="quantity">Quantidade:</label>
                        <select id="quantity">
                            <option value="1">1 Unidade</option>
                            <option value="2">2 Unidades</option>
                            <option value="3">3 Unidades</option>
                        </select>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="buy-now-button">Comprar Agora</button>
                    <button class="add-to-cart-button" id="add-to-cart-button">Adicionar ao Carrinho</button>
                </div>

                <div class="seller-info">
                    <p>Vendido por: <span class="seller-name" id="seller-name"></span></p>
                </div>
            </aside>
        </div>
    </main>

    <div class="cart-flyout" id="cart-flyout">
        <div class="flyout-content-wrapper">
            <div class="flyout-header">
                <h3>Seu Carrinho</h3>
                <button id="close-cart-flyout" class="close-flyout">&times;</button>
            </div>
            <div class="flyout-body">
                <p>O carrinho está vazio.</p>
                </div>
            <div class="flyout-footer">
                <p>Total: R$ 0,00</p>
                <div class="flyout-actions">
                    <a href="#" id="continue-shopping" class="back-button">Continuar Comprando</a>
                    <a href="checkout.php" class="checkout-button">Finalizar Compra</a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // O objeto 'product' que você tinha no JS agora é criado dinamicamente aqui
        const productData = <?= $produto_json; ?>; 
    </script>
    
    <script src="../script/script-produto.js"></script> 
    
    <script>
        // Função do seu JS original, ajustada para usar os dados do BD
        function displayProductDetails(product) {
            document.getElementById('page-title').textContent = `${product.name} - Grillo Store`;
            document.getElementById('product-title').textContent = product.name;
            
            // Formatando o preço
            const formatBRL = (value) => value.toFixed(2).replace('.', ',');
            const formattedPrice = `R$ ${formatBRL(product.price)}`;
            document.getElementById('price-value').textContent = formattedPrice;
            document.getElementById('current-price-sidebar').textContent = formattedPrice;
            
            document.getElementById('seller-name').textContent = product.seller;

            if (product.installments && product.installments.count > 0 && product.installments.value > 0) {
                const installmentText = `ou ${product.installments.count}x de R$ ${formatBRL(product.installments.value)} sem juros`;
                document.getElementById('installments-text').textContent = installmentText;
                
                document.getElementById('installment-amount-sidebar').textContent = `${product.installments.count}x de R$ ${formatBRL(product.installments.value)}`;
                document.getElementById('installment-details-sidebar').textContent = `sem juros`;
            } else {
                 document.getElementById('installments-text').textContent = `Pagamento à vista`;
                 document.getElementById('installment-amount-sidebar').textContent = `1x de R$ ${formatBRL(product.price)}`;
                 document.getElementById('installment-details-sidebar').textContent = `sem juros`;
            }

            if (product.color !== "N/A") {
                document.getElementById('product-color-value').textContent = product.color;
            } else {
                const colorElement = document.getElementById('product-color');
                if (colorElement) {
                    document.getElementById('product-color-value').textContent = 'Cor Variada'; 
                }
            }
            
            const specsList = document.getElementById('specs-list');
            if (specsList) {
                specsList.innerHTML = '';
                product.specs.forEach(spec => {
                    const li = document.createElement('li');
                    li.textContent = spec;
                    specsList.appendChild(li);
                });
            }

            const mainImage = document.getElementById('main-product-image');
            const thumbnailGallery = document.getElementById('thumbnail-gallery');
            
            if (mainImage && thumbnailGallery) {
                mainImage.src = product.images[0];
                mainImage.alt = product.name;

                thumbnailGallery.innerHTML = '';
                product.images.forEach((imgSrc, index) => {
                    const img = document.createElement('img');
                    img.src = imgSrc;
                    img.alt = `Miniatura do Produto ${index + 1}`;
                    img.classList.add('thumbnail-image');
                    if (index === 0) {
                        img.classList.add('active');
                    }
                    thumbnailGallery.appendChild(img);
                });

                document.querySelectorAll('.thumbnail-image').forEach(thumbnail => { 
                    thumbnail.addEventListener('click', () => {
                        const currentActive = document.querySelector('.thumbnail-image.active');
                        if (currentActive) {
                            currentActive.classList.remove('active');
                        }
                        thumbnail.classList.add('active');
                        mainImage.src = thumbnail.src;
                    });
                });
            }
        }

        // Função para redirecionar para checkout (usando o ID dinâmico corrigido)
        function goToCheckout() {
            window.location.href = `checkout.php?produto=${productData.slug}`; 
        }


        // INICIALIZAÇÃO DA PÁGINA
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Carrega os detalhes do produto com os dados do BD
            displayProductDetails(productData);

            // 2. Lógica para os botões Comprar Agora (usando a função goToCheckout)
            const buyNowButton = document.querySelector('.buy-now-button');
            if (buyNowButton) {
                buyNowButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    goToCheckout();
                });
            }
        });
    </script>
    
    <?php include "../componentes/footer.php"; ?>
</body>
</html>