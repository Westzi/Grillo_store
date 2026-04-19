<?php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $mensagem = $_POST['mensagem'];
    
    // ⚠️ TROCA PELO SEU NÚMERO! (formato: 5511999999999)
    $numero_whatsapp = "5521999999999"; // EXEMPLO
    
    // Monta mensagem para WhatsApp
    $msg_whatsapp = "Olá! Nova mensagem do site:%0A%0A";
    $msg_whatsapp .= "*Nome:* $nome%0A";
    $msg_whatsapp .= "*E-mail:* $email%0A";
    $msg_whatsapp .= "*Mensagem:*%0A$mensagem%0A%0A";
    $msg_whatsapp .= "Data: " . date('d/m/Y H:i:s');
    
    // Salva backup
    $dados = "Nome: $nome | E-mail: $email | Mensagem: $mensagem | Data: " . date('d/m/Y H:i:s') . "\n";
    file_put_contents('backup_whatsapp.txt', $dados, FILE_APPEND);
    
    // Retorna sucesso + link do WhatsApp
    echo "success|https://wa.me/$numero_whatsapp?text=$msg_whatsapp";
    
} else {
    echo "error";
}
?>