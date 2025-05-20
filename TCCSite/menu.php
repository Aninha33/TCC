<?php
session_start(); // Inicia a sessão para controle de login
if (isset($_SESSION['user_connected']) && $_SESSION['user_connected'] === true) { // Verifica se o usuário está logado (sessão válida)
  $usuario = $_SESSION['nome_usuario'] ?? 'Administrador'; // Pega o nome do usuário armazenado na sessão ou define 'Administrador' caso não esteja definido
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
 <meta charset="UTF-8">
  <title>Menu - Sistema de Horários</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Estilos personalizados do sistema -->
  <link href="./css/style.css" rel="stylesheet">
  <link href="./css/menuStyle.css" rel="stylesheet">

  <!-- Ícones e fontes externas -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="icon" href="./imgs/icons/iconTCC.png" type="image/x-icon">

</head>
<body>

<!-- Cabeçalho -->
<div class="header">
  <div class="logo-container">
    <a href="menu.php">
      <img src="./imgs/logoUTFPR.png" alt="Logo UTFPR" title="Voltar ao menu" id="logoImage">
    </a>
    <div>
      <h3 class="m-0">Horário Gerado</h3>
      <small>Olá, <?= htmlspecialchars($usuario, ENT_QUOTES, "UTF-8") ?> 👋</small>
    </div>
  </div>
  <div class="icon-bar">
    <img id="accessibility_btn" src="./imgs/accessibilityPreta.png" alt="Acessibilidade">
    <a href="end_session.php"><img src="./imgs/logout.png" alt="Sair"></a>
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

<!-- Área de navegação do menu -->
  <div class="card-grid">
    <!-- Botão de Gerar Horarios -->
    <a href="gerarHorarios.php" class="menu-card" tabindex="0">
      <img src="./imgs/icons/gerarHorarios.gif" alt="Gerar Horários">
      <div class="menu-title">Gerar Horários</div>
    </a>
    <!-- Botão de Gerenciar Professores -->
    <a href="gerenciarProfessores.php" class="menu-card" tabindex="0">
      <img src="./imgs/icons/gerenciarProfessores.gif" alt="Gerenciar Professores">
      <div class="menu-title">Gerenciar Professores</div>
    </a>
    <!-- Botão de Relatórios -->
    <a href="relatorios.php" class="menu-card" tabindex="0">
      <img src="./imgs/icons/relatorios.gif" alt="Relatórios">
      <div class="menu-title">Relatórios</div>
    </a>
    <!-- Botão de Mais informações -->
    <a href="maisInfo.php" class="menu-card" tabindex="0">
      <img src="./imgs/icons/sobre.gif" alt="Mais Informações">
      <div class="menu-title">Mais Informações</div>
    </a>
  </div>

  <!-- Bibliotecas JavaScript necessárias -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="./scripts/scripts.js"></script>

</body>
</html>

<?php } else {
  header("Location: login.php"); // Caso a sessão não seja válida, redireciona para a página de login
  exit();
} ?>
