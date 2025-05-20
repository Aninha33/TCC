<?php
session_start(); // Inicia a sessão para controle de login
if (isset($_SESSION['user_connected']) && $_SESSION['user_connected'] === true) { // Verifica se o usuário está logado (sessão válida)
  $usuario = $_SESSION['nome_usuario'] ?? 'Administrador'; // Pega o nome do usuário armazenado na sessão ou define 'Administrador' caso não esteja definido

  // Busca da correspondência EXATA do professor
  $search_term = isset($_GET["q"]) ? trim($_GET["q"]) : "";
  // Inicializa as condições SQL e os parâmetros da busca como vazios
  $search_sql_condition = "";
  $search_params = [];

  // Define a condição de busca se houver termo informado
  if (!empty($search_term)) {
    if (is_numeric($search_term)) {
        // Se o termo de busca for numérico, busca por período 
        $search_sql_condition = " WHERE ah.periodo = ?";
        $search_params[] = (int)$search_term;
    } else {
        // Se não for numérico, busca por nome de professor 
        $search_sql_condition = " WHERE p.nome_completo = ?"; 
        $search_params[] = $search_term; // Sem os wildcards '%'
    }
  }

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Horário Gerado</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <!-- Estilos gerais -->
  <link href="./css/style.css" rel="stylesheet">
  <link href="./css/horarios.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="icon" href="./imgs/icons/iconTCC.png" type="image/x-icon">

  <!-- Estilos específicos da busca e da tabela -->
  <style>
    /* Estilos específicos para o campo de busca */
    .search-container {
      text-align: center;
      padding: 20px;
    }
    /* Estiliza o formulário da busca */
    .search-form {
      position: relative;
      display: inline-flex;
      align-items: center;
      max-width: 400px;
      width: 100%;
    }
    /* Estiliza o campo de busca */
    .search-input {
      padding: 10px 40px 10px 20px;
      border-radius: 20px;
      border: 1px solid #ccc;
      width: 100%;
      font-family: 'Poppins', sans-serif;
      height: 42px;
      box-sizing: border-box;
    }
     /* Estiliza o botão da lupa dentro do campo de busca */
    .search-button {
      position: absolute;
      right: 12px;
      background: transparent;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0;
      margin: 0;
      height: 24px;
      width: 24px;
    }
    
    .search-icon {
      color: #555;
      font-size: 16px;
    }
   
  </style>
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

<!-- Campo de Busca -->
<div class="search-container">
  <form method="GET" action="gerarHorarios.php" class="search-form">
    <input type="text" name="q" placeholder="Buscar por período ou nome exato do professor"
           value="<?= htmlspecialchars($search_term, ENT_QUOTES, "UTF-8") ?>"
           class="search-input">
    <button type="submit" class="search-button">
      <i class="fas fa-search search-icon"></i>
    </button>
  </form>
</div>

<!-- Tabela de Horários -->
<div style="padding: 0 2rem 2rem; overflow-x: auto;">
  <table class="tabela-horario">
    <thead>
      <tr>
        <th>(1)</th>
        <th>INÍCIO</th>
        <th>TÉRM.</th>
        <th>SEGUNDA [2]</th>
        <th>TERÇA [3]</th>
        <th>QUARTA [4]</th>
        <th>QUINTA [5]</th>
        <th>SEXTA [6]</th>
      </tr>
    </thead>
    <tbody>
<?php
if (file_exists("conexao.php")) { // Inclui a conexão com o banco de dados
    include "conexao.php";
} elseif (file_exists(__DIR__ . "/conexao.php")) { 
    include __DIR__ . "/conexao.php";
} else {
    $pdo = null; // Se não encontrar o arquivo, define a conexão como nula
}

//  Define o template fixo dos horários com seus respectivos intervalos
$horarios_template = [
    "M1" => ["inicio" => "07h30", "termino" => "08h20"], "M2" => ["inicio" => "08h20", "termino" => "09h10"],
    "M3" => ["inicio" => "09h10", "termino" => "10h00"], "M4" => ["inicio" => "10h20", "termino" => "11h10"],
    "M5" => ["inicio" => "11h10", "termino" => "12h00"], "M6" => ["inicio" => "12h00", "termino" => "12h50"],
    "T1" => ["inicio" => "13h00", "termino" => "13h50"], "T2" => ["inicio" => "13h50", "termino" => "14h40"],
    "T3" => ["inicio" => "14h40", "termino" => "15h30"], "T4" => ["inicio" => "15h50", "termino" => "16h40"],
    "T5" => ["inicio" => "16h40", "termino" => "17h30"], "T6" => ["inicio" => "17h30", "termino" => "18h20"],
];

// Define os dias da semana
$dias_semana_db = ["Segunda", "Terça", "Quarta", "Quinta", "Sexta"];

// Inicializa a estrutura da grade de horários
$grade_horarios = [];
foreach ($horarios_template as $cod_horario => $details) {
    $grade_horarios[$cod_horario] = [
        "inicio" => $details["inicio"],
        "termino" => $details["termino"],
    ];
    foreach($dias_semana_db as $dia) {
        $grade_horarios[$cod_horario][$dia] = ""; // Inicializa vazio
    }
}

// Verifica se a conexão com o banco foi estabelecida corretamente
if (isset($pdo) && $pdo instanceof PDO) {
    try {
        $sql = "SELECT
                    ah.horario AS cod_horario,
                    ah.dia_semana,
                    d.nome_disciplina,
                    p.nome_completo AS nome_professor,
                    ah.periodo
                FROM
                    alocacao_horarios ah
                JOIN
                    disciplinas d ON ah.id_disciplina = d.id_disciplina
                JOIN
                    professores p ON ah.id_professor = p.id_professor"
                . $search_sql_condition . // Adiciona a condição de busca
                " ORDER BY
                    FIELD(ah.horario, 'M1', 'M2', 'M3', 'M4', 'M5', 'M6', 'T1', 'T2', 'T3', 'T4', 'T5', 'T6'),
                    FIELD(ah.dia_semana, 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($search_params);
        $alocacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Preenche a grade com os resultados da consulta
        foreach ($alocacoes as $alocacao) {
            $cod_horario_db = $alocacao["cod_horario"];
            $dia_db = $alocacao["dia_semana"];

            if (isset($grade_horarios[$cod_horario_db]) && in_array($dia_db, $dias_semana_db)) {
                // Monta o conteúdo da célula
                $display_text = "<div class='alocacao-item'>";
                $display_text .= "<strong>" . htmlspecialchars($alocacao["nome_disciplina"], ENT_QUOTES, "UTF-8") . "</strong>";
                $display_text .= "Professor: " . htmlspecialchars($alocacao["nome_professor"], ENT_QUOTES, "UTF-8");
                if (!empty($alocacao["periodo"])) {
                    $display_text .= " (Período: " . htmlspecialchars($alocacao["periodo"], ENT_QUOTES, "UTF-8") . ")";
                }
                $display_text .= "</div>";
                // Adiciona o conteúdo à grade
                if (!empty($grade_horarios[$cod_horario_db][$dia_db])) {
                    $grade_horarios[$cod_horario_db][$dia_db] .= $display_text; 
                } else {
                    $grade_horarios[$cod_horario_db][$dia_db] = $display_text; 
                }
            }
        }

    } catch (PDOException $e) { // Exibe mensagem de erro caso a consulta falhe
        echo "<tr><td colspan='8' style='text-align:center; color:red;'>Erro ao buscar horários: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, "UTF-8") . "</td></tr>";
    }
} else { // Mensagem se a conexão não estiver disponível
     echo "<tr><td colspan='8' style='text-align:center; color:red;'>A conexão com o banco de dados não foi estabelecida. Verifique o arquivo conexao.php.</td></tr>";
}

// Renderiza a tabela com os dados preenchidos
foreach ($grade_horarios as $cod_horario_display => $dados_horario) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($cod_horario_display, ENT_QUOTES, "UTF-8") . "</td>";
    echo "<td>" . htmlspecialchars($dados_horario["inicio"], ENT_QUOTES, "UTF-8") . "</td>";
    echo "<td>" . htmlspecialchars($dados_horario["termino"], ENT_QUOTES, "UTF-8") . "</td>";
    foreach ($dias_semana_db as $dia_key) {
        echo "<td>" . $dados_horario[$dia_key] . "</td>";
    }
    echo "</tr>";
}
?>
    </tbody>
  </table>

  <!-- Botão de voltar -->
  <div style="text-align:center; margin-top: 30px;">
    <a href="menu.php" class="btn-voltar"><i class="fas fa-arrow-left"></i> Voltar ao Menu</a>
  </div>
</div>

<!-- Scripts necessários -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="./scripts/scripts.js"></script>
</body>
</html>


<?php } else {
  header("Location: login.php"); // Redireciona para o login caso o usuário não esteja autenticado
  exit();
} ?>
