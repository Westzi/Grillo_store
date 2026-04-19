<?php
session_start();

if(isset($_POST['produto_id'])) {
    $produto_id = $_POST['produto_id'];

    if(isset($_SESSION['carrinho'][$produto_id])) {
        unset($_SESSION['carrinho'][$produto_id]);
    }

    // Recalcula total e quantidade
    $total = 0;
    $quantidade_carrinho = 0;
    if(isset($_SESSION['carrinho'])) {
        foreach($_SESSION['carrinho'] as $item) {
            $total += $item['preco'] * $item['quantidade'];
            $quantidade_carrinho += $item['quantidade'];
        }
    }

    echo json_encode([
        'success' => true,
        'total' => number_format($total, 2, ',', '.'),
        'quantidade_carrinho' => $quantidade_carrinho
    ]);
    exit;
}
?>
