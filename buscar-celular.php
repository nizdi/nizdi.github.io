<?php
session_start();
require_once 'db_connection.php';
require_once 'functions.php';
$conn = getConnection();
if (!$conn) {
    die("Falha na conexão com o banco de dados.");
}

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    header('Location: login.html');
    exit;
}

$user_id = $_SESSION['id'];
$user_name = $_SESSION['name'];
$message = '';

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.html');
    exit;
}

$celular_encontrado = null;

// Buscar celular por IMEI ou número
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['buscar'])) {
    $busca = preg_replace('/[^0-9]/', '', $_GET['busca']);
    $celular_encontrado = buscarCelular($conn, $busca);
}

$celulares_usuario = listarCelularesDoUsuario($conn, $user_id);
?>

<head>
    <link rel="stylesheet" href="styles.css">
</head>

<h2>Buscar Celular por IMEI ou Número</h2>
<form id="buscarForm" onsubmit="return false;">
    <input type="text" name="busca" id="busca" placeholder="IMEI ou Número do Celular" required>
    <button type="button" id="buscarBtn" class="search-button">
        <i class="fas fa-search"></i> Buscar
    </button>
</form>


<div id="resultadoBusca"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const buscarForm = document.getElementById('buscarForm');
    const buscarBtn = document.getElementById('buscarBtn');
    const buscarInput = document.getElementById('busca');
    const resultadoBusca = document.getElementById('resultadoBusca');

    function realizarBusca() {
        const busca = buscarInput.value;
        console.log("Valor de busca:", busca);

        fetch('buscar_celular_ajax.php?busca=' + encodeURIComponent(busca))
            .then(response => {
                console.log("Resposta recebida:", response);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(data => {
                console.log("Dados recebidos:", data);
                resultadoBusca.innerHTML = data;
            })
            .catch(error => {
                console.error('Erro:', error);
            });
    }

    buscarBtn.addEventListener('click', realizarBusca);
    
    buscarInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            realizarBusca();
        }
    });
});
</script>
