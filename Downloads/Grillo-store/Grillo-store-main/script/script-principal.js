// Espera o DOM ser completamente carregado antes de executar o script
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script carregado - iniciando...');

    // Variável global (simulada) para verificar login. Assume-se que ela é definida no HTML/PHP.
    // Ex: <script>window.isUserLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;</script>
    const isUserLoggedIn = window.isUserLoggedIn === true || window.isUserLoggedIn === 'true'; 

    // =========================================================================
    // --- FUNÇÕES DE UTILIDADE E MODAIS ---
    // =========================================================================

    const formatCurrency = (value) => {
        // Garante que o valor é um número antes de formatar
        return (parseFloat(value) || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    };

    function displayInfoPopup(message, title = 'Informação') {
        alert(title + ': ' + message);
    }

    // Certificar que os modais de bloqueio e seus controles existem antes de usar
    const blockModalCep = document.getElementById('block-modal-cep');
    const closeBlockModalCep = document.getElementById('close-block-modal-cep');
    const blockModalProduct = document.getElementById('block-modal-product');
    const closeBlockModalProduct = document.getElementById('close-block-modal-product');
    const cartModal = document.getElementById('cart-modal');

    // Funções para mostrar e esconder modal (Ajustada para usar 'flex')
    function showModal(modalElement, event = null) {
        if (event) event.preventDefault();
        if (modalElement) {
            modalElement.style.display = 'flex';
            modalElement.classList.add('show');
        }
    }

    function hideModal(modalElement) {
        if (modalElement) {
            modalElement.style.display = 'none';
            modalElement.classList.remove('show');
        }
    }

    // =========================================================================
    // --- 1. LÓGICA DO CARRINHO (AJAX para Renderização e Remoção) ---
    // =========================================================================

    const cartItemsContainer = document.getElementById('cart-items-container');
    const emptyCartMessage = document.getElementById('empty-cart-message');
    const cartTotalValue = document.getElementById('cart-total-value');
    const cartBadgeCount = document.getElementById('cart-badge-count');

    /**
     * Carrega os dados do carrinho via AJAX e renderiza os itens no modal.
     */
    function renderCart() {
        if (!cartItemsContainer || !emptyCartMessage || !cartTotalValue) return;

        // **AJUSTE O CAMINHO SE NECESSÁRIO**
        fetch('get_carrinho_data.php') 
            .then(response => response.json())
            .then(data => {
                let total = 0;
                let totalItems = 0;
                cartItemsContainer.innerHTML = ''; // Limpa os itens atuais

                if (data.carrinho && data.carrinho.length > 0) {
                    emptyCartMessage.style.display = 'none';
                    
                    data.carrinho.forEach(item => {
                        const preco = parseFloat(item.preco);
                        const quantidade = parseInt(item.quantidade);

                        const itemTotal = preco * quantidade;
                        total += itemTotal;
                        totalItems += quantidade;

                        const itemHtml = `
                            <div class="cart-item" data-produto-id="${item.id}">
                                <div class="item-details">
                                    <h4>${item.nome}</h4>
                                    <p>${formatCurrency(item.preco)} x ${quantidade} = 
                                        <span style="font-weight: bold;">${formatCurrency(itemTotal)}</span>
                                    </p>
                                </div>
                                <button class="btn-remover-item" data-produto-id="${item.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                        cartItemsContainer.insertAdjacentHTML('beforeend', itemHtml);
                    });
                } else {
                    emptyCartMessage.style.display = 'block';
                }

                // Atualiza o total e o badge
                cartTotalValue.textContent = formatCurrency(total);
                if (cartBadgeCount) {
                    cartBadgeCount.textContent = totalItems;
                }

            })
            .catch(error => {
                console.error('Erro ao renderizar o carrinho:', error);
                if (emptyCartMessage) emptyCartMessage.style.display = 'block';
                cartItemsContainer.innerHTML = '';
                cartTotalValue.textContent = formatCurrency(0);
            });
    }

    /**
     * Envia o produto para o PHP adicionar à sessão.
     */
    function adicionarAoCarrinho(produtoId, nome, preco) {
        // **AJUSTE O CAMINHO SE NECESSÁRIO**
        fetch('adicionar_ao_carrinho.php', { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `produto_id=${produtoId}&nome=${encodeURIComponent(nome)}&preco=${preco}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'sucesso') {
                displayInfoPopup(`"${nome}" foi adicionado ao seu carrinho!`, 'Produto Adicionado');
                
                // Atualiza o badge do carrinho
                if (cartBadgeCount && data.cart_count !== undefined) {
                    cartBadgeCount.textContent = data.cart_count;
                }

                // Se o modal estiver aberto, atualiza o conteúdo
                if (cartModal && cartModal.style.display === 'flex') {
                    renderCart(); 
                }

            } else {
                displayInfoPopup(`Erro ao adicionar o produto: ${data.message || 'Erro desconhecido.'}`, 'Erro no Carrinho');
            }
        })
        .catch(error => {
            console.error('Erro na comunicação com o servidor (adicionar):', error);
            displayInfoPopup('Houve um erro de rede ao tentar adicionar o produto.', 'Erro de Rede');
        });
    }

    /**
     * Remove um item do carrinho via AJAX.
     */
    function removerItemDoCarrinho(produtoId) {
        // **AJUSTE O CAMINHO SE NECESSÁRIO**
        fetch('remover_carrinho_principal.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `produto_id=${produtoId}` 
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'sucesso') {
                // Atualiza a visualização do carrinho (badge e total)
                renderCart(); 
            } else {
                console.error('Erro ao remover:', data.message);
                displayInfoPopup(`Erro ao remover o produto ${produtoId}.`, 'Erro de Remoção');
            }
        })
        .catch(error => {
            console.error('Erro na comunicação com o servidor (remover):', error);
            displayInfoPopup('Houve um erro de rede ao tentar remover o produto.', 'Erro de Rede');
        });
    }


    // =========================================================================
    // --- 2. CONFIGURAÇÃO DO MODAL DO CARRINHO (Novo Elemento) ---
    // =========================================================================
    
    const cartBtn = document.getElementById('cart-btn'); // O gatilho para abrir o modal do carrinho
    const closeCartBtn = cartModal ? cartModal.querySelector('.close-btn') : null;

    if (cartBtn && cartModal) {
        // Evento para abrir o modal do carrinho
        cartBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // CHAMA A FUNÇÃO PARA CARREGAR OS DADOS ANTES DE ABRIR
            renderCart(); 
            showModal(cartModal);
        });

        // Evento para fechar modal do carrinho
        if (closeCartBtn) {
            closeCartBtn.addEventListener('click', function() {
                hideModal(cartModal);
            });
        }

        // Fechar modal ao clicar fora
        cartModal.addEventListener('click', function(e) {
            if (e.target === cartModal) {
                hideModal(cartModal);
            }
        });
    }

    // =========================================================================
    // --- 3. INICIALIZAÇÃO DE EVENTOS DE PRODUTO E CARRINHO ---
    // =========================================================================
    
    // --- Adicionar ao carrinho (AGORA COM AJAX) ---
    const addToCartButtons = document.querySelectorAll('.btn-add-to-cart');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            
            // Pega os dados dos atributos data-
            const produtoId = button.getAttribute('data-produto-id');
            const nome = button.getAttribute('data-nome');
            const preco = button.getAttribute('data-preco'); 

            if (produtoId && nome && preco) {
                adicionarAoCarrinho(produtoId, nome, preco);
            } else {
                console.error("Dados do produto faltando no botão de carrinho.", button);
                displayInfoPopup("Erro: Dados do produto incompletos.", 'Erro');
            }
        });
    });

    // --- Delegação de eventos para Remover do Carrinho (Botão dentro do modal) ---
    if (cartItemsContainer) {
        cartItemsContainer.addEventListener('click', (event) => {
            const removerButton = event.target.closest('.btn-remover-item');

            if (removerButton) {
                const produtoId = removerButton.getAttribute('data-produto-id');
                if (produtoId) {
                    removerItemDoCarrinho(produtoId);
                }
            }
        });
    }
    
    // =========================================================================
    // --- OUTROS CÓDIGOS EXISTENTES (Mantidos da sua estrutura) ---
    // =========================================================================


    // -------------------------------------------------------------------------
    // --- NOVO CARROSSEL ---
    // -------------------------------------------------------------------------
    const newCarouselTrack = document.querySelector('.new-carousel-track');
    const newSlides = document.querySelectorAll('.new-carousel-slide');
    const newPrevBtn = document.querySelector('.new-prev-btn');
    const newNextBtn = document.querySelector('.new-next-btn');
    const newCarouselDotsContainer = document.querySelector('.new-carousel-dots');

    let newCurrentSlideIndex = 0;
    let newAutoPlayInterval;

    if (newSlides.length > 0 && newCarouselTrack && newCarouselDotsContainer) {
        function createNewDots() {
            newCarouselDotsContainer.innerHTML = '';
            newSlides.forEach((_, index) => {
                const dot = document.createElement('span');
                dot.classList.add('new-carousel-dot');
                if (index === newCurrentSlideIndex) dot.classList.add('active');
                dot.addEventListener('click', () => {
                    stopNewAutoPlay();
                    newCurrentSlideIndex = index;
                    updateNewCarousel();
                    startNewAutoPlay();
                });
                newCarouselDotsContainer.appendChild(dot);
            });
        }

        function updateNewCarousel() {
            const offset = -newCurrentSlideIndex * 100;
            newCarouselTrack.style.transform = `translateX(${offset}%)`;
            updateNewDots();
        }

        function updateNewDots() {
            const dots = document.querySelectorAll('.new-carousel-dot');
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === newCurrentSlideIndex);
            });
        }

        function nextNewSlide() {
            newCurrentSlideIndex = (newCurrentSlideIndex + 1) % newSlides.length;
            updateNewCarousel();
        }

        function prevNewSlide() {
            newCurrentSlideIndex = (newCurrentSlideIndex - 1 + newSlides.length) % newSlides.length;
            updateNewCarousel();
        }

        function startNewAutoPlay() {
            stopNewAutoPlay();
            newAutoPlayInterval = setInterval(nextNewSlide, 5000);
        }

        function stopNewAutoPlay() {
            if (newAutoPlayInterval) {
                clearInterval(newAutoPlayInterval);
            }
        }

        createNewDots();
        updateNewCarousel();
        startNewAutoPlay();

        if (newNextBtn) {
            newNextBtn.addEventListener('click', () => {
                stopNewAutoPlay();
                nextNewSlide();
                startNewAutoPlay();
            });
        }

        if (newPrevBtn) {
            newPrevBtn.addEventListener('click', () => {
                stopNewAutoPlay();
                prevNewSlide();
                startNewAutoPlay();
            });
        }
    }

    // -------------------------------------------------------------------------
    // --- MODAL CEP ---
    // -------------------------------------------------------------------------
    
    const cepModal = document.getElementById('cep-modal');
    const closeCepBtn = document.getElementById('close-cep-modal');
    const headerCepBtn = document.getElementById('header-cep-btn');

    // Evento para abrir modal CEP
    if (headerCepBtn) {
        headerCepBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (isUserLoggedIn) {
                showModal(cepModal);
            } else {
                if (blockModalCep) {
                    showModal(blockModalCep);
                } else {
                    console.warn('blockModalCep não encontrado; abrindo cepModal como fallback');
                    showModal(cepModal);
                }
            }
        });
    }

    // Evento para fechar modal CEP
    if (closeCepBtn) {
        closeCepBtn.addEventListener('click', function() {
            hideModal(cepModal);
        });
    }

    // Fechar modal ao clicar fora
    if (cepModal) {
        cepModal.addEventListener('click', function(e) {
            if (e.target === cepModal) {
                hideModal(cepModal);
            }
        });
    }

    // -------------------------------------------------------------------------
    // --- BUSCA DE CEP ---
    // -------------------------------------------------------------------------
    const cepInput = document.getElementById('cep');
    const logradouroInput = document.getElementById('logradouro');
    const bairroInput = document.getElementById('bairro');
    const cidadeInput = document.getElementById('cidade');
    const estadoInput = document.getElementById('estado');
    const buscarCepBtn = document.getElementById('buscar-cep-btn');
    const cepLoading = document.getElementById('cep-loading');

    async function searchCep(cep) {
        cep = cep.replace(/\D/g, '');
        if (cep.length !== 8) {
            return { error: 'CEP inválido. Por favor, insira 8 dígitos.' };
        }

        try {
            const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
            const data = await response.json();
            if (data.erro) {
                return { error: 'CEP não encontrado. Verifique o número e tente novamente.' };
            }
            return data;
        } catch (error) {
            console.error('Erro ao buscar CEP:', error);
            return { error: 'Erro de comunicação. Tente novamente mais tarde.' };
        }
    }

    function fillAddressFromData(data) {
        if (logradouroInput) logradouroInput.value = data.logradouro || '';
        if (bairroInput && !bairroInput.value) bairroInput.value = data.bairro || '';
        if (cidadeInput && !cidadeInput.value) cidadeInput.value = data.localidade || '';
        if (estadoInput && !estadoInput.value) estadoInput.value = data.uf || '';
    }

    // Buscar CEP
    if (buscarCepBtn && cepInput) {
        buscarCepBtn.addEventListener('click', async function() {
            const cepValue = cepInput.value;
            if (!cepValue) {
                displayInfoPopup('Informe o CEP antes de buscar.', 'Aviso');
                cepInput.focus();
                return;
            }

            if (cepLoading) cepLoading.style.display = 'inline';
            
            try {
                const result = await searchCep(cepValue);
                if (cepLoading) cepLoading.style.display = 'none';
                
                if (result.error) {
                    displayInfoPopup(result.error, 'Erro de CEP');
                } else {
                    fillAddressFromData(result);
                    displayInfoPopup('Endereço preenchido a partir do CEP.', 'Sucesso');
                }
            } catch (error) {
                if (cepLoading) cepLoading.style.display = 'none';
                displayInfoPopup('Erro ao buscar CEP.', 'Erro');
            }
        });
    }

    // -------------------------------------------------------------------------
    // --- DARK MODE ---
    // -------------------------------------------------------------------------
    const darkModeToggle = document.getElementById('darkModeToggle');
    const body = document.body;

    function enableDarkMode() {
        body.classList.add('dark-mode');
        localStorage.setItem('darkMode', 'enabled');
    }

    function disableDarkMode() {
        body.classList.remove('dark-mode');
        localStorage.setItem('darkMode', 'disabled');
    }

    // Inicializar Dark Mode
    if (localStorage.getItem('darkMode') === 'enabled') {
        enableDarkMode();
    }

    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function() {
            if (body.classList.contains('dark-mode')) {
                disableDarkMode();
            } else {
                enableDarkMode();
            }
        });
    }

    // -------------------------------------------------------------------------
    // --- BLOQUEIO DE ACESSO ---
    // -------------------------------------------------------------------------
    const viewAllButton = document.querySelector('.btn-view-all');

    if (viewAllButton) {
        viewAllButton.addEventListener('click', function(e) {
            if (!isUserLoggedIn) {
                e.preventDefault();
                if (blockModalProduct) {
                    showModal(blockModalProduct);
                }
            }
        });
    }

    // Handlers para fechar e clicar fora do modal de CEP de bloqueio
    if (closeBlockModalCep && blockModalCep) {
        closeBlockModalCep.addEventListener('click', function() {
            hideModal(blockModalCep);
        });

        blockModalCep.addEventListener('click', function(e) {
            if (e.target === blockModalCep) {
                hideModal(blockModalCep);
            }
        });
    }

    // Handlers para fechar e clicar fora do modal de PRODUCT de bloqueio
    if (closeBlockModalProduct && blockModalProduct) {
        closeBlockModalProduct.addEventListener('click', function() {
            hideModal(blockModalProduct);
        });

        blockModalProduct.addEventListener('click', function(e) {
            if (e.target === blockModalProduct) {
                hideModal(blockModalProduct);
            }
        });
    }

    // -------------------------------------------------------------------------
    // --- PRODUTOS (Cards Clicáveis) ---
    // -------------------------------------------------------------------------
    
    const productCards = document.querySelectorAll('.product-card');

    // Cards clicáveis (redirecionamento)
    productCards.forEach(card => {
        let url = card.getAttribute('data-url');
        if (url) {
            if (url.endsWith('.html')) {
                url = url.replace('.html', '.php');
            }

            card.style.cursor = 'pointer';
            
            card.addEventListener('click', function(e) {
                // Não redirecionar se clicar em botões
                if (e.target.closest('button') || e.target.closest('.wishlist-btn') || e.target.closest('.btn-add-to-cart')) {
                    return;
                }

                if (isUserLoggedIn) {
                    window.location.href = url;
                } else {
                    e.preventDefault();
                    if (blockModalProduct) {
                        showModal(blockModalProduct);
                    }
                }
            });
        }
    });

    // -------------------------------------------------------------------------
    // --- WISHLIST (MANTIDO) ---
    // -------------------------------------------------------------------------
    const wishlistButtons = document.querySelectorAll('.wishlist-btn');

    wishlistButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopPropagation(); 
            e.preventDefault(); 
            
            const icon = button.querySelector('i');
            icon.classList.toggle('far');
            icon.classList.toggle('fas');
            
            if (icon.classList.contains('fas')) {
                displayInfoPopup('Produto adicionado à sua lista de desejos!', 'Lista de Desejos');
            } else {
                displayInfoPopup('Produto removido da sua lista de desejos!', 'Lista de Desejos');
            }
        });
    });

    console.log('Script inicializado com sucesso!');
});