<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'grillo_store_db';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Erro ao conectar: " . $conn->connect_error);
}

// Buscar mensagens não lidas primeiro
$sql = "SELECT * FROM mensagens ORDER BY lida ASC, data_envio DESC";
$result = $conn->query($sql);

// Contar mensagens não lidas
$sql_count = "SELECT COUNT(*) as total_nao_lidas FROM mensagens WHERE lida = 0";
$count_result = $conn->query($sql_count);
$nao_lidas = $count_result->fetch_assoc()['total_nao_lidas'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel de Mensagens - Grillo Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../estilo/painel_mensagens.css">
</head>
<body>
    <!-- HEADER -->
    <header class="auth-header">
        <a href="super-administrador.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <div class="logo-container">
            <i class="fas fa-shopping-cart"></i> Grillo Store
        </div>
        <button id="darkModeToggle" class="btn-dark-mode" aria-label="Alternar modo claro/escuro">
            <i class="fas fa-moon"></i>
        </button>
    </header>

    <div class="container">
        <div class="painel-header">
            <h1>📨 Painel de Mensagens</h1>
            <div style="display: flex; align-items: center; gap: 20px;">
                <div class="stats">
                    <strong>Total: <?php echo $result->num_rows; ?> mensagens</strong>
                    <?php if($nao_lidas > 0): ?>
                    <span class="badge"><?php echo $nao_lidas; ?> não lidas</span>
                    <?php endif; ?>
                </div>
                
                <!-- BOTÕES DO GMAIL -->
                <div class="gmail-buttons">
                    <button class="btn-gmail" onclick="abrirGmailCompleto()">
                        <i class="fas fa-envelope"></i> Abrir Email da Loja
                    </button>
                </div>
            </div>
        </div>
        
        <?php while($msg = $result->fetch_assoc()): ?>
        <div class="mensagem <?php echo $msg['lida'] ? 'lida' : 'nao-lida'; ?>">
            <div class="mensagem-header">
                <div class="cliente-info">
                    <h3><?php echo htmlspecialchars($msg['nome']); ?></h3>
                    <div class="cliente-email"><?php echo htmlspecialchars($msg['email']); ?></div>
                </div>
                <div class="data">Enviado em: <?php echo date('d/m/Y H:i', strtotime($msg['data_envio'])); ?></div>
            </div>
            
            <div class="mensagem-texto">
                <?php echo nl2br(htmlspecialchars($msg['mensagem'])); ?>
            </div>
            
            <div class="acoes">
                <?php if(!$msg['lida']): ?>
                <button class="btn-lida" onclick="marcarLida(<?php echo $msg['id']; ?>)">
                    <i class="fas fa-check"></i> Marcar como lida
                </button>
                <?php endif; ?>
                <button class="btn-responder" onclick="responderMensagem('<?php echo $msg['email']; ?>', '<?php echo htmlspecialchars($msg['nome']); ?>')">
                    <i class="fas fa-reply"></i> Responder Mensagem
                </button>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

     <?php include "../componentes/footer.php"; ?>


    <!-- SCRIPT CORRIGIDO -->
    <script>
    // Dark Mode
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Dark Mode Script Carregado!');
        
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;
        
        // Verificar se dark mode estava ativo
        if (localStorage.getItem('darkMode') === 'enabled') {
            body.classList.add('dark-mode');
            if (darkModeToggle) {
                darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
            }
        }
        
        // Clique no botão dark mode
        if (darkModeToggle) {
            darkModeToggle.addEventListener('click', function() {
                if (body.classList.contains('dark-mode')) {
                    body.classList.remove('dark-mode');
                    darkModeToggle.innerHTML = '<i class="fas fa-moon"></i>';
                    localStorage.setItem('darkMode', 'disabled');
                } else {
                    body.classList.add('dark-mode');
                    darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
                    localStorage.setItem('darkMode', 'enabled');
                }
            });
        }
    });

    function marcarLida(id) {
        fetch('marcar_lida.php?id=' + id)
            .then(() => {
                location.reload();
            });
    }
    
    // FUNÇÃO CORRIGIDA - Email do cliente no "Para:", email da loja na assinatura
    function responderMensagem(emailCliente, nomeCliente) {
        const assunto = "Resposta - Grillo Store";
        const corpo = `Olá ${nomeCliente},\n\nAgradecemos seu contato!\n\nComo podemos ajudá-lo?\n\nAtenciosamente,\nEquipe Grillo Store\ngrillostore378@gmail.com`;
        
        // CORRETO: "to=" é o email do CLIENTE, assinatura é email da LOJA
        const gmailUrl = `https://mail.google.com/mail/u/0/?view=cm&fs=1&to=${encodeURIComponent(emailCliente)}&su=${encodeURIComponent(assunto)}&body=${encodeURIComponent(corpo)}`;
        window.open(gmailUrl, '_blank');
    }
    
    function abrirGmailCompleto() {
        window.open('https://mail.google.com/mail/u/0/', '_blank');
    }
    </script>
</body>
</html>
<?php $conn->close(); ?>