<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Contato - Grillo Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../estilo/contato.css">
</head>
<body>
    <!-- HEADER -->
    <header class="auth-header">
        <a href="Principal.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <div class="logo-container">
            <i class="fas fa-shopping-cart"></i> Grillo Store
        </div>
        <button id="darkModeToggle" class="btn-dark-mode" aria-label="Alternar modo claro/escuro">
            <i class="fas fa-moon"></i>
        </button>
    </header>

    <!-- FORMULÁRIO -->
    <div class="form-contato">
        <h2>Como podemos te ajudar?</h2>

        <form id="formContato" method="POST">
            <div class="form-group">
                <label for="nome">Seu Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>

            <div class="form-group">
                <label for="email">Seu E-mail:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="mensagem">Mensagem:</label>
                <textarea id="mensagem" name="mensagem" rows="5" required placeholder="Descreva como podemos ajudá-lo..."></textarea>
            </div>

            <button type="submit" name="submit">Enviar Mensagem</button>
        </form>
    </div>

    <!-- POPUPS -->
    <div id="popupSuccess" class="popup">
        <div class="popup-content">
            <span class="close-popup">&times;</span>
            <div class="popup-icon">✅</div>
            <h3>Mensagem Enviada com Sucesso!</h3>
            <p>Sua mensagem foi recebida e nossa equipe já foi notificada.</p>
            <p><strong>📧 Responderemos pelo e-mail informado em até 1 dia útil.</strong></p>
            <div class="email-info">
                <small>E-mail de resposta: Grillo Store/small>
            </div>
            <button class="btn-close">Entendido</button>
        </div>
    </div>

    <div id="popupError" class="popup">
        <div class="popup-content">
            <span class="close-popup">&times;</span>
            <div class="popup-icon">❌</div>
            <h3>Erro ao Enviar</h3>
            <p>Houve um erro ao enviar sua mensagem. Tente novamente.</p>
            <button class="btn-close">Tentar Novamente</button>
        </div>
    </div>

    <!-- JAVASCRIPT INTERNO -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Script do Contato Carregado!');
        
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
                    // Desligar dark mode
                    body.classList.remove('dark-mode');
                    darkModeToggle.innerHTML = '<i class="fas fa-moon"></i>';
                    localStorage.setItem('darkMode', 'disabled');
                } else {
                    // Ligar dark mode
                    body.classList.add('dark-mode');
                    darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
                    localStorage.setItem('darkMode', 'enabled');
                }
            });
        }

        // Script do formulário
        document.getElementById('formContato').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('enviar_banco.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if(data === 'success') {
                    document.getElementById('popupSuccess').style.display = 'flex';
                    document.getElementById('formContato').reset();
                } else {
                    document.getElementById('popupError').style.display = 'flex';
                }
            })
            .catch(error => {
                document.getElementById('popupError').style.display = 'flex';
            });
        });

        document.querySelectorAll('.close-popup, .btn-close').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.popup').forEach(popup => {
                    popup.style.display = 'none';
                });
            });
        });

        document.querySelectorAll('.popup').forEach(popup => {
            popup.addEventListener('click', function(e) {
                if(e.target === this) {
                    this.style.display = 'none';// Fecha o popup ao clicar fora do conteúdo
                }
            });
        });
    });
    </script>
    <?php include "../componentes/footer.php"; ?>
</body>
</html>