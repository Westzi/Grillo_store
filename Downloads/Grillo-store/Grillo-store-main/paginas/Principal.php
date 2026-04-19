<?php
// Inicia a sessão do usuário - permite armazenar variáveis de sessão persistentes durante a navegação
session_start();

// Exibir mensagens de erro de login
// Verifica se existe uma mensagem de erro de login armazenada na sessão
if (isset($_SESSION['erro_login'])) {
    // Exibe a mensagem de erro de login com estilo vermelho centralizado
    echo "<p style='color:red; text-align:center;'>" . $_SESSION['erro_login'] . "</p>";
    // Remove a mensagem de erro da sessão após exibir (evita que apareça novamente no próximo acesso)
    unset($_SESSION['erro_login']);
}

// Exibir mensagens de sucesso ou erro do CEP
// Verifica se existe uma mensagem de sucesso armazenada na sessão
if (isset($_SESSION['sucesso'])) {
    // Exibe a mensagem de sucesso com estilo de alerta verde customizado
    echo "<div class='alert alert-success' style='background: #d4edda; color: #155724; padding: 15px; margin: 10px 0; border: 1px solid #c3e6cb; border-radius: 5px; text-align: center;'>" . $_SESSION['sucesso'] . "</div>";
    // Remove a mensagem de sucesso da sessão após exibir
    unset($_SESSION['sucesso']);
}

// Verifica se existe uma mensagem de erro armazenada na sessão
if (isset($_SESSION['erro'])) {
    // Exibe a mensagem de erro com estilo de alerta vermelho customizado
    echo "<div class='alert alert-danger' style='background: #f8d7da; color: #721c24; padding: 15px; margin: 10px 0; border: 1px solid #f5c6cb; border-radius: 5px; text-align: center;'>" . $_SESSION['erro'] . "</div>";
    // Remove a mensagem de erro da sessão após exibir
    unset($_SESSION['erro']);
}

// Inicializa a quantidade total de itens no carrinho para o badge (número que aparece no ícone do carrinho)
// Usa operador ternário: se a sessão carrinho existe, soma todas as quantidades individuais, senão inicializa com 0
$cart_count = isset($_SESSION['carrinho']) ? array_sum(array_column($_SESSION['carrinho'], 'quantidade')) : 0;
// array_column() extrai a coluna 'quantidade' de cada item do carrinho
// array_sum() soma todos os valores extraídos para obter a quantidade total

?>

<!-- Declaração do tipo de documento como HTML5 -->
<!DOCTYPE html>
<!-- Define a linguagem do documento como Português Brasileiro -->
<html lang="pt-br">

<head>
    <!-- Define a codificação de caracteres como UTF-8 para suportar acentuação e caracteres especiais -->
    <meta charset="UTF-8">
    <!-- Define a viewport para responsividade em dispositivos móveis com escala inicial de 1.0 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Título que aparece na aba do navegador -->
    <title>Grillo Store</title>
    <!-- Importa o CSS da página principal com cache bust (time()) para sempre carregar a versão mais recente -->
    <link rel="stylesheet" href="../estilo/estilo-pgprincipal.css?v=<?php echo time(); ?>">
    <!-- Importa o CSS específico para o painel de super administrador -->
    <link rel="stylesheet" href="../estilo/super-administrador.css">
    <!-- Importa a biblioteca de ícones Font Awesome versão 6.0.0-beta3 do CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Carrega o script JavaScript principal com atributo 'defer' para executar após carregar o HTML -->
    <script src="../script/script-principal.js" defer></script>
    <!-- Define o favicon (ícone pequeno na aba) usando a imagem do grilo -->
    <link rel="icon" href="../imagem-grilo/grilo.png" type="image/x-icon">
</head>

