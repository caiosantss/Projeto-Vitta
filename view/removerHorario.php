<?php
session_start();
include('../model/conexao.php');

header('Content-Type: application/json');

// Verifica se é médico logado
if (!isset($_SESSION['id']) || $_SESSION['tipo_usuario'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['horario_id'])) {
    $horario_id = (int)$_POST['horario_id'];
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
    
    // Verifica se o horário pertence ao médico e está disponível
    $sql_check = "SELECT id FROM horarios_disponiveis 
                  WHERE id = ? AND medico_id = ? AND disponivel = 1";
    $stmt = $mysqli->prepare($sql_check);
    $stmt->bind_param("ii", $horario_id, $medico['id']);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Horário não encontrado ou já ocupado']);
        exit();
    }
    
    // Remove o horário
    $sql_delete = "DELETE FROM horarios_disponiveis WHERE id = ?";
    $stmt = $mysqli->prepare($sql_delete);
    $stmt->bind_param("i", $horario_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Horário removido com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao remover horário']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
}

$mysqli->close();
?>
