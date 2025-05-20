<?php
session_start(); // Inicia a sessão para controle de login
if (isset($_SESSION['user_connected']) && $_SESSION['user_connected'] === true) { // Verifica se o usuário está logado (sessão válida)
  $usuario = $_SESSION['nome_usuario'] ?? 'Administrador'; // Pega o nome do usuário armazenado na sessão ou define 'Administrador' caso não esteja definido

// Conexão com banco
require_once 'conexao.php';  

// Nome do usuário logado ou define como 'Administrador' caso não esteja definido
$usuario = $_SESSION['nome_usuario'] ?? 'Administrador';

// Define variáveis padrão para o formulário de cadastro/edição
$modo       = 'inserir';        // Modo padrão: inserção
$idAtual    = '';               // ID vazio por padrão
$nomeAtual  = '';               // Nome vazio por padrão
$emailAtual = '';               // E-mail vazio por padrão
$botaoTxt   = 'Cadastrar';      // Texto padrão do botão

// Exclusão de professor
if (!empty($_GET['delete_id'])) {
    $delId = (int) $_GET['delete_id']; // Converte o ID para inteiro
    $pdo->prepare("DELETE FROM professores WHERE id_professor = ?")
        ->execute([$delId]); // Executa a exclusão
    header("Location: gerenciarProfessores.php"); // Redireciona para recarregar a página
    exit;
}

// Carrega dados para edição
if (!empty($_GET['edit_id'])) {
    $modo    = 'editar'; // Altera o modo para edição
    $idAtual = (int) $_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT nome_completo, email_institucional FROM professores WHERE id_professor = ?");
    $stmt->execute([$idAtual]);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $nomeAtual  = $row['nome_completo'];        // Carrega o nome atual
        $emailAtual = $row['email_institucional'];  // Carrega o e-mail atual
        $botaoTxt   = 'Salvar Modificações';        // Altera o texto do botão
    } else {
       // Se não encontrar o professor, volta ao modo de inserção
        $modo    = 'inserir';
        $idAtual = '';
    }
}

// Insere ou atualiza registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome        = trim($_POST['nome_completo']);         // Nome do professor
    $email       = trim($_POST['email_institucional']);   // E-mail do professor
    $senha_plain = $_POST['senha'] ?? '';                 // Senha informada

    // Atualização de professor existente
    if (!empty($_POST['id_professor'])) {
        $id = (int) $_POST['id_professor'];
        // Atualiza também a senha se ela foi informada
        if ($senha_plain !== '') {
            $hash = password_hash($senha_plain, PASSWORD_DEFAULT);
            $sql  = "UPDATE professores
                       SET nome_completo       = ?,
                           email_institucional = ?,
                           senha               = ?
                     WHERE id_professor = ?";
            $pdo->prepare($sql)->execute([$nome, $email, $hash, $id]);
        } else {
            // Atualiza apenas nome e e-mail
            $sql = "UPDATE professores
                       SET nome_completo       = ?,
                           email_institucional = ?
                     WHERE id_professor = ?";
            $pdo->prepare($sql)->execute([$nome, $email, $id]);
        }
    } else {
        // Inserção de novo professor
        $hash = password_hash($senha_plain, PASSWORD_DEFAULT); // Gera o hash da senha
        $pdo->prepare("INSERT INTO professores (nome_completo, email_institucional, senha) VALUES (?,?,?)")
            ->execute([$nome, $email, $hash]);
    }
     // Redireciona para evitar reenvio do formulário
    header("Location: gerenciarProfessores.php");
    exit;
}

// Lista todos os professores
$professores = $pdo->query(
    "SELECT id_professor, nome_completo, email_institucional
       FROM professores
      ORDER BY nome_completo ASC"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerenciar Professores</title>
  
  <!-- Ícone da aba do navegador -->
  <link rel="icon" href="./imgs/favicon.ico" type="image/x-icon">

  <!-- Bibliotecas de estilo e ícones -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="icon" href="./imgs/icons/iconTCC.png" type="image/x-icon">


  <!-- Estilos personalizados do sistema -->
  <link href="./css/style.css" rel="stylesheet">
  <link href="./css/index.css" rel="stylesheet">
  <link href="./css/professores.css" rel="stylesheet">
</head>
<body>
  <!-- Cabeçalho -->
  <div class="header">
    <div class="logo-container">
      <a href="menu.php" title="Voltar ao Menu">
        <img id="logoImage" src="./imgs/logoUTFPR.png" alt="Logo UTFPR">
      </a>
      <div>
        <h3 class="m-0">Gerenciar Professores</h3>
        <small>Olá, <?= htmlspecialchars($usuario) ?> 👋</small>
      </div>
    </div>
    <!-- Barra de ícones: acessibilidade, sair e modo noturno -->
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

  <div class="container my-4">
    <!-- Cadastro/Edição -->
    <div class="card p-4 mb-4 shadow form-professores">
      <h4 class="text-center mb-4"><?= $modo === 'editar' ? 'Editar Professor' : 'Cadastrar Professor' ?></h4>
      <form method="POST">
        <!-- Campo oculto com o ID do professor, apenas no modo de edição -->
        <?php if ($modo === 'editar'): ?>
          <input type="hidden" name="id_professor" value="<?= $idAtual ?>">
        <?php endif ?>
        <!-- Campo para o nome completo do professor -->
        <div class="mb-3">
            <label for="nome_completo" class="form-label">Nome Completo</label>
            <input type="text" name="nome_completo" id="nome_completo" class="form-control rounded-3" placeholder="Nome Completo" value="<?= htmlspecialchars($nomeAtual) ?>" required>
        </div>
        <!-- Campo para o e-mail institucional do professor -->
        <div class="mb-3">
            <label for="email_institucional" class="form-label">E-mail Institucional</label>
            <input type="email" name="email_institucional" id="email_institucional" class="form-control rounded-3" placeholder="E-mail Institucional" value="<?= htmlspecialchars($emailAtual) ?>" required>
        </div>
        <!-- Campo para senha, com comportamento adaptado para edição ou cadastro -->
        <div class="mb-3">
            <label for="senha" class="form-label"><?= $modo === 'editar' ? 'Nova senha (opcional)' : 'Senha' ?></label>
            <div class="senha-container">
              <input type="password" name="senha" id="senha" class="form-control senha-input" placeholder="Digite a senha aqui" <?= $modo === 'inserir' ? 'required' : '' ?>>
              <!-- Botão para mostrar/ocultar senha -->
              <button type="button" id="toggleSenha" class="senha-toggle">
                <i class="fas fa-eye"></i>
              </button>
            </div>
        </div>
        
        <!-- Botão de envio do formulário -->
        <div class="text-center">
          <button type="submit" class="button"><?= $botaoTxt ?></button> <!-- Texto varia conforme o modo -->

          <!-- Botão de cancelar (apenas no modo edição) -->
          <?php if ($modo === 'editar'): ?>
            <a href="gerenciarProfessores.php" class="btn btn-secondary ms-2">Cancelar</a>
          <?php endif ?>
        </div>
      </form>
    </div>

    <!-- Visualizador de Professores Cadastrados -->
    <div class="text-center mb-3">
      <button class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#professoresCadastrados">
        Visualizar Professores Cadastrados
      </button>
    </div>
    <!-- Tabela que lista todos os professores cadastrados -->
    <div class="collapse show" id="professoresCadastrados">
      <div class="card p-4 shadow tabela-professores">
        <table class="table table-striped mb-0">
          <thead>
            <tr><th>Nome Completo</th><th>E-mail Institucional</th><th>Ações</th></tr>
          </thead>
          <tbody>
            <!-- Lista dinâmica dos professores cadastrados -->
            <?php foreach ($professores as $prof): ?>
              <tr>
                <td><?= htmlspecialchars($prof['nome_completo']) ?></td>
                <td><?= htmlspecialchars($prof['email_institucional']) ?></td>
                <td>
                  <!-- Botão para editar o professor -->
                  <a href="?edit_id=<?= $prof['id_professor'] ?>" class="btn btn-warning btn-sm me-1">Editar</a>
                  <!-- Botão para excluir o professor (com confirmação) -->
                  <a href="?delete_id=<?= $prof['id_professor'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Confirmar exclusão?')">Excluir</a>
                </td>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>
    <!-- Botão Voltar -->
    <div class="text-center mt-4">
      <a href="menu.php" class="btn-voltar"><i class="fas fa-arrow-left"></i> Voltar ao Menu</a>
    </div>
  </div>
  
  <!-- Scripts necessários -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./scripts/scripts.js"></script>
  <script>
    // Script específico para o toggle de senha
    $(document).ready(function() {
      // --- MOSTRAR/OCULTAR SENHA ---
      $("#toggleSenha").on("click", function() {
        console.log("Botão de toggle de senha clicado");  // Log para depuração
        const senhaInput = $("#senha");                   // Campo de senha
        const tipo = senhaInput.attr("type");             // Tipo atual do campo (password ou text)
        const icon = $(this).find("i");                   // Ícone do botão
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

<?php } else {
  header("Location: login.php");
  exit();
} ?>
