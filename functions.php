<?php
function isAdmin($conn, $user_id)
{
    return true; // Temporariamente retorna sempre true para teste
}

function inserirCelularRoubado($conn, $dados, $user_id)
{
    $sql = "INSERT INTO celulares_roubados (imei, numero, data_roubo, marca, cor, proprietario, email_contato, telefone_contato, id_usuario) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", $dados['imei'], $dados['numero'], $dados['data_roubo'], $dados['marca'], $dados['cor'], $dados['proprietario'], $dados['email_contato'], $dados['telefone_contato'], $user_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}


function editarCelularRoubado($conn, $id, $dados)
{
    $sql = "UPDATE celulares_roubados SET 
            imei = ?, 
            numero = ?, 
            data_roubo = ?,
            marca = ?, 
            cor = ?, 
            proprietario = ?, 
            email_contato = ?, 
            telefone_contato = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $dados['imei'], $dados['numero'], $dados['marca'], $dados['cor'], $dados['proprietario'], $dados['email_contato'], $dados['telefone_contato'], $id);
    return $stmt->execute();
}

function listarCelularesRoubados($conn)
{
    //echo "Listando celulares roubados<br>";
    $sql = "SELECT * FROM celulares_roubados ORDER BY data_registro DESC";
    $result = $conn->query($sql);

    if (!$result) {
        echo "Erro na consulta: " . $conn->error . "<br>";
        return false;
    }

    $celulares = $result->fetch_all(MYSQLI_ASSOC);
    //echo "Número de celulares encontrados: " . count($celulares) . "<br>";
    return $celulares;
}

function buscarCelularPorIMEI($conn, $imei)
{
    $sql = "SELECT * FROM celulares_roubados WHERE imei = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $imei);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function listarCelularesDoUsuario($conn, $user_id)
{
    $sql = "SELECT * FROM celulares_roubados WHERE id_usuario = ? ORDER BY data_registro DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function apagarCelularRoubado($conn, $id, $user_id)
{
    $sql = "DELETE FROM celulares_roubados WHERE id = ? AND id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $user_id);
    return $stmt->execute();
}

function buscarCelular($conn, $busca)
{
    $sql = "SELECT * FROM celulares_roubados WHERE imei = ? OR numero = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $busca, $busca);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}


?>