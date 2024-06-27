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
?>

<p style="margin-top: 0;">Bem-vindo de volta, <?= htmlspecialchars($_SESSION['name'], ENT_QUOTES) ?>!
        </p>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

<img src="http://localhost/logog.png" alt="Logo do PCPB" style="display: block; margin: 0 auto;"/>
