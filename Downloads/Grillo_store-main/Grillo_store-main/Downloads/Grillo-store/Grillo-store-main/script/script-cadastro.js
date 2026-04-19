// Selecionando elementos do formulário
const form = document.getElementById('cadastroForm');
const limparBtn = document.getElementById('limparBtn');
const nome = document.getElementById('nome');
const dataNascimento = document.getElementById('dataNascimento');
const email = document.getElementById('email');
const senha = document.getElementById('senha');
const confirmaSenha = document.getElementById('confirmaSenha');
const cpf = document.getElementById('cpf');
const telefone = document.getElementById('telefone');

// Spans de erro
const errorSpans = {
    dataNascimento: document.getElementById('idadeError'),
    email: document.getElementById('emailError'),
    senha: document.getElementById('senhaError'),
    confirmaSenha: document.getElementById('confirmaSenhaError'),
    cpf: document.getElementById('cpfError'),
    telefone: document.getElementById('telefoneError')
};

// Campos e ordem de validação
const campos = [
    { id: 'nome', proximo: 'dataNascimento' },
    { id: 'dataNascimento', proximo: 'email' },
    { id: 'email', proximo: 'senha' },
    { id: 'senha', proximo: 'confirmaSenha' },
    { id: 'confirmaSenha', proximo: 'cpf' },
    { id: 'cpf', proximo: 'telefone' },
    { id: 'telefone', proximo: null }
];

// REMOVIDO: Código do pop-up HTML (o redirecionamento fará o trabalho)

// ----------------------
// Funções de validação (MANTIDAS INTACTAS)
// ----------------------
function validarNome(nome) { return nome.trim().length > 0; }
function validarIdade(data) {
    if (!data) return false;
    const hoje = new Date();
    const nascimento = new Date(data);
    let idade = hoje.getFullYear() - nascimento.getFullYear();
    const m = hoje.getMonth() - nascimento.getMonth();
    if (m < 0 || (m === 0 && hoje.getDate() < nascimento.getDate())) idade--;
    return idade >= 18;
}
function validarEmail(e) { return /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(e); }
function validarSenha(s) { return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(s); }
function validarConfirmacaoSenha(s1, s2) { return s1 === s2; }
function validarCPF(cpfValor) {
    cpfValor = cpfValor.replace(/\D/g, '');
    if (cpfValor.length !== 11 || /^(\d)\1+$/.test(cpfValor)) return false;
    let soma = 0, resto;
    for (let i = 1; i <= 9; i++) soma += parseInt(cpfValor.substring(i - 1, i)) * (11 - i);
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpfValor.substring(9, 10))) return false;
    soma = 0;
    for (let i = 1; i <= 10; i++) soma += parseInt(cpfValor.substring(i - 1, i)) * (12 - i);
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpfValor.substring(10, 11))) return false;
    return true;
}
function validarTelefone(t) {
    const telefoneLimpo = t.replace(/\D/g, '');
    return /^(\d{2})?(\d{4,5}\d{4})$/.test(telefoneLimpo) && (telefoneLimpo.length === 10 || telefoneLimpo.length === 11);
}

// Formatação (MANTIDAS INTACTAS)
const formatarCPF = (input) => { input.value = input.value.replace(/\D/g, '').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2'); };
const formatarTelefone = (input) => { input.value = input.value.replace(/\D/g, '').replace(/(\d{2})(\d)/, '($1) $2').replace(/(\d{4,5})(\d{4})$/, '$1-$2'); };

// Limpar validação (MANTIDA INTACTA)
function limparValidacao(input) {
    if (!input) return;
    input.classList.remove('error', 'valid');
    const errorSpan = errorSpans[input.id];
    if (errorSpan) errorSpan.textContent = '';
}

// Validar campo (MANTIDA INTACTA)
async function validarCampo(campo) {
    const input = document.getElementById(campo.id);
    if (!input) return false;
    limparValidacao(input);
    let isValid = false;

    switch (campo.id) {
        case 'nome': input.value = input.value.toUpperCase(); isValid = validarNome(input.value); if (!isValid) input.title = 'Nome é obrigatório.'; break;
        case 'dataNascimento': isValid = validarIdade(input.value); if (!isValid) errorSpans.dataNascimento.textContent = 'Você precisa ter pelo menos 18 anos.'; break;
        case 'email': isValid = validarEmail(input.value); if (!isValid) errorSpans.email.textContent = 'E-mail inválido. Ex: nome@dominio.com'; break;
        case 'senha': isValid = validarSenha(input.value); if (!isValid) errorSpans.senha.textContent = 'Senha fraca! Mínimo 8 caracteres, com letras maiúsculas, minúsculas, números e símbolo.'; break;
        case 'confirmaSenha': isValid = validarConfirmacaoSenha(senha.value, input.value); if (!isValid) errorSpans.confirmaSenha.textContent = 'As senhas não coincidem!'; break;
        case 'cpf': formatarCPF(input); isValid = validarCPF(input.value); if (!isValid) errorSpans.cpf.textContent = 'CPF inválido!'; break;
        case 'telefone': formatarTelefone(input); isValid = validarTelefone(input.value); if (!isValid) errorSpans.telefone.textContent = 'Telefone inválido! Formato: (XX) XXXX-XXXX ou (XX) XXXXX-XXXX'; break;
    }

    if (isValid) input.classList.add('valid'); else input.classList.add('error');
    return isValid;
}

// Habilita/desabilita campos (MANTIDA INTACTA)
function toggleCampos(startIndex) {
    campos.forEach((campo, index) => {
        const input = document.getElementById(campo.id);
        if (input) input.disabled = index >= startIndex;
    });
}

// Eventos de validação (MANTIDOS INTACTOS)
document.addEventListener('DOMContentLoaded', () => {
    toggleCampos(1);
    document.getElementById(campos[0].id).disabled = false;
    document.getElementById(campos[0].id).focus();

    campos.forEach((campo, index) => {
        const input = document.getElementById(campo.id);
        if (!input) return;

        input.addEventListener('input', async () => {
            if (await validarCampo(campo)) {
                if (campo.proximo) document.getElementById(campo.proximo).disabled = false;
            } else {
                for (let i = index + 1; i < campos.length; i++) document.getElementById(campos[i].id).disabled = true;
            }
        });

        if (campo.id === 'dataNascimento') input.addEventListener('change', async () => await validarCampo(campo));
    });
});

// Botão de limpar (MANTIDO INTACTO)
limparBtn.addEventListener('click', () => {
    form.reset();
    campos.forEach(c => limparValidacao(document.getElementById(c.id)));
    toggleCampos(1);
    document.getElementById(campos[0].id).disabled = false;
    document.getElementById(campos[0].id).focus();
});

// Toggle de olho nos campos de senha com mousedown/mouseup
document.addEventListener('DOMContentLoaded', () => {
    // Configurar o toggle para o campo de senha
    const senhaInput = document.getElementById('senha');
    const olhoBtn = document.getElementById('olho');
    
    if (senhaInput && olhoBtn) {
        olhoBtn.addEventListener('mousedown', (e) => {
            e.preventDefault();
            senhaInput.type = 'text';
            olhoBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
        });
        
        olhoBtn.addEventListener('mouseup', (e) => {
            e.preventDefault();
            senhaInput.type = 'password';
            olhoBtn.innerHTML = '<i class="fas fa-eye"></i>';
        });
        
        olhoBtn.addEventListener('mouseout', () => {
            senhaInput.type = 'password';
            olhoBtn.innerHTML = '<i class="fas fa-eye"></i>';
        });
    }
    
    // Configurar o toggle para o campo de confirmar senha
    const confirmaSenhaInput = document.getElementById('confirmaSenha');
    const olhoConfirmaBtn = document.getElementById('olho-confirma');
    
    if (confirmaSenhaInput && olhoConfirmaBtn) {
        olhoConfirmaBtn.addEventListener('mousedown', (e) => {
            e.preventDefault();
            confirmaSenhaInput.type = 'text';
            olhoConfirmaBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
        });
        
        olhoConfirmaBtn.addEventListener('mouseup', (e) => {
            e.preventDefault();
            confirmaSenhaInput.type = 'password';
            olhoConfirmaBtn.innerHTML = '<i class="fas fa-eye"></i>';
        });
        
        olhoConfirmaBtn.addEventListener('mouseout', () => {
            confirmaSenhaInput.type = 'password';
            olhoConfirmaBtn.innerHTML = '<i class="fas fa-eye"></i>';
        });
    }
});
// Envio do formulário (MODIFICADO PARA REMOVER O POP-UP E ADICIONAR REDIRECIONAMENTO)

 // ====================================
    // 7. MODO ESCURO
    // ====================================
    const body = document.body;
    const darkModeToggle = document.getElementById('darkModeToggle');
    // Ícones simplificados aqui, assumindo que foram definidos no seu HTML ou CSS
    const sunIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sun"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>`;
    const moonIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-moon"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>`;

    function enableDarkMode() {
        body.classList.add('dark-mode');
        if (darkModeToggle) darkModeToggle.innerHTML = sunIcon;
        localStorage.setItem('darkMode', 'enabled');
    }

    function disableDarkMode() {
        body.classList.remove('dark-mode');
        if (darkModeToggle) darkModeToggle.innerHTML = moonIcon;
        localStorage.setItem('darkMode', 'disabled');
    }

    if (localStorage.getItem('darkMode') === 'enabled') enableDarkMode();
    else if (darkModeToggle) darkModeToggle.innerHTML = moonIcon;

    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', () => {
            body.classList.contains('dark-mode') ? disableDarkMode() : enableDarkMode();
        });
    }

