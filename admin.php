<?php
session_start();
require_once 'db_connection.php';
require_once __DIR__ . '/functions.php';

if (!function_exists('listarCelularesRoubados')) {
    die("A função listarCelularesRoubados não foi definida. Verifique o arquivo functions.php.");
}

$conn = getConnection();
if (!$conn) {
    die("Falha na conexão com o banco de dados.");
}

if (!isset($_SESSION['id']) || !isAdmin($conn, $_SESSION['id'])) {
    header('Location: login.html');
    exit;
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $dados = [
            'imei' => $_POST['imei'],
            'numero' => $_POST['numero'],
            'marca' => $_POST['marca'],
            'cor' => $_POST['cor'],
            'proprietario' => $_POST['proprietario'],
            'email_contato' => $_POST['email_contato'],
            'telefone_contato' => $_POST['telefone_contato']
        ];
        editarCelularRoubado($conn, $id, $dados);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        deletarCelularRoubado($conn, $id);
    } elseif (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $email = $_POST['email'];
        $name = $_POST['name'];
        $is_admin = isset($_POST['is_admin']) ? 1 : 0;

        // Primeiro, verifique se o usuário já existe
        $check_stmt = $conn->prepare("SELECT id FROM usuarios WHERE username = ?");
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            // Usuário já existe, exiba uma mensagem de erro
            $error_message = "Erro: O nome de usuário já existe.";
        } else {
            // O usuário não existe, podemos inserir
            $insert_stmt = $conn->prepare("INSERT INTO usuarios (username, password, email, is_admin, name) VALUES (?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("sssis", $username, $password, $email, $is_admin, $name);

            if ($insert_stmt->execute()) {
                $success_message = "Usuário adicionado com sucesso!";
            } else {
                $error_message = "Erro ao adicionar usuário: " . $insert_stmt->error;
            }
            $insert_stmt->close();
        }
        $check_stmt->close();
    }
}

$celulares = listarCelularesRoubados($conn);

