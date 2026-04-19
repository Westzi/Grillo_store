<?php
header('Content-Type: application/json'); // Informa ao navegador que a resposta é um JSON

require_once('conexao.php');

// Consulta SQL: Agrupa por categoria e calcula o Valor Total de Estoque (Preco * Estoque)
$sql = "
    SELECT 
        categoria, 
        SUM(preco * estoque) AS valor_total_categoria
    FROM 
        produtos
    GROUP BY 
        categoria
    HAVING 
        valor_total_categoria > 0
    ORDER BY 
        valor_total_categoria DESC
";

$resultado = $conexao->query($sql);

$categorias = [];
$valores = [];

if ($resultado) {
    while ($row = $resultado->fetch_assoc()) {
        // Coleta os nomes das categorias (para os rótulos do eixo X)
        $categorias[] = htmlspecialchars($row['categoria']);
        // Coleta os valores totais (para as alturas das barras)
        $valores[] = round($row['valor_total_categoria'], 2);
    }
}

// Formata os dados no array final que o JavaScript irá consumir
$dados_grafico = [
    'labels' => $categorias,
    'data' => $valores
];

// Retorna o resultado como JSON
echo json_encode($dados_grafico);

$conexao->close();
?>