<?php
if (!extension_loaded('pdo_pgsql')) {
    echo "❌ Extensão pdo_pgsql NÃO está carregada.<br>";
} else {
    echo "✅ Extensão pdo_pgsql está carregada!<br>";
}

if (!extension_loaded('pgsql')) {
    echo "❌ Extensão pgsql NÃO está carregada.<br>";
} else {
    echo "✅ Extensão pgsql está carregada!<br>";
}

$python = 'C:\\Python311\\python.exe';
$script = __DIR__ . '\\otimizador.py';
$cmd    = "\"$python\" \"$script\" 2>&1";
$output = shell_exec($cmd);
$schedule = json_decode($output, true);


?>
