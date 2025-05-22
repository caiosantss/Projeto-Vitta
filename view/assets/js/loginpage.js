/**
 * VITTA - Login Page JavaScript
 * animações e interações da página de login
 */

// Aguarda o carregamento completo da página
document.addEventListener('DOMContentLoaded', function() {
    
    // ===== GERENCIAMENTO DE ANIMAÇÕES DE ERRO =====
    
    /**
     * Remove a animação de shake do formulário após sua execução
     */
    function removeShakeAnimation() {
        const form = document.querySelector('.form-shake');
        if (form) {
            setTimeout(() => {
                form.classList.remove('form-shake');
            }, 400); // Tempo sincronizado com a duração da animação CSS
        }
    }

    /**
     * Remove o destaque vermelho dos campos quando o usuário começa a digitar
     */
    function setupErrorInputHandlers() {
        const errorInputs = document.querySelectorAll('.error-input');
        
        errorInputs.forEach(input => {
            // Remove o erro quando o usuário começa a digitar
            input.addEventListener('input', function() {
                this.classList.remove('error-input');
            });

            // Remove o erro quando o campo recebe foco (opcional)
            input.addEventListener('focus', function() {
                // Adiciona uma transição suave ao remover o erro
                this.style.transition = 'border-color 0.3s ease, box-shadow 0.3s ease';
            });
        });
    }


    /**
     * Adiciona feedback visual ao botão de submit
     */
    function setupSubmitButton() {
        const submitButton = document.querySelector('button[type="submit"]');
        const form = document.querySelector('form');

        if (submitButton && form) {
            form.addEventListener('submit', function() {
                // Desabilita o botão temporariamente para evitar duplo clique
                submitButton.disabled = true;
                submitButton.textContent = 'Entrando...';
                
                // Reabilita após 3 segundos (caso algo dê errado)
                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Entrar';
                }, 3000);
            });
        }
    }

    /**
     * Adiciona efeito de fade-out na mensagem de erro após alguns segundos
     */
    function setupErrorMessageTimeout() {
        const errorMessage = document.querySelector('.error-message');
        
        if (errorMessage) {
            // Remove a mensagem automaticamente após 5 segundos
            setTimeout(() => {
                errorMessage.style.transition = 'opacity 0.5s ease';
                errorMessage.style.opacity = '0';
                
                // Remove completamente do DOM após a transição
                setTimeout(() => {
                    if (errorMessage.parentNode) {
                        errorMessage.parentNode.removeChild(errorMessage);
                    }
                }, 500);
            }, 5000);
        }
    }

    /**
     * Adiciona validação em tempo real dos campos
     */
    function setupRealtimeValidation() {
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('senha');

        if (emailInput) {
            emailInput.addEventListener('blur', function() {
                const email = this.value.trim();
                if (email && !isValidEmail(email)) {
                    this.style.borderColor = '#e74c3c';
                } else if (email) {
                    this.style.borderColor = '#2e8b57';
                }
            });
        }

        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    this.style.borderColor = '#2e8b57';
                }
            });
        }
    }

    /**
     * Valida formato do email
     * @param {string} email 
     * @returns {boolean}
     */
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Adiciona efeito de loading na página durante o redirecionamento
     */

    function setupLoadingEffect() {
        const form = document.querySelector('form');
        
        if (form) {
            form.addEventListener('submit', function() {
                // overlay de loading 
                const loadingOverlay = document.createElement('div');
                loadingOverlay.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(46, 139, 87, 0.1);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                    backdrop-filter: blur(2px);
                `;
                
                // Adiciona spinner de loading
                const spinner = document.createElement('div');
                spinner.style.cssText = `
                    width: 40px;
                    height: 40px;
                    border: 4px solid #f3f3f3;
                    border-top: 4px solid #2e8b57;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                `;
                
                loadingOverlay.appendChild(spinner);
                document.body.appendChild(loadingOverlay);
                
                // Remove o loading após 3 segundos (segurança)
                setTimeout(() => {
                    if (loadingOverlay.parentNode) {
                        loadingOverlay.parentNode.removeChild(loadingOverlay);
                    }
                }, 3000);
            });
        }
    }

    // ===== INICIALIZAÇÃO =====
    
    // Executa todas as funções de configuração
    removeShakeAnimation();
    setupErrorInputHandlers();
    setupSubmitButton();
    setupErrorMessageTimeout();
    setupRealtimeValidation();
    setupLoadingEffect();

    // Log de inicialização (pode ser removido em produção)
    console.log('🔐 VITTA Login - JavaScript carregado com sucesso!');
});

// ===== CSS DINÂMICO PARA ANIMAÇÕES =====

// Adiciona a animação de spin para o loading spinner
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);