<?php
session_start();

// 1. Verificar se há dados de confirmação na sessão
if (!isset($_SESSION['pedido_confirmado'])) {
    // Se não houver, redireciona para a listagem (ou para o checkout)
    header('Location: listagem-produtos.php');
    exit;
}

// Carrega os dados da sessão
$dados_pedido = $_SESSION['pedido_confirmado'];

$previsao = $dados_pedido['previsao'] ?? '3 a 7 dias úteis';
$id_pedido = $dados_pedido['id'] ?? 'N/A';
$nome_cliente = $dados_pedido['nome_cliente'] ?? 'Cliente';
$nome_produto = $dados_pedido['nome_produto'] ?? 'Seu Produto';
$metodo_pagamento = $dados_pedido['pagamento'] ?? 'N/A';
$total = number_format($dados_pedido['total'] ?? 0, 2, ',', '.'); // Formatação BRL

// **OPCIONAL:** Limpar a sessão após ler (boas práticas)
unset($_SESSION['pedido_confirmado']); 
// if (isset($_SESSION['carrinho'])) unset($_SESSION['carrinho']);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado | Grillo Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../estilo/estilo-confirmacao.css">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="listagem-produtos.php" class="logo">Grillo Store</a> 
        </div>
    </header>

    <main class="confirmation-container">
        <div class="confirmation-box">
            <i class="fas fa-check-circle success-icon"></i>
            <h1 class="page-title">🎉 Obrigado, <?php echo htmlspecialchars($nome_cliente); ?>!</h1>
            
            <p class="order-id">
                Seu pedido **#<?php echo htmlspecialchars($id_pedido); ?>** foi confirmado!
            </p>

            <div class="summary-details">
                <p><strong>Item:</strong> <?php echo htmlspecialchars($nome_produto); ?></p>
                <p><strong>Total Pago:</strong> R$ <?php echo $total; ?></p>
                <p><strong>Pagamento:</strong> <?php echo htmlspecialchars($metodo_pagamento); ?></p>
            </div>
            
            <div class="delivery-time-info">
                <h2>🗓️ Previsão de Entrega</h2>
                <p>
                    O prazo estimado para a entrega do seu pedido é de:
                </p>
                <div class="delivery-time-box">
                    <strong><?php echo htmlspecialchars($previsao); ?></strong>
                </div>
            </div>

            <p style="margin-top: 20px;">
                Você receberá todos os detalhes por e-mail no endereço **<?php echo htmlspecialchars($dados_pedido['email_cliente'] ?? 'cadastrado'); ?>**.
            </p>
            
            <a href="listagem-produtos.php" class="button primary continue-shopping-btn">
                <i class="fas fa-arrow-left"></i> Continuar Comprando
            </a>
        </div>
    </main>
      
    <?php include "../componentes/footer.php"; ?>
    </body>
</html>