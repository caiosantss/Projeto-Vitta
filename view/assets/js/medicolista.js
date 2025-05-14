const diasSemana = [
    { sigla: "dom", nome: "Dom", data: "18/05" },
    { sigla: "sab", nome: "Sáb", data: "17/05" },
    { sigla: "sex", nome: "Sex", data: "16/05" },
    { sigla: "qui", nome: "Qui", data: "15/05" },
  ];
  
  const horariosPorDia = {
    qui: ["08:30", "08:50", "09:10", "09:30"],
    sex: ["08:50", "09:10", "09:50", "10:10"],
    sab: ["10:20", "11:00", "11:20", "11:40"],
    dom: ["13:00", "13:20", "13:40", "14:00"],
  };
  
  function parseData(dataStr) {
    const [dia, mes] = dataStr.split("/").map(Number);
    const anoAtual = new Date().getFullYear();
    return new Date(anoAtual, mes - 1, dia);
  }
  
  function ordenarDiasPorProximidadeComSegunda(dias) {
    const segunda = obterSegundaFeiraAtual();
  
    return dias
      .map((d) => ({ ...d, dataObj: parseData(d.data) }))
      .sort((a, b) => a.dataObj - b.dataObj);
  }
  
  function obterSegundaFeiraAtual() {
    const hoje = new Date();
    const diaSemana = hoje.getDay(); // 0 (domingo) a 6 (sábado)
    const diff = diaSemana === 0 ? -6 : 1 - diaSemana;
    const segunda = new Date();
    segunda.setDate(hoje.getDate() + diff);
    return segunda;
  }
  
  function popularHorariosOrdenados() {
    const diasOrdenados = ordenarDiasPorProximidadeComSegunda(diasSemana);
    const diasContainer = document.querySelector(".dias-semana");
    diasContainer.innerHTML = ""; // limpa antes de inserir
  
    diasOrdenados.forEach((dia) => {
      const coluna = document.createElement("div");
      coluna.className = "dia";
  
      const nome = document.createElement("div");
      nome.className = "nome-dia";
      nome.textContent = dia.nome;
  
      const data = document.createElement("div");
      data.className = "data-dia";
      data.textContent = dia.data;
  
      const horariosColuna = document.createElement("div");
      horariosColuna.className = "horarios-coluna";
  
      (horariosPorDia[dia.sigla] || []).forEach((hora) => {
        const btn = document.createElement("button");
        btn.textContent = hora;
        btn.onclick = () => {
          document
            .querySelectorAll(".horarios-coluna button")
            .forEach((b) => b.classList.remove("selected"));
          btn.classList.add("selected");
        };
        horariosColuna.appendChild(btn);
      });
  
      coluna.appendChild(nome);
      coluna.appendChild(data);
      coluna.appendChild(horariosColuna);
      diasContainer.appendChild(coluna);
    });
  }
  
  window.onload = popularHorariosOrdenados;
  