$stmt = $conn->prepare('SELECT password, email, username FROM usuarios WHERE id = ?');
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($password, $email, $username);
$stmt->fetch();
$_SESSION['username'] = $username;
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração - Celulares Roubados</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #121212;
            color: #fff;
            zoom: 90%
        }

        .container {
            display: flex;
        }

        .content {
            flex: 1;
            padding: 20px;
        }

        h1,
        h2 {
            color: #00bcd4;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #1e1e1e;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        th {
            background-color: #2c2c2c;
            font-weight: bold;
        }

        tr:hover {
            background-color: #2c2c2c;
        }

        a {
            color: #00bcd4;
            text-decoration: none;
            transition: 0.3s;
        }

        a:hover {
            color: #008ba3;
        }

        form {
            background-color: #444;
            padding: 30px;
            margin-bottom: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            background-color: #2c2c2c;
            border: 1px solid #444;
            color: #fff;
            font-family: 'Roboto', sans-serif;
        }

        input[type="submit"] {
            background-color: #00bcd4;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            transition: 0.3s;
            font-family: 'Roboto', sans-serif;
        }

        input[type="submit"]:hover {
            background-color: #008ba3;
            font-family: 'Roboto', sans-serif;
        }

        .delete-button {
            color: #ff0000;
            margin-left: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="sidebar">
            <h2>Menu</h2>
            <ul>
                <li><a href="#inicio">Início</a></li>
                <li><a href="#adicionar">Adicionar Celular</a></li>
                <li><a href="#celulares.php">Todos os Celulares Cadastrados</a></li>
                <li><a href="#buscar">Buscar Celulares</a></li>
                <li><a href="?logout=1">Logout</a></li>
            </ul>
        </div>
        <div class="content">
            <h1>Administração</h1>
            <div id="adicionar_usuario" style="max-width: 800px; margin: 0 auto;">
                <h2>Adicionar Novo Usuário</h2>
                <form method="post" action=""
                    style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; alling-items: left">
                    <input type="text" name="username" placeholder="Matrícula" required style="width: 100%;"><br>
                    <input type="password" name="password" placeholder="Senha" required style="width: 100%;"><br>
                    <input type="email" name="email" placeholder="Email" required style="width: 100%;"><br>
                    <input type="text" name="name" placeholder="Nome" required style="width: 100%;">
                    <label style="grid-column: 1 / -1;">
                        <label style="vertical-align: middle; width: 100%;">
                            <input type="checkbox" name="is_admin"
                                style="width: 20px; height: 20px; background-color: red;">
                            <span style="font-size: 20px;">É administrador?</span>
                        </label>
                    </label>
                    <input type="submit" name="add_user" value="Adicionar Usuário"
                        style="grid-column: 1 / -1; width: 200px; margin: 10px auto;">
                </form>
                <?php
                if (isset($error_message)) {
                    echo "<p style='color: red; text-align: center;'>$error_message</p>";
                } elseif (isset($success_message)) {
                    echo "<p style='color: green; text-align: center;'>$success_message</p>";
                }
                ?>
            </div>
            <div id="celulares">
                <h2>Celulares Roubados</h2>
                <table>
                    <tr>
                        <th>IMEI</th>
                        <th>Número</th>
                        <th>Marca</th>
                        <th>Cor</th>
                        <th>proprietario</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                    <?php foreach ($celulares as $celular_encontrado): ?>

                        <tr data-id="<?php echo $celular_encontrado['id']; ?>">
                            <td><?php echo htmlspecialchars($celular_encontrado['imei']); ?></td>
                            <td><?php echo preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', htmlspecialchars($celular_encontrado['numero'])); ?></td>
                            <td><?php echo htmlspecialchars($celular_encontrado['marca']); ?></td>
                            <td><?php echo htmlspecialchars($celular_encontrado['cor']); ?></td>
                            <td><?php echo htmlspecialchars($celular_encontrado['proprietario']); ?></td>
                            <td><?php echo htmlspecialchars($celular_encontrado['email_contato']); ?></td>
                            <td><?php echo htmlspecialchars($celular_encontrado['telefone_contato']); ?></td>
                            <td><?php echo date('d/m/Y - H:i', strtotime($celular_encontrado['data_roubo'])); ?></td>
                            <td>
                                <a href="#" class="edit-button"
                                    data-id="<?php echo $celular_encontrado['id']; ?>">Editar</a>
                                <a href="#" class="delete-button" data-id="<?php echo $celular_encontrado['id']; ?>">🗑️</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
    </div>
    </div>

    <script>
        function editarCelularRoubado(id) {
            const row = document.querySelector(`tr[data-id="${id}"]`);
            const cells = row.querySelectorAll('td');

            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="edit" value="1">
                <td><input type="text" name="imei" value="${cells[0].textContent}" required></td>
                <td><input type="text" name="numero" value="${cells[1].textContent}" required></td>
                <td><input type="text" name="marca" value="${cells[2].textContent}" required></td>
                <td><input type="text" name="cor" value="${cells[3].textContent}" required></td>
                <td><input type="text" name="proprietario" value="${cells[4].textContent}" required></td>
                <td><input type="email" name="email_contato" value="${cells[5].textContent}" required></td>
                <td><input type="text" name="telefone_contato" value="${cells[6].textContent}" required></td>
                <td>
                    <input type="submit" value="Salvar">
                    <a href="#" class="cancel-edit">Cancelar</a>
                </td>
            `;

            row.innerHTML = '';
            const newCell = row.insertCell();
            newCell.colSpan = 8;
            newCell.appendChild(form);

            const cancelButton = form.querySelector('.cancel-edit');
            cancelButton.addEventListener('click', (e) => {
                e.preventDefault();
                location.reload();
            });
        }

        function deletarCelularRoubado(id) {
            if (confirm('Tem certeza que deseja excluir este celular?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="id" value="${id}">
                    <input type="hidden" name="delete" value="1">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('.edit-button');
            const deleteButtons = document.querySelectorAll('.delete-button');

            editButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    editarCelularRoubado(id);
                });
            });

            deleteButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    deletarCelularRoubado(id);
                });
            });
        });
    </script>
</body>

</html>