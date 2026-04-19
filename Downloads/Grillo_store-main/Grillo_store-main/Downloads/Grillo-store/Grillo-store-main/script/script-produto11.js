const product = {
    id: 11,
    name: "Headset Gamer Evolut EG307 Rival, LED, Drivers 40mm, USB, P3, Preto",
    price: 46.99, 
    oldPrice: 0.00,
    installments: { count: 2, value: 27.64, total: 55.28, isInterest: true }, 
    color: "Preto",
    images: [
        "../imagens-produtos/fone1.jpg", 
        "../imagens-produtos/fone2.jpg", 
        "../imagens-produtos/fone3.jpg",
        "../imagens-produtos/fone5.jpg"
    ],
    specs: [
        "Marca: Evolut",
        "Modelo: EG-307 Rival",
        "Performance Sonora: Drivers de 40mm para áudio nítido e potente.",
        "Conforto: Headband totalmente adaptável, ideal para sessões prolongadas.",
        "Funcionalidades Especiais: Design over-ear com almofadas macias que isolam ruídos externos.",
        "Conexão: USB e P3"
    ],
    seller: "Grillo Store"
};


/// =====================================================
// FUNÇÕES DE EXIBIÇÃO DO PRODUTO (REUTILIZADAS)
// =====================================================
function displayProductDetails(product) {
    document.getElementById('page-title').textContent = `${product.name} - Grillo Store`;
    document.getElementById('product-title').textContent = product.name;

    const formatter = new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
        minimumFractionDigits: 2
    });
    const formattedPrice = formatter.format(product.price);

    document.getElementById('price-value').textContent = formattedPrice;
    document.getElementById('current-price-sidebar').textContent = formattedPrice;
    document.getElementById('seller-name').textContent = product.seller;

    if (product.installments) {
        // Tolerância de 1% para erro de arredondamento em casos sem juros
        const hasInterest = product.installments.isInterest || (product.installments.total > (product.price * 1.01)); 
        const installmentType = hasInterest ? 'com juros' : 'sem juros';
        const installmentValueFormatted = product.installments.value.toFixed(2).replace('.', ',');
        let installmentText = `ou ${product.installments.count}x de R$ ${installmentValueFormatted} ${installmentType}`;
        
        if (hasInterest && product.installments.total) {
            const totalFormatted = product.installments.total.toFixed(2).replace('.', ',');
            installmentText += ` (Total: R$ ${totalFormatted})`;
        }
        
        document.getElementById('installments-text').textContent = installmentText;
        document.getElementById('installment-amount-sidebar').textContent = `${product.installments.count}x de R$ ${installmentValueFormatted}`;
        document.getElementById('installment-details-sidebar').textContent = installmentType; 
    }

    if (product.color && product.color !== "N/A") {
        document.getElementById('product-color-value').textContent = product.color;
    } else {
        const colorElement = document.getElementById('product-color');
        if (colorElement) colorElement.style.display = 'none';
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
            if (index === 0) img.classList.add('active');
            thumbnailGallery.appendChild(img);
        });

        document.querySelectorAll('.thumbnail-image').forEach(thumbnail => { 
            thumbnail.addEventListener('click', () => {
                const currentActive = document.querySelector('.thumbnail-image.active');
                if (currentActive) currentActive.classList.remove('active');
                thumbnail.classList.add('active');
                mainImage.src = thumbnail.src;
            });
        });
    }
}

// =====================================================
// NOTIFICAÇÃO E FLIGHT OUT (REUTILIZADAS)
// =====================================================
function showNotification(message) {
    const popup = document.getElementById('notification-popup');
    if (popup) {
        popup.textContent = message;
        popup.classList.remove('removal'); 
        popup.classList.add('success'); 
        popup.classList.add('visible');
        setTimeout(() => {
            popup.classList.remove('visible');
            popup.classList.remove('success'); 
            popup.classList.remove('removal'); 
        }, 3000); 
    }
}

function gerenciarNotificacaoPHP() {
    const popup = document.getElementById('notification-popup');
    if (popup && popup.classList.contains('visible')) {
        setTimeout(() => {
            popup.classList.remove('visible'); 
            popup.classList.remove('success'); 
            popup.classList.remove('removal'); 
        }, 3000); 
    }
}

// =====================================================
// REMOÇÃO DE ITEM VIA AJAX (REUTILIZADA)
// =====================================================
function removerItemComAjax(produtoId) {
    if (!produtoId) return;
    const formData = new FormData();
    formData.append('produto_id', produtoId);

    fetch('remover_carrinho.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Remove o item visualmente
            const item = document.getElementById(`flyout-item-${produtoId}`); 
            if (item) item.remove();

            // Lógica para atualizar o total e status de carrinho vazio
            const flyoutTotal = document.querySelector('.flyout-total strong');
            if (flyoutTotal) {
                const totalFormatado = data.total_carrinho
                    ? Number(data.total_carrinho).toFixed(2).replace('.', ',')
                    : '0,00';
                flyoutTotal.textContent = `R$ ${totalFormatado}`;
            }

            if (document.querySelectorAll('.flyout-item').length === 0) {
                const body = document.querySelector('.flyout-body');
                if (body) body.innerHTML = "<p style='text-align: center; padding: 20px; color: var(--text-color);'>Seu carrinho está vazio.</p>";
                const footer = document.querySelector('.flyout-footer .flyout-actions');
                if (footer) footer.style.display = 'none';
            }
        } else {
            alert('Erro ao remover o produto: ' + data.message);
        }
    })
    .catch(err => {
        console.error('Erro AJAX:', err);
        alert('Erro ao tentar remover o item. Verifique a conexão.');
    });
}

// =====================================================
// INICIALIZAÇÃO DE EVENTOS (REUTILIZADA)
// =====================================================
document.addEventListener('DOMContentLoaded', () => {

    displayProductDetails(product);
    gerenciarNotificacaoPHP();

    // Lógica de Checkout / Compra Rápida
    const buyNowButton = document.querySelector('.buy-now-button');
    if (buyNowButton) {
        buyNowButton.addEventListener('click', (e) => {
            e.preventDefault();
            showNotification(`Iniciando Compra Rápida para "${product.name}"...`);
            setTimeout(() => window.location.href = 'checkout.php', 500);
        });
    }

    // Flyout carrinho
    const cartIcon = document.querySelector('.cart-icon'); 
    const cartFlyout = document.getElementById('cart-flyout');
    const closeFlyoutButton = document.getElementById('close-cart-flyout');
    const continueShoppingButton = document.getElementById('continue-shopping'); 

    function openFlyout() {
        if (cartFlyout) {
            cartFlyout.classList.add('visible');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeFlyout() {
        if (cartFlyout) {
            cartFlyout.classList.remove('visible');
            setTimeout(() => document.body.style.overflow = '', 300);
        }
    }

    if (cartIcon) cartIcon.addEventListener('click', openFlyout);
    if (closeFlyoutButton) closeFlyoutButton.addEventListener('click', closeFlyout);
    if (continueShoppingButton) continueShoppingButton.addEventListener('click', closeFlyout);

    // Delegação para remover itens e fechar flyout ao clicar no overlay
    document.addEventListener('click', (event) => {
        const removeButton = event.target.closest('.remove-item-button');
        if (removeButton) {
            event.preventDefault();
            const produtoId = removeButton.dataset.productId;
            if (produtoId) removerItemComAjax(produtoId);
        }

        if (cartFlyout && cartFlyout.classList.contains('visible') && event.target === cartFlyout) {
            closeFlyout();
        }
    });

    // Dark Mode
    const bodyElement = document.body;
    const darkModeButton = document.getElementById('darkModeToggle');
    const sunIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sun"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>`;
    const moonIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-moon"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>`;

    function enableDarkMode() {
        bodyElement.classList.add('dark-mode');
        if (darkModeButton) darkModeButton.innerHTML = sunIcon;
        localStorage.setItem('darkMode', 'enabled');
    }

    function disableDarkMode() {
        bodyElement.classList.remove('dark-mode');
        if (darkModeButton) darkModeButton.innerHTML = moonIcon;
        localStorage.setItem('darkMode', 'disabled');
    }

    if (localStorage.getItem('darkMode') === 'enabled') enableDarkMode();
    else if (darkModeButton) darkModeButton.innerHTML = moonIcon;

    if (darkModeButton) {
        darkModeButton.addEventListener('click', () => {
            if (bodyElement.classList.contains('dark-mode')) disableDarkMode();
            else enableDarkMode();
        });
    }

});