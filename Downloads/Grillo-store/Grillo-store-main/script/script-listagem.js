document.addEventListener('DOMContentLoaded', () => {

    // ====================================
    // FUNÇÕES AUXILIARES (Modal e Carrinho)
    // ====================================

    // Função robusta para configurar modais
    function setupModal(triggerSelector, modalId, onOpenCallback = () => {}) {
        const triggerElement = document.querySelector(triggerSelector);
        const modalElement = document.getElementById(modalId);
        
        // 🚨 Proteção: Se o gatilho ou o modal não existirem, o código para aqui.
        if (!triggerElement || !modalElement) {
            return; 
        }

        const closeButton = modalElement.querySelector('.close-btn'); 

        // Abre o modal
        triggerElement.addEventListener('click', (e) => {
            e.preventDefault();
            onOpenCallback(); 
            modalElement.style.display = 'block';
        });

        // Fecha o modal pelo botão
        if (closeButton) {
            closeButton.addEventListener('click', () => {
                modalElement.style.display = 'none';
            });
        }

        // Fecha o modal clicando fora
        window.addEventListener('click', (e) => {
            if (e.target === modalElement) {
                modalElement.style.display = 'none';
            }
        });
    }

    // Função auxiliar para remover item via AJAX (mantida como está)
    function removerItemDoCarrinho(produtoId, buttonElement) {
        fetch('remover_carrinho.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `produto_id=${produtoId}` 
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const itemParaRemover = buttonElement.closest('.cart-item');
                if (itemParaRemover) {
                    itemParaRemover.remove();
                }
                
                if (document.getElementById('cart-badge-count') && data.nova_quantidade !== undefined) {
                    document.getElementById('cart-badge-count').textContent = data.nova_quantidade;
                }
                
                console.log(`Produto ${produtoId} removido com sucesso.`);
            } else {
                console.error('Erro ao remover:', data.message);
                alert(`Erro ao remover o produto ${produtoId}. Mensagem do servidor: ${data.message || 'Erro desconhecido'}`);
            }
        })
        .catch(error => {
            console.error('Erro na comunicação com o servidor:', error);
            alert('Houve um erro de rede ao tentar remover o produto.');
        });
    }


    // ====================================
    // 1. MODO ESCURO (DARK MODE) - AGORA CLICÁVEL!
    // ====================================
    const body = document.body;
    const darkModeToggle = document.getElementById('darkModeToggle');

    // Ícones SVG
    const sunIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sun"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>`;
    const moonIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-moon"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>`;

    function enableDarkMode() {
        body.classList.add('dark-mode');
        if (darkModeToggle) darkModeToggle.innerHTML = sunIcon;
        localStorage.setItem('darkMode', 'enabled');
    }

    function disableDarkMode() {
        body.classList.remove('dark-mode');
        if (darkModeToggle) darkModeToggle.innerHTML = moonIcon;
        localStorage.setItem('darkMode', 'disabled');
    }

    if (localStorage.getItem('darkMode') === 'enabled') {
        enableDarkMode();
    } else {
        if (darkModeToggle) darkModeToggle.innerHTML = moonIcon;
    }

    // Listener de Clique
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
    // 2. MÁSCARA DO CEP - AGORA FUNCIONANDO!
    // ====================================
    // O input do CEP no modal tem o ID 'cepInput'
    const cepInput = document.getElementById('cepInput');

    // Adiciona a Máscara do CEP
    if (cepInput) {
        cepInput.addEventListener('input', (e) => {
            // Remove tudo que não for dígito e limita a 8 caracteres
            let value = e.target.value.replace(/\D/g, '').slice(0, 8);
            // Adiciona o hífen (formato XXXXX-XXX)
            if (value.length > 5) value = value.slice(0, 5) + '-' + value.slice(5, 8);
            e.target.value = value;
        });
    }


    // ====================================
    // 3. LÓGICA DA BARRA DE PESQUISA
    // ====================================
    const searchInput = document.getElementById('searchInput');
    const productCards = document.querySelectorAll('.product-card');

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const searchText = e.target.value.toLowerCase();

            productCards.forEach(card => {
                // Usando optional chaining (?.) para robustez
                const title = card.querySelector('.product-title')?.textContent.toLowerCase() || '';
                const category = card.querySelector('.product-category')?.textContent.toLowerCase() || '';
                const parentLink = card.closest('.product-card-link');
                
                if (parentLink) { 
                    if (title.includes(searchText) || category.includes(searchText)) {
                        parentLink.style.display = 'block';
                    } else {
                        parentLink.style.display = 'none';
                    }
                }
            });
        });
    }

    // ====================================
    // 4. LÓGICA DOS MODAIS E OUTROS EVENTOS
    // ====================================
    
    // Delegação de Eventos de Carrinho
    const cartItemsContainer = document.getElementById('cart-items-container');
    if (cartItemsContainer) {
        cartItemsContainer.addEventListener('click', (event) => {
            const removerButton = event.target.closest('.btn-remover-item');
            if (removerButton) {
                const produtoId = removerButton.getAttribute('data-produto-id');
                if (produtoId) {
                    removerItemDoCarrinho(produtoId, removerButton);
                }
            }
        });
    }

    // Checkout Button
    const btnCheckout = document.querySelector('.btn-checkout');
    if (btnCheckout) {
        btnCheckout.addEventListener('click', () => {
            window.location.href = 'checkout.php';
        });
    }

    // CEP Modal (Setup)
    // O setupModal captura o '#header-cep-btn' e o modal de ID 'cep-modal'
    setupModal('#header-cep-btn', 'cep-modal'); 

    const checkCepBtn = document.getElementById('checkCepBtn');
    if (checkCepBtn) {
        checkCepBtn.addEventListener('click', (e) => {
            e.preventDefault();
            
            const cepValue = cepInput ? cepInput.value : 'N/A'; // Usa o input que já tem a máscara
            alert(`Verificando o CEP: ${cepValue}`);
        });
    }

    // Login Modal (Setup)
    setupModal('#login-btn', 'login-modal');

    const loginForm = document.getElementById('login-form');
    const loginModal = document.getElementById('login-modal');

    if (loginForm && loginModal) { 
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Usando optional chaining (?.) para prevenir crash se inputs não existirem
            const email = document.getElementById('email')?.value;
            const password = document.getElementById('password')?.value;

            if (email && password) {
                alert(`Login simulado com sucesso! Usuário: ${email}`);
                loginModal.style.display = 'none'; 
            } else {
                alert('Por favor, preencha todos os campos.');
            }
        });
    }

    // Wishlist Buttons
    const wishlistButtons = document.querySelectorAll('.wishlist-btn');

    wishlistButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopPropagation(); 
            e.preventDefault(); 
            
            const icon = button.querySelector('i');
            icon.classList.toggle('far');
            icon.classList.toggle('fas');
            
            if (icon.classList.contains('fas')) {
                alert('Produto adicionado à sua lista de desejos!');
            } else {
                alert('Produto removido da sua lista de desejos!');
            }
        });
    });

    console.log("Script da Listagem: Execução finalizada com sucesso. Dark Mode e CEP ativos.");
});