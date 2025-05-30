<?php
session_start();
include('../model/conexao.php');

header('Content-Type: application/json');

// Verifica se é médico logado
if (!isset($_SESSION['id']) || $_SESSION['tipo_usuario'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['consulta_id']) && isset($_POST['status'])) {
    $consulta_id = (int)$_POST['consulta_id'];
    $novo_status = $_POST['status'];
    $usuario_id = $_SESSION['id'];
    
    // Busca o médico
    $sql_medico = "SELECT id FROM medicos WHERE usuario_id = ?";
    $stmt = $mysqli->prepare($sql_medico);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $medico = $stmt->get_result()->fetch_assoc();
    
    if (!$medico) {
        echo json_encode(['success' => false, 'message' => 'Médico não encontrado']);
        exit();
    }
    
    // Verifica se a consulta pertence ao médico
    $sql_check = "SELECT id FROM consultas WHERE id = ? AND medico_id = ?";
    $stmt = $mysqli->prepare($sql_check);
    $stmt->bind_param("ii", $consulta_id, $medico['id']);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Consulta não encontrada']);
        exit();
    }
    
    // Atualiza o status
    $sql_update = "UPDATE consultas SET status = ? WHERE id = ?";
    $stmt = $mysqli->prepare($sql_update);
    $stmt->bind_param("si", $novo_status, $consulta_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Status atualizado com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar status']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
}

$mysqli->close();
?>
    