<?php
session_start();
header('Content-Type: application/json');


if (!isset($_POST['produto_id'])) {
    echo json_encode(['status' => 'erro', 'message' => 'ID do produto não fornecido.']);
    exit;
}

$id_remover = filter_var($_POST['produto_id'], FILTER_SANITIZE_NUMBER_INT);


if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    echo json_encode(['status' => 'erro', 'message' => 'Carrinho vazio.']);
    exit;
}

$removido = false;


foreach ($_SESSION['carrinho'] as $indice => $item) {
 
    if (isset($item['id']) && $item['id'] == $id_remover) {
        unset($_SESSION['carrinho'][$indice]);
        $removido = true;
        break; 
    }
}

if ($removido) {
    
    $_SESSION['carrinho'] = array_values($_SESSION['carrinho']);
    
   
    $new_cart_count = 0;
    if (!empty($_SESSION['carrinho'])) {
         $new_cart_count = array_sum(array_column($_SESSION['carrinho'], 'quantidade'));
    }

    echo json_encode([
        'status' => 'sucesso', 
        'message' => 'Produto removido com sucesso.', 
        'cart_count' => $new_cart_count
    ]);
} else {
    echo json_encode(['status' => 'erro', 'message' => 'Produto não encontrado no carrinho.']);
}
?>