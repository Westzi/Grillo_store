<?php
session_start();

// AJUSTE O CAMINHO DA CONEXÃO
require_once('conexao.php'); 

// 1. Verificar se a requisição é POST e se os dados essenciais foram enviados
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['slug'])) {
    // Redireciona de volta se não for POST válido
    header('Location: listagem-produtos.php');
    exit;
}

// 2. Coleta e Sanitização dos Dados
$slug = htmlspecialchars($_POST['slug']);
$nome_produto = htmlspecialchars($_POST['nome']); 
// Coletamos o Nome e Email, mas não vamos salvar no DB.
$nome_cliente = htmlspecialchars($_POST['name']); 
$email_cliente = htmlspecialchars($_POST['email']); 

$total = floatval($_POST['total']);
$qty = intval($_POST['qty']);
$pagamento = htmlspecialchars($_POST['pagamento']);
$parcelas = htmlspecialchars($_POST['parcelas'] ?? '1');

// Endereço (concatenamos para salvar no campo 'endereco_entrega' do DB)
$endereco_completo = implode(", ", [
    htmlspecialchars($_POST['address']),
    htmlspecialchars($_POST['number']),
    htmlspecialchars($_POST['complement'] ?? ''),
    htmlspecialchars($_POST['city']),
    htmlspecialchars($_POST['state']),
    htmlspecialchars($_POST['cep'])
]);

// Opcional: Coleta de outros dados para confirmação
$previsao = htmlspecialchars($_POST['previsao'] ?? '3-7 dias úteis');
$cor = htmlspecialchars($_POST['cor'] ?? 'Padrão');
$img = htmlspecialchars($_POST['img'] ?? '../imagens-produtos/placeholder.jpg');


// 3. Validação Básica
if ($total <= 0 || $qty <= 0) {
    header('Location: checkout.php?error=invalid_data');
    exit;
}

// 4. INSERIR O PEDIDO NO BANCO DE DADOS (DB)
// A QUERY FOI AJUSTADA: REMOVIDOS nome_cliente e email_cliente
$sql = "INSERT INTO pedidos (produto_slug, nome_produto, preco_total, quantidade, metodo_pagamento, endereco_entrega) 
        VALUES (?, ?, ?, ?, ?, ?)"; 

if ($stmt = $conexao->prepare($sql)) {
    // Liga os parâmetros (s=string, d=double/float, i=integer)
    // OS PARÂMETROS FORAM AJUSTADOS: REMOVIDOS NOME e EMAIL
    $stmt->bind_param("ssdiss", 
        $slug, 
        $nome_produto, 
        $total, 
        $qty, 
        $pagamento, 
        $endereco_completo
    );

    if ($stmt->execute()) {
        // Obter o ID real do pedido inserido no banco
        $id_real_pedido = $conexao->insert_id;

        // 5. SALVAR DADOS PARA A PÁGINA DE CONFIRMAÇÃO NA SESSÃO
        // O Nome e o Email do cliente serão salvos APENAS na sessão para exibição
        $_SESSION['pedido_confirmado'] = [
            'id' => $id_real_pedido,
            'slug' => $slug,
            'nome_produto' => $nome_produto,
            'total' => $total,
            'pagamento' => $pagamento,
            'parcelas' => $parcelas,
            'endereco' => $endereco_completo,
            'previsao' => $previsao, 
            'nome_cliente' => $nome_cliente, // Salvo na SESSÃO (Não no DB)
            'email_cliente' => $email_cliente, // Salvo na SESSÃO (Não no DB)
            'img' => $img
        ];
        
        // Opcional: Limpa a sessão do carrinho/item (se existisse)
        unset($_SESSION['carrinho']);
        
        // 6. Redirecionamento SÓ PARA A PÁGINA DE Confirmação
        header("Location: confirmacao-de-pedido.php"); 
        exit;

    } else {
        // Erro na execução
        die("Erro ao salvar o pedido: " . $stmt->error);
    }
    $stmt->close();
} else {
    // Erro na preparação do SQL
    die("Erro na preparação da consulta: " . $conexao->error);
}
?>