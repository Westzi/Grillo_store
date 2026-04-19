-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21/11/2025 às 00:17
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `grillo_store_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `enderecos`
--

CREATE TABLE `enderecos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `logradouro` varchar(100) NOT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `complemento` varchar(50) DEFAULT NULL,
  `bairro` varchar(50) DEFAULT NULL,
  `cidade` varchar(50) NOT NULL,
  `estado` varchar(2) NOT NULL,
  `cep` varchar(10) NOT NULL,
  `tipo` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens`
--

CREATE TABLE `mensagens` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mensagem` text NOT NULL,
  `data_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `lida` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `estoque` int(11) DEFAULT 0,
  `categoria` varchar(50) DEFAULT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `imagem` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `preco`, `estoque`, `categoria`, `data_criacao`, `imagem`) VALUES
(1, 'Kit Camiseta Básica Masculina', 'Kit com 3 camisetas de algodão de alta qualidade.', 47.49, 100, 'Moda Masculina', '2025-10-20 20:52:42', NULL),
(2, 'Kit 4 Camisetas Feminina Academia', 'Kit de camisetas dry-fit, ideal para exercícios.', 53.99, 80, 'Moda Feminina', '2025-10-20 20:52:42', NULL),
(3, 'Notebook Acer Aspire Go Intel Core i5 13420H 8GB RAM 512GB', 'Notebook com excelente performance para trabalho e estudo.', 2890.00, 50, 'Informática', '2025-10-20 20:52:42', NULL),
(4, 'Impressora Multifuncional HP Smart Tank', 'Impressora de tanque de tinta com alto rendimento.', 730.90, 30, 'Periféricos', '2025-10-20 20:52:42', NULL),
(5, 'Câmera instantânea Fujifilm Instax Kit Mini 12 + 10 fotos lilac purple', 'Câmera com fotos instantâneas, perfeita para momentos especiais.', 535.00, 25, 'Fotografia', '2025-10-20 20:52:42', NULL),
(6, 'Câmera Fotográfica Digital Profissional A6x G Zoom', 'Câmera compacta com bom zoom e estabilidade.', 163.83, 40, 'Fotografia', '2025-10-20 20:52:42', NULL),
(7, 'Macaco Elétrico 2 Toneladas 12v 100w Controle Carro', 'Equipamento essencial para troca de pneus com segurança.', 379.99, 15, 'Automotivo', '2025-10-20 20:52:42', NULL),
(8, 'Cabo de Carga para Bateria Chupeta 3,5M Famastil', 'Cabo resistente para recarga de baterias automotivas.', 66.16, 60, 'Automotivo', '2025-10-20 20:52:42', NULL),
(9, 'Kit De Jardinagem 10 Peças + Maleta', 'Kit completo com ferramentas e maleta para jardinagem.', 155.52, 35, 'Casa e Jardim', '2025-10-20 20:52:42', NULL),
(10, 'Mangueira Flexível Tramontina 15m Flex', 'Mangueira de alta pressão e flexibilidade para jardim.', 60.79, 70, 'Casa e Jardim', '2025-10-20 20:52:42', NULL),
(11, 'Headset Gamer', 'Headset confortável com microfone para jogos.', 47.99, 90, 'Eletrônicos', '2025-10-20 20:52:42', NULL),
(12, 'Caixa de Som Amplificada Portátil, Bluetooth, USB, Microfone, LED RGB', 'Caixa de som potente, ideal para festas e eventos.', 179.90, 45, 'Eletrônicos', '2025-10-20 20:52:42', NULL),
(13, 'Sofá Cama Colchão Casal', 'Sofá conversível em cama, prático e confortável.', 1851.35, 10, 'Móveis', '2025-10-20 20:52:42', NULL),
(14, 'Conjunto Sala de Jantar Cel Móveis com 08 Cadeiras', 'Mesa de jantar elegante para até 8 pessoas.', 2632.48, 8, 'Móveis', '2025-10-20 20:52:42', NULL),
(15, 'Sony PlayStation 4 Pro 1TB', 'Console de videogame de alta performance.', 2499.00, 20, 'Games', '2025-10-20 20:52:42', NULL),
(16, 'Microsoft Xbox 360 Super Slim 250GB Standard cor preto 2010', 'Console de videogame clássico com boa capacidade.', 1190.00, 18, 'Games', '2025-10-20 20:52:42', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome_completo` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `data_cadastro` datetime NOT NULL DEFAULT current_timestamp(),
  `is_super_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome_completo`, `email`, `senha`, `cpf`, `data_nascimento`, `telefone`, `data_cadastro`, `is_super_admin`) VALUES
(11, 'SAMUEL', 'sdvr2017@gmail.com', '$2y$10$OyrSmTdo1uIGCKYTJylTueLRh0IeJdWi8ElRjBo9mFYuwGfIe6A.K', '188.732.427-58', '2004-05-09', '(21) 99286-3887', '2025-11-17 23:04:38', 0),
(12, 'JORGE', 'jorge@gmail.com', '$2y$10$pxsTOctooqCLXsl7x.Ryk.zxj1eaqDq43e17XnY366sQH1WNLMv/2', '188.732.427-58', '2005-05-20', '(21) 99286-3887', '2025-11-17 23:52:03', 0);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `enderecos`
--
ALTER TABLE `enderecos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_is_super_admin` (`is_super_admin`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `enderecos`
--
ALTER TABLE `enderecos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `enderecos`
--
ALTER TABLE `enderecos`
  ADD CONSTRAINT `enderecos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
