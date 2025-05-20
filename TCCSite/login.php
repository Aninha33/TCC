<?php
session_start(); // Inicia a sessão 
require_once 'conexao.php'; // conexão PDO com banco MySQL

if ($_SERVER["REQUEST_METHOD"] === "POST") { // Verifica se o formulário foi enviado via método POST
    // Recupera os dados enviados pelo formulário
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Login forçado para admin
    if ($email === "admin@utfpr.edu.br" && $senha === "admin") {
        $_SESSION['user_connected'] = true;
        $_SESSION['tipo_usuario'] = "admin";
        $_SESSION['nome_usuario'] = "Administrador";
        header("Location: menu.php"); // Redireciona para o menu principal
        exit;
    }

    // Login pelo banco de dados (professores)
    $sql = "SELECT * FROM professores WHERE email_institucional = :email LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC); // Busca o resultado da consulta

    // Verifica se o usuário existe e se a senha está correta
    if ($usuario && !empty($usuario['senha']) && password_verify($senha, $usuario['senha'])) {
        $_SESSION['user_connected'] = true;
        $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
        $_SESSION['nome_usuario'] = $usuario['nome_completo'];
        $_SESSION['id_professor'] = $usuario['id_professor'];
        header("Location: menu.php"); // Redireciona para o menu principal
        exit;
    } else {
        $_SESSION['login_error'] = "E-mail ou senha incorretos.";  // Caso o login falhe, define uma mensagem de erro na sessão
        header("Location: index.php");
        exit;
    }
} else {
    header("Location: index.php");  // Se a requisição não for POST, redireciona para a página de login
    exit;
}
