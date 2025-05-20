
// Script principal para as páginas do sistema
$(document).ready(function() {
  console.log("Script carregado");
  
  // --- MODO ESCURO ---
  const corpo = $("body");                           // Body da página
  const modoToggle = $("#modoToggle");               // Botão de alternância do modo escuro
  const logoImage = $("#logoImage");                 // Logo da UTFPR
  const accessibilityBtn = $("#accessibility_btn");  // Ícone de acessibilidade


  // Verifica o localStorage para o modo escuro
  if (localStorage.getItem("modo") === "dark") {
    corpo.addClass("dark-mode");
    if (logoImage.length) logoImage.attr("src", "./imgs/logoBranca.png");
    if (accessibilityBtn.length) accessibilityBtn.attr("src", "./imgs/accessibilityBranca.png");
    modoToggle.prop("checked", true);
  }

  // Alterna o modo escuro
  modoToggle.change(function() {
    corpo.toggleClass("dark-mode");
    if (corpo.hasClass("dark-mode")) {
      if (logoImage.length) logoImage.attr("src", "./imgs/logoBranca.png");
      if (accessibilityBtn.length) accessibilityBtn.attr("src", "./imgs/accessibilityBranca.png");
      localStorage.setItem("modo", "dark");
    } else {
      if (logoImage.length) logoImage.attr("src", "./imgs/logoUTFPR.png");
      if (accessibilityBtn.length) accessibilityBtn.attr("src", "./imgs/accessibilityPreta.png");
      localStorage.setItem("modo", "light");
    }
  });

  // --- ACESSIBILIDADE - TAMANHO DA FONTE ---
  $("#accessibility_btn").click(function() {
    $("#modalFonte").toggleClass("active"); // Abre ou fecha o modal de acessibilidade
  });

  $("#closeModalFonteBtn").click(function() {
    $("#modalFonte").removeClass("active"); // Fecha o modal
  });
  // Altera o tamanho da fonte dinamicamente e salva a configuração
  $("#fontSlider").on("input", function() {
    const valor = $(this).val(); // Obtém o valor do slider (50% a 200%)
    $("body").css("font-size", valor + "%"); // Aplica o tamanho ao corpo da página
    localStorage.setItem("fontSize", valor); // Salva no localStorage
  });
  
  // Restaura o tamanho da fonte salvo
  const savedFontSize = localStorage.getItem("fontSize");
  if (savedFontSize) {
    $("body").css("font-size", savedFontSize + "%");
    $("#fontSlider").val(savedFontSize); // Atualiza o slider para refletir o valor salvo
  }

  // Busca na tabela de horários
  $("input[placeholder*=\"Buscar\"]").on("keyup", function () {
    const termo = $(this).val().toLowerCase(); // Captura o termo digitado
    $(".tabela-horario tbody tr").each(function () {
      const linha = $(this).text().toLowerCase(); // Pega o conteúdo da linha
      $(this).toggle(linha.includes(termo)); // Mostra ou esconde a linha conforme o termo
    });
  });

  // Adaptação do estilo do campo de busca em gerarHorarios.php para modo escuro
  function ajustarBuscaModoEscuro() {
    if ($("input[name=\"q\"]").length){
      if ($("body").hasClass("dark-mode")) {
        $("input[name=\"q\"]").css({
          "background-color": "#333",
          "color": "#fff",
          "border": "1px solid #555"
        });
      } else {
        $("input[name=\"q\"]").css({
          "background-color": "", 
          "color": "",
          "border": ""
        });
      }
    }
  }
  ajustarBuscaModoEscuro(); // Aplica o ajuste ao carregar a página
  $("#modoToggle").change(ajustarBuscaModoEscuro); // Aplica o ajuste ao alternar o modo

});
