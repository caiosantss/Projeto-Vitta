<?php
session_start();
include('../model/conexao.php');

// Verifica se é médico logado
if (!isset($_SESSION['id']) || $_SESSION['tipo_usuario'] != 1) {
    header("Location: loginpage.php");
    exit();
}

// Busca dados do médico
$usuario_id = $_SESSION['id'];
$sql_medico = "SELECT m.*, e.nome as especialidade_nome, u.nome as usuario_nome 
               FROM medicos m 
               JOIN especialidades e ON m.especialidade_id = e.id 
               JOIN usuarios u ON m.usuario_id = u.id 
               WHERE m.usuario_id = ?";
$stmt = $mysqli->prepare($sql_medico);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$medico = $stmt->get_result()->fetch_assoc();

if (!$medico) {
    echo "Erro: Dados do médico não encontrados. Entre em contato com o administrador.";
    exit();
}

// Busca consultas do dia
$hoje = date('Y-m-d');
$sql_consultas_hoje = "SELECT c.*, u.nome as paciente_nome, u.email as paciente_email
                       FROM consultas c 
                       JOIN usuarios u ON c.paciente_id = u.id 
                       WHERE c.medico_id = ? AND DATE(c.data_hora) = ? 
                       ORDER BY c.data_hora";
$stmt = $mysqli->prepare($sql_consultas_hoje);
$stmt->bind_param("is", $medico['id'], $hoje);
$stmt->execute();
$consultas_hoje = $stmt->get_result();

// Busca próximas consultas (próximos 7 dias)
$proxima_semana = date('Y-m-d', strtotime('+7 days'));
$sql_proximas = "SELECT c.*, u.nome as paciente_nome 
                 FROM consultas c 
                 JOIN usuarios u ON c.paciente_id = u.id 
                 WHERE c.medico_id = ? AND DATE(c.data_hora) BETWEEN ? AND ? 
                 ORDER BY c.data_hora LIMIT 5";
$stmt = $mysqli->prepare($sql_proximas);
$stmt->bind_param("iss", $medico['id'], $hoje, $proxima_semana);
$stmt->execute();
$proximas_consultas = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VITTA - Painel Médico</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/painelMedico.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="../main.html"><img src="assets/img/vitta-logo.png" alt="VITTA Logo"></a>
        </div>
        <nav>
            <ul>
                <li><a href="../main.html">Início</a></li>
                <li><a href="gerenciarHorarios.php">Meus Horários</a></li>
                <li><a href="minhasConsultasMedico.php">Todas as Consultas</a></li>
            </ul>
        </nav>
        <a href="logout.php" class="button">Sair</a>
    </header>

    <div class="container">
        <div class="welcome-section">
            <h1>Bem-vindo, Dr(a). <?php echo htmlspecialchars($medico['usuario_nome']); ?>!</h1>
            <div class="medico-info">
                <p><strong>Especialidade:</strong> <?php echo htmlspecialchars($medico['especialidade_nome']); ?></p>
                <p><strong>CRM:</strong> <?php echo htmlspecialchars($medico['crm']); ?></p>
                <p><strong>Valor da Consulta:</strong> R$ <?php echo number_format($medico['valor_consulta'], 2, ',', '.'); ?></p>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Consultas de Hoje -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-calendar-day"></i> Consultas de Hoje</h2>
                    <span class="date"><?php echo date('d/m/Y'); ?></span>
                </div>
                <div class="card-content">
                    <?php if ($consultas_hoje->num_rows > 0): ?>
                        <div class="consultas-list">
                            <?php while ($consulta = $consultas_hoje->fetch_assoc()): ?>
                                <div class="consulta-item">
                                    <div class="consulta-time">
                                        <?php echo date('H:i', strtotime($consulta['data_hora'])); ?>
                                    </div>
                                    <div class="consulta-info">
                                        <h4><?php echo htmlspecialchars($consulta['paciente_nome']); ?></h4>
                                        <p><?php echo htmlspecialchars($consulta['paciente_email']); ?></p>
                                        <span class="status status-<?php echo $consulta['status']; ?>">
                                            <?php echo ucfirst($consulta['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-data">Nenhuma consulta agendada para hoje.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Próximas Consultas -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-calendar-week"></i> Próximas Consultas</h2>
                </div>
                <div class="card-content">
                    <?php if ($proximas_consultas->num_rows > 0): ?>
                        <div class="consultas-list">
                            <?php while ($consulta = $proximas_consultas->fetch_assoc()): ?>
                                <div class="consulta-item">
                                    <div class="consulta-date">
                                        <?php echo date('d/m', strtotime($consulta['data_hora'])); ?>
                                        <small><?php echo date('H:i', strtotime($consulta['data_hora'])); ?></small>
                                    </div>
                                    <div class="consulta-info">
                                        <h4><?php echo htmlspecialchars($consulta['paciente_nome']); ?></h4>
                                        <span class="status status-<?php echo $consulta['status']; ?>">
                                            <?php echo ucfirst($consulta['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-data">Nenhuma consulta agendada para os próximos dias.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="card actions-card">
                <div class="card-header">
                    <h2><i class="fas fa-tools"></i> Ações Rápidas</h2>
                </div>
                <div class="card-content">
                    <div class="action-buttons">
                        <a href="gerenciarHorarios.php" class="action-btn">
                            <i class="fas fa-clock"></i>
                            <span>Gerenciar Horários</span>
                        </a>
                        <a href="minhasConsultasMedico.php" class="action-btn">
                            <i class="fas fa-list"></i>
                            <span>Ver Todas as Consultas</span>
                        </a>
                        <a href="relatorioConsultas.php" class="action-btn">
                            <i class="fas fa-chart-bar"></i>
                            <span>Relatórios</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
