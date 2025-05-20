
<?php
session_start(); // Inicia a sessão para controle de login
if (isset($_SESSION['user_connected']) && $_SESSION['user_connected'] === true) { // Verifica se o usuário está logado (sessão válida)
  $usuario = $_SESSION['nome_usuario'] ?? 'Administrador'; // Pega o nome do usuário armazenado na sessão ou define 'Administrador' caso não esteja definido
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mais Informações</title>
   <!-- Ícones e fontes externas -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"/>
  <link rel="icon" href="./imgs/icons/iconTCC.png" type="image/x-icon">


  <!-- Estilos personalizados do sistema -->
  <link rel="stylesheet" href="./css/style.css">
  <style>

    /* Cor de fundo do modo claro */
    body {
      background-color: #ecec83;
    }

    /* Cor de fundo no modo escuro */
    body.dark-mode {
      background-color: #1e1e1e;
    }

    /* Container principal com animação de entrada */
    .content {
      text-align: center;
      padding: 30px;
      max-width: 900px;
      margin: 0 auto;
      position: relative;
      z-index: 2;
      animation: fadeIn 1s ease-in;
    }

    /* Caixa de informação estilizada com borda pontilhada e sombra */
    .info-box {
      background: linear-gradient(to bottom, #fffbea, #fff3cd);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      border: 2px dashed #f5ef32;
      position: relative;
      overflow: hidden;
    }

    /* Efeito ao passar o mouse na caixa */
    .info-box:hover {
      transform: scale(1.01);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
    }

    .info-box:hover .heart {
      animation: floatHeart 2s linear forwards;
    }

    /* Estilo da info-box no modo escuro */
    body.dark-mode .info-box {
      background: #2a2a2a;
      border: 2px dashed #e6e68c;
    }

    /* Título principal */
    h1 {
      font-weight: bold;
      color: #222;
      margin-bottom: 20px;
    }

    body.dark-mode h1 {
      color: #f9f9f9;
    }

    /* Texto padrão */
    p {
      font-size: 1rem;
      color: #444;
      transition: font-size 0.3s ease;
    }

    body.dark-mode p {
      color: #dddddd;
    }

    /* Estilo das pétalas que caem pela tela */
    .petal {
      position: fixed;
      top: 0;
      width: 25px;
      height: 25px;
      background: url('./imgs/petal.png') no-repeat center/contain;
      opacity: 0.8;
      animation: fall linear infinite;
      z-index: 99999;
      pointer-events: none;
    }

    /* Animação das pétalas */
    @keyframes fall {
      0% {
        transform: translateY(-10%);
      }
      100% {
        transform: translateY(100vh) rotate(360deg);
        opacity: 0;
      }
    }

    /* Animação de fade-in para o conteúdo */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Área de brilho suave que segue o mouse */
    .glow {
      position: fixed;
      width: 100vw;
      height: 100vh;
      top: 0;
      left: 0;
      z-index: 9999;
      pointer-events: none;
    }

    /* Círculo de brilho que se move com o mouse */
    .glow::after {
      content: "";
      position: absolute;
      top: var(--y);
      left: var(--x);
      width: 200px;
      height: 200px;
      background: radial-gradient(circle, rgba(255, 182, 193, 0.4) 0%, transparent 70%);
      transform: translate(-50%, -50%);
      transition: top 0.1s, left 0.1s;
    }

    /* Botão de voltar com gradiente e efeito hover */
    .btn-voltar {
      margin-top: 40px;
      background: linear-gradient(to right, #f5ef32, #fbf66a);
      color: #333;
      border: none;
      padding: 10px 25px;
      border-radius: 30px;
      font-weight: bold;
      transition: 0.3s ease;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .btn-voltar:hover {
      background: linear-gradient(to right, #fbf66a, #f5ef32);
      transform: scale(1.05);
      box-shadow: 0 6px 14px rgba(0, 0, 0, 0.2);
      color: #333;
    }


    /* Estilo dos corações gerados ao passar o mouse */
    .heart {
      position: absolute;
      font-size: 18px;
      animation: floatHeart 2s linear forwards;
      pointer-events: none;
    }

    /* Animação dos corações subindo e desaparecendo */
    @keyframes floatHeart {
      0% {
        transform: translateY(0) scale(1);
        opacity: 1;
      }
      100% {
        transform: translateY(-150px) scale(1.5);
        opacity: 0;
      }
    }
  </style>
</head>

<body>
  <div class="glow"></div>

<!-- Cabeçalho  -->
  <div class="header">
  <div class="logo-container">
    <a href="menu.php">
      <img src="./imgs/logoUTFPR.png" alt="Logo UTFPR" title="Voltar ao menu" id="logoImage">
    </a>
    <div>
      <h3 class="m-0">Mais Informações</h3>
      <small>Olá, <?= htmlspecialchars($usuario, ENT_QUOTES, "UTF-8") ?> 👋</small>
    </div>
  </div>
  <!-- Barra de ícones: acessibilidade, sair e alternância de modo -->
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


  <!-- Conteúdo -->
  <div class="content">
    <div class="info-box" id="infoBox">
      <p class="descricao-texto">
        Este sistema foi desenvolvido como parte do Trabalho de Conclusão de Curso (TCC)<br>
        de Engenharia de Computação na UTFPR - Campus Apucarana. <br><br>
        O objetivo é auxiliar a coordenação na atribuição de horários de aulas, tornando esse<br>
        processo mais eficiente, justo e automatizado através de um modelo matemático de otimização.<br><br>
        Agradecimentos especiais à coordenação do curso e à minha orientadora maravilhosa Tamara Baldo, pelo apoio e incentivo 💛
      </p>
      <a href="menu.php" class="btn btn-voltar">Voltar ao Menu</a>
    </div>
  </div>
  

   <!-- Scripts -->
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="./scripts/scripts.js"></script>
  <!-- <script src="./scripts/scripts.js"></script> -->
  <script>
    
    // Gera pétalas em posições aleatórias com durações variadas
    function criarPetala() {
      const petala = document.createElement('div');
      petala.classList.add('petal');
      petala.style.left = Math.random() * window.innerWidth + 'px';
      petala.style.animationDuration = (3 + Math.random() * 5) + 's';
      document.body.appendChild(petala);
      setTimeout(() => petala.remove(), 8000); // remove após 8s
    }
    setInterval(criarPetala, 300); // gera uma nova pétala a cada 300ms

    // Cria efeito de brilho que segue o cursor do mouse
    const glow = document.querySelector('.glow');
    document.addEventListener('mousemove', e => {
      glow.style.setProperty('--x', e.clientX + 'px');
      glow.style.setProperty('--y', e.clientY + 'px');
    });


    // Lista de emojis de coração para variar
    const emojis = ['❤️', ' 💛', ' 💜', ' 🖤'];

    // Cria animação de corações ao mover o mouse na info-box
    const infoBox = document.getElementById('infoBox');
    infoBox.addEventListener('mousemove', function (e) {
      const heart = document.createElement('div');
      heart.classList.add('heart');
      heart.innerText = emojis[Math.floor(Math.random() * emojis.length)]; // emoji aleatório
      heart.style.left = (e.clientX - infoBox.getBoundingClientRect().left) + 'px';
      heart.style.top = (e.clientY - infoBox.getBoundingClientRect().top) + 'px';
      infoBox.appendChild(heart);
      setTimeout(() => heart.remove(), 2000); // remove coração após 2s
    });
  </script>

</body>
</html>

<?php } else {
  header("Location: login.php"); // Caso a sessão não seja válida, redireciona para a página de login
  exit();
} ?>
