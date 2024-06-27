<?php
// Informações de conexão com o banco de dados
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'celular';

// Função para estabelecer a conexão com o banco de dados
function getConnection() {
    global $DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME;

    $conn = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

    // Verifica se a conexão foi bem-sucedida
    if (!$conn) {
        die("Falha na conexão: " . mysqli_connect_error());
    }

    // Define o conjunto de caracteres para UTF-8
    mysqli_set_charset($conn, "utf8");

    return $conn;
}

// Você pode adicionar outras funções úteis relacionadas ao banco de dados aqui, se necessário