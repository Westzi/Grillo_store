<?php
session_start();
// AJUSTE O CAMINHO DA CONEXÃO CONFORME SUA ESTRUTURA
require_once('conexao.php');

$produto_json = "{}"; // Objeto padrão para JS
$produto_db = null;
$erro = null;
$produto_encontrado = false;

// 🚨 VERIFICAÇÃO INICIAL DA CONEXÃO
if (!isset($conexao) || ($conexao->connect_error ?? false)) {
  $erro = "Falha na conexão com o banco de dados. " . ($conexao->connect_error ?? '');
}

function formatar_preco($preco)
{
  $preco = is_numeric($preco) ? $preco : 0;
  return number_format($preco, 2, ',', '.');
}

// 1. CAPTURAR O ID DO PRODUTO DA URL
$produto_id = htmlspecialchars($_GET['produto'] ?? ''); // Busca ID

if (empty($produto_id)) {
  $erro = "Nenhum produto especificado na URL. Use /checkout.php?produto=ID_DO_PRODUTO";
} else if (!$erro) { // Só tenta buscar se não houver erro de conexão
  // 2. BUSCAR NO BANCO DE DADOS PELO ID
  $sql = "SELECT id, nome, preco, imagem FROM produtos WHERE id = ? LIMIT 1";

  if ($stmt = $conexao->prepare($sql)) {
    // 'i' indica que o parâmetro é um inteiro
    $stmt->bind_param("i", $produto_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
      $produto_db = $resultado->fetch_assoc();
      $produto_encontrado = true;

      // Caminho da imagem: Correção para a estrutura 'uploads/'
      $caminho_imagem = 'uploads/' . $produto_db['imagem'];

      // 3. PREPARAR DADOS PARA O JAVASCRIPT
      $frete = ($produto_db['preco'] < 19) ? 9.90 : 0.00;
      // Lógica de parcelas: Maior que R$1000 = 18x, senão 12x
      $parcelas_max = ($produto_db['preco'] >= 1000.00) ? 18 : 12;
      $cor = "Padrão"; // Cor fixa ou ajuste para buscar no DB, se aplicável

      $dados_js = [
        'id' => (int)$produto_db['id'],
        'slug' => 'item-' . $produto_db['id'],
        'nome' => $produto_db['nome'],
        'preco' => (float)$produto_db['preco'],
        'imagem' => $caminho_imagem,
        'cor' => $cor,
        'parcelas' => $parcelas_max,
        'frete' => $frete
      ];
      $produto_json = json_encode($dados_js);
    } else {
      $erro = "Produto não encontrado no banco de dados para o ID: " . $produto_id;
    }
    $stmt->close();
  } else {
    $erro = "Erro de preparação da consulta: " . $conexao->error;
  }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Finalizar Pedido - Checkout | Grillo Store</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="../estilo/style-checkout.css">
</head>

<body>
  <header class="header">
    <div class="header-content">
      <a href="listagem-produtos.php" class="logo">Grillo Store</a>
      <div class="header-actions">
        <button id="darkModeToggle" class="dark-mode-button">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
            viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="feather feather-moon">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
          </svg>
        </button>
        <div class="secure-checkout">
          <i class="fas fa-lock"></i>
          <span>Compra Segura</span>
        </div>
      </div>
    </div>
  </header>

  <main class="checkout-container">
    <h1 class="page-title">Finalizar Pedido</h1>

    <?php if ($erro): ?>
      <div style="background-color: #fdd; border: 1px solid #f00; padding: 15px; margin-bottom: 20px; border-radius: 4px; color: #c00;">
        <h2>⚠️ Erro Crítico</h2>
        <p><?= htmlspecialchars($erro); ?></p>
        <p>Verifique o ID do produto na URL ou a conexão com o banco de dados.</p>
      </div>
    <?php endif; ?>

    <form id="checkout-form" class="checkout-form" action="processar_pedido.php" method="POST">
      <div class="checkout-sections">

        <section class="section-contact">
          <h2 class="section-title">1. Contato e Endereço de Entrega</h2>
          <div class="form-group">
            <label for="name">Nome Completo</label>
            <input type="text" id="name" name="name" required>
          </div>
          <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required>
          </div>
          <div class="form-group">
            <label for="cep">CEP</label>
            <input type="text" id="cep" name="cep" placeholder="Ex: 00000-000" maxlength="9" required>
          </div>

          <div class="address-group">
            <div class="form-group address-field">
              <label for="address">Endereço (Rua, Avenida, etc.)</label>
              <input type="text" id="address" name="address" required>
            </div>
            <div class="form-group number-field">
              <label for="number">Número</label>
              <input type="text" id="number" name="number" required>
            </div>
          </div>

          <div class="form-group">
            <label for="complement">Complemento (Opcional)</label>
            <input type="text" id="complement" name="complement">
          </div>

          <div class="city-group">
            <div class="form-group">
              <label for="city">Cidade</label>
              <input type="text" id="city" name="city" required>
            </div>
            <div class="form-group">
              <label for="state">Estado (UF)</label>
              <input type="text" id="state" name="state" maxlength="2" required>
            </div>
          </div>
        </section>

        <section class="section-payment">
          <h2 class="section-title">2. Método de Pagamento</h2>

          <div class="payment-options-list">
            <div class="payment-option selected" data-method="credit-card">
              <i class="far fa-credit-card"></i>
              <label for="pay-card">Cartão de Crédito/Débito</label>
            </div>
            <div class="payment-option" data-method="pix">
              <i class="fas fa-qrcode"></i>
              <label for="pay-pix">Pix</label>
            </div>
            <div class="payment-option" data-method="boleto">
              <i class="fas fa-barcode"></i>
              <label for="pay-boleto">Boleto Bancário</label>
            </div>
          </div>

                    <div id="credit-card-form" class="payment-details-form">
            <p></p>
          </div>

          <div id="pix-info" class="payment-details-info hidden"></div>
          <div id="boleto-info" class="payment-details-info hidden"></div>

          <button type="submit" class="button primary finalize-button">
            Finalizar Pedido e Pagar
          </button>

          <a href="listagem-produtos.php" class="continue-shopping">Continuar Comprando</a>
        </section>

      </div>

      <aside class="order-summary-sidebar">
        <h2 class="section-title">Resumo do Pedido</h2>

        <div class="order-item" id="order-items">
          <img src="<?= $produto_db ? htmlspecialchars($caminho_imagem) : '' ?>" alt="Produto" class="item-image" id="checkout-img">
          <div class="item-details">
            <h3 class="item-name" id="checkout-name">
              <?php echo $produto_db ? htmlspecialchars($produto_db['nome']) : ($erro ? 'Erro ao carregar produto' : 'Carregando Produto...'); ?>
            </h3>
            <p class="item-specs" id="checkout-color">Cor: <?= $produto_db ? 'Padrão' : 'N/A' ?></p>
            <p class="item-quantity">Qtd: 1</p>
            <p class="item-price" id="checkout-price">R$ <?= $produto_db ? formatar_preco($produto_db['preco']) : '0,00' ?></p>
          </div>
        </div>

        <div class="summary-details">
          <div class="summary-line">
            <span>Subtotal</span>
            <span id="subtotal-value">R$ <?= $produto_db ? formatar_preco($produto_db['preco']) : '0,00' ?></span>
          </div>
          <div class="summary-line">
            <span>Frete</span>
            <span id="shipping-value"><?= $produto_db ? (($frete == 0) ? 'Grátis' : 'Calculando...') : 'Calculando...' ?></span>
          </div>
          <div class="summary-separator"></div>
          <div class="summary-line total">
            <span>Total a Pagar</span>
            <span id="total-value">R$ 0,00</span>
          </div>
        </div>
      </aside>
    </form>

  </main>

  <script src="../script/script-checkout.js"></script>

  <script>
    // Transfere o objeto PHP para uma variável JavaScript global
    window.produtoData = <?= $produto_json; ?>;
    <?php if ($erro): ?>
      // Se houver erro, passa a flag ou mensagem para o JS (embora o PHP já mostre a mensagem)
      window.produtoError = "<?= htmlspecialchars($erro); ?>";
    <?php endif; ?>
  </script>

  <?php // include "../componentes/footer.php"; 
  ?>
</body>

</html>