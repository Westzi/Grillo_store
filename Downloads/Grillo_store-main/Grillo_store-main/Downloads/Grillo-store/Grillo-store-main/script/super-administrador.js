// ============================================================================
// VARIÁVEIS GLOBAIS
// ============================================================================
const body = document.body;
const darkModeToggle = document.getElementById('darkModeToggle');

// Ícones SVG para o botão
const sunIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sun"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>`;
const moonIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-moon"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>`;

let myChart; // Variável global para armazenar a instância do gráfico

// ============================================================================
// LÓGICA DO DARK MODE
// ============================================================================

function applyTheme(isDark) {
    if (isDark) {
        body.classList.add('dark-mode');
        if (darkModeToggle) darkModeToggle.innerHTML = sunIcon;
        localStorage.setItem('darkMode', 'enabled');
    } else {
        body.classList.remove('dark-mode');
        if (darkModeToggle) darkModeToggle.innerHTML = moonIcon;
        localStorage.setItem('darkMode', 'disabled');
    }
    // Redesenha o gráfico para aplicar as cores do tema
    if (myChart) {
        desenharGrafico(isDark);
    }
}

// ============================================================================
// LÓGICA DO GRÁFICO (Chart.js)
// ============================================================================

async function desenharGrafico(isDarkMode = body.classList.contains('dark-mode')) {
    const ctxElement = document.getElementById('graficoEstoqueCategoria');
    if (!ctxElement) return;

    try {
        // Usa a versão do gráfico que busca dados (é mais completa)
        const response = await fetch('dados_grafico.php');
        const data = await response.json();
        
        // Define as cores do texto do gráfico com base no tema
        const textColor = isDarkMode ? '#f0f0f0' : '#333';
        const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';

        const newChartConfig = {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Valor Total de Estoque (R$)',
                    data: data.data,
                    // Use suas cores originais ou defina novas
                    backgroundColor: [
                        'rgba(48, 221, 89, 0.7)', // Verde primário
                        'rgba(54, 162, 235, 0.7)', 
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                    ],
                    borderColor: [
                        '#30dd59', // Borda Verde
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    x: {
                        grid: { color: gridColor },
                        ticks: { color: textColor },
                        title: { display: true, text: 'Categorias', color: textColor }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: { color: textColor },
                        title: { display: true, text: 'Valor em Reais (R$)', color: textColor }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        };

        // Destrói a instância anterior do gráfico (se existir)
        if (myChart) {
            myChart.destroy();
        }

        // Cria a nova instância do gráfico
        myChart = new Chart(ctxElement.getContext('2d'), newChartConfig);

    } catch (error) {
        console.error('Erro ao buscar dados do gráfico:', error);
        if (ctxElement.parentElement) {
            ctxElement.parentElement.innerHTML = '<p style="text-align:center; color:var(--text-color);">Não foi possível carregar os dados do gráfico. Verifique o arquivo dados_grafico.php.</p>';
        }
    }
}

// ============================================================================
// INICIALIZAÇÃO DA PÁGINA (Executada após o carregamento do HTML)
// ============================================================================
document.addEventListener('DOMContentLoaded', () => {
    // 1. Inicializa o Dark Mode
    const savedTheme = localStorage.getItem('darkMode');
    if (savedTheme === 'enabled') {
        applyTheme(true);
    } else {
        // Se não houver preferência, aplica o tema claro e define o ícone
        applyTheme(false); 
    }

    // 2. Configura o Listener de clique
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', () => {
            const isCurrentlyDark = body.classList.contains('dark-mode');
            applyTheme(!isCurrentlyDark); // Inverte e aplica o tema
        });
    }

    // 3. Desenha o Gráfico (inclui a lógica para aplicar cores do tema)
    desenharGrafico();
});