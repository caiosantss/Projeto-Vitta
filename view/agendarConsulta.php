<?php
session_start();
include('../model/conexao.php');

if (!isset($_SESSION['id'])) {
    header("Location: loginpage.php");
    exit();
}

$medico_id = $_GET['medico'] ?? 0;
$paciente_id = $_SESSION['id'];

// Buscar dados do médico
$sql_medico = "SELECT m.*, u.nome as nome_medico, e.nome as especialidade_nome 
               FROM medicos m 
               JOIN usuarios u ON m.usuario_id = u.id 
               JOIN especialidades e ON m.especialidade_id = e.id 
               WHERE m.id = ?";
$stmt = $mysqli->prepare($sql_medico);
$stmt->bind_param("i", $medico_id);
$stmt->execute();
$medico = $stmt->get_result()->fetch_assoc();

if (!$medico) {
    header("Location: escolhas.php");
    exit();
}

// Processar agendamento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agendar'])) {
    $horario_id = $_POST['horario_id'];
    
    // Verificar se o horário ainda está disponível
    $sql_check = "SELECT data_hora FROM horarios_disponiveis 
                  WHERE id = ? AND medico_id = ? AND disponivel = 1";
    $stmt = $mysqli->prepare($sql_check);
    $stmt->bind_param("ii", $horario_id, $medico_id);
    $stmt->execute();
    $horario = $stmt->get_result()->fetch_assoc();
    
    if ($horario) {
        // Iniciar transação
        $mysqli->begin_transaction();
        
        try {
            // Inserir consulta
            $sql_consulta = "INSERT INTO consultas (paciente_id, medico_id, data_hora, status) 
                            VALUES (?, ?, ?, 'agendada')";
            $stmt = $mysqli->prepare($sql_consulta);
            $stmt->bind_param("iis", $paciente_id, $medico_id, $horario['data_hora']);
            $stmt->execute();
            
            // Marcar horário como ocupado
            $sql_update = "UPDATE horarios_disponiveis SET disponivel = 0 WHERE id = ?";
            $stmt = $mysqli->prepare($sql_update);
            $stmt->bind_param("i", $horario_id);
            $stmt->execute();
            
            $mysqli->commit();
            $_SESSION['sucesso_agendamento'] = "Consulta agendada com sucesso!";
            header("Location: minhasConsultas.php");
            exit();
            
        } catch (Exception $e) {
            $mysqli->rollback();
            $erro = "Erro ao agendar consulta. Tente novamente.";
        }
    } else {
        $erro = "Horário não disponível. Escolha outro horário.";
    }
}

// Buscar horários disponíveis (próximos 30 dias)
$data_limite = date('Y-m-d', strtotime('+30 days'));
$sql_horarios = "SELECT * FROM horarios_disponiveis 
                 WHERE medico_id = ? AND disponivel = 1 
                 AND DATE(data_hora) >= CURDATE() AND DATE(data_hora) <= ?
                 ORDER BY data_hora";
$stmt = $mysqli->prepare($sql_horarios);
$stmt->bind_param("is", $medico_id, $data_limite);
$stmt->execute();
$horarios = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VITTA - Agendar Consulta</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../MainStyles.css">
    <link rel="stylesheet" href="assets/css/agendarConsulta.css">
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
                    <li><a href="escolhas.php">Especialidades</a></li>
                    <li><a href="minhasConsultas.php">Minhas Consultas</a></li>
                    <li><a href="logout.php" class="button">Sair</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container main-content">
        <div class="page-header">
            <a href="listarMedicos.php?especialidade=<?php echo $medico['especialidade_id']; ?>" class="back-btn">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <h1>Agendar Consulta</h1>
        </div>

        <?php if (isset($erro)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo $erro; ?>
            </div>
        <?php endif; ?>

        <div class="content-grid">
            <!-- Informações do Médico -->
            <div class="medico-info-card">
                <div class="medico-header">
                    <div class="medico-avatar">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="medico-details">
                        <h2>Dr(a). <?php echo htmlspecialchars($medico['nome_medico']); ?></h2>
                        <p class="especialidade"><?php echo htmlspecialchars($medico['especialidade_nome']); ?></p>
                        <p class="crm">CRM: <?php echo htmlspecialchars($medico['crm']); ?></p>
                    </div>
                </div>
                <div class="consulta-info">
                    <div class="preco">
                        <span class="label">Valor da consulta:</span>
                        <span class="valor">R$ <?php echo number_format($medico['valor_consulta'], 2, ',', '.'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Horários Disponíveis -->
            <div class="horarios-card">
                <h3><i class="fas fa-calendar-alt"></i> Escolha um horário</h3>
                
                <?php if ($horarios->num_rows > 0): ?>
                    <form method="POST" class="horarios-form">
                        <div class="horarios-grid">
                            <?php 
                            $data_atual = '';
                            while ($horario = $horarios->fetch_assoc()): 
                                $data_horario = date('Y-m-d', strtotime($horario['data_hora']));
                                
                                if ($data_atual != $data_horario) {
                                    if ($data_atual != '') echo '</div></div>';
                                    echo '<div class="data-group">';
                                    echo '<h4 class="data-titulo">' . date('d/m/Y - l', strtotime($horario['data_hora'])) . '</h4>';
                                    echo '<div class="horarios-dia">';
                                    $data_atual = $data_horario;
                                }
                            ?>
                                <label class="horario-option">
                                    <input type="radio" name="horario_id" value="<?php echo $horario['id']; ?>" required>
                                    <span class="horario-btn">
                                        <?php echo date('H:i', strtotime($horario['data_hora'])); ?>
                                    </span>
                                </label>
                            <?php 
                            endwhile; 
                            if ($data_atual != '') echo '</div></div>';
                            ?>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="agendar" class="btn-agendar">
                                <i class="fas fa-check"></i>
                                Confirmar Agendamento
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="no-horarios">
                        <i class="fas fa-calendar-times"></i>
                        <h4>Nenhum horário disponível</h4>
                        <p>Este médico não possui horários disponíveis no momento.</p>
                        <a href="listarMedicos.php?especialidade=<?php echo $medico['especialidade_id']; ?>" class="btn-voltar">
                            Escolher outro médico
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
