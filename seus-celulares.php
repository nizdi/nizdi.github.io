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
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];
$message = '';

// Listar celulares do usuário
$celulares_usuario = listarCelularesDoUsuario($conn, $user_id);

// Lógica para apagar celular (se necessário)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apagar'])) {
    $id = $_POST['id'];
    if (apagarCelularRoubado($conn, $id, $user_id)) {
        $message = "Celular removido com sucesso.";
        // Atualizar a lista após a remoção
        $celulares_usuario = listarCelularesDoUsuario($conn, $user_id);
    } else {
        $message = "Erro ao remover celular.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<h1>Seus Celulares Registrados</h1>

<?php if ($message): ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

<?php if (!empty($celulares_usuario)): ?>
    <table>
        <tr>
            <th>IMEI</th>
            <th>Número</th>
            <th>Data do Roubo</th>
            <th>Modelo</th>
            <th>Cor</th>
            <th>Dono</th>
            <th>Email</th>
            <th>Telefone</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($celulares_usuario as $celular): ?>
            <tr>
                <td><?php echo htmlspecialchars($celular['imei']); ?></td>
                <td><?php echo htmlspecialchars($celular['numero']); ?></td>
                <td><?php echo htmlspecialchars($celular['data_roubo']); ?></td>
                <td><?php echo htmlspecialchars($celular['marca']); ?></td>
                <td><?php echo htmlspecialchars($celular['cor']); ?></td>
                <td><?php echo htmlspecialchars($celular['proprietario']); ?></td>
                <td><?php echo htmlspecialchars($celular['email_contato']); ?></td>
                <td><?php echo htmlspecialchars($celular['telefone_contato']); ?></td>
                <td>
                    <form method="POST" onsubmit="return confirm('Tem certeza que deseja apagar este registro?');">
                        <input type="hidden" name="id" value="<?php echo $celular['id']; ?>">
                        <input type="submit" name="apagar" value="Apagar">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Você ainda não registrou nenhum celular roubado.</p>
<?php endif; ?>
</div>
</div>
</body>

</html>