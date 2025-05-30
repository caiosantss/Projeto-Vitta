<?php
session_start();
include('../model/conexao.php');

if (!isset($_SESSION['id'])) {
    header("Location: loginpage.php");
    exit();
}

$especialidade_id = $_GET['especialidade'] ?? 1;

// Buscar especialidade
$sql_especialidade = "SELECT nome FROM especialidades WHERE id = ?";
$stmt = $mysqli->prepare($sql_especialidade);
$stmt->bind_param("i", $especialidade_id);
$stmt->execute();
$especialidade = $stmt->get_result()->fetch_assoc();

if (!$especialidade) {
    header("Location: escolhas.php");
    exit();
}

// Buscar médicos da especialidade
$sql_medicos = "SELECT m.*, u.nome as nome_medico, e.nome as especialidade_nome 
                FROM medicos m 
                JOIN usuarios u ON m.usuario_id = u.id 
                JOIN especialidades e ON m.especialidade_id = e.id 
                WHERE m.especialidade_id = ?";
$stmt = $mysqli->prepare($sql_medicos);
$stmt->bind_param("i", $especialidade_id);
$stmt->execute();
$medicos = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VITTA - <?php echo htmlspecialchars($especialidade['nome']); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../MainStyles.css">
    <link rel="stylesheet" href="assets/css/listarMedicos.css">
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
            <a href="escolhas.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <h1><?php echo htmlspecialchars($especialidade['nome']); ?></h1>
            <p>Escolha um profissional para agendar sua consulta</p>
        </div>

        <div class="medicos-grid">
            <?php if ($medicos->num_rows > 0): ?>
                <?php while ($medico = $medicos->fetch_assoc()): ?>
                    <div class="medico-card">
                        <div class="medico-info">
                            <div class="medico-avatar">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <div class="medico-details">
                                <h3>Dr(a). <?php echo htmlspecialchars($medico['nome_medico']); ?></h3>
                                <p class="especialidade"><?php echo htmlspecialchars($medico['especialidade_nome']); ?></p>
                                <p class="crm">CRM: <?php echo htmlspecialchars($medico['crm']); ?></p>
                                <div class="preco">
                                    <span class="valor">R$ <?php echo number_format($medico['valor_consulta'], 2, ',', '.'); ?></span>
                                    <small>por consulta</small>
                                </div>
                            </div>
                        </div>
                        <div class="medico-actions">
                            <a href="agendarConsulta.php?medico=<?php echo $medico['id']; ?>" class="btn-agendar">
                                <i class="fas fa-calendar-plus"></i>
                                Agendar Consulta
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-medicos">
                    <i class="fas fa-user-md"></i>
                    <h3>Nenhum médico encontrado</h3>
                    <p>Não há médicos disponíveis para esta especialidade no momento.</p>
                    <a href="escolhas.php" class="btn-voltar">Escolher outra especialidade</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
