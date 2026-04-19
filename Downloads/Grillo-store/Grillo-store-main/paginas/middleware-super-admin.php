<?php
/**
 * ============================================================================
 * MIDDLEWARE: VALIDAÇÃO DE SUPER ADMINISTRADOR
 * ============================================================================
 * 
 * Arquivo reutilizável para proteger páginas que precisam de acesso exclusivo
 * para super administradores.
 * 
 * USO:
 * ====
 * No início de qualquer página PHP que necessite proteção, use:
 * 
 *     session_start();
 *     require_once('middleware-super-admin.php');
 * 
 * FLUXO DE VALIDAÇÃO:
 * ===================
 * 1. Verifica se usuário está logado ($_SESSION['usuario_email'] existe?)
 * 2. Verifica se email está na whitelist de super admins
 * 3. Se falhar em qualquer validação → redireciona para login.php
 * 4. Se passar → permite acesso à página
 * 
 * SEGURANÇA:
 * ==========
 * - Whitelist de emails (hard-coded)
 * - Validação antes de qualquer output HTML
 * - Redireciona com exit para impedir execução de código indevido
 */

/**
 * WHITELIST DE SUPER ADMINISTRADORES
 * ===================================
 * Array contendo todos os emails autorizados a acessar páginas administrativas.
 * 
 * Para adicionar novo super admin:
 * 1. Certifique-se que o email está cadastrado no banco (tabela usuarios)
 * 2. Adicione o email neste array
 * 3. Pronto! O novo admin terá acesso instantâneo
 * 
 * Exemplo de adição:
 *     $emails_permitidos[] = 'novoadmin@example.com';
 */
$emails_permitidos = [
    'sdvr2017@gmail.com',           // SAMUEL - Admin 
    'pabloviniciusog@gmail.com',    // PABLO - Admin
    'Beatriz.ffsilva16@gmail.com',  // BEATRIZ - Admin
    'gabrielsuliano240@gmail.com'     // GABRIEL - Admin
];

/**
 * VALIDAÇÃO DE AUTENTICAÇÃO
 * =========================
 * Condição 1: isset($_SESSION['usuario_email'])
 *     - Verifica se a variável de sessão 'usuario_email' existe
 *     - Se não existir = usuário não está logado
 * 
 * Condição 2: in_array($_SESSION['usuario_email'], $emails_permitidos)
 *     - Verifica se o email logado está na lista de super admins
 *     - Se não estiver = usuário logado mas sem permissão
 * 
 * Operador && (AND):
 *     - Ambas condições devem ser TRUE
 *     - Se qualquer uma for FALSE, o usuário é redirecionado
 */
if (
    !isset($_SESSION['usuario_email']) || 
    !in_array($_SESSION['usuario_email'], $emails_permitidos)
) {
    /**
     * REDIRECIONAMENTO DE ACESSO NEGADO
     * ==================================
     * Usuário não logado OU sem permissão de super admin
     * 
     * header('location: login.php'):
     *     - Instrui o navegador a ir para a página de login
     *     - Note: 'location:' é sinônimo de 'Location:' (case-insensitive)
     *     - O navegador fará uma requisição GET para login.php
     * 
     * exit;
     *     - Encerra a execução do script PHP
     *     - Impede que código após header seja executado
     *     - OBRIGATÓRIO após header (boa prática)
     */
    header('location: login.php');
    exit;
}

/**
 * ARMAZENAR EMAIL DO ADMIN EM VARIÁVEL GLOBAL (OPCIONAL)
 * =======================================================
 * Facilitates acesso rápido aos dados do admin na página
 * 
 * Uso: $admin_email pode ser utilizado em qualquer lugar da página
 */
$admin_email = $_SESSION['usuario_email'];
$admin_nome = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Admin';

?>
