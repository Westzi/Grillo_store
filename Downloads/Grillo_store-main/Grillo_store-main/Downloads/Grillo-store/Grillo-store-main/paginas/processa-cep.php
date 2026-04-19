<?php
session_start();

// Garante que o acesso é via POST (formulário)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: cep.php");
        exit;
}

include_once('conexao.php'); // Conexão com o banco

// ID do usuário logado (se houver). Se visitante, será null e trataremos abaixo.
$usuario_id = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : null;

// Recebe o CEP e remove qualquer caractere não numérico
$cep_raw = $_POST['cep'] ?? '';
$cep_digits = preg_replace('/\D/', '', $cep_raw);

// Valida o CEP (deve ter exatamente 8 dígitos)
if (strlen($cep_digits) !== 8) {
    $_SESSION['erro'] = 'CEP inválido.';
    header('Location: cep.php');
    exit;
}

// Formata o CEP com traço para armazenar (opcional)
$cep = substr($cep_digits, 0, 5) . '-' . substr($cep_digits, 5);

// Sanitiza e limita tamanho dos demais campos recebidos do formulário.
// strip_tags evita tags HTML; trim remove espaços; mb_substr limita o tamanho.
$logradouro   = mb_substr(trim(strip_tags($_POST['logradouro'] ?? '')), 0, 255);
$numero       = mb_substr(trim(strip_tags($_POST['numero'] ?? '')), 0, 20);
$complemento  = mb_substr(trim(strip_tags($_POST['complemento'] ?? '')), 0, 255);
$bairro       = mb_substr(trim(strip_tags($_POST['bairro'] ?? '')), 0, 255);
$cidade       = mb_substr(trim(strip_tags($_POST['cidade'] ?? '')), 0, 255);
$estado       = strtoupper(mb_substr(trim(strip_tags($_POST['estado'] ?? '')), 0, 2));
$tipo         = mb_substr(trim(strip_tags($_POST['tipo'] ?? 'principal')), 0, 50); // ex.: 'principal' ou 'entrega'

// Validação mínima: número do endereço obrigatório
if ($numero === '') {
    // Mensagem de erro exibida ao voltar para cep.php
    $_SESSION['erro'] = 'O número do endereço é obrigatório.<br><span class="erro-pequena" style="font-size:0.9em;color:#666;">Insira o número (ex.: 123)</span>';
    header('Location: cep.php');
    exit;
}

// Se o usuário não estiver logado, armazenamos o endereço temporariamente na sessão
if ($usuario_id === null) {
    $_SESSION['guest_endereco'] = [
        'cep' => $cep,
        'logradouro' => $logradouro,
        'numero' => $numero,
        'complemento' => $complemento,
        'bairro' => $bairro,
        'cidade' => $cidade,
        'estado' => $estado,
        'tipo' => $tipo,
    ];
    unset($_SESSION['erro']);
    // Redireciona de volta para a página principal (ou onde preferir)
    header('Location: Principal.php');
    exit;
}

// Nome da tabela onde os endereços são armazenados
$table = 'enderecos';

// Verifica se já existe endereço para este usuário e tipo
$query = "SELECT id FROM {$table} WHERE usuario_id = ? AND tipo = ? LIMIT 1";
if ($stmt = $conexao->prepare($query)) {
    $stmt->bind_param("is", $usuario_id, $tipo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Já existe: faz UPDATE no registro existente
        $stmt->bind_result($endereco_id);
        $stmt->fetch();
        $stmt->close();

        $sql = "UPDATE {$table} SET logradouro = ?, numero = ?, complemento = ?, bairro = ?, cidade = ?, estado = ?, cep = ? WHERE id = ?";
        if ($upd = $conexao->prepare($sql)) {
            $upd->bind_param("sssssssi", $logradouro, $numero, $complemento, $bairro, $cidade, $estado, $cep, $endereco_id);
            if ($upd->execute()) {
                // Sucesso: limpa erro e redireciona para a página principal
                unset($_SESSION['erro']);
                header("Location: Principal.php");
                exit;
            } else {
                // Loga o erro no servidor e informa usuário de forma genérica
                error_log("processa-cep update execute error: " . $upd->error);
                $_SESSION['erro'] = 'Erro ao atualizar o endereço.';
                header('Location: cep.php');
                exit;
            }
        } else {
            error_log("processa-cep update prepare error: " . $conexao->error);
            $_SESSION['erro'] = 'Erro interno.';
            header('Location: cep.php');
            exit;
        }
    } else {
        // Não existe: insere novo endereço
        $stmt->close();
        $sql = "INSERT INTO {$table} (usuario_id, logradouro, numero, complemento, bairro, cidade, estado, cep, tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if ($ins = $conexao->prepare($sql)) {
            // bind_param: i = integer, s = string (ordem conforme colunas)
            $ins->bind_param("issssssss", $usuario_id, $logradouro, $numero, $complemento, $bairro, $cidade, $estado, $cep, $tipo);
            if ($ins->execute()) {
                unset($_SESSION['erro']);
                header("Location: Principal.php");
                exit;
            } else {
                error_log("processa-cep insert execute error: " . $ins->error);
                $_SESSION['erro'] = 'Erro ao salvar o endereço.';
                header('Location: cep.php');
                exit;
            }
        } else {
            error_log("processa-cep insert prepare error: " . $conexao->error);
            $_SESSION['erro'] = 'Erro interno.';
            header('Location: cep.php');
            exit;
        }
    }
} else {
    // Erro ao preparar SELECT inicial
    error_log("processa-cep select prepare error: " . $conexao->error);
    $_SESSION['erro'] = 'Erro interno.';
    header('Location: cep.php');
    exit;
}

// Fecha a conexão ao final
$conexao->close();

// redireciona ou responde JSON se for AJAX
header('Location: Principal.php');
exit;
?>



