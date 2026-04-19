<?php
session_start();
include_once('conexao.php');

$nome_completo = trim($_POST['nome']);
$data_nascimento = $_POST['dataNascimento'];
$email = trim($_POST['email']);
$senha = $_POST['senha'];
$confirmarSenha = $_POST['confirmaSenha'];
$cpf = trim($_POST['cpf']);
$telefone = trim($_POST['telefone']);
$data_cadastro = date("Y-m-d H:i:s"); 


if ($senha !== $confirmarSenha) {
    die("As senhas não coincidem.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Email inválido.");
}

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);


$stmt = $conexao->prepare("INSERT INTO usuarios(nome_completo, data_nascimento, email, senha, cpf, telefone, data_cadastro) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $nome_completo, $data_nascimento, $email, $senha_hash, $cpf, $telefone, $data_cadastro);


if ($stmt->execute()) {
    // Salva dados do usuário na sessão
    $_SESSION['usuario_id'] = $stmt->insert_id;
    $_SESSION['usuario_nome'] = $nome_completo;
    $_SESSION['usuario_email'] = $email;

    // Redireciona para a página principal
    header("Location: Principal.php");
    exit;
} else {
    $_SESSION['erro_cadastro'] = "Erro ao cadastrar: " . $stmt->error;
    header("Location: cadastro.php");
    exit;
}

$stmt->close();
$conexao->close();