<?php
session_start();
include('../model/conexao.php');

// Verifica se é médico logado
if (!isset($_SESSION['id']) || $_SESSION['tipo_usuario'] != 1) {
    header("Location: loginpage.php");
    exit();
}

$usuario_id = $_SESSION['id'];

// Busca dados do médico
$sql_medico = "SELECT m.*, u.nome as usuario_nome FROM medicos m 
               JOIN usuarios u ON m.usuario_id = u.id 
               WHERE m.usuario_id = ?";
$stmt = $mysqli->prepare($sql_medico);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$medico = $stmt->get_result()->fetch_assoc();

if (!$medico) {
    echo "Erro: Dados do médico não encontrados.";
    exit();
}

// Filtros
$filtro_status = $_GET['status'] ?? 'todas';
$filtro_data = $_GET['data'] ?? '';

// Construir query com filtros
$where_conditions = ["c.medico_id = ?"];
$params = [$medico['id']];
$param_types = "i";

if ($filtro_status != 'todas') {
    $where_conditions[] = "c.status = ?";
    $params[] = $filtro_status;
    $param_types .= "s";
}

if ($filtro_data) {
    $where_conditions[] = "DATE(c.data_hora) = ?";
    $params[] = $filtro_data;
    $param_types .= "s";
}

$where_clause = implode(" AND ", $where_conditions);

// Buscar consultas do médico
$sql_consultas = "SELECT c.*, u.nome as paciente_nome, u.email as paciente_email, u.cpf as paciente_cpf
                  FROM consultas c 
                  JOIN usuarios u ON c.paciente_id = u.id 
                  WHERE $where_clause
                  ORDER BY c.data_hora DESC";
$stmt = $mysqli->prepare($sql_consultas);
$stmt->bind_param($param_types, ...$params);
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
    <link rel="stylesheet" href="assets/css/painelMedico.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="../main.html"><img src="assets/img/vitta-logo.png" alt="VITTA Logo"></a>
        </div>
        <nav>
            <ul>
                <li><a href="painelMedico.php">Dashboard</a></li>
                <li><a href="gerenciarHorarios.php">Horários</a></li>
                <li><a href="minhasConsultasMedico.php" class="active">Consultas</a></li>
            </ul>
        </nav>
        <a href="logout.php" class="button">Sair</a>
    </header>

    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-clipboard-list"></i> Minhas Consultas</h1>
            <p>Dr(a). <?php echo htmlspecialchars($medico['usuario_nome']); ?></p>
        </div>

        <!-- Filtros -->
        <div class="filtros-container">
            <form method="GET" class="filtros-form">
                <div class="filtro-group">
                    <label for="status">Status:</label>
                    <select name="status" id="status">
                        <option value="todas" <?php echo $filtro_status == 'todas' ? 'selected' : ''; ?>>Todas</option>
                        <option value="agendada" <?php echo $filtro_status == 'agendada' ? 'selected' : ''; ?>>Agendadas</option>
                        <option value="confirmada" <?php echo $filtro_status == 'confirmada' ? 'selected' : ''; ?>>Confirmadas</option>
                        <option value="realizada" <?php echo $filtro_status == 'realizada' ? 'selected' : ''; ?>>Realizadas</option>
                        <option value="cancelada" <?php echo $filtro_status == 'cancelada' ? 'selected' : ''; ?>>Canceladas</option>
                    </select>
                </div>
                
                <div class="filtro-group">
                    <label for="data">Data:</label>
                    <input type="date" name="data" id="data" value="<?php echo htmlspecialchars($filtro_data); ?>">
                </div>
                
                <button type="submit" class="btn-filtrar">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                
                <a href="minhasConsultasMedico.php" class="btn-limpar">
                    <i class="fas fa-times"></i> Limpar
                </a>
            </form>
        </div>

        <!-- Lista de Consultas -->
        <div class="consultas-container">
            <?php if ($consultas->num_rows > 0): ?>
                <div class="consultas-list">
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
                                <div class="paciente-info">
                                    <div class="paciente-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="paciente-details">
                                        <h3><?php echo htmlspecialchars($consulta['paciente_nome']); ?></h3>
                                        <p class="email"><?php echo htmlspecialchars($consulta['paciente_email']); ?></p>
                                        <p class="cpf">CPF: <?php echo htmlspecialchars($consulta['paciente_cpf']); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($consulta['observacoes']): ?>
                                <div class="consulta-observacoes">
                                    <strong>Observações:</strong>
                                    <p><?php echo htmlspecialchars($consulta['observacoes']); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <div class="consulta-actions">
                                <?php if ($consulta['status'] == 'agendada'): ?>
                                    <button class="btn-confirmar" onclick="confirmarConsulta(<?php echo $consulta['id']; ?>)">
                                        <i class="fas fa-check"></i> Confirmar
                                    </button>
                                <?php endif; ?>
                                
                                <?php if ($consulta['status'] == 'confirmada'): ?>
                                    <button class="btn-realizar" onclick="realizarConsulta(<?php echo $consulta['id']; ?>)">
                                        <i class="fas fa-check-circle"></i> Marcar como Realizada
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
                    <p>Não há consultas com os filtros selecionados.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function confirmarConsulta(consultaId) {
            if (confirm('Confirmar esta consulta?')) {
                fetch('atualizarStatusConsulta.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'consulta_id=' + consultaId + '&status=confirmada'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erro: ' + data.message);
                    }
                });
            }
        }

        function realizarConsulta(consultaId) {
            if (confirm('Marcar esta consulta como realizada?')) {
                fetch('atualizarStatusConsulta.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'consulta_id=' + consultaId + '&status=realizada'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erro: ' + data.message);
                    }
                });
            }
        }
    </script>
</body>
</html>
