<?php
session_start();

$is_ajax = isset($_POST['is_ajax']) && $_POST['is_ajax'] === 'true';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && 
    isset($_POST['produto_id'], $_POST['produto_nome'], $_POST['produto_preco'])) {
    
  
    $produto_id = filter_var($_POST['produto_id'], FILTER_SANITIZE_NUMBER_INT);
    
  
    $quantidade = isset($_POST['quantidade']) ? filter_var($_POST['quantidade'], FILTER_SANITIZE_NUMBER_INT) : 1; 
    $quantidade = max(1, (int)$quantidade); 
    
    
    $produto_nome = filter_var($_POST['produto_nome'], FILTER_SANITIZE_SPECIAL_CHARS);
    
 
    $produto_preco = filter_var($_POST['produto_preco'], FILTER_VALIDATE_FLOAT);

    
    if ($produto_id <= 0 || $quantidade <= 0 || $produto_preco === false) {
        
       
        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Dados do produto inválidos.']);
            exit();
        }
        
        $_SESSION['erro'] = 'Erro ao adicionar. Dados do produto inválidos (ID, Preço ou Quantidade).';
        
    } else {
       
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }

     
        $item_existe = false;
        
        $item_key = (string)$produto_id;
        
       
        if (array_key_exists($item_key, $_SESSION['carrinho'])) {
            
            $_SESSION['carrinho'][$item_key]['quantidade'] += $quantidade;
            $item_existe = true;
            
        } 
        
    
        if (!$item_existe) {
            
            $_SESSION['carrinho'][$item_key] = [
                'id' => $produto_id,
                'quantidade' => $quantidade,
                'nome' => $produto_nome,
                'preco' => $produto_preco, 
            ];
        }

        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Produto adicionado.']);
            exit();
        }
        
        $_SESSION['carrinho_sucesso'] = true; 
    }

    $pagina_destino = $_SERVER['HTTP_REFERER'] ?? 'listagem.php'; 
    
    header('Location: ' . $pagina_destino);
    exit();

} else {

    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Requisição inválida (dados mínimos ausentes).']);
        exit();
    }
    
    $_SESSION['erro'] = 'Requisição inválida. Não foi possível adicionar o produto.';
    header('Location: listagem-produtos.php'); 
    exit();
}
?>