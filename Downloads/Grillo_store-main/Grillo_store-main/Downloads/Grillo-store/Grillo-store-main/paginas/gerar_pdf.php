<?php
// Inclui o autoloader gerado pelo Composer (CORRETO: vendor/ foi instalado na pasta 'paginas')
require 'vendor/autoload.php';
// Inclui a conexão com o banco de dados
require_once('conexao.php');

use Dompdf\Dompdf;
use Dompdf\Options;

// 1. Coleta dos Dados
$produtos = [];
$resultado = $conexao->query('SELECT * FROM produtos ORDER BY id DESC');
if ($resultado) {
    while ($row = $resultado->fetch_assoc()) {
        $produtos[] = $row;
    }
}

// 2. Montagem do Conteúdo HTML do Relatório
$html = '
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        h1 { color: #1c4587; text-align: center; margin-bottom: 20px; }
        .data { text-align: right; font-size: 8pt; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .total { text-align: right; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Relatório de Inventário de Produtos</h1>
    <p class="data">Gerado em: ' . date('d/m/Y H:i:s') . '</p>
    
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 40%;">Nome</th>
                <th style="width: 20%;">Categoria</th>
                <th style="width: 15%; text-align: right;">Preço</th>
                <th style="width: 10%; text-align: right;">Estoque</th>
                <th style="width: 10%; text-align: right;">Valor Total</th>
            </tr>
        </thead>
        <tbody>';

$valor_total_estoque_geral = 0;

foreach ($produtos as $p) {
    // Calcula o valor total por produto
    $valor_produto_estoque = $p['preco'] * $p['estoque'];
    $valor_total_estoque_geral += $valor_produto_estoque;
    
    $html .= '
        <tr>
            <td>' . $p['id'] . '</td>
            <td>' . htmlspecialchars($p['nome']) . '</td>
            <td>' . htmlspecialchars($p['categoria']) . '</td>
            <td style="text-align: right;">R$ ' . number_format($p['preco'], 2, ',', '.') . '</td>
            <td style="text-align: right;">' . $p['estoque'] . '</td>
            <td style="text-align: right;">R$ ' . number_format($valor_produto_estoque, 2, ',', '.') . '</td>
        </tr>';
}

$html .= '
        <tr>
            <td colspan="5" class="total">Valor Total Geral do Estoque:</td>
            <td class="total" style="text-align: right;">R$ ' . number_format($valor_total_estoque_geral, 2, ',', '.') . '</td>
        </tr>
        </tbody>
    </table>
</body>
</html>';

// 3. Configuração e Geração do PDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Envia o PDF para o navegador
$filename = "relatorio_inventario_" . date('Ymd_His') . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
?>