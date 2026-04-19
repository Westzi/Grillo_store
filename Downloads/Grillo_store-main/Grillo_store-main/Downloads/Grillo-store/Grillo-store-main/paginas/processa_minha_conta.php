<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome_completo']);
    $email = trim($_POST['email']);
    $cpf = trim($_POST['cpf']);
    $telefone = trim($_POST['telefone']);
    $data_nascimento = trim($_POST['data_nascimento']);

    $sql = "UPDATE usuarios SET nome_completo = ?, email = ?, cpf = ?, telefone = ?, data_nascimento = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sssssi", $nome, $email, $cpf, $telefone, $data_nascimento, $id_usuario);

    if ($stmt->execute()) {
        $_SESSION['nome_completo'] = $nome;
        header("Location: minha_conta.php?atualizado=1");
        exit();
    } else {
        echo "Erro ao atualizar: " . $stmt->error;
    }
}
?>
