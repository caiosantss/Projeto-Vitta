document.addEventListener("DOMContentLoaded", () => {
    const links = document.querySelectorAll("nav a[href^='#']");
    const modal = document.getElementById("modal");
    const modalText = document.getElementById("modalText");
    const closeModal = document.getElementById("closeModal");
  
    const contents = {
      sobre: "<h2>Sobre nós</h2><p>Somos a VITTA, dedicada a facilitar seu acesso à saúde.</p>",
      especialidades: "<h2>Especialidades</h2><p>Oferecemos clínicas de dermatologia, psicologia, nutrição, e mais.</p>",
      contato: "<h2>Contato</h2><p>Você pode nos encontrar pelo WhatsApp, e-mail ou em nossa unidade física.</p>"
    };
  
    links.forEach(link => {
      link.addEventListener("click", e => {
        e.preventDefault();
        const id = link.getAttribute("href").substring(1);
        if (contents[id]) {
          modalText.innerHTML = contents[id];
          modal.classList.add("show");
        }
      });
    });
  
    closeModal.addEventListener("click", () => {
      modal.classList.remove("show");
    });
  
    window.addEventListener("click", e => {
      if (e.target === modal) {
        modal.classList.remove("show");
      }
    });
  });
  