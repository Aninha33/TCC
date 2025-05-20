<?php

$host = 'mysql-tcc.alwaysdata.net';     // Endereço do servidor MySQL
$db   = 'tcc_horarios';                 // Nome do banco de dados
$user = 'tcc';                          // Usuário do banco de dados
$pass = 'socorrodeus';                  // senha está visível no código
$port = '3306';                         // Porta padrão do MySQL
$charset = 'utf8mb4';                   // Conjunto de caracteres recomendado

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset"; // Monta a string de conexão (DSN)

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Exibe exceções em caso de erro
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna resultados como array associativo
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa prepared statements reais
];

// Tenta estabelecer a conexão com o banco
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // Testando se conexão está ativada
    // echo "✅ Conectado ao banco de dados MySQL online ($db) com sucesso!";
} catch (PDOException $e) {
    // Testando se conexão está erro
    echo "❌ Erro ao conectar ao banco de dados MySQL online: " . $e->getMessage();
    // exit(); 
}


?>


