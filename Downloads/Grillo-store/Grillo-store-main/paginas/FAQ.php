<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../estilo/FAQ.css">
    <title>FAQ - Grillo Store</title>
</head>
<body>
    <!-- HEADER -->
    <header class="header">
        <div class="header-content">
            <a href="Principal.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Voltar para a Loja
            </a>
            <div class="logo">
                <i class="fas fa-shopping-cart"></i> Grillo Store
            </div>
            <button class="btn-dark-mode">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </header>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="main-content">
        <!-- CABEÇALHO -->
        <div class="faq-header">
            <h1>Perguntas Frequentes</h1>
            <p>Encontre respostas para as dúvidas mais comuns sobre nossos produtos e serviços</p>
        </div>

        <!-- PEDIDOS E COMPRAS -->
        <section class="faq-section">
            <h2 class="section-title">Pedidos e Compras</h2>
            <div class="cards-grid">
                <div class="faq-card">
                    <h3>Como faço um pedido?</h3>
                    <p>Navegue pelos produtos, adicione ao carrinho e finalize a compra. É rápido e seguro!</p>
                </div>

                <div class="faq-card">
                    <h3>Quais formas de pagamento?</h3>
                    <p>Aceitamos cartão, PIX, boleto e transferência. Parcelamos em até 12x sem juros.</p>
                </div>

                <div class="faq-card">
                    <h3>O site é seguro?</h3>
                    <p>Sim! Usamos tecnologia avançada para proteger seus dados e pagamentos.</p>
                </div>
            </div>
        </section>

        <!-- LINHA DIVISÓRIA -->
        <hr class="divider">

        <!-- ENTREGAS -->
        <section class="faq-section">
            <h2 class="section-title">Entregas e Prazos</h2>
            <div class="cards-grid">
                <div class="faq-card">
                    <h3>Qual o prazo de entrega?</h3>
                    <p>Entregamos em 3 a 7 dias úteis. Confirme o prazo no seu CEP durante a compra.</p>
                </div>

                <div class="faq-card">
                    <h3>Como rastrear meu pedido?</h3>
                    <p>Enviamos o código de rastreamento por email. Acompanhe em tempo real!</p>
                </div>

                <div class="faq-card">
                    <h3>Entregam em todo o Brasil?</h3>
                    <p>Sim! Entregamos em todas as cidades do Brasil através dos Correios.</p>
                </div>
            </div>
        </section>

        <!-- LINHA DIVISÓRIA -->
        <hr class="divider">

        <!-- TROCAS E DEVOLUÇÕES -->
        <section class="faq-section">
            <h2 class="section-title">Trocas e Devoluções</h2>
            <div class="cards-grid">
                <div class="faq-card">
                    <h3>Qual a política de trocas?</h3>
                    <p>Você tem 30 dias para solicitar trocas. O produto deve estar em perfeito estado.</p>
                </div>

                <div class="faq-card">
                    <h3>Como devolver um produto?</h3>
                    <p>Entre em contato conosco e enviaremos as instruções para a devolução.</p>
                </div>

                <div class="faq-card">
                    <h3>Qual o prazo do reembolso?</h3>
                    <p>Reembolsamos em até 10 dias úteis após recebermos o produto de volta.</p>
                </div>
            </div>
        </section>
    </main>
    
    <?php include "../componentes/footer.php"; ?>

    <script>
        // Dark Mode Toggle
        const darkModeToggle = document.querySelector('.btn-dark-mode');
        const darkModeIcon = darkModeToggle.querySelector('i');
        
        darkModeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            
            if (document.body.classList.contains('dark-mode')) {
                darkModeIcon.classList.remove('fa-moon');
                darkModeIcon.classList.add('fa-sun');
            } else {
                darkModeIcon.classList.remove('fa-sun');
                darkModeIcon.classList.add('fa-moon');
            }
        });
    </script>
</body>
</html>