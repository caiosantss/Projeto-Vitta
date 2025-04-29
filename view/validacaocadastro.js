document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
  
    form.addEventListener('submit', function(event) {
      const nome = form.nome.value.trim();
      const email = form.email.value.trim();
      const senha = form.password.value;
      const confirmarSenha = form['confirm-password'].value;
  
      if (!nome || !email || !senha || !confirmarSenha) {
        alert('Por favor, preencha todos os campos.');
        event.preventDefault(); // Impede o envio do formulário
        return;
      }
  
      if (!validarEmail(email)) {
        alert('Por favor, insira um e-mail válido.');
        event.preventDefault();
        return; //verifica se o email é valido ou não
      }
  
      if (senha !== confirmarSenha) {
        alert('As senhas não coincidem.');
        event.preventDefault();
        return; // verifica se as duas senhas são identicas
      }
  
      // Se passou todas as validações, o formulário é enviado normalmente
    });
  
    function validarEmail(email) {
      // Expressão regular simples para validar e-mail
      const re = /\S+@\S+\.\S+/;
      return re.test(email);
    }
  });
  