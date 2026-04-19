<?php
session_start();
require_once('middleware-super-admin.php');
require_once('conexao.php');

// Mensagens curtas via GET
$sucesso = $_GET['ok'] ?? '';
$erro = $_GET['erro'] ?? '';

// Se editar
$produto_editar = null;
if (isset($_GET['editar'])) {
	$id = intval($_GET['editar']);
	$stmt = $conexao->prepare('SELECT * FROM produtos WHERE id = ?');
	$stmt->bind_param('i', $id);
	$stmt->execute();
	$result = $stmt->get_result();
	$produto_editar = $result->fetch_assoc();
	$stmt->close();
}

// Pega todos produtos
$produtos = [];
$resultado = $conexao->query('SELECT * FROM produtos ORDER BY id DESC');
if ($resultado) {
	while ($row = $resultado->fetch_assoc()) {
		$produtos[] = $row;
	}
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Painel Super Administrador - Grillo Store</title>
	<link rel="stylesheet" href="../estilo/super-administrador.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
	<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>

<body>

	<div class="Titulo">
        <div class="header-controls">
            <div class="botao-voltar">
                <button onclick="window.location.href='Principal.php'" class="back-button">
                    <i class="fas fa-arrow-left"></i> Voltar
                </button>
            </div>

            <div class="darkmode-container">
                <button id="darkModeToggle" aria-label="Alternar modo escuro"></button>
            </div>
        </div>
        
        <h1>Painel do Super Administrador</h1>
    </div>

	<div class="Botoes navegacao">
		<nav>
			<ul>
				
				<li><a href="listagem-produtos.php">Listagem de Produtos</a></li>
				<li><a href="painel_mensagens.php">Mensagens</a></li>
			
			</ul>	
		</nav>
	</div>

	<div class="super-admin-container">
		<div style="width:95%; max-width:1200px;">

			<?php if ($sucesso): ?>
				<div style="background:#e6ffed; color:#124b21; padding:12px; border-radius:6px; margin-bottom:10px;"><?= htmlspecialchars($sucesso) ?></div>
			<?php endif; ?> <!-- Mensagem de sucesso -->
			<?php if ($erro): ?>
				<div style="background:#ffecec; color:#8a1f1f; padding:12px; border-radius:6px; margin-bottom:10px;"><?= htmlspecialchars($erro) ?></div>
			<?php endif; ?>

			<section style="background:white; padding:18px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.05); margin-bottom:20px;">
				<h2><?= $produto_editar ? 'Editar Produto' : 'Adicionar Produto' ?></h2>

				<!-- Formulário de adicionar/editar produto -->
				<form action="processa_produto.php" method="POST" enctype="multipart/form-data">
					<input type="hidden" name="acao" value="<?= $produto_editar ? 'update' : 'create' ?>">
					<?php if ($produto_editar): ?>
						<input type="hidden" name="id" value="<?= $produto_editar['id'] ?>">
					<?php endif; ?>
					<div style="display:flex; flex-wrap:wrap; gap:12px; margin-top:12px;">
						<input type="text" name="nome" placeholder="Nome do produto" required style="flex:1; min-width:200px; padding:8px;" value="<?= $produto_editar ? htmlspecialchars($produto_editar['nome']) : '' ?>">
						<input type="text" name="categoria" placeholder="Categoria" style="width:220px; padding:8px;" value="<?= $produto_editar ? htmlspecialchars($produto_editar['categoria']) : '' ?>">
						<input type="number" step="0.01" name="preco" placeholder="Preço" required style="width:160px; padding:8px;" value="<?= $produto_editar ? htmlspecialchars($produto_editar['preco']) : '' ?>">
						<input type="number" name="estoque" placeholder="Estoque" style="width:120px; padding:8px;" value="<?= $produto_editar ? htmlspecialchars($produto_editar['estoque']) : '0' ?>">
					</div>
					<div style="margin-top:10px;">
						<textarea name="descricao" placeholder="Descrição" rows="4" style="width:100%; padding:10px;"><?= $produto_editar ? htmlspecialchars($produto_editar['descricao']) : '' ?></textarea>
					</div>

					<!-- Campo de imagem adicionado -->
					<div style="margin-top:10px;">
						<label>Imagem do produto:</label>
						<input type="file" name="imagem" accept="image/*">
						<?php if ($produto_editar && !empty($produto_editar['imagem'])): ?>
							<p>Imagem atual:</p>
							<img src="uploads/<?= htmlspecialchars($produto_editar['imagem']) ?>" alt="Imagem do produto" style="max-width:150px; border:1px solid #ccc; padding:4px;">
						<?php endif; ?>
					</div>

					<div style="margin-top:10px; display:flex; gap:8px;">
						<button class="super-admin-btn" type="submit"><?= $produto_editar ? 'Salvar Alterações' : 'Adicionar Produto' ?></button>

						<?php if (!$produto_editar): ?>
							<a class="super-admin-btn"
								style="background:#007bff; padding:10px 15px; text-decoration:none; display:inline-block; border-radius:4px; color:white; cursor:pointer;"
								href="gerar_pdf.php"
								target="_blank">
								Gerar PDF
							</a>
						<?php endif; ?>

						<?php if ($produto_editar): ?>
							<a href="super-administrador.php" style="align-self:center; color:#666;">Cancelar edição</a>
						<?php endif; ?>
					</div>
				</form>
			</section>

			<section style="background:white; padding:18px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.05); margin-bottom:20px; margin-top:20px;">
				<h2>📈 Valor Total de Estoque por Categoria</h2>
				<div style="max-width: 800px; margin: 0 auto; margin-top: 20px;">
					<canvas id="graficoEstoqueCategoria"></canvas>
				</div>
			</section>
			<section style="background:white; padding:18px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.05);">

				<section style="background:white; padding:18px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.05);">
					<h2>Lista de Produtos</h2>
					<div style="overflow:auto; margin-top:10px;">
						<table style="width:100%; border-collapse:collapse;">
							<thead>
								<tr style="background:#f8f9fa; text-align:left;">
									<th style="padding:8px; border-bottom:1px solid #eee;">ID</th>
									<th style="padding:8px; border-bottom:1px solid #eee;">Nome</th>
									<th style="padding:8px; border-bottom:1px solid #eee;">Preço</th>
									<th style="padding:8px; border-bottom:1px solid #eee;">Estoque</th>
									<th style="padding:8px; border-bottom:1px solid #eee;">Categoria</th>
									<th style="padding:8px; border-bottom:1px solid #eee;">Ações</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($produtos as $p): ?>
									<tr>
										<td style="padding:10px; vertical-align:middle;"><?= $p['id'] ?></td>
										<td style="padding:10px; vertical-align:middle;"><?= htmlspecialchars($p['nome']) ?></td>
										<td style="padding:10px; vertical-align:middle;">R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
										<td style="padding:10px; vertical-align:middle;"><?= $p['estoque'] ?></td>
										<td style="padding:10px; vertical-align:middle;"><?= htmlspecialchars($p['categoria']) ?></td>
										<td style="padding:10px; vertical-align:middle;">
											<a class="super-admin-btn" style="background:#556bff; padding:7px 12px; text-decoration:none; margin-right:8px;" href="super-administrador.php?editar=<?= $p['id'] ?>">Editar</a>
											<form action="processa_produto.php" method="POST" style="display:inline;" onsubmit="return confirm('Deseja realmente excluir este produto?');">
												<input type="hidden" name="acao" value="delete">
												<input type="hidden" name="id" value="<?= $p['id'] ?>">
												<button class="super-admin-btn" type="submit" style="background:#ff5c5c;">Excluir</button>
											</form>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</section>

		</div>
	</div>

	<?php include "../componentes/footer.php"; ?>
	<script src="../script/super-administrador.js"></script>

</body>

</html>