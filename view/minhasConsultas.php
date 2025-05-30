<?php
session_start();
include('../model/conexao.php');

if (!isset($_SESSION['id'])) {
    header("Location: loginpage.php");
    exit();
}

$paciente_id = $_SESSION['id'];

// Verificar se há mensagem de sucesso
$sucesso = $_SESSION['sucesso_agendamento'] ?? '';
unset($_SESSION['sucesso_agendamento']);

// Processar cancelamento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancelar_consulta'])) {
    $consulta_id = $_POST['consulta_id'];
    
    // Verificar se a consulta pertence ao paciente
    $sql_check = "SELECT c.*, h.id as horario_id 
                  FROM consultas c 
                  LEFT JOIN horarios_disponiveis h ON c.data_hora = h.data_hora AND c.medico_id = h.medico_id
                  WHERE c.id = ? AND c.paciente_id = ? AND c.status = 'agendada'";
    $stmt = $mysqli->prepare($sql_check);
    $stmt->bind_param("ii", $consulta_id, $paciente_id);
    $stmt->execute();
    $consulta = $stmt->get_result()->fetch_assoc();
    
    if ($consulta) {
        $mysqli->begin_transaction();
        
        try {
            // Cancelar consulta
            $sql_cancel = "UPDATE consultas SET status = 'cancelada' WHERE id = ?";
            $stmt = $mysqli->prepare($sql_cancel);
            $stmt->bind_param("i", $consulta_id);
            $stmt->execute();
            
            // Liberar horário se existir
            if ($consulta['horario_id']) {
                $sql_liberar = "UPDATE horarios_disponiveis SET disponivel = 1 WHERE id = ?";
                $stmt = $mysqli->prepare($sql_liberar);
                $stmt->bind_param("i", $consulta['horario_id']);
                $stmt->execute();
            }
            
            $mysqli->commit();
            $sucesso = "Consulta cancelada com sucesso!";
            
        } catch (Exception $e) {
            $mysqli->rollback();
            $erro = "Erro ao cancelar consulta.";
        }
    }
}

// Buscar consultas do paciente
$sql_consultas = "SELECT c.*, u.nome as medico_nome, e.nome as especialidade_nome, m.crm, m.valor_consulta
                  FROM consultas c 
                  JOIN medicos m ON c.medico_id = m.id 
                  JOIN usuarios u ON m.usuario_id = u.id 
                  JOIN especialidades e ON m.especialidade_id = e.id 
                  WHERE c.paciente_id = ? 
                  ORDER BY c.data_hora DESC";
$stmt = $mysqli->prepare($sql_consultas);
$stmt->bind_param("i", $paciente_id);
$stmt->execute();
$consultas = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VITTA - Minhas Consultas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../MainStyles.css">
    <link rel="stylesheet" href="assets/css/minhasConsultas.css">
</head>
<body>
    <header>
        <div class="container header-container">
            <div class="logo">
               <a href="../main.html"><img src="assets/img/vitta-logo.png" alt="VITTA Logo"></a> 
            </div>
            <nav>
                <ul>
                    <li><a href="painelCliente.php">Meu Painel</a></li>
                    <li><a href="escolhas.php">Agendar Consulta</a></li>
                    <li><a href="minhasConsultas.php" class="active">Minhas Consultas</a></li>
                    <li><a href="logout.php" class="button">Sair</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container main-content">
        <div class="page-header">
            <h1><i class="fas fa-clipboard-list"></i> Minhas Consultas</h1>
            <p>Gerencie suas consultas agendadas</p>
        </div>

        <?php if ($sucesso): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $sucesso; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($erro)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo $erro; ?>
            </div>
        <?php endif; ?>

        <div class="consultas-container">
            <?php if ($consultas->num_rows > 0): ?>
                <div class="consultas-grid">
                    <?php while ($consulta = $consultas->fetch_assoc()): ?>
                        <div class="consulta-card status-<?php echo $consulta['status']; ?>">
                            <div class="consulta-header">
                                <div class="data-hora">
                                    <div class="data"><?php echo date('d/m/Y', strtotime($consulta['data_hora'])); ?></div>
                                    <div class="hora"><?php echo date('H:i', strtotime($consulta['data_hora'])); ?></div>
                                </div>
                                <div class="status">
                                    <span class="status-badge status-<?php echo $consulta['status']; ?>">
                                        <?php echo ucfirst($consulta['status']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="consulta-body">
                                <div class="medico-info">
                                    <div class="medico-avatar">
                                        <i class="fas fa-user-md"></i>
                                    </div>
                                    <div class="medico-details">
                                        <h3>Dr(a). <?php echo htmlspecialchars($consulta['medico_nome']); ?></h3>
                                        <p class="especialidade"><?php echo htmlspecialchars($consulta['especialidade_nome']); ?></p>
                                        <p class="crm">CRM: <?php echo htmlspecialchars($consulta['crm']); ?></p>
                                    </div>
                                </div>
                                
                                <div class="consulta-valor">
                                    <span class="valor">R$ <?php echo number_format($consulta['valor_consulta'], 2, ',', '.'); ?></span>
                                </div>
                            </div>
                            
                            <?php if ($consulta['observacoes']): ?>
                                <div class="consulta-observacoes">
                                    <strong>Observações:</strong>
                                    <p><?php echo htmlspecialchars($consulta['observacoes']); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <div class="consulta-actions">
                                <?php if ($consulta['status'] == 'agendada' && strtotime($consulta['data_hora']) > time()): ?>
                                    <button class="btn-cancelar" onclick="cancelarConsulta(<?php echo $consulta['id']; ?>)">
                                        <i class="fas fa-times"></i>
                                        Cancelar
                                    </button>
                                <?php endif; ?>
                                
                                <?php if ($consulta['status'] == 'realizada'): ?>
                                    <button class="btn-avaliar" onclick="avaliarConsulta(<?php echo $consulta['id']; ?>)">
                                        <i class="fas fa-star"></i>
                                        Avaliar
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-consultas">
                    <i class="fas fa-calendar-times"></i>
                    <h3>Nenhuma consulta encontrada</h3>
                    <p>Você ainda não possui consultas agendadas.</p>
                    <a href="escolhas.php" class="btn-agendar">
                        <i class="fas fa-plus"></i>
                        Agendar primeira consulta
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal de Cancelamento -->
    <div id="modalCancelamento" class="modal">
        <div class="modal-content">
            <h3>Cancelar Consulta</h3>
            <p>Tem certeza que deseja cancelar esta consulta?</p>
            <form method="POST" id="formCancelamento">
                <input type="hidden" name="consulta_id" id="consultaIdCancelamento">
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="fecharModal()">Não</button>
                    <button type="submit" name="cancelar_consulta" class="btn-danger">Sim, cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function cancelarConsulta(consultaId) {
            document.getElementById('consultaIdCancelamento').value = consultaId;
            document.getElementById('modalCancelamento').style.display = 'flex';
        }

        function fecharModal() {
            document.getElementById('modalCancelamento').style.display = 'none';
        }

        function avaliarConsulta(consultaId) {
            alert('Funcionalidade de avaliação em desenvolvimento');
        }

        // Fechar modal clicando fora
        window.onclick = function(event) {
            const modal = document.getElementById('modalCancelamento');
            if (event.target == modal) {
                fecharModal();
            }
        }
    </script>
</body>
</html>
