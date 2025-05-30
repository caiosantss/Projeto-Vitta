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

// Processar adição de horários
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['adicionar_horarios'])) {
    $data = $_POST['data'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fim = $_POST['hora_fim'];
    $intervalo = (int)$_POST['intervalo'];
    
    // Gerar horários baseado no intervalo
    $inicio = new DateTime($data . ' ' . $hora_inicio);
    $fim = new DateTime($data . ' ' . $hora_fim);
    
    $horarios_inseridos = 0;
    
    while ($inicio < $fim) {
        $data_hora = $inicio->format('Y-m-d H:i:s');
        
        // Verificar se o horário já existe
        $sql_check = "SELECT id FROM horarios_disponiveis WHERE medico_id = ? AND data_hora = ?";
        $stmt_check = $mysqli->prepare($sql_check);
        $stmt_check->bind_param("is", $medico['id'], $data_hora);
        $stmt_check->execute();
        
        if ($stmt_check->get_result()->num_rows == 0) {
            // Inserir novo horário
            $sql_insert = "INSERT INTO horarios_disponiveis (medico_id, data_hora, disponivel) VALUES (?, ?, 1)";
            $stmt_insert = $mysqli->prepare($sql_insert);
            $stmt_insert->bind_param("is", $medico['id'], $data_hora);
            $stmt_insert->execute();
            $horarios_inseridos++;
        }
        
        $inicio->add(new DateInterval('PT' . $intervalo . 'M'));
    }
    
    $mensagem = "✅ $horarios_inseridos horários adicionados com sucesso!";
}

// Buscar horários existentes (próximos 30 dias)
$data_limite = date('Y-m-d', strtotime('+30 days'));
$sql_horarios = "SELECT * FROM horarios_disponiveis 
                 WHERE medico_id = ? AND DATE(data_hora) >= CURDATE() AND DATE(data_hora) <= ?
                 ORDER BY data_hora";
$stmt = $mysqli->prepare($sql_horarios);
$stmt->bind_param("is", $medico['id'], $data_limite);
$stmt->execute();
$horarios_existentes = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VITTA - Gerenciar Horários</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/gerenciarHorarios.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="../main.html"><img src="assets/img/vitta-logo.png" alt="VITTA Logo"></a>
        </div>
        <nav>
            <ul>
                <li><a href="painelMedico.php">Dashboard</a></li>
                <li><a href="minhasConsultasMedico.php">Consultas</a></li>
                <li><a href="gerenciarHorarios.php" class="active">Horários</a></li>
            </ul>
        </nav>
        <a href="logout.php" class="button">Sair</a>
    </header>

    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-clock"></i> Gerenciar Horários</h1>
            <p>Dr(a). <?php echo htmlspecialchars($medico['usuario_nome']); ?></p>
        </div>

        <?php if (isset($mensagem)): ?>
            <div class="alert alert-success">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <div class="content-grid">
            <!-- Formulário para adicionar horários -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-plus"></i> Adicionar Novos Horários</h2>
                </div>
                <div class="card-content">
                    <form method="POST" class="horario-form">
                        <div class="form-group">
                            <label for="data">Data:</label>
                            <input type="date" id="data" name="data" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="hora_inicio">Hora Início:</label>
                                <input type="time" id="hora_inicio" name="hora_inicio" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="hora_fim">Hora Fim:</label>
                                <input type="time" id="hora_fim" name="hora_fim" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="intervalo">Intervalo entre consultas (minutos):</label>
                            <select id="intervalo" name="intervalo" required>
                                <option value="30">30 minutos</option>
                                <option value="45">45 minutos</option>
                                <option value="60">1 hora</option>
                                <option value="90">1h 30min</option>
                            </select>
                        </div>
                        
                        <button type="submit" name="adicionar_horarios" class="btn-primary">
                            <i class="fas fa-plus"></i> Adicionar Horários
                        </button>
                    </form>
                </div>
            </div>

            <!-- Lista de horários existentes -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-calendar"></i> Horários Disponíveis</h2>
                </div>
                <div class="card-content">
                    <?php if ($horarios_existentes->num_rows > 0): ?>
                        <div class="horarios-grid">
                            <?php 
                            $data_atual = '';
                            while ($horario = $horarios_existentes->fetch_assoc()): 
                                $data_horario = date('Y-m-d', strtotime($horario['data_hora']));
                                
                                if ($data_atual != $data_horario) {
                                    if ($data_atual != '') echo '</div>';
                                    echo '<div class="data-group">';
                                    echo '<h3 class="data-titulo">' . date('d/m/Y - l', strtotime($horario['data_hora'])) . '</h3>';
                                    echo '<div class="horarios-dia">';
                                    $data_atual = $data_horario;
                                }
                            ?>
                                <div class="horario-item <?php echo $horario['disponivel'] ? 'disponivel' : 'ocupado'; ?>">
                                    <span class="hora"><?php echo date('H:i', strtotime($horario['data_hora'])); ?></span>
                                    <span class="status">
                                        <?php echo $horario['disponivel'] ? 'Disponível' : 'Ocupado'; ?>
                                    </span>
                                    <?php if ($horario['disponivel']): ?>
                                        <button class="btn-remove" onclick="removerHorario(<?php echo $horario['id']; ?>)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php 
                            endwhile; 
                            if ($data_atual != '') echo '</div></div>';
                            ?>
                        </div>
                    <?php else: ?>
                        <p class="no-data">Nenhum horário cadastrado ainda.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function removerHorario(id) {
            if (confirm('Tem certeza que deseja remover este horário?')) {
                fetch('removerHorario.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'horario_id=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erro ao remover horário: ' + data.message);
                    }
                });
            }
        }
    </script>
</body>
</html>
