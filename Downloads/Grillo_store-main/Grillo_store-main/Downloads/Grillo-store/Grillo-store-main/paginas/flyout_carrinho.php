<?php 

$total_carrinho = 0; 
?>
<div class="cart-flyout" id="cart-flyout">
    <div class="flyout-content-wrapper">
        <div class="flyout-header">
            <h3>Seu Carrinho</h3>
            <button class="close-flyout" id="close-cart-flyout">&times;</button>
        </div>
        
        <div class="flyout-body">
            <?php 
          
            if (!empty($_SESSION['carrinho'])) {
                
                echo "<ul class='flyout-item-list'>";
                
                foreach ($_SESSION['carrinho'] as $chave => $item) { 
                  
                    $subtotal = $item['quantidade'] * $item['preco'];
                    $total_carrinho += $subtotal;
                    $preco_unitario_formatado = number_format($item['preco'], 2, ',', '.');
                    $subtotal_formatado = number_format($subtotal, 2, ',', '.');

                
                    echo "<li class='flyout-item' id='flyout-item-" . htmlspecialchars($chave) . "'>";
                    echo "  <div class='item-details-block'>";
                    echo "      <p class='item-name'><strong>" . htmlspecialchars($item['nome']) . "</strong></p>";
                    echo "      <p class='item-details'>" . htmlspecialchars($item['quantidade']) . " x R$ " . $preco_unitario_formatado . "</p>";
                    echo "  </div>";
                    echo "  <div class='item-actions-block'>";
                    echo "      <p class='item-subtotal'>R$ " . $subtotal_formatado . "</p>";
                    
                  
                    echo "      <button 
                                        type='button' 
                                        class='remove-item-button' 
                                        onclick='removerItemComAjax(\"" . htmlspecialchars($chave) . "\")'>
                                        Remover
                                    </button>";

                    echo "  </div>";
                    echo "</li>"; 
                }
                echo "</ul>";
                
            } else {
                
                echo "<p style='text-align: center; padding: 20px; color: var(--text-color);'>Seu carrinho está vazio.</p>";
            }
            ?>
        </div>
        
        <div class="flyout-footer">
            <?php if (!empty($_SESSION['carrinho'])): ?>
                <p class='flyout-total'><strong>Total: R$ <?php echo number_format($total_carrinho, 2, ',', '.'); ?></strong></p>
                <div class="flyout-actions">
                    <button class="back-button" id="continue-shopping">Continuar Comprando</button>
                    <a href="carrinho.php" class="checkout-button" id="checkout-flyout-button">Finalizar Compra</a>
                </div>
            <?php else: ?>
                <div class="flyout-actions">
                    <button class="back-button" id="continue-shopping">Voltar às Compras</button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>