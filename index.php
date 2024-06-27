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

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.html');
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GUARDIÃO MÓVEL</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="sidebar">
            <img src="http://localhost/logop.png" alt="Logo" class="logo-sidebar"
                style="width: 200px; height: auto; align-self: center; margin-bottom: 0px;">
            <ul>
                <li><a href="javascript:void(0)" onclick="loadContent('home.php')">Início</a></li>
                <li><a href="javascript:void(0)" onclick="loadContent('adicionar-celular.php')">Adicionar Celular</a>
                </li>
                <li><a href="javascript:void(0)" onclick="loadContent('seus-celulares.php')">Celulares Cadastrados</a>
                </li>
                <li><a href="javascript:void(0)" onclick="loadContent('buscar-celular.php')">Buscar Celulares</a></li>
                <li><a href="?logout=1">Logout</a></li>
            </ul>
        </div>
        <div class="topbar">
            <div class="topbar-content">
                <div
                    style="position: absolute; top: 0; left: 0; right: 0; display: flex; align-items: center; justify-content: center; background-color: #212529; padding: 5px; height: 100%;">
                    <div style="display: flex; align-items: center;">
                        <img src="http://localhost/logo.png" alt="Logo"
                            style="width: 50px; height: auto; margin-right: 10px;">
                        <div style="display: flex; flex-direction: column;">
                            <h1 style="font-size: 30px; margin: 0; color: white; line-height: 1;">GUARDIÃO MÓVEL</h1>
                            <span style="font-size: 15px; margin: 0; color: white;">Sistema de Registro de Celulares
                                Roubados e Furtados</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content" id="content"></div>>
        </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/pt.js"></script>
<script>
    console.log("Script principal carregado");
    document.addEventListener('DOMContentLoaded', function () {
        console.log("DOM carregado");
        function loadContent(page) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', page, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById('content').innerHTML = xhr.responseText;
                    if (page === 'adicionar-celular.php') {
                        initializeAdicionarCelular();
                    }
                    if (page === 'buscar-celular.php') {
                        initializeBuscarCelular();
                    }
                    window.scrollTo(0, 0);
                }
            };
            xhr.send();
        }

        window.loadContent = loadContent;

        loadContent('home.php');

        document.body.addEventListener('click', function (e) {
            if (e.target && e.target.getAttribute('onclick') && e.target.getAttribute('onclick').startsWith('loadContent')) {
                e.preventDefault();
                var page = e.target.getAttribute('onclick').match(/'([^']+)'/)[1];
                loadContent(page);
            }
        });

        window.initializeAdicionarCelular = function () {
            console.log("Inicializando Adicionar Celular");
            $('#numero_celular').mask('(00)000000000');

            const coresEmPortugues = {
                'vermelho': 'red',
                'azul': 'blue',
                'verde': 'green',
                'amarelo': 'yellow',
                'preto': 'black',
                'branco': 'white',
                'cinza': 'gray',
                'roxo': 'purple',
                'laranja': 'orange',
                'marrom': 'brown'
            };

            $('#cor').on('input', function () {
                const corDigitada = $(this).val().toLowerCase();
                const corEmIngles = coresEmPortugues[corDigitada] || corDigitada;
                $('#cor-preview').css('background-color', corEmIngles);
            });

            initializeDateTimePickers();

            const adicionarForm = document.getElementById('adicionarCelularForm');
            const adicionarBtn = document.getElementById('adicionarBtn');
            const mensagemDiv = document.getElementById('mensagem');

            if (adicionarBtn) {
                adicionarBtn.addEventListener('click', adicionarCelular);
            }

            if (adicionarForm) {
                adicionarForm.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        adicionarCelular();
                    }
                });
            }
        }

        window.initializeBuscarCelular = function () {
            console.log("Inicializando Buscar Celular");
            initializeDateTimePickers();

            const buscarForm = document.getElementById('buscarForm');
            const buscarBtn = document.getElementById('buscarBtn');
            const buscarInput = document.getElementById('busca');
            const resultadoBusca = document.getElementById('resultadoBusca');

            if (buscarBtn) {
                buscarBtn.addEventListener('click', realizarBusca);
            }

            if (buscarInput) {
                buscarInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        realizarBusca();
                    }
                });
            }
        }

        window.initializeDateTimePickers = function () {
            flatpickr("#data_roubo", {
                dateFormat: "d/m/Y",
                allowInput: true,
                locale: "pt",
            });

            flatpickr("#hora_roubo", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 1,
                allowInput: true,
                locale: "pt"
            });

            if (document.getElementById('calendar-icon')) {
                document.getElementById('calendar-icon').addEventListener('click', function () {
                    document.getElementById('data_roubo')._flatpickr.open();
                });
            }
        }

        function formatDate(input) {
            var value = input.value.replace(/\D/g, '').substring(0, 8);
            var formatted = '';
            if (value.length > 0) formatted += value.substring(0, 2);
            if (value.length > 2) formatted += '/' + value.substring(2, 4);
            if (value.length > 4) formatted += '/' + value.substring(4, 8);
            input.value = formatted;
        }

        function formatarDataRoubo(dia, mes, ano, hora) {
            return `${ano}-${mes.padStart(2, '0')}-${dia.padStart(2, '0')} ${hora}:00`;
        }

        $(document).on('input', '#data_roubo', function () {
            formatDate(this);
        });

        $(document).on('input', '#data_roubo_dia, #data_roubo_mes, #data_roubo_ano, #data_roubo_hora', function () {
            var dia = $('#data_roubo_dia').val();
            var mes = $('#data_roubo_mes').val();
            var ano = $('#data_roubo_ano').val();
            var hora = $('#data_roubo_hora').val();
            $('#data_roubo').val(formatarDataRoubo(dia, mes, ano, hora));
        });

        function adicionarCelular() {
            const formData = new FormData(document.getElementById('adicionarCelularForm'));

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
                    document.getElementById('mensagem').innerHTML = data;
                    if (data.includes("sucesso")) {
                        document.getElementById('adicionarCelularForm').reset();
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    document.getElementById('mensagem').innerHTML = "Erro ao adicionar celular.";
                });
        }

        function realizarBusca() {
            const busca = document.getElementById('busca').value;
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
                    document.getElementById('resultadoBusca').innerHTML = data;
                })
                .catch(error => {
                    console.error('Erro:', error);
                });
        }
    });
</script>
<script>document.addEventListener('click', function (e) {
        if (e.target && e.target.id === 'buscarBtn') {
            e.preventDefault();
            console.log("Botão de busca clicado");
            const busca = document.getElementById('busca').value;
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
                    document.getElementById('resultadoBusca').innerHTML = data;
                })
                .catch(error => {
                    console.error('Erro:', error);
                });
        }
    });
</script>

</body>
</html>