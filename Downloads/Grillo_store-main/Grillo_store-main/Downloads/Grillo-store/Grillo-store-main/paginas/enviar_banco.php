<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'grillo_store_db';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    echo "error";
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $nome = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $mensagem = $conn->real_escape_string($_POST['mensagem']);
    
    $sql = "INSERT INTO mensagens (nome, email, mensagem) VALUES ('$nome', '$email', '$mensagem')";
    
    if ($conn->query($sql) === TRUE) {
        // REMOVIDO: Envio de emails lentos
        echo "success";
    } else {
        // Backup rápido em arquivo
        $dados = "Nome: $nome | E-mail: $email | Mensagem: $mensagem | Data: " . date('d/m/Y H:i:s') . "\n";
        file_put_contents('backup_mensagens.txt', $dados, FILE_APPEND);
        echo "success"; // Mesmo com erro no banco, mostra sucesso para o usuário
    }
    
    $conn->close();
    
} else {
    echo "error";
}
?>