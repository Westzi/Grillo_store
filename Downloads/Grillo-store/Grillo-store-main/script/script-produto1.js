//
// LÓGICA DA PÁGINA DE PRODUTO INDIVIDUAL
//
const product = {
    id: 1,
    name: "Kit Camiseta Básica Masculina c/ 5 Peças - Básicos",
    price: 47.49,
    oldPrice: 0.00,
    installments: { count: 2, value: 25.00 },
    color: "Preto e Branco (Cores Variadas)",
    images: [
        "../imagens-produtos/camisa1.jpg", 
        "../imagens-produtos/camisa2.jpg", 
        "../imagens-produtos/camisa3.jpg", 
        "../imagens-produtos/camisa4.jpg",
        "../imagens-produtos/camisa5.jpg"
    ],
    specs: [
        "Marca: Básicos",
        "Material: Algodão",
        "Estilo da Peça: Básica / Kit com 5 Unidades",
        "Gola: Careca",
        "Tamanho: P"
    ],
    seller: "Básicos",
    // ⭐️ CORREÇÃO: Slug adicionado
    slug: "produto-1-camiseta-basica.php"
};

// ====================================
// FUNÇÕES DE EXIBIÇÃO E GALERIA
// ====================================

function displayProductDetails(product) {
    document.getElementById('page-title').textContent = `${product.name} - Grillo Store`;
    document.getElementById('product-title').textContent = product.name;
    document.getElementById('price-value').textContent = `R$ ${product.price.toFixed(2).replace('.', ',')}`;
    document.getElementById('current-price-sidebar').textContent = `R$ ${product.price.toFixed(2).replace('.', ',')}`;
    document.getElementById('seller-name').textContent = product.seller;

    if (product.installments) {
        const installmentText = `ou ${product.installments.count}x de R$ ${product.installments.value.toFixed(2).replace('.', ',')} sem juros`;
        document.getElementById('installments-text').textContent = installmentText;
        
        document.getElementById('installment-amount-sidebar').textContent = `${product.installments.count}x de R$ ${product.installments.value.toFixed(2).replace('.', ',')}`;
        document.getElementById('installment-details-sidebar').textContent = `sem juros`;
    }

    if (product.color !== "N/A") {
        document.getElementById('product-color-value').textContent = product.color;
    } else {
        const colorElement = document.getElementById('product-color');
        if (colorElement) {
            colorElement.style.display = 'none';
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

// ====================================
// FUNÇÃO PARA REDIRECIONAR PARA CHECKOUT
// ====================================

function goToCheckout() {
    // ⭐️ CORREÇÃO: Redireciona para checkout com o slug do produto
    window.location.href = `checkout.php?produto=${product.slug}`;
}

// ====================================
// INICIALIZAÇÃO E EVENTOS
// ====================================

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Carrega os detalhes do produto
    displayProductDetails(product);

    // 2. Lógica do botão "Adicionar ao Carrinho"
    const addToCartButton = document.getElementById('add-to-cart-button');
    if (addToCartButton) {
        addToCartButton.addEventListener('click', () => {
            // Ação temporária: Apenas um console.log para indicar que foi clicado.
            console.log(`Botão 'Adicionar ao Carrinho' clicado para o produto ID ${product.id}. Nenhuma ação de carrinho foi executada.`);
            
            // Se precisar de alguma resposta visual mínima para testar:
            // alert("A função de carrinho está desativada."); 
        });
    }

    // ⭐️ CORREÇÃO: Botão "Comprar Agora" redireciona para checkout com o produto
    const buyNowButton = document.querySelector('.buy-now-button');
    if (buyNowButton) {
        // Remove qualquer evento anterior e usa a função goToCheckout
        buyNowButton.replaceWith(buyNowButton.cloneNode(true));
        const newBuyNowButton = document.querySelector('.buy-now-button');
        
        newBuyNowButton.addEventListener('click', function(e) {
            e.preventDefault();
            goToCheckout();
        });
    }

    // 3. Lógica para o ícone do Carrinho (Modal Centralizado/Flyout)
    const cartIcon = document.querySelector('.cart-icon'); 
    const cartFlyout = document.getElementById('cart-flyout');
    const closeFlyoutButton = document.getElementById('close-cart-flyout');
    const continueShoppingButton = document.getElementById('continue-shopping'); 

    function openFlyout() {
        if (cartFlyout) {
            cartFlyout.classList.add('visible');
            document.body.style.overflow = 'hidden'; // Bloqueia scroll do body
        }
    }

    function closeFlyout() {
        if (cartFlyout) {
            cartFlyout.classList.remove('visible');
            
            setTimeout(() => {
                document.body.style.overflow = '';
            }, 300);
        }
    }

    if (cartIcon) {
        cartIcon.addEventListener('click', openFlyout);
    }

    if (closeFlyoutButton) {
        closeFlyoutButton.addEventListener('click', closeFlyout);
    }
    
    if (continueShoppingButton) {
        continueShoppingButton.addEventListener('click', closeFlyout);
    }
    
    document.addEventListener('click', (event) => {
        const isVisible = cartFlyout && cartFlyout.classList.contains('visible');
        const cartElements = [cartIcon, cartFlyout].filter(Boolean);
        
        const clickedOnOverlay = event.target === cartFlyout; 

        if (isVisible && clickedOnOverlay) {
            // Garante que feche ao clicar no overlay, mas não no ícone
            if (!cartElements.some(el => el.contains(event.target))) {
                closeFlyout();
            }
        }
    });

    // 4. LÓGICA DO MODO ESCURO
    
    const body = document.body;
    const darkModeToggle = document.getElementById('darkModeToggle');
    const sunIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sun"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>`;
    const moonIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-moon"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>`;

    function enableDarkMode() {
        body.classList.add('dark-mode');
        if (darkModeToggle) {
            darkModeToggle.innerHTML = sunIcon;
        }
        localStorage.setItem('darkMode', 'enabled');
    }

    function disableDarkMode() {
        body.classList.remove('dark-mode');
        if (darkModeToggle) {
            darkModeToggle.innerHTML = moonIcon;
        }
        localStorage.setItem('darkMode', 'disabled');
    }

    if (localStorage.getItem('darkMode') === 'enabled') {
        enableDarkMode();
    } else {
        if (darkModeToggle) {
            darkModeToggle.innerHTML = moonIcon;
        }
    }

    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', () => {
            if (body.classList.contains('dark-mode')) {
                disableDarkMode();
            } else {
                enableDarkMode();
            }
        });
    }
});