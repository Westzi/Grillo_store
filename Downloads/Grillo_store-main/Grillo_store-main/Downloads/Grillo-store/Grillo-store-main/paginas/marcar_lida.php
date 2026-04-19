<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'grillo_store_db';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    header("Location: painel_mensagens.php?error=conexao");
    exit;
}

if(isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $sql = "UPDATE mensagens SET lida = 1 WHERE id = '$id'";
    
    if($conn->query($sql)) {
        // REDIRECIONAMENTO COM SUCESSO
        header("Location: painel_mensagens.php?success=lida");
    } else {
        header("Location: painel_mensagens.php?error=update");
    }
} else {
    header("Location: painel_mensagens.php?error=id");
}

$conn->close();
?>