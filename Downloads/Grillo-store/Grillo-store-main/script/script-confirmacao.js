// ====================================
// script-checkout.js (COMPLETO E CORRIGIDO)
// ====================================

document.addEventListener('DOMContentLoaded', () => {

    // Helper para formatar BRL
    const formatBRL = (value) => {
        return value.toFixed(2)
            .replace('.', ',')
            .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    };

    // 1. DADOS DO PRODUTO (INJETADOS PELO PHP)
    const produto = window.produtoData;
    const produtoError = window.produtoError;

    // ====================================
    // 2. ELEMENTOS DOM E PREENCHIMENTO
    // ====================================
    const orderItemsContainer = document.getElementById('order-items');
    const subtotalValue = document.getElementById('subtotal-value');
    const totalValue = document.getElementById('total-value');
    const cardInstallments = document.getElementById('card-installments');

    // Objeto de Formulários de Pagamento
    const paymentForms = {
        'credit-card': document.getElementById('credit-card-form'),
        'pix': document.getElementById('pix-info'),
        'boleto': document.getElementById('boleto-info')
    };

    let totalAmount = 0; // Inicializando o total

    if (produto && produto.nome) {
        document.getElementById('checkout-img').src = produto.imagem;
        document.getElementById('checkout-name').textContent = produto.nome;
        document.getElementById('checkout-color').textContent = `Cor: ${produto.cor}`;
        document.getElementById('checkout-price').textContent = `R$ ${formatBRL(produto.preco)}`;

        // Subtotal
        subtotalValue.textContent = `R$ ${formatBRL(produto.preco)}`;

        // Frete
        let frete = produto.frete;
        document.getElementById('shipping-value').textContent = frete > 0 ? `R$ ${formatBRL(frete)}` : 'Grátis';

        // Total
        totalAmount = produto.preco + frete;
        totalValue.textContent = `R$ ${formatBRL(totalAmount)}`;

        // 2.1. CONTEÚDO PIX E BOLETO

        // Conteúdo PIX
        if (paymentForms['pix']) {
            const pixTotalFormatted = formatBRL(totalAmount);
            paymentForms['pix'].innerHTML = `
                <h3 class="payment-title">Pague com Pix e Receba Imediatamente!</h3>
                <p>Escaneie o QR Code abaixo usando o aplicativo do seu banco ou copie a chave Pix.</p>
                <div style="text-align: center; margin: 20px 0;">
                    <img src="../imagens-icones/qrcode-placeholder.png" alt="QR Code Pix" style="width: 150px; height: 150px; border: 1px solid #ccc; background-color: white;">
                </div>
                <div class="pix-info-details">
                    <p><strong>Valor:</strong> R$ ${pixTotalFormatted}</p>
                    <p><strong>Chave Aleatória (Copia e Cola):</strong> 1a2b3c4d-5e6f-7g8h-9i0j-k1l2m3n4o5p6</p>
                    <button class="button primary" onclick="navigator.clipboard.writeText('1a2b3c4d-5e6f-7g8h-9i0j-k1l2m3n4o5p6')">Copiar Chave Pix</button>
                </div>
            `;
        }

        // Conteúdo BOLETO
        if (paymentForms['boleto']) {
            const boletoTotalFormatted = formatBRL(totalAmount);
            const dueDate = new Date();
            dueDate.setDate(dueDate.getDate() + 3);
            const formattedDueDate = dueDate.toLocaleDateString('pt-BR');

            paymentForms['boleto'].innerHTML = `
                <h3 class="payment-title">Pague com Boleto Bancário</h3>
                <p>O boleto será gerado ao finalizar o pedido e o pagamento pode levar até 3 dias úteis para ser compensado.</p>
                <div class="boleto-details">
                    <p><strong>Valor Total:</strong> R$ ${boletoTotalFormatted}</p>
                    <p><strong>Vencimento Estimado:</strong> ${formattedDueDate}</p>
                    <div style="margin: 20px 0; padding: 10px; border: 1px dashed #000; background: #f9f9f9;">
                        <p style="font-family: monospace; font-size: 14px; word-break: break-all;">
                            <strong>Linha Digitável:</strong> 12345.67890 12345.678901 23456.789012 3 00000000000000
                        </p>
                    </div>
                    <button class="button secondary" onclick="alert('Gerando o Boleto...');">Gerar Boleto em PDF</button>
                </div>
            `;
        }

        // Parcelas
        if (cardInstallments) {
            cardInstallments.innerHTML = '';
            for (let i = 1; i <= produto.parcelas; i++) {
                const valorParcela = (totalAmount / i);
                const option = document.createElement('option');
                option.value = i;
                option.textContent = `${i}x de R$ ${formatBRL(valorParcela)} sem juros`;
                cardInstallments.appendChild(option);
            }
        }
    } else {
        // Se o PHP não encontrou (Erro)
        orderItemsContainer.innerHTML = `<p style="color:red; margin: 20px;">${produtoError || 'Erro ao carregar os dados do produto. Tente novamente.'}</p>`;
        subtotalValue.textContent = `R$ 0,00`;
        totalValue.textContent = `R$ 0,00`;
    }

    // ====================================
    // 3. MÉTODOS DE PAGAMENTO (ATIVAÇÃO E CONTROLE DE VISIBILIDADE)
    // ====================================
    const paymentOptions = document.querySelectorAll('.payment-option');

    paymentOptions.forEach(option => {
        option.addEventListener('click', () => {
            paymentOptions.forEach(opt => opt.classList.remove('selected'));
            option.classList.add('selected');

            // Oculta todos os formulários/infos de pagamento
            Object.values(paymentForms).forEach(f => {
                if(f) f.classList.add('hidden');
            }); 
            
            // Mostra o formulário/info selecionado
            const method = option.dataset.method;
            if (paymentForms[method]) paymentForms[method].classList.remove('hidden');
        });
    });

    // Garante que o Cartão de Crédito é a opção selecionada e visível ao carregar a página
    const defaultOption = document.querySelector('.payment-option[data-method="credit-card"]');
    if (defaultOption) {
        // O HTML já marca como 'selected', mas o JS pode reforçar
        defaultOption.classList.add('selected'); 

        // 💡 Importante: Garante que apenas o form de cartão de crédito esteja visível no carregamento
        const defaultForm = paymentForms[defaultOption.dataset.method];
        if (defaultForm) defaultForm.classList.remove('hidden');
    }
    
    // Oculta os outros para garantir (Caso o HTML tenha vindo sem o 'hidden')
    if (paymentForms['pix']) paymentForms['pix'].classList.add('hidden');
    if (paymentForms['boleto']) paymentForms['boleto'].classList.add('hidden');


    // ====================================
    // 4. FINALIZAÇÃO DO PEDIDO (INJEÇÃO DE DADOS E SUBMISSÃO POST)
    // ====================================
    const checkoutForm = document.getElementById('checkout-form');

    // Função para criar input oculto
    const createHiddenInput = (name, value) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        return input;
    };

    checkoutForm.addEventListener('submit', (e) => {

        // 1. Validação do Produto e Campos
        if (!produto || produtoError) {
            alert("Não foi possível finalizar o pedido: Produto não carregado.");
            e.preventDefault();
            return;
        }

        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const address = document.getElementById('address').value.trim();
        const number = document.getElementById('number').value.trim();
        const city = document.getElementById('city').value.trim();
        const state = document.getElementById('state').value.trim();
        const selectedMethod = document.querySelector('.payment-option.selected')?.dataset.method;

        if (!name || !email || !address || !number || !city || !state || !selectedMethod) {
            alert("Por favor, preencha todos os campos de Contato e Entrega e selecione um Método de Pagamento.");
            e.preventDefault(); // ⬅️ BLOQUEIA A SUBMISSÃO
            return;
        }

        // Validação de campos específicos do cartão, se selecionado
        if (selectedMethod === 'credit-card') {
            const cardNumber = document.getElementById('card-number').value.replace(/\s/g, '').length;
            const cardName = document.getElementById('card-name').value.trim();
            const cardExpiry = document.getElementById('card-expiry').value.length;
            const cardCvv = document.getElementById('card-cvv').value.length;

            if (cardNumber < 13 || !cardName || cardExpiry !== 5 || cardCvv < 3) {
                alert("Por favor, preencha corretamente os detalhes do Cartão de Crédito.");
                e.preventDefault();
                return;
            }
        }


        // 2. Cálculo Final e Previsão de Entrega
        let frete = produto.frete;
        const totalAmount = produto.preco + frete;
        // O JS define a previsão aqui para enviar ao PHP
        const previsao = frete > 0 ? "7 a 15 dias úteis (Frete Padrão)" : "2 a 5 dias úteis (Frete Grátis)";

        // 3. Remover inputs ocultos antigos e injetar novos dados para o POST
        checkoutForm.querySelectorAll('input[type="hidden"]').forEach(input => input.remove());

        // Dados do Produto e Totais
        checkoutForm.appendChild(createHiddenInput('slug', produto.slug));
        checkoutForm.appendChild(createHiddenInput('nome', produto.nome));
        checkoutForm.appendChild(createHiddenInput('total', totalAmount.toFixed(2)));
        checkoutForm.appendChild(createHiddenInput('cor', produto.cor));
        checkoutForm.appendChild(createHiddenInput('qty', 1));
        checkoutForm.appendChild(createHiddenInput('img', produto.imagem));

        // Dados de Confirmação (previsão é usada na confirmação)
        checkoutForm.appendChild(createHiddenInput('previsao', previsao));

        // Dados de Pagamento
        let metodoText = '';
        let parcelas = '1';

        if (selectedMethod === 'credit-card') {
            parcelas = document.getElementById('card-installments')?.value || '1';
            metodoText = `Cartão de Crédito - ${parcelas}x`;
        } else if (selectedMethod === 'pix') {
            metodoText = 'Pix';
        } else if (selectedMethod === 'boleto') {
            metodoText = 'Boleto Bancário';
        }

        checkoutForm.appendChild(createHiddenInput('pagamento', metodoText));
        checkoutForm.appendChild(createHiddenInput('parcelas', parcelas));

        // 4. O fluxo é continuado naturalmente para processar_pedido.php
    });

    // ====================================
    // 5. MÁSCARAS DE INPUT
    // ====================================
    const cepInput = document.getElementById('cep');
    if (cepInput) cepInput.addEventListener('input', (e) => {
        e.target.value = e.target.value.replace(/\D/g, '').slice(0, 8);
        if (e.target.value.length > 5) e.target.value = e.target.value.slice(0, 5) + '-' + e.target.value.slice(5, 8);
    });

    const cardExpiryInput = document.getElementById('card-expiry');
    if (cardExpiryInput) cardExpiryInput.addEventListener('input', (e) => {
        e.target.value = e.target.value.replace(/\D/g, '').slice(0, 4);
        if (e.target.value.length > 2) e.target.value = e.target.value.slice(0, 2) + '/' + e.target.value.slice(2, 4);
    });

    const cardNumberInput = document.getElementById('card-number');
    if (cardNumberInput) cardNumberInput.addEventListener('input', (e) => {
        e.target.value = e.target.value.replace(/\D/g, '').slice(0, 16).replace(/(\d{4})/g, '$1 ').trim();
    });

    const cardCvvInput = document.getElementById('card-cvv');
    if (cardCvvInput) cardCvvInput.addEventListener('input', (e) => {
        e.target.value = e.target.value.replace(/\D/g, '').slice(0, 4);
    });

    // ====================================
    // 6. MODO ESCURO
    // ====================================
    const body = document.body;
    const darkModeToggle = document.getElementById('darkModeToggle');
    const sunIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sun"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>`;
    const moonIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-moon"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>`;

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

    if (localStorage.getItem('darkMode') === 'enabled') enableDarkMode();
    else if (darkModeToggle) darkModeToggle.innerHTML = moonIcon;

    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', () => {
            body.classList.contains('dark-mode') ? disableDarkMode() : enableDarkMode();
        });
    }

});