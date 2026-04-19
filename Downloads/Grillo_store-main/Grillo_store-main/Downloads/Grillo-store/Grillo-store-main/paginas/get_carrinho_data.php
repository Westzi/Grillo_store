<?php
// OBRIGATÓRIO: Iniciar a sessão para acessar $_SESSION
session_start();

header('Content-Type: application/json');

$carrinho_data = [
    
    'carrinho' => isset($_SESSION['carrinho']) ? array_values($_SESSION['carrinho']) : [],
   
    'cart_count' => isset($_SESSION['carrinho']) ? array_sum(array_column($_SESSION['carrinho'], 'quantidade')) : 0,
];


echo json_encode($carrinho_data);
?>