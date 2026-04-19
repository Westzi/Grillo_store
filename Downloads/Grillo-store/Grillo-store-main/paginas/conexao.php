<?php 

$servername = "localhost"; // geralmente localhost
$username = "root";        // usuário do MySQL
$password = "";            // senha do MySQL (vazia por padrão no XAMPP)
$database = "grillo_store_db"; // substitua pelo nome do seu banco

// Criar conexão
$conexao = new mysqli($servername, $username, $password, $database);

// Verificar conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

?>