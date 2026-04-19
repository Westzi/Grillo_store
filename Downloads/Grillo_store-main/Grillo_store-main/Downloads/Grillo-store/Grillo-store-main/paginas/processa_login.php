<?php
// DEBUG - REMOVA DEPOIS DE TESTAR
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include_once('conexao.php');

// 1. Verifica se o formulário foi submetido (se o botão 'submit' foi clicado)
if (isset($_POST['submit'])) {

    // Recebe os dados do formulário
    $email = trim($_POST['email']);
    $senha_digitada = $_POST['senha']; // Senha em texto puro digitada pelo usuário

    // DEBUG - Verifica se os dados estão chegando
    error_log("Tentativa de login - Email: $email");

    // Verifica se o email é válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['erro_login'] = "Email inválido.";
        header('Location: login.php');
        exit;
    }

    // Verifica se os campos não estão vazios
    if (empty($email) || empty($senha_digitada)) {
        $_SESSION['erro_login'] = "Preencha todos os campos.";
        header('Location: login.php');
        exit;
    }

    try {
        // 2. Consulta Preparada: Busca o usuário apenas pelo email
        $sql = "SELECT id, nome_completo, email, senha FROM usuarios WHERE email = ?";

        // Prepara a instrução
        $stmt = $conexao->prepare($sql);

        // Verifica se a preparação falhou
        if ($stmt === false) {
            throw new Exception('Erro na preparação da consulta: ' . $conexao->error);
        }

        // Associa o parâmetro (s = string)
        $stmt->bind_param("s", $email);

        // Executa
        $stmt->execute();

        // Obtém o resultado
        $result = $stmt->get_result();

        // 3. Verifica o Resultado da Busca
        if ($result->num_rows === 1) {

            $usuario = $result->fetch_assoc();
            $senha_hash = $usuario['senha']; // HASH da senha salva no banco

            // DEBUG
            error_log("Usuário encontrado: " . $usuario['email']);
            error_log("Hash no banco: " . $senha_hash);

            // 4. VERIFICAÇÃO FINAL DE SENHA (USANDO O HASH)
            if (password_verify($senha_digitada, $senha_hash)) {

                // LOGIN BEM-SUCEDIDO: Cria as variáveis de Sessão
                $_SESSION['logado'] = true;
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome_completo'];
                $_SESSION['usuario_email'] = $usuario['email'];

                // DEBUG
                error_log("Login bem-sucedido para: " . $usuario['email']);

                // Redireciona para a página principal
                header('Location: Principal.php');
                exit;
            } else {
                // Senha Incorreta
                error_log("Senha incorreta para: $email");
                $_SESSION['erro_login'] = "Email ou senha inválidos.";
                header('Location: login.php');
                exit;
            }
        } else {
            // Usuário (email) não encontrado
            error_log("Usuário não encontrado: $email");
            $_SESSION['erro_login'] = "Email ou senha inválidos.";
            header('Location: login.php');
            exit;
        }

        $stmt->close();
    } catch (Exception $e) {
        error_log("Erro no login: " . $e->getMessage());
        $_SESSION['erro_login'] = "Erro interno do sistema. Tente novamente.";
        header('Location: login.php');
        exit;
    }
} else {
    // Acesso direto ao arquivo, redireciona para o formulário
    header('Location: login.php');
    exit;
}

// Não feche a conexão aqui para evitar problemas com sessões