<body>

    <!-- Barra superior com informações de contato e promoção -->
    <header class="top-bar">
        <div class="top-bar-content">
            <!-- Texto esquerdo com informação de frete grátis -->
            <div class="left-text">
                Frete grátis para compras acima de R$ 200
            </div>
            <!-- Texto direito com número de atendimento e ícone de ajuda -->
            <div class="right-text">
                Atendimento: (11) 9999-9999
                <i class="fas fa-question-circle"></i>
            </div>
        </div>
    </header>

    <!-- Barra de navegação principal -->
    <nav class="navbar">
        <div class="nav-container">
            <!-- Logo da loja -->
            <div class="logo">
                <div class="grilo-logo">
                    <!-- Imagem do grilo com texto da marca -->
                    <img src="../imagem-grilo/grilo.png" alt="Grillo Store"> Grillo Store
                </div>
            </div>
            
            <!-- Lista de links e botões de navegação -->
            <ul class="nav-links">
                <!-- Verifica se o usuário está logado na sessão -->
                <?php if (isset($_SESSION['usuario_nome'])): ?>
                    <!-- Se logado, mostra saudação personalizada com o nome do usuário e link para conta -->
                    <li><a href="minha_conta.php"><i class="fas fa-user"></i> Olá, <?= $_SESSION['usuario_nome']; ?></a></li>
                    <!-- Link para fazer logout (sair) -->
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                <!-- Se não estiver logado, mostra opções de login e cadastro -->
                <?php else: ?>
                    <!-- Link para acessar a página de conta (que redireciona para login se não logado) -->
                    <li><a href="minha_conta.php" rel="account"><i class="fas fa-user"></i> Minha Conta</a></li>
                    <!-- Botão de cadastro com estilo secundário -->
                    <li><a href="cadastro.php" class="btn btn-primary">Cadastro</a></li>
                    <!-- Botão de login com estilo secundário e id para controle via JavaScript -->
                    <li><a href="login.php" class="btn btn-secondary" id="login-btn">Login</a></li>
                <?php endif; ?>

                <?php
                // Define um array com os emails dos super administradores autorizados
                $super_admins = [
                    'sdvr2017@gmail.com',
                    'pabloviniciusog@gmail.com',
                    'Beatriz.ffsilva16@gmail.com',
                    'gabrielsuliano240@gmail.com'
                ];

                // Verifica se o usuário está logado E se seu email está na lista de super administradores
                if (isset($_SESSION['usuario_email']) && in_array($_SESSION['usuario_email'], $super_admins)) {
                    // Se é super admin, exibe o botão para acessar o painel de administrador
                    echo '<li><a href="super-administrador.php" class="super-admin-btn" style="display:inline-flex; align-items:center; gap:.5rem;">';
                    echo '<i class="fas fa-user-shield"></i> Painel Admin'; // Ícone de escudo + texto
                    echo '</a></li>';
                }
                ?>
                
                <!-- Item do carrinho de compras -->
              
                <!-- Link para inserir CEP (código postal) na modal -->
                <li class="cep-link"><a href="#" id="header-cep-btn"><i class="fas fa-map-marker-alt"></i> Inserir CEP </a></li>

            </ul>

            <!-- Container para botão de modo escuro/claro -->
            <div class="darkmode-container">
                <!-- Botão para alternar entre modo claro e escuro com emojis de sol e lua -->
                <button id="darkModeToggle" class="btn-dark-mode" aria-label="Alternar modo claro/escuro">
                    <!-- Ícone de sol para modo claro -->
                    <span class="sun-icon">🔆</span>
                    <!-- Ícone de lua para modo escuro -->
                    <span class="moon-icon">🌙</span>
                </button>
            </div>
        </div>
    </nav>

    <main>
        <!-- Seção de carrossel de imagens em destaque -->
        <section class="new-carousel-section">
            <div class="new-carousel-container">
                <!-- Container que contém todos os slides do carrossel -->
                <div class="new-carousel-track">
                    <!-- Primeiro slide do carrossel - Eletrônicos -->
                    <div class="new-carousel-slide">
                        <img src="../imagem/eletronicos.png" alt="Destaque 1">
                        <!-- Legenda do slide com título, descrição e botão de ação -->
                        <div class="slide-caption">
                            <h3>Super Ofertas em Eletrônicos!</h3>
                            <p>Encontre os gadgets mais recentes com preços incríveis.</p>
                            <a href="#" class="carousel-action-btn">Compre Agora</a>
                        </div>
                    </div>
                    <!-- Segundo slide do carrossel - Moda -->
                    <div class="new-carousel-slide">
                        <img src="../imagem/moda.png" alt="Destaque 2">
                        <!-- Legenda do slide -->
                        <div class="slide-caption">
                            <h3>Nova Coleção de Moda Feminina</h3>
                            <p>Estilo e elegância para todas as ocasiões.</p>
                            <a href="#" class="carousel-action-btn">Ver Coleção</a>
                        </div>
                    </div>
                    <!-- Terceiro slide do carrossel - Jardim e Casa -->
                    <div class="new-carousel-slide">
                        <img src="../imagem/jardim-casa.png" alt="Destaque 3">
                        <!-- Legenda do slide -->
                        <div class="slide-caption">
                            <h3>Renove sua Casa e Jardim</h3>
                            <p>Produtos essenciais para deixar seu lar ainda mais bonito.</p>
                            <a href="#" class="carousel-action-btn">Explorar</a>
                        </div>
                    </div>
                </div>
                <!-- Botão para ir ao slide anterior (seta para esquerda) -->
                <button class="new-carousel-btn new-prev-btn">&#10094;</button>
                <!-- Botão para ir ao próximo slide (seta para direita) -->
                <button class="new-carousel-btn new-next-btn">&#10095;</button>
                <!-- Container para os pontos indicadores (dots) do carrossel -->
                <div class="new-carousel-dots"></div>
            </div>
        </section>

        <!-- Seção de mega promoção com banner em destaque -->
        <section class="mega-promo-section">
            <!-- Container do banner de promoção -->
            <div class="mega-promo-banner">
                <!-- Tag indicando que é uma oferta relâmpago -->
                <p class="flash-sale-tag">FLASH SALE</p>
                <!-- Título principal da seção de promoção -->
                <h2>Mega Promoção</h2>
                <!-- Descrição da promoção -->
                <p class="promo-description">Até 70% de desconto em produtos selecionados</p>
                <!-- Informação de tempo limitado da oferta com ícone de relógio -->
                <p class="timer"><i class="fas fa-clock"></i> Oferta válida por tempo limitado!</p>
                <!-- Botão desabilitado para informações sobre ofertas (provavelmente será ativado com JavaScript) -->
                <button class="btn-promo-info" disabled>
                    <i class="fas fa-tag"></i> Ofertas Especiais
                </button>
            </div>
            <!-- Área para imagem/conteúdo visual da promoção -->
            <div class="mega-promo-image">
            </div>
        </section>

        <!-- Seção que exibe produtos em destaque -->
        <section class="products-highlight">
            <!-- Título da seção -->
            <h2>Produtos em Destaque</h2>
            <!-- Subtítulo da seção -->
            <p>Os melhores produtos com os maiores descontos</p>
            <!-- Grid (grade) de produtos em destaque -->
            <!-- Grid (grade) de produtos em destaque -->
            <div class="product-grid">
                <!-- ============ PRIMEIRO PRODUTO - CÂMERA POLAROID ============ -->
                <!-- Container do produto com atributo data-url para navegação ao clicar -->
                <div class="product-card" data-url="produto-5-polaroide.php">
                    <!-- Badge indicando o desconto percentual -->
                    <div class="product-badge">-10%</div>
                    <!-- Botão para adicionar produto à lista de desejos/favoritos -->
                    <button class="wishlist-btn"><i class="far fa-heart"></i></button>
                    <!-- Imagem do produto com fallback para SVG caso a imagem não carregar -->
                    <img src="../imagens-produtos/pola1.jpg" alt="Câmera Polaroid Fujifilm" onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjgwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzY2NjY2NiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPlBvbGFyb2lkPC90ZXh0Pjwvc3ZnPg=='">
                    <!-- Container com as informações do produto -->
                    <div class="product-info">
                        <!-- Categoria do produto -->
                        <p class="product-category">Fotografia</p>
                        <!-- Título/nome do produto -->
                        <h3 class="product-title">Câmera Fujifilm Kit Mini 12 + Filmes</h3>
                        <!-- Rating (avaliação) do produto usando ícones de estrelas -->
                        <div class="product-rating">
                            <!-- Exibe 4 estrelas cheias e 1 meia estrela = 4.5/5 -->
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i>
                            <!-- Número de avaliações recebidas -->
                            <span>(123 avaliações)</span>
                        </div>
                        <!-- Preço com valor de desconto (antigo preço em cinza) -->
                        <p class="product-price">R$ 535,00 <span class="old-price">R$ 800,00</span></p>
                        <!-- Botão para adicionar produto ao carrinho com atributos de dados do produto -->
                        <button class="btn-add-to-cart" data-produto-id="5" data-nome="Câmera Fujifilm Kit Mini 12 + Filmes" data-preco="535.00">
                            <i class="fas fa-shopping-cart"></i> Adicionar ao Carrinho
                        </button>
                    </div>
                </div>

                <!-- ============ SEGUNDO PRODUTO - XBOX 360 ============ -->
                <!-- Container do produto com atributo data-url para navegação -->
                <div class="product-card" data-url="produto-16-xbox.php">
                    <!-- Badge indicando desconto de 40% -->
                    <div class="product-badge">-40%</div>
                    <!-- Botão de favoritos -->
                    <button class="wishlist-btn"><i class="far fa-heart"></i></button>
                    <!-- Imagem do Xbox com fallback SVG -->
                    <img src="../imagens-produtos/box1.jpg" alt="Xbox 360" onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjgwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzY2NjY2NiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPlhib3g8L3RleHQ+PC9zdmc+'">
                    <!-- Container com informações do produto -->
                    <div class="product-info">
                        <!-- Categoria: Games -->
                        <p class="product-category">Games</p>
                        <!-- Nome do produto -->
                        <h3 class="product-title">Microsoft Xbox 360 Super 250GB</h3>
                        <!-- Rating do produto: 3.5/5 estrelas -->
                        <div class="product-rating">
                            <!-- 3 estrelas cheias, 1 meia estrela, 1 vazia = 3.5/5 -->
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i><i class="far fa-star"></i>
                            <!-- Número de avaliações -->
                            <span>(99 avaliações)</span>
                        </div>
                        <!-- Preço com desconto -->
                        <p class="product-price">R$ 1.190,00 <span class="old-price">R$ 1.400,00</span></p>
                        <!-- Botão de adicionar ao carrinho com dados do produto -->
                        <button class="btn-add-to-cart" data-produto-id="16" data-nome="Microsoft Xbox 360 Super 250GB" data-preco="1190.00">
                            <i class="fas fa-shopping-cart"></i> Adicionar ao Carrinho
                        </button>
                    </div>
                </div>

                <!-- ============ TERCEIRO PRODUTO - CAMISETA BÁSICA ============ -->
                <!-- Container do produto -->
                <div class="product-card" data-url="produto-1-camiseta-basica.php">
                    <!-- Badge de desconto: 20% -->
                    <div class="product-badge">-20%</div>
                    <!-- Botão de favoritos -->
                    <button class="wishlist-btn"><i class="far fa-heart"></i></button>
                    <!-- Imagem da camiseta com fallback SVG -->
                    <img src="../imagens-produtos/camisa1.jpg" alt="Kit Camiseta Básica" onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjgwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzY2NjY2NiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkNhbWlzZXRhPC90ZXh0Pjwvc3ZnPg=='">
                    <!-- Container com informações do produto -->
                    <div class="product-info">
                        <!-- Categoria: Moda -->
                        <p class="product-category">Moda</p>
                        <!-- Nome do produto -->
                        <h3 class="product-title">Kit Camiseta Básica Masculina - 3 Peças</h3>
                        <!-- Rating do produto: 5/5 estrelas (perfeito!) -->
                        <div class="product-rating">
                            <!-- 5 estrelas cheias = 5/5 -->
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            <!-- Número de avaliações -->
                            <span>(50 avaliações)</span>
                        </div>
                        <!-- Preço com desconto -->
                        <p class="product-price">R$ 47,49 <span class="old-price">R$ 60,49</span></p>
                        <!-- Botão de adicionar ao carrinho com dados do produto -->
                        <button class="btn-add-to-cart" data-produto-id="1" data-nome="Kit Camiseta Básica Masculina - 3 Peças" data-preco="47.49">
                            <i class="fas fa-shopping-cart"></i> Adicionar ao Carrinho
                        </button>
                    </div>
                </div>
            </div>

            <!-- Link para visualizar todos os produtos da loja -->
            <a href="listagem-produtos.php">
                <!-- Botão estilizado para ver todos os produtos -->
                <button class="btn-view-all">Ver Todos os Produtos</button>
            </a>
        </section>
    </main>

    <!-- Ícone flutuante no canto inferior direito para dúvidas/suporte -->
    <div class="bottom-right-icon">
        <i class="fas fa-question-circle"></i>
    </div>

    <!-- ============ MODAL DO CARRINHO ============ -->
    <!-- Modal que exibe os itens do carrinho de compras -->
    <div id="cart-modal" class="modal">
        <div class="modal-content">
            <!-- Botão para fechar o modal (X) -->
            <span class="close-btn">&times;</span>
            <!-- Título do modal com ícone de carrinho -->
            <h2><i class="fas fa-shopping-cart"></i> Seu Carrinho</h2>
            <!-- Container onde os itens do carrinho serão inseridos dinamicamente via JavaScript -->
            <div id="cart-items-container" class="cart-items">
                <!-- Mensagem inicial quando o carrinho está vazio -->
                <p id="empty-cart-message" style="text-align: center; color: #666; padding: 20px;">Seu carrinho está vazio.</p>
            </div>
            <!-- Resumo do carrinho mostrando o total a pagar -->
            <div class="cart-summary">
                <p>Total: <span id="cart-total-value">R$ 0,00</span></p>
            </div>
            <!-- Botão para ir para a página de checkout/finalizar compra -->
            <button class="btn-primary btn-checkout" style="width: 100%; margin-top: 15px;">Finalizar Compra</button>
        </div>
    </div>

    <!-- ============ MODAL DE CEP (CÁLCULO DE FRETE) ============ -->
    <!-- Modal para inserir endereço usando busca de CEP -->
    <div id="cep-modal" class="modal">
        <div class="modal-content">
            <!-- Botão para fechar o modal -->
            <span class="close-btn" id="close-cep-modal">&times;</span>
            <!-- Título da modal -->
            <h2>Inserir Endereço</h2>
            <!-- Instrução para preenchimento -->
            <p>Preencha os campos abaixo para salvar seu endereço.</p>
            <!-- Formulário para envio de dados de endereço -->
            <form id="cep-form" method="POST" action="processa-cep.php" novalidate>
                <!-- Grupo de formulário para o CEP com layout em linha -->
                <div class="form-group cep-row">
                    <label for="cep">CEP</label>
                    <!-- Container wrapper para input CEP com botão de busca -->
                    <div class="cep-input-wrap">
                        <!-- Input para digitar o CEP no formato 00000-000 -->
                        <input type="text" id="cep" name="cep" placeholder="00000-000" maxlength="9" required>
                        <!-- Botão para buscar dados do CEP em API externa -->
                        <button type="button" id="buscar-cep-btn" class="btn-small">Buscar</button>
                        <!-- Indicador de carregamento que aparece enquanto busca o CEP -->
                        <span id="cep-loading" style="display:none;margin-left:8px;font-size:0.9em;color:#666;">Buscando...</span>
                    </div>
                </div>
                <!-- Grupo: campo para o logradouro (rua) - preenchido automaticamente -->
                <div class="form-group">
                    <label for="logradouro">Rua</label>
                    <!-- Campo readonly pois é preenchido automaticamente pela busca do CEP -->
                    <input type="text" id="logradouro" name="logradouro" placeholder="Ex: Rua das Flores" readonly>
                </div>
                <!-- Grupo: campo para número da residência -->
                <div class="form-group">
                    <label for="numero">Número</label>
                    <input type="text" id="numero" name="numero" placeholder="Ex: 123" required>
                </div>
                <!-- Grupo: campo para o bairro (opcional) -->
                <div class="form-group">
                    <label for="bairro">Bairro</label>
                    <input type="text" id="bairro" name="bairro" placeholder="Ex: Centro">
                </div>
                <!-- Grupo: campo para a cidade (opcional) -->
                <div class="form-group">
                    <label for="cidade">Cidade</label>
                    <input type="text" id="cidade" name="cidade" placeholder="Ex: São Paulo">
                </div>
                <!-- Grupo: campo para o estado - sigla (opcional) -->
                <div class="form-group">
                    <label for="estado">Estado</label>
                    <input type="text" id="estado" name="estado" placeholder="Ex: SP">
                </div>
                <!-- Campo oculto para indicar que é endereço principal -->
                <input type="hidden" name="tipo" value="principal">
                <!-- Botão para enviar o formulário -->
                <button type="submit" class="btn-primary">Salvar Endereço</button>
            </form>
        </div>
    </div>

    <!-- ============ MODAL DE BLOQUEIO - CEP ============ -->
    <!-- Modal que bloqueia acesso à funcionalidade de CEP para usuários não logados -->
    <div id="block-modal-cep" class="modal" style="display:none;">
        <div class="modal-content block-content">
            <!-- Botão para fechar o modal -->
            <span class="close-btn" id="close-block-modal-cep">&times;</span>
            <!-- Título indicando restrição -->
            <h2>Área restrita - CEP</h2>
            <!-- Descrição da restrição -->
            <p>Para calcular o frete e inserir o endereço, você precisa estar logado.</p>
            <!-- Ícone de cadeado para indicar restrição -->
            <i class="fas fa-lock"></i>
            <!-- Texto motivacional para login -->
            <p>Faça login ou crie uma conta para prosseguir:</p>
            <!-- Lista de benefícios ao fazer login -->
            <ul>
                <li><i class="fas fa-check"></i> Calcular frete</li>
                <li><i class="fas fa-check"></i> Salvar endereços</li>
            </ul>
            <!-- Container com botões de ação -->
            <div class="btn-container">
                <!-- Link para página de login -->
                <a href="login.php" class="btn-primary">Fazer Login</a>
                <!-- Link para página de cadastro -->
                <a href="cadastro.php" class="btn-secondary">Criar Conta</a>
            </div>
        </div>
    </div>

    <!-- ============ MODAL DE BLOQUEIO - PRODUTOS ============ -->
    <!-- Modal que bloqueia acesso aos detalhes de produtos para usuários não logados -->
    <div id="block-modal-product" class="modal" style="display:none;">
        <div class="modal-content block-content">
            <!-- Botão para fechar o modal -->
            <span class="close-btn" id="close-block-modal-product">&times;</span>
            <!-- Título indicando restrição -->
            <h2>Área restrita - Produtos</h2>
            <!-- Descrição do que requer login -->
            <p>Para acessar detalhes do produto ou a listagem completa, faça login.</p>
            <!-- Ícone de cadeado para indicar restrição -->
            <i class="fas fa-lock"></i>
            <!-- Texto motivacional para criar conta -->
            <p>Ao criar uma conta você poderá salvar endereços, acompanhar pedidos e finalizar compras.</p>
            <!-- Lista de benefícios ao fazer login -->
            <ul>
                <li><i class="fas fa-check"></i> Ver detalhes do produto</li>
                <li><i class="fas fa-check"></i> Acessar listagem completa</li>
            </ul>
            <!-- Container com botões de ação -->
            <div class="btn-container">
                <!-- Link para página de login -->
                <a href="login.php" class="btn-primary">Fazer Login</a>
                <!-- Link para página de cadastro -->
                <a href="cadastro.php" class="btn-secondary">Criar Conta</a>
            </div>
        </div>
    </div>

    <!-- ============ SCRIPT FINAL ============ -->
    <script>
        // Define uma variável global JavaScript para armazenar o status de login do usuário
        // Se 'usuario_nome' existe na sessão PHP, retorna 'true', caso contrário 'false'
        window.isUserLoggedIn = <?php echo isset($_SESSION['usuario_nome']) ? 'true' : 'false'; ?>;
        // Exibe no console do navegador o status de login para fins de debug
        console.log('Status de login:', window.isUserLoggedIn);
    </script>
    <!-- Inclui o arquivo footer.php que contém o rodapé do site (copyright, links, etc) -->
    <?php include "../componentes/footer.php"; ?>
</body>

</html>