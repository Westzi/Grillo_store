<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../estilo/sobrenos.css">
    <title>Sobre Nós - Grillo Store</title>
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

    <!-- CONTEÚDO -->
    <main class="auth-page">
        <div class="auth-card">
            <h1>👥 Sobre Nós</h1>
            <p style="color: var(--light-text); margin-bottom: 30px;">Conheça a equipe por trás da Grillo Store</p>

            <!-- MISSÃO -->
            <div class="content-section">
                <div class="section-header">
                    <i class="fas fa-bullseye"></i>
                    <h2>Nossa Missão</h2>
                </div>
                <div class="section-content">
                    <p>Desenvolver soluções digitais inovadoras que proporcionem a melhor experiência para nossos clientes, unindo tecnologia, design e funcionalidade.</p>
                </div>
            </div>

            <!-- EQUIPE -->
            <div class="content-section">
                <div class="section-header">
                    <i class="fas fa-users"></i>
                    <h2>Nossa Equipe</h2>
                </div>

                <div class="equipe-grid">
                    <div class="dev-card">
                        <img class="dev-image" src="../imagem/gabriel.jpg" alt="Gabriel Suliano" onerror="this.src='https://via.placeholder.com/100x100/28a745/ffffff?text=G'">
                        <div class="dev-info">
                            <h3>Gabriel Suliano</h3>
                            <p>Desenvolvedor</p>
                        </div>
                    </div>

                    <div class="dev-card">
                        <img class="dev-image" src="../imagem/Valentim.jpeg" alt="Samuel Valentim" onerror="this.src='https://via.placeholder.com/100x100/28a745/ffffff?text=S'">
                        <div class="dev-info">
                            <h3>Samuel Valentim</h3>
                            <p>Desenvolvedor</p>
                        </div>
                    </div>

                    <div class="dev-card">
                        <img class="dev-image" src="../imagem/pablo.jpeg" alt="Pablo Vinicius" onerror="this.src='https://via.placeholder.com/100x100/28a745/ffffff?text=P'">
                        <div class="dev-info">
                            <h3>Pablo Vinicius</h3>
                            <p>Desenvolvedor</p>
                        </div>
                    </div>

                    <div class="dev-card">
                        <img class="dev-image" src="../imagem/beatriz.jpg" alt="Beatriz Freitas" onerror="this.src='https://via.placeholder.com/100x100/28a745/ffffff?text=B'">
                        <div class="dev-info">
                            <h3>Beatriz Freitas</h3>
                            <p>Desenvolvedor</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TECNOLOGIAS -->
            <div class="content-section">
                <div class="section-header">
                    <i class="fas fa-code"></i>
                    <h2>Tecnologias</h2>
                </div>
                <div class="tech-grid">
                    <div class="tech-item">
                        <i class="fab fa-html5"></i>
                        <span>HTML5</span>
                    </div>
                    <div class="tech-item">
                        <i class="fab fa-css3-alt"></i>
                        <span>CSS3</span>
                    </div>
                    <div class="tech-item">
                        <i class="fab fa-js"></i>
                        <span>JavaScript</span>
                    </div>
                    <div class="tech-item">
                        <i class="fab fa-php"></i>
                        <span>PHP</span>
                    </div>
                    <div class="tech-item">
                        <i class="fas fa-database"></i>
                        <span>SQL</span>
                    </div>
                </div>
            </div>
    </main>
     
    <?php include "../componentes/footer.php"; ?>

    <!-- DARK MODE SCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const darkModeToggle = document.getElementById('darkModeToggle');
            const body = document.body;

            // Verificar dark mode salvo
            if (localStorage.getItem('darkMode') === 'enabled') {
                body.classList.add('dark-mode');
                if (darkModeToggle) {
                    darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
                }
            }

            // Clique no botão
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
    </script>
</body>

</html>