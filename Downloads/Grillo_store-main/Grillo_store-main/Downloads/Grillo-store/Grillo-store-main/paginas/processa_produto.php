<?php
session_start(); // Adicionado para consistência, se middleware usar
require_once('conexao.php');

$acao = $_POST['acao'] ?? '';
$id = $_POST['id'] ?? null; // ID é necessário para update e delete

// Redirecionamento de erro padrão
function redirecionar_erro($msg) {
    header("Location: super-administrador.php?erro=" . urlencode($msg));
    exit();
}

if ($acao === 'create' || $acao === 'update') {
    // 1. Coleta e validação básica de dados
    $nome = $_POST['nome'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $preco = $_POST['preco'] ?? 0.00;
    $estoque = $_POST['estoque'] ?? 0;
    $descricao = $_POST['descricao'] ?? '';

    // Se a ação for UPDATE, buscamos a imagem antiga
    $imagemAntiga = null;
    if ($acao === 'update') {
        if ($id === null) {
            redirecionar_erro("ID do produto é obrigatório para atualização.");
        }
        $stmt = $conexao->prepare("SELECT imagem FROM produtos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $imagemAntiga = $row['imagem'];
        }
        $stmt->close();
    }

    // 2. Lógica de Upload
    $nomeImagemSalvar = $imagemAntiga; // Por padrão, mantém a imagem antiga

    if (!empty($_FILES['imagem']['name'])) {
        $arquivo = $_FILES['imagem'];
        
        // Validação de erro do arquivo
        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
             redirecionar_erro("Erro no upload do arquivo: Código " . $arquivo['error']);
        }

        $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
        $nomeImagem = uniqid() . "." . $extensao; // Gera um nome único
        $caminhoDestino = "uploads/" . $nomeImagem;

        // Tenta mover o arquivo
        if (move_uploaded_file($arquivo['tmp_name'], $caminhoDestino)) {
            $nomeImagemSalvar = $nomeImagem;
            
            // Se for update e o upload deu certo, deleta a imagem antiga
            if ($acao === 'update' && $imagemAntiga && file_exists("uploads/" . $imagemAntiga)) {
                unlink("uploads/" . $imagemAntiga);
            }
        } else {
            redirecionar_erro("Falha ao mover o arquivo de upload. Verifique as permissões da pasta 'uploads/'.");
        }
    }
    
    // 3. Execução do Banco de Dados
    if ($acao === 'create') {
        $sql = "INSERT INTO produtos (nome, categoria, preco, estoque, descricao, imagem) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        // Tipos: s (string), s (string), d (double/decimal), i (integer), s (string), s (string)
        $stmt->bind_param("ssdiss", $nome, $categoria, $preco, $estoque, $descricao, $nomeImagemSalvar);
        
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: super-administrador.php?ok=Produto adicionado com sucesso!");
            exit();
        } else {
            redirecionar_erro("Erro ao inserir no banco: " . $stmt->error);
        }
    }

    if ($acao === 'update') {
        $sql = "UPDATE produtos SET nome=?, categoria=?, preco=?, estoque=?, descricao=?, imagem=? WHERE id=?";
        $stmt = $conexao->prepare($sql);
        // Tipos: s, s, d, i, s, s, i
        $stmt->bind_param("ssdissi", $nome, $categoria, $preco, $estoque, $descricao, $nomeImagemSalvar, $id);
        
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: super-administrador.php?ok=Produto atualizado com sucesso!");
            exit();
        } else {
            redirecionar_erro("Erro ao atualizar no banco: " . $stmt->error);
        }
    }
}

// Ação de DELETAR
if ($acao === 'delete') {
    if ($id === null) {
         redirecionar_erro("ID do produto é obrigatório para exclusão.");
    }
    
    // Opcional: Busca a imagem para deletar o arquivo do servidor
    $stmt = $conexao->prepare("SELECT imagem FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $imagemParaDeletar = $row['imagem'];
        if ($imagemParaDeletar && file_exists("uploads/" . $imagemParaDeletar)) {
            unlink("uploads/" . $imagemParaDeletar);
        }
    }
    $stmt->close();
    
    // Deleta o registro no BD
    $stmt = $conexao->prepare("DELETE FROM produtos WHERE id=?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: super-administrador.php?ok=Produto excluído com sucesso!");
        exit();
    } else {
        redirecionar_erro("Erro ao deletar no banco: " . $stmt->error);
    }
}

// Fechamento final da conexão (boa prática)
$conexao->close();

?>