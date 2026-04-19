<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="page-title">Detalhes do Produto - Grillo Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../estilo/style-produto.css">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="listagem.html" class="logo">Grillo Store</a>
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
        <a href="listagem.html" class="back-button">
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

    <script src="../script/script-produto.js"></script>
</body>
</html>