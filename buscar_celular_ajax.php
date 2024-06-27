<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'db_connection.php';
require_once 'functions.php';
$conn = getConnection();
if (!$conn) {
    die("Falha na conexão com o banco de dados.");
}

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    die("Usuário não está logado.");
}

$celular_encontrado = null;

if (isset($_GET['busca'])) {
    $busca = preg_replace('/[^0-9]/', '', $_GET['busca']);
    $celular_encontrado = buscarCelular($conn, $busca);
}

if ($celular_encontrado): ?>
    <h3>Resultado da Busca</h3>
    <table>
        <tr>
            <th>IMEI</th>
            <th>Número</th>
            <th>Data do Roubo</th>
            <th>Marca</th>
            <th>Cor</th>
            <th>Proprietário</th>
            <th>Email</th>
            <th>Telefone</th>
        </tr>
        <tr>
            <td><?php echo htmlspecialchars($celular_encontrado['imei']); ?></td>
            <td><?php echo preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', htmlspecialchars($celular_encontrado['numero'])); ?>
            </td>
            <td><?php echo date('d/m/Y - H:i', strtotime($celular_encontrado['data_roubo'])); ?></td>
            <td><?php echo htmlspecialchars($celular_encontrado['marca']); ?></td>
            <td><?php echo htmlspecialchars($celular_encontrado['cor']); ?></td>
            <td><?php echo htmlspecialchars($celular_encontrado['proprietario']); ?></td>
            <td><?php echo htmlspecialchars($celular_encontrado['email_contato']); ?></td>
            <td><?php echo htmlspecialchars($celular_encontrado['telefone_contato']); ?></td>
        </tr>
    </table>
<?php else: ?>
    <p><strong>Nenhum celular encontrado com este IMEI ou número.</strong></p>
<?php endif; ?>
