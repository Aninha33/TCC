<?php
session_start();

// Função para gerar o HTML da tabela de horários (para pré-visualização)
function gerarHtmlTabelaHorarios($pdo_conn, $search_sql_condition_func, $search_params_func, &$error_message_func) {
    $grade_html = "<p>Nenhum dado para gerar o relatório.</p>"; // Mensagem padrão
    if (isset($pdo_conn) && $pdo_conn instanceof PDO) {  // Verifica se a conexão PDO foi fornecida corretamente
        try {
            // Monta a consulta SQL dinâmica com as condições fornecidas
            $sql_func = "SELECT 
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
                    . $search_sql_condition_func . 
                    " ORDER BY
                        FIELD(ah.horario, 'M1', 'M2', 'M3', 'M4', 'M5', 'M6', 'T1', 'T2', 'T3', 'T4', 'T5', 'T6'),
                        FIELD(ah.dia_semana, 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta')";

            // Prepara e executa a consulta SQL
            $stmt_func = $pdo_conn->prepare($sql_func);
            $stmt_func->execute($search_params_func);
            $alocacoes_result_func = $stmt_func->fetchAll(PDO::FETCH_ASSOC); // Busca todos os resultados como array associativo

            if (count($alocacoes_result_func) > 0) {  // Verifica se existem alocações para exibir
                $horarios_template_func = [
                    "M1" => ["inicio" => "07h30", "termino" => "08h20"], "M2" => ["inicio" => "08h20", "termino" => "09h10"],
                    "M3" => ["inicio" => "09h10", "termino" => "10h00"], "M4" => ["inicio" => "10h20", "termino" => "11h10"],
                    "M5" => ["inicio" => "11h10", "termino" => "12h00"], "M6" => ["inicio" => "12h00", "termino" => "12h50"],
                    "T1" => ["inicio" => "13h00", "termino" => "13h50"], "T2" => ["inicio" => "13h50", "termino" => "14h40"],
                    "T3" => ["inicio" => "14h40", "termino" => "15h30"], "T4" => ["inicio" => "15h50", "termino" => "16h40"],
                    "T5" => ["inicio" => "16h40", "termino" => "17h30"], "T6" => ["inicio" => "17h30", "termino" => "18h20"],
                ];
                $dias_semana_db_func = ["Segunda", "Terça", "Quarta", "Quinta", "Sexta"];
                $grade_horarios_relatorio_func = []; // Inicializa a estrutura da grade com os horários e dias da semana vazios
                foreach ($horarios_template_func as $cod_horario_f => $details_f) {
                    $grade_horarios_relatorio_func[$cod_horario_f] = [
                        "inicio" => $details_f["inicio"],
                        "termino" => $details_f["termino"],
                    ];
                    foreach($dias_semana_db_func as $dia_f) {
                        $grade_horarios_relatorio_func[$cod_horario_f][$dia_f] = "";  // Inicializa vazio
                    }
                }

                foreach ($alocacoes_result_func as $alocacao_item_f) {
                    $cod_horario_db_f = $alocacao_item_f["cod_horario"];
                    $dia_db_f = $alocacao_item_f["dia_semana"];
                    // Verifica se o horário e o dia existem na grade
                    if (isset($grade_horarios_relatorio_func[$cod_horario_db_f]) && in_array($dia_db_f, $dias_semana_db_func)) {
                         // Monta o texto a ser exibido na célula da tabela
                        $display_text_f = "<div class='alocacao-item'>";
                        $display_text_f .= "<strong>" . htmlspecialchars($alocacao_item_f["nome_disciplina"], ENT_QUOTES, "UTF-8") . "</strong>";
                        $display_text_f .= "Professor: " . htmlspecialchars($alocacao_item_f["nome_professor"], ENT_QUOTES, "UTF-8");
                        // Exibe o período caso exista
                        if (!empty($alocacao_item_f["periodo"])) {  
                            $display_text_f .= " (Período: " . htmlspecialchars($alocacao_item_f["periodo"], ENT_QUOTES, "UTF-8") . ")";
                        }
                        $display_text_f .= "</div>";
                        // Se já houver conteúdo, concatena, senão insere o novo conteúdo
                        if (!empty($grade_horarios_relatorio_func[$cod_horario_db_f][$dia_db_f])) {
                            $grade_horarios_relatorio_func[$cod_horario_db_f][$dia_db_f] .= $display_text_f; 
                        } else {
                            $grade_horarios_relatorio_func[$cod_horario_db_f][$dia_db_f] = $display_text_f; 
                        }
                    }
                }
                // Monta o HTML da tabela com os dados preenchidos
                $grade_html = "<div class='div-tabela-responsiva' id='tabelaRelatorioContainer'>";
                $grade_html .= "<table class='tabela-horario'><thead><tr><th>(1)</th><th>INÍCIO</th><th>TÉRM.</th>";
                // Cabeçalho da tabela com os dias da semana
                foreach($dias_semana_db_func as $index => $dia_header) {
                    $grade_html .= "<th>" . htmlspecialchars($dia_header, ENT_QUOTES, "UTF-8") . " [" . ($index + 2) . "]</th>";
                }
                $grade_html .= "</tr></thead><tbody>";
                // Geração das linhas da tabela
                foreach ($grade_horarios_relatorio_func as $cod_h_display => $dados_h) {
                    $grade_html .= "<tr>";
                    $grade_html .= "<td>" . htmlspecialchars($cod_h_display, ENT_QUOTES, "UTF-8") . "</td>";
                    $grade_html .= "<td>" . htmlspecialchars($dados_h["inicio"], ENT_QUOTES, "UTF-8") . "</td>";
                    $grade_html .= "<td>" . htmlspecialchars($dados_h["termino"], ENT_QUOTES, "UTF-8") . "</td>";
                    // Preenchimento das colunas de dias da semana
                    foreach ($dias_semana_db_func as $dia_k) {
                        $grade_html .= "<td>" . $dados_h[$dia_k] . "</td>";
                    }
                    $grade_html .= "</tr>";
                }
                $grade_html .= "</tbody></table></div>";  // Fecha as tags da tabela e da div
                return $grade_html;  // Retorna o HTML gerado

            } else {
                return null;  // Retorna nulo se não houver registros
            }
        } catch (PDOException $e_func) {
            // Captura e retorna mensagens de erro caso a consulta falhe
            $error_message_func = "Erro ao buscar dados para o relatório: " . $e_func->getMessage();
            return null;
        }
    } elseif (empty($error_message_func) && (!isset($pdo_conn) || !$pdo_conn instanceof PDO)) {
         $error_message_func = "A conexão com o banco de dados não foi estabelecida.";
         return null;
    }
    return null; 
}

// Verifica se há uma sessão de usuário autenticado
if (isset($_SESSION['user_connected']) && $_SESSION['user_connected'] === true) {
    // Recupera o nome do usuário da sessão, ou define como 'Administrador' caso não exista
    $usuario = $_SESSION['nome_usuario'] ?? 'Administrador';

    // Tenta incluir o arquivo de conexão com o banco de dados
    if (file_exists("conexao.php")) {
        include "conexao.php";
    } elseif (file_exists(__DIR__ . "/conexao.php")) { 
        include __DIR__ . "/conexao.php";
    } else {
        $pdo = null;  // Define conexão como nula se não encontrar o arquivo de conexão
    }

    // Inicializa variáveis de exibição e controle
    $search_term_display = '';              // Termo de busca digitado pelo usuário
    $report_type_display = '';              // Tipo de relatório selecionado
    $error_message_display = '';            // Mensagem de erro para exibir ao usuário
    $show_table_display = false;            // Controle de exibição da tabela
    $html_tabela_para_visualizacao = '';    // HTML gerado da tabela de horários

    if ($_SERVER["REQUEST_METHOD"] == "POST") {                     // Verifica se houve uma requisição do tipo POST (envio de formulário)
        $report_type_action = $_POST['report_type'] ?? '';          // Tipo de relatório escolhido
        $search_term_action = trim($_POST['search_term'] ?? '');    // Termo de busca informado
        // Salva os dados recebidos para exibição posterior
        $report_type_display = $report_type_action;
        $search_term_display = $search_term_action;
        // Define as condições e parâmetros SQL padrão como vazios
        $search_sql_condition_main = "";
        $search_params_main = [];
        // Verifica o tipo de relatório selecionado
        if ($report_type_action == "completo"){
            // Nenhuma condição adicional é necessária
        } elseif (empty($report_type_action)) {
            $error_message_display = "Por favor, selecione o tipo de relatório.";
        } elseif (empty($search_term_action) && $report_type_action != "completo") {
            $error_message_display = "Por favor, digite o termo de busca para este tipo de relatório.";
        } else {
            // Tratamento para relatório por professor
            if ($report_type_action == "professor") {
                if (is_numeric($search_term_action)){
                    $error_message_display = "Para relatório por professor, por favor, digite um nome.";
                } else {
                    // Define condição SQL para buscar por professor exato
                    $search_sql_condition_main = " WHERE p.nome_completo = ?";
                    $search_params_main[] = $search_term_action;
                }
            // Tratamento para relatório por período
            } elseif ($report_type_action == "periodo") {
                if (!is_numeric($search_term_action)) {
                    $error_message_display = "Para relatório por período, por favor, digite um número.";
                } else {
                     // Define condição SQL para buscar por período específico
                    $search_sql_condition_main = " WHERE ah.periodo = ?";
                    $search_params_main[] = (int)$search_term_action;
                }
            // Tratamento para tipo inválido
            } else {
                 if ($report_type_action != "completo") { 
                    $error_message_display = "Tipo de relatório inválido.";
                 }
            }
        }
        // Caso não haja erro nas validações acima
        if (empty($error_message_display)) {
            // Gera o HTML da tabela com base nas condições definidas
            $html_tabela_gerada = gerarHtmlTabelaHorarios($pdo, $search_sql_condition_main, $search_params_main, $error_message_display);
            if ($html_tabela_gerada !== null) { // Se a geração foi bem-sucedida
                $show_table_display = true; 
                $html_tabela_para_visualizacao = $html_tabela_gerada;
            } else { // Caso nenhuma alocação tenha sido encontrada
                 if (empty($error_message_display)) { 
                    if ($report_type_action == "professor") {
                        $error_message_display = "Nenhuma alocação encontrada para o professor '" . htmlspecialchars($search_term_action, ENT_QUOTES, "UTF-8") . "'. Verifique o nome digitado.";
                    } elseif ($report_type_action == "periodo") {
                        $error_message_display = "Nenhuma alocação encontrada para o período '" . htmlspecialchars($search_term_action, ENT_QUOTES, "UTF-8") . "'.";
                    } elseif ($report_type_action == "completo") {
                         $error_message_display = "Nenhuma alocação encontrada no sistema para gerar o relatório completo.";
                    }
                }
            }
        }
    }
} else {
    // Redireciona para o login se o usuário não estiver autenticado
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Relatórios</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Fontes e Ícones -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="icon" href="./imgs/icons/iconTCC.png" type="image/x-icon">


  <!-- Bibliotecas JS para exportar PDF e capturar HTML como imagem -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Estilos personalizados -->
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/relatorios.css">

</head>
<body>

<div class="header">
  <div class="logo-container">
    <a href="menu.php">   <!-- Logotipo da UTFPR que redireciona ao menu -->
      <img id="logoImage" src="./imgs/logoUTFPR.png" alt="Logo UTFPR" title="Voltar ao menu">
    </a>
    <div>
      <h3 class="m-0">Relatórios</h3>
      <small>Olá, <?= htmlspecialchars($usuario, ENT_QUOTES, "UTF-8") ?> 👋</small> <!-- Saudação ao usuário autenticado -->
    </div>
  </div>
  <!-- Barra de ícones: acessibilidade, sair, modo noturno -->
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

<!-- Formulário para Selecionar o Tipo de Relatório -->
<div class="container my-5 relatorio-box">
  <h2> <i class="fas fa-chart-bar"></i> Relatórios do Sistema</h2>
  
  <form method="POST" action="relatorios.php" id="formRelatorios">
    <div class="mb-3">
      <label for="report_type" class="form-label">Tipo de Relatório:</label>
      <select class="form-select" id="report_type" name="report_type">
        <option value="" <?= ($report_type_display == '' ? 'selected' : '') ?>>Selecione...</option>
         <!-- Opções de relatório -->
        <option value="completo" <?= ($report_type_display == 'completo' ? 'selected' : '') ?>>Relatório Completo</option>
        <option value="professor" <?= ($report_type_display == 'professor' ? 'selected' : '') ?>>Relatório por Professor</option>
        <option value="periodo" <?= ($report_type_display == 'periodo' ? 'selected' : '') ?>>Relatório por Período</option>
      </select>
    </div>

    <!-- Campo de busca, aparece apenas se o relatório for por professor ou período -->
    <div class="mb-3" id="search_term_div" style="<?= ($report_type_display == 'completo' || $report_type_display == '' ? 'display:none;' : '') ?>">
      <label for="search_term" class="form-label">Buscar por:</label>
      <input type="text" class="form-control" id="search_term" name="search_term" value="<?= htmlspecialchars($search_term_display, ENT_QUOTES, "UTF-8") ?>" placeholder="Digite o nome completo do professor ou o número do período">
    </div>
    
    <!-- Botão para gerar o relatório -->
    <button type="submit" class="btn btn-primary" name="action" value="generate"> <i class="fas fa-eye me-2"></i>  Gerar Relatório (Pré-visualizar)</button>
    
    <!-- Botão para baixar o relatório em PDF (aparece apenas se houver resultados) -->
    <?php if ($show_table_display && empty($error_message_display)): ?>
      <button type="button" class="btn btn-primary" id="downloadPdfBtn"><i class="fas fa-file-pdf me-2"></i> Baixar Relatório PDF</button>
    <?php endif; ?>
  </form>

    <!-- Exibição de mensagens de erro (se tiver) -->
    <?php if (!empty($error_message_display)): ?>
        <div class="alert alert-danger mt-3" role="alert">
        <?= htmlspecialchars($error_message_display, ENT_QUOTES, "UTF-8") ?>
        </div>
    <?php endif; ?>
    
    <!-- Pré-visualização do relatório em tabela -->
    <?php if ($show_table_display && empty($error_message_display)): ?>
        <div class="mt-4" id="preview_area">
        <h3>Pré-visualização do Relatório</h3>
        <?= $html_tabela_para_visualizacao ?>
        </div>
    <?php endif; ?>


    <!-- Botão Voltar -->
    <div class="text-center mt-4">
      <a href="menu.php" class="btn-voltar"><i class="fas fa-arrow-left"></i> Voltar ao Menu</a>
    </div>
  
  
</div>

<script src="./scripts/scripts.js"></script>

<!-- Script para controle dinâmico da exibição do campo de busca -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const reportTypeSelect = document.getElementById('report_type');
    const searchTermDiv = document.getElementById('search_term_div');
    const searchTermInput = document.getElementById('search_term');
    
    // Alterna a exibição do campo de busca conforme o tipo de relatório selecionado
    if (reportTypeSelect) {
        reportTypeSelect.addEventListener('change', function() {
            if (this.value === 'completo' || this.value === '') {
                searchTermDiv.style.display = 'none';
                searchTermInput.value = ''; 
            } else {
                searchTermDiv.style.display = 'block';
                if (this.value === 'professor') {
                    searchTermInput.placeholder = 'Digite o nome completo do professor';
                } else if (this.value === 'periodo') {
                    searchTermInput.placeholder = 'Digite o número do período';
                }
            }
        });
    }

    // Função para gerar e baixar o relatório em PDF ao clicar no botão 
    const downloadPdfBtn = document.getElementById('downloadPdfBtn'); // Captura o botão de download do PDF pelo ID
    if (downloadPdfBtn) { // Verifica se o botão existe antes de adicionar o comportamento
        downloadPdfBtn.addEventListener('click', function() {  // Define o comportamento quando o botão é clicado
            const { jsPDF } = window.jspdf;
            const tableToCapture = document.querySelector('#tabelaRelatorioContainer .tabela-horario'); // Captura a tabela em si
            
            // Verifica se o elemento existe na página
            if (tableToCapture) {
                
                const containerDiv = document.getElementById('tabelaRelatorioContainer');
                document.body.classList.add('pdf-capturing');
                // Salvar o scroll atual para restaurar depois
                const scrollX = containerDiv.scrollLeft;
                const scrollY = containerDiv.scrollTop;

                // Forçar a div a mostrar todo o conteúdo para o html2canvas
                containerDiv.style.overflow = 'visible';
                containerDiv.style.height = tableToCapture.scrollHeight + 'px';
                containerDiv.style.width = tableToCapture.scrollWidth + 'px';
                
                // Usa html2canvas para converter o elemento HTML em um canvas (uma imagem desenhada via JavaScript)
                html2canvas(tableToCapture, {
                    scale: 1.5, // Escala para melhor qualidade
                    useCORS: true,
                    logging: false,
                    width: tableToCapture.scrollWidth,
                    height: tableToCapture.scrollHeight,
                    windowWidth: tableToCapture.scrollWidth,
                    windowHeight: tableToCapture.scrollHeight,
                    scrollX: 0, // Garantir que comece do início
                    scrollY: 0  // Garantir que comece do início
                }).then(canvas => {
                    // Restaurar estilos da div
                    containerDiv.style.overflow = 'auto'; // ou o valor original se guardado
                    containerDiv.style.height = ''; // Volta ao CSS
                    containerDiv.style.width = '';  // Volta ao CSS
                    containerDiv.scrollLeft = scrollX;
                    containerDiv.scrollTop = scrollY;
                    document.body.classList.remove('pdf-capturing');

                    const imgData = canvas.toDataURL('image/png'); // Converte o canvas para o formato de imagem PNG em base64
                    const pdf = new jsPDF({ // Cria um novo documento PDF
                        orientation: 'landscape',
                        unit: 'pt',
                        format: 'a4'
                    });

                    const canvasWidth = canvas.width; // Define a largura da página do PDF
                    const canvasHeight = canvas.height;

                    // Calcula a altura proporcional da imagem com base na largura do PDF
                    const pdfPageWidth = pdf.internal.pageSize.getWidth();
                    const pdfPageHeight = pdf.internal.pageSize.getHeight();
                    const margin = 30; // Margem para melhor visualização
                    const usablePdfPageWidth = pdfPageWidth - (2 * margin);
                    const usablePdfPageHeight = pdfPageHeight - (2 * margin);

                    const scaleToFitWidth = usablePdfPageWidth / canvasWidth;
                    const scaledCanvasHeight = canvasHeight * scaleToFitWidth;

                    // Adiciona a imagem ao PDF na posição (0,0) ocupando toda a largura disponível
                    let yPositionInCanvas = 0;
                    let pageNumber = 0;

                    while (yPositionInCanvas < canvasHeight) {
                        pageNumber++;
                        if (pageNumber > 1) {
                            pdf.addPage();
                        }

                        let segmentHeightOnCanvas = (usablePdfPageHeight / scaleToFitWidth);
                        if (yPositionInCanvas + segmentHeightOnCanvas > canvasHeight) {
                            segmentHeightOnCanvas = canvasHeight - yPositionInCanvas;
                        }

                        const tempCanvas = document.createElement('canvas');
                        tempCanvas.width = canvasWidth;
                        tempCanvas.height = segmentHeightOnCanvas;
                        const tempCtx = tempCanvas.getContext('2d');
                        
                        tempCtx.drawImage(canvas, 0, yPositionInCanvas, canvasWidth, segmentHeightOnCanvas, 0, 0, canvasWidth, segmentHeightOnCanvas);
                        const segmentImgData = tempCanvas.toDataURL('image/png');
                        const segmentImgHeightScaled = segmentHeightOnCanvas * scaleToFitWidth;

                        pdf.addImage(segmentImgData, 'PNG', margin, margin, usablePdfPageWidth, segmentImgHeightScaled);
                        yPositionInCanvas += segmentHeightOnCanvas;
                    }

                    // Salva e inicia o download do arquivo PDF com o nome "relatorio_horarios.pdf"
                    pdf.save('relatorio_horarios.pdf');

                }).catch(err => {
                    containerDiv.style.overflow = 'auto';
                    containerDiv.style.height = '';
                    containerDiv.style.width = '';
                    containerDiv.scrollLeft = scrollX;
                    containerDiv.scrollTop = scrollY;
                    document.body.classList.remove('pdf-capturing');
                    console.error("Erro ao gerar PDF: ", err);
                    alert("Ocorreu um erro ao gerar o PDF. Verifique o console para mais detalhes.");
                });
            } else {
                alert("Elemento da tabela do relatório não encontrado para gerar o PDF.");
            }
        });
    }
});
</script>

</body>
</html>
