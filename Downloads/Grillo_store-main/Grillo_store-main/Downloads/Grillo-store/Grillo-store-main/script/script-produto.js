//
// LÓGICA DA PÁGINA DE PRODUTO INDIVIDUAL
// Este script carrega os detalhes de um único produto.
//

// ====================================
// 1. DADOS DO PRODUTO (Altere apenas este bloco em cada arquivo)
// ====================================
const product = {
    id: 1,
    name: "Smartphone X Pro 128GB",
    price: 2500.00,
    oldPrice: 2999.00,
    installments: { count: 12, value: 250.00 },
    color: "Preto",
    images: [
        "https://via.placeholder.com/450x350?text=Smartphone+Preto", 
        "https://via.placeholder.com/60x60?text=SM1", 
        "https://via.placeholder.com/60x60?text=SM2", 
        "https://via.placeholder.com/60x60?text=SM3"
    ],
    specs: [
        "Memória interna de 128 GB.",
        "Processador Octa-Core de 2.8GHz.",
        "Câmera traseira de 64MP.",
        "Bateria de longa duração de 5000mAh."
    ],
    seller: "Grillo Store"
};


// ====================================
// 2. LÓGICA DE CARREGAMENTO
// ====================================

document.addEventListener('DOMContentLoaded', () => {
    // A função é chamada diretamente, pois o 'product' já está disponível.
    displayProductDetails(product);

    // Adiciona evento de adicionar ao carrinho
    document.getElementById('add-to-cart-button').addEventListener('click', async () => {
        await addToCart(product.id);
    });
});

/**
 * ⭐️ Função adaptada para simular a adição do produto ao carrinho
 * em um ambiente PHP e redirecionar para a página do carrinho.
 * @param {number} productId O ID do produto a ser adicionado.
 */
async function addToCart(productId) {
    console.log(`Iniciando adição do produto ID ${productId} ao carrinho...`);
    
    // Simulação de chamada AJAX para um endpoint PHP.
    // Em um ambiente real, você usaria fetch() aqui:
    // const response = await fetch('api/adicionarAoCarrinho.php', {
    //     method: 'POST',
    //     headers: { 'Content-Type': 'application/json' },
    //     body: JSON.stringify({ product_id: productId, quantity: 1 })
    // });

    // if (response.ok) {
        alert(`O produto "${product.name}" foi adicionado ao seu carrinho! Redirecionando...`);
        // ⭐️ Redireciona para a nova página de carrinho baseada em PHP
        window.location.href = 'carrinho.php';
    // } else {
    //     alert('Houve um erro ao adicionar o produto ao carrinho. Tente novamente.');
    // }
}

function displayProductDetails(product) {
    // Atualiza o título da página
    document.getElementById('page-title').textContent = `${product.name} - Grillo Store`;

    // Atualiza os elementos da página com os dados do produto
    document.getElementById('product-title').textContent = product.name;
    document.getElementById('price-value').textContent = `R$ ${product.price.toFixed(2).replace('.', ',')}`;
    document.getElementById('current-price-sidebar').textContent = `R$ ${product.price.toFixed(2).replace('.', ',')}`;
    document.getElementById('seller-name').textContent = product.seller;

    if (product.installments) {
        const installmentText = `${product.installments.count}x R$${product.installments.value.toFixed(2).replace('.', ',')} com juros`;
        document.getElementById('installments-text').textContent = installmentText;
        document.getElementById('installment-amount-sidebar').textContent = `R$${(product.installments.value * product.installments.count).toFixed(2).replace('.', ',')}`;
        document.getElementById('installment-details-sidebar').textContent = `${product.installments.count}x R$${product.installments.value.toFixed(2).replace('.', ',')}`;
    }

    if (product.color !== "N/A") {
        document.getElementById('product-color-value').textContent = product.color;
    } else {
        const colorElement = document.getElementById('product-color');
        if (colorElement) {
            colorElement.style.display = 'none';
        }
    }
    
    // Preenche a lista de especificações
    const specsList = document.getElementById('specs-list');
    if (specsList) {
        specsList.innerHTML = '';
        product.specs.forEach(spec => {
            const li = document.createElement('li');
            li.textContent = spec;
            specsList.appendChild(li);
        });
    }

    // Preenche a galeria de imagens
    const mainImage = document.getElementById('main-product-image');
    const thumbnailGallery = document.getElementById('thumbnail-gallery');
    
    if (mainImage && thumbnailGallery) {
        // Define a imagem principal como a primeira da lista
        mainImage.src = product.images[0];
        mainImage.alt = product.name;

        // Limpa miniaturas existentes
        thumbnailGallery.innerHTML = '';
        
        // Cria novas miniaturas
        product.images.forEach((imgSrc, index) => {
            const img = document.createElement('img');
            img.src = imgSrc;
            img.alt = `Miniatura do Produto ${index + 1}`;
            img.classList.add('thumbnail');
            if (index === 0) {
                img.classList.add('active');
            }
            thumbnailGallery.appendChild(img);
        });

        // Adiciona evento de clique para a galeria de imagens
        document.querySelectorAll('.thumbnail').forEach(thumbnail => {
            thumbnail.addEventListener('click', () => {
                document.querySelector('.thumbnail.active').classList.remove('active');
                thumbnail.classList.add('active');
                mainImage.src = thumbnail.src;
            });
        });
    }
}


// ====================================
// 3. LÓGICA DO MODO ESCURO
// ====================================

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


// ====================================
// 4. LÓGICA DA GALERIA DE IMAGENS E ZOOM
// ====================================
const mainProductImage = document.getElementById('main-product-image');

if (mainProductImage) {
    mainProductImage.addEventListener('mousemove', (e) => {
        const rect = e.target.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const xPercent = (x / rect.width) * 100;
        const yPercent = (y / rect.height) * 100;
        
        mainProductImage.style.transformOrigin = `${xPercent}% ${yPercent}%`;
    });

    mainProductImage.addEventListener('mouseenter', () => {
        mainProductImage.classList.add('zoomed');
    });

    mainProductImage.addEventListener('mouseleave', () => {
        mainProductImage.classList.remove('zoomed');
    });
}