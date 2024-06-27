<?php
session_start();
require_once 'db_connection.php';
require_once 'functions.php';

if (!isset($_SESSION['id'])) {
    echo "Usuário não autenticado.";
    exit;
}

$conn = getConnection();
if (!$conn) {
    echo "Falha na conexão com o banco de dados.";
    exit;
}

$user_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data_roubo = DateTime::createFromFormat('d/m/Y H:i', $_POST['data_roubo'] . ' ' . $_POST['hora_roubo']);

    $dados = [
        'imei' => $_POST['imei'],
        'numero' => preg_replace('/[^0-9]/', '', $_POST['numero_celular']),
        'data_roubo' => $data_roubo ? $data_roubo->format('Y-m-d H:i:s') : null,
        'marca' => $_POST['marca'],
        'cor' => $_POST['cor'],
        'proprietario' => $_POST['proprietario'],
        'email_contato' => $_POST['email_contato'],
        'telefone_contato' => $_POST['telefone_contato']
    ];

    if (strlen($dados['imei']) !== 15) {
        echo "O IMEI deve ter exatamente 15 dígitos.";
    } elseif (!$dados['data_roubo']) {
        echo "Data e hora do roubo inválidas.";
    } else {
        if (inserirCelularRoubado($conn, $dados, $user_id)) {
            echo "Celular adicionado com sucesso!";
        } else {
            echo "Erro ao adicionar celular.";
        }
    }
} else {
    echo "Método de requisição inválido.";
}
?>