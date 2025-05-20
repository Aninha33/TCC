<?php
session_start(); // Inicia a sessão 
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login - Sistema de Horários</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Ícone da aba do navegador -->
  <link rel="icon" href="./imgs/favicon.ico" type="image/x-icon">

  <!-- Bibliotecas de estilo e ícones -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="icon" href="./imgs/icons/iconTCC.png" type="image/x-icon">

  <!-- Estilos personalizados do sistema -->
  <link href="./css/style.css" rel="stylesheet">
  <link href="./css/index.css" rel="stylesheet">
  
</head>
<body>

<!-- Cabeçalho -->
<div class="header">
  <div class="logo-container">
    <img src="./imgs/logoUTFPR.png" alt="Logo UTFPR" id="logoImage">
    <h3 class="mb-0">Login</h3>
  </div>
  <div class="icon-bar">
    <img id="accessibility_btn" src="./imgs/accessibilityPreta.png" alt="Acessibilidade">
    <label class="switch">
      <input type="checkbox" id="modoToggle">
      <span class="slider-switch"></span>
    </label>
  </div>
</div>

<!-- Modal de Acessibilidade -->
<div id="modalFonte">
  <p>Ajustar tamanho da fonte:</p>
  <input type="range" min="50" max="200" value="100" id="fontSlider">
  <button id="closeModalFonteBtn">Fechar</button>
</div>

<!-- Caixa de Login -->
<div class="d-flex justify-content-center align-items-center" style="min-height: 60vh;">
  <div class="login-box" >
    <h4 class="text-center mb-4">Bem-vindo!</h4>

    <!-- Mensagem de erro de login (se houver) -->
    <?php
    if (isset($_SESSION['login_error'])):
      echo '<div class="alert alert-danger text-center">'. $_SESSION['login_error'] .'</div>';
      unset($_SESSION['login_error']);
    endif;
     ?>
    <!-- Formulário de Login -->
    <form action="login.php" method="post">
      <div class="mb-3">
        <label for="email" class="form-label">E-mail institucional*</label>
        <input type="email" name="email" id="email" class="form-control" required placeholder="exemplo@utfpr.edu.br">
      </div>
      <!-- Campo de senha com botão de mostrar/ocultar -->
      <div class="mb-3">
        <label for="senha" class="form-label">Senha*</label>
        <div class="senha-container">
          <input type="password" name="senha" id="senha" class="form-control senha-input" placeholder="Digite sua senha" required>
          <button type="button" id="toggleSenha" class="senha-toggle">
            <i class="fas fa-eye"></i>
          </button>
        </div>
      </div>
      <!-- Botão de envio do formulário -->
      <div class="text-center">
        <button type="submit" class="button">Entrar</button>
      </div>
    </form>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="./scripts/scripts.js"></script>
<script>
  // Script específico para o toggle de senha (mantido inline para garantir funcionamento)
  $(document).ready(function() {    
    // --- MOSTRAR/OCULTAR SENHA ---
    $("#toggleSenha").on("click", function() {
      console.log("Botão de toggle de senha clicado"); // Log para depuração
      const senhaInput = $("#senha");                 // Campo de senha
      const tipo = senhaInput.attr("type");          // Tipo atual do campo (password ou text)
      const icon = $(this).find("i");               // Ícone do botão
      // Alterna entre mostrar e ocultar senha
      if (tipo === "password") {
        senhaInput.attr("type", "text");
        icon.removeClass("fa-eye").addClass("fa-eye-slash");
        console.log("Senha visível"); // Log para depuração
      } else {
        senhaInput.attr("type", "password");
        icon.removeClass("fa-eye-slash").addClass("fa-eye");
        console.log("Senha oculta"); // Log para depuração
      }
    });
  });
</script>
</body>
</html>
