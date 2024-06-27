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

// O processamento do formulário será feito via AJAX
?>

<h2>Adicionar Novo Celular Roubado</h2>
<form id="adicionarCelularForm" onsubmit="return false;">
    <input type="text" name="imei" id="imei" placeholder="IMEI" required maxlength="15" pattern="\d{15}"
        title="O IMEI deve conter exatamente 15 dígitos numéricos">
    <input type="text" name="numero_celular" id="numero_celular" placeholder="Número do Celular" required>
    <div class="input-group" style="display: flex; align-items: center; justify-content: center;">
        <div style="display: flex; align-items: center;">
            <label style="margin-top: -20px; margin-left: 5px; margin-right: 10px; font-size: 18px; font-weight: bold;">Data</label>
            <input type="text" name="data_roubo" id="data_roubo" placeholder="__/__/____" required>
            <span class="input-group-addon" style="cursor: pointer; margin-top: -20px; margin-left: 15px; margin-right: 10px;">
                <i class="fa fa-calendar" id="calendar-icon"></i>
            </span>
        </div>
        <label style="margin-top: -20px; margin-left: 15px; margin-right: 10px;  font-size: 18px; font-weight: bold;">Horário</label>
        <input type="text" name="hora_roubo" id="hora_roubo" placeholder="00:00" required>
    </div>
    <input type="text" name="marca" placeholder="Marca" required>
    <div class="input-container">
        <input type="text" name="cor" id="cor" placeholder="Cor" required onkeydown="return event.key != ' '">
        <span id="cor-preview"></span>
    </div>
    <input type="text" name="proprietario" placeholder="Nome do proprietário" required>
    <input type="email" name="email_contato" placeholder="Email de Contato" required>
    <input type="tel" name="telefone_contato" placeholder="Outro telefone de Contato" required>
    <input type="submit" id="adicionarBtn" value="Adicionar Celular">
</form>
<div id="mensagem"></div>
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p id="modal-message"></p>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const adicionarForm = document.getElementById('adicionarCelularForm');
    const adicionarBtn = document.getElementById('adicionarBtn');
    const mensagemDiv = document.getElementById('mensagem');

    function adicionarCelular() {
        const formData = new FormData(adicionarForm);

        fetch('adicionar_celular_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(data => {
            mensagemDiv.innerHTML = data;
            if (data.includes("sucesso")) {
                adicionarForm.reset();
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mensagemDiv.innerHTML = "Erro ao adicionar celular.";
        });
    }

    adicionarBtn.addEventListener('click', adicionarCelular);
    
    adicionarForm.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            adicionarCelular();
        }
    });

    if (typeof initializeAdicionarCelular === 'function') {
        initializeAdicionarCelular();
    }
});
</script>
