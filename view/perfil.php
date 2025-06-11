<?php
session_start();
include('../model/conexao.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: loginpage.php");
    exit();
}

$usuario_id = $_SESSION['id'];
$mensagem = '';
$tipo_mensagem = '';

// Verificar se a tabela perfil_usuario existe, se não, criá-la
$check_table = $mysqli->query("SHOW TABLES LIKE 'perfil_usuario'");
if ($check_table->num_rows == 0) {
    // Tabela não existe, vamos criá-la
    $create_table_sql = "CREATE TABLE IF NOT EXISTS perfil_usuario (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        telefone VARCHAR(20),
        celular VARCHAR(20),
        cep VARCHAR(10),
        endereco VARCHAR(255),
        numero VARCHAR(20),
        complemento VARCHAR(100),
        bairro VARCHAR(100),
        cidade VARCHAR(100),
        estado VARCHAR(2),
        data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    )";
    
    $mysqli->query($create_table_sql);
}

// Buscar dados do usuário
$sql_usuario = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $mysqli->prepare($sql_usuario);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

// Buscar ou criar perfil do usuário
$sql_perfil = "SELECT * FROM perfil_usuario WHERE usuario_id = ?";
$stmt = $mysqli->prepare($sql_perfil);

// Verificar se a preparação foi bem-sucedida
if ($stmt === false) {
    $mensagem = "Erro ao preparar consulta: " . $mysqli->error;
    $tipo_mensagem = "erro";
} else {
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $perfil = $result->fetch_assoc();

    // Se não existir perfil, criar um
    if (!$perfil) {
        $sql_criar_perfil = "INSERT INTO perfil_usuario (usuario_id) VALUES (?)";
        $stmt = $mysqli->prepare($sql_criar_perfil);
        if ($stmt === false) {
            $mensagem = "Erro ao preparar inserção: " . $mysqli->error;
            $tipo_mensagem = "erro";
        } else {
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            
            // Buscar o perfil recém-criado
            $stmt = $mysqli->prepare($sql_perfil);
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $perfil = $stmt->get_result()->fetch_assoc();
        }
    }
}

// Processar atualização de dados pessoais
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['atualizar_dados'])) {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $celular = trim($_POST['celular']);
    
    // Verificar se o email já existe para outro usuário
    if ($email != $usuario['email']) {
        $sql_check_email = "SELECT id FROM usuarios WHERE email = ? AND id != ?";
        $stmt = $mysqli->prepare($sql_check_email);
        $stmt->bind_param("si", $email, $usuario_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $mensagem = "Este e-mail já está sendo usado por outro usuário.";
            $tipo_mensagem = "erro";
        }
    }
    
    if (empty($mensagem)) {
        // Atualizar dados do usuário
        $sql_update_usuario = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?";
        $stmt = $mysqli->prepare($sql_update_usuario);
        $stmt->bind_param("ssi", $nome, $email, $usuario_id);
        
        // Atualizar perfil
        $sql_update_perfil = "UPDATE perfil_usuario SET telefone = ?, celular = ? WHERE usuario_id = ?";
        $stmt_perfil = $mysqli->prepare($sql_update_perfil);
        $stmt_perfil->bind_param("ssi", $telefone, $celular, $usuario_id);
        
        if ($stmt->execute() && $stmt_perfil->execute()) {
            $mensagem = "Dados pessoais atualizados com sucesso!";
            $tipo_mensagem = "sucesso";
            
            // Atualizar dados da sessão
            $_SESSION['nome'] = $nome;
            $_SESSION['email'] = $email;
            
            // Recarregar dados do usuário
            $stmt = $mysqli->prepare($sql_usuario);
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $usuario = $stmt->get_result()->fetch_assoc();
            
            // Recarregar dados do perfil
            $stmt = $mysqli->prepare($sql_perfil);
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $perfil = $stmt->get_result()->fetch_assoc();
        } else {
            $mensagem = "Erro ao atualizar dados pessoais.";
            $tipo_mensagem = "erro";
        }
    }
}

// Processar atualização de endereço
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['atualizar_endereco'])) {
    $cep = trim($_POST['cep']);
    $endereco = trim($_POST['endereco']);
    $numero = trim($_POST['numero']);
    $complemento = trim($_POST['complemento']);
    $bairro = trim($_POST['bairro']);
    $cidade = trim($_POST['cidade']);
    $estado = trim($_POST['estado']);
    
    $sql_update_endereco = "UPDATE perfil_usuario SET 
                            cep = ?, endereco = ?, numero = ?, complemento = ?, 
                            bairro = ?, cidade = ?, estado = ? 
                            WHERE usuario_id = ?";
    $stmt = $mysqli->prepare($sql_update_endereco);
    $stmt->bind_param("sssssssi", $cep, $endereco, $numero, $complemento, $bairro, $cidade, $estado, $usuario_id);
    
    if ($stmt->execute()) {
        $mensagem = "Endereço atualizado com sucesso!";
        $tipo_mensagem = "sucesso";
        
        // Recarregar dados do perfil
        $stmt = $mysqli->prepare($sql_perfil);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $perfil = $stmt->get_result()->fetch_assoc();
    } else {
        $mensagem = "Erro ao atualizar endereço.";
        $tipo_mensagem = "erro";
    }
}

// Processar alteração de senha
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['alterar_senha'])) {
    $senha_atual = $_POST['senha_atual'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    // Verificar se a senha atual está correta
    if (!password_verify($senha_atual, $usuario['senha'])) {
        $mensagem = "Senha atual incorreta.";
        $tipo_mensagem = "erro";
    } 
    // Verificar se a nova senha tem pelo menos 8 caracteres
    elseif (strlen($nova_senha) < 8) {
        $mensagem = "A nova senha deve ter pelo menos 8 caracteres.";
        $tipo_mensagem = "erro";
    }
    // Verificar se a nova senha contém pelo menos uma letra maiúscula e um número
    elseif (!preg_match('/[A-Z]/', $nova_senha) || !preg_match('/[0-9]/', $nova_senha)) {
        $mensagem = "A nova senha deve conter pelo menos uma letra maiúscula e um número.";
        $tipo_mensagem = "erro";
    }
    // Verificar se as senhas coincidem
    elseif ($nova_senha !== $confirmar_senha) {
        $mensagem = "As senhas não coincidem.";
        $tipo_mensagem = "erro";
    } else {
        // Atualizar senha
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $sql_update_senha = "UPDATE usuarios SET senha = ? WHERE id = ?";
        $stmt = $mysqli->prepare($sql_update_senha);
        $stmt->bind_param("si", $senha_hash, $usuario_id);
        
        if ($stmt->execute()) {
            $mensagem = "Senha alterada com sucesso!";
            $tipo_mensagem = "sucesso";
        } else {
            $mensagem = "Erro ao alterar senha.";
            $tipo_mensagem = "erro";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VITTA - Meu Perfil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/perfil.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="../main.html"><img src="assets/img/vitta-logo.png" alt="VITTA Logo"></a>
        </div>
        <nav>
            <ul>
                <li><a href="painelCliente.php">Meu Painel</a></li>
                <li><a href="escolhas.php">Agendar Consulta</a></li>
                <li><a href="minhasConsultas.php">Minhas Consultas</a></li>
                <li><a href="logout.php" class="button">Sair</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-user-circle"></i> Meu Perfil</h1>
            <p>Gerencie suas informações pessoais</p>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-<?php echo $tipo_mensagem; ?>">
                <?php if ($tipo_mensagem == 'sucesso'): ?>
                    <i class="fas fa-check-circle"></i>
                <?php else: ?>
                    <i class="fas fa-exclamation-triangle"></i>
                <?php endif; ?>
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <div class="profile-tabs">
            <div class="tab-buttons">
                <button class="tab-btn active" data-tab="dados-pessoais">
                    <i class="fas fa-user"></i> Dados Pessoais
                </button>
                <button class="tab-btn" data-tab="endereco">
                    <i class="fas fa-map-marker-alt"></i> Endereço
                </button>
                <button class="tab-btn" data-tab="seguranca">
                    <i class="fas fa-lock"></i> Segurança
                </button>
            </div>

            <div class="tab-content">
                <!-- Dados Pessoais -->
                <div class="tab-pane active" id="dados-pessoais">
                    <form method="POST" class="profile-form">
                        <div class="form-group">
                            <label for="nome">Nome Completo</label>
                            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="cpf">CPF</label>
                            <input type="text" id="cpf" value="<?php echo htmlspecialchars($usuario['cpf']); ?>" disabled>
                            <small>O CPF não pode ser alterado.</small>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefone">Telefone</label>
                                <input type="tel" id="telefone" name="telefone" value="<?php echo htmlspecialchars($perfil['telefone'] ?? ''); ?>" placeholder="(00) 0000-0000">
                            </div>

                            <div class="form-group">
                                <label for="celular">Celular</label>
                                <input type="tel" id="celular" name="celular" value="<?php echo htmlspecialchars($perfil['celular'] ?? ''); ?>" placeholder="(00) 00000-0000">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="atualizar_dados" class="btn-primary">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Endereço -->
                <div class="tab-pane" id="endereco">
                    <form method="POST" class="profile-form">
                        <div class="form-group">
                            <label for="cep">CEP</label>
                            <input type="text" id="cep" name="cep" value="<?php echo htmlspecialchars($perfil['cep'] ?? ''); ?>" placeholder="00000-000">
                            <button type="button" id="buscar-cep" class="btn-secondary">
                                <i class="fas fa-search"></i> Buscar CEP
                            </button>
                        </div>

                        <div class="form-group">
                            <label for="endereco">Endereço</label>
                            <input type="text" id="endereco" name="endereco" value="<?php echo htmlspecialchars($perfil['endereco'] ?? ''); ?>">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="numero">Número</label>
                                <input type="text" id="numero" name="numero" value="<?php echo htmlspecialchars($perfil['numero'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="complemento">Complemento</label>
                                <input type="text" id="complemento" name="complemento" value="<?php echo htmlspecialchars($perfil['complemento'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="bairro">Bairro</label>
                            <input type="text" id="bairro" name="bairro" value="<?php echo htmlspecialchars($perfil['bairro'] ?? ''); ?>">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="cidade">Cidade</label>
                                <input type="text" id="cidade" name="cidade" value="<?php echo htmlspecialchars($perfil['cidade'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <select id="estado" name="estado">
                                    <option value="">Selecione</option>
                                    <option value="AC" <?php echo ($perfil['estado'] ?? '') == 'AC' ? 'selected' : ''; ?>>Acre</option>
                                    <option value="AL" <?php echo ($perfil['estado'] ?? '') == 'AL' ? 'selected' : ''; ?>>Alagoas</option>
                                    <option value="AP" <?php echo ($perfil['estado'] ?? '') == 'AP' ? 'selected' : ''; ?>>Amapá</option>
                                    <option value="AM" <?php echo ($perfil['estado'] ?? '') == 'AM' ? 'selected' : ''; ?>>Amazonas</option>
                                    <option value="BA" <?php echo ($perfil['estado'] ?? '') == 'BA' ? 'selected' : ''; ?>>Bahia</option>
                                    <option value="CE" <?php echo ($perfil['estado'] ?? '') == 'CE' ? 'selected' : ''; ?>>Ceará</option>
                                    <option value="DF" <?php echo ($perfil['estado'] ?? '') == 'DF' ? 'selected' : ''; ?>>Distrito Federal</option>
                                    <option value="ES" <?php echo ($perfil['estado'] ?? '') == 'ES' ? 'selected' : ''; ?>>Espírito Santo</option>
                                    <option value="GO" <?php echo ($perfil['estado'] ?? '') == 'GO' ? 'selected' : ''; ?>>Goiás</option>
                                    <option value="MA" <?php echo ($perfil['estado'] ?? '') == 'MA' ? 'selected' : ''; ?>>Maranhão</option>
                                    <option value="MT" <?php echo ($perfil['estado'] ?? '') == 'MT' ? 'selected' : ''; ?>>Mato Grosso</option>
                                    <option value="MS" <?php echo ($perfil['estado'] ?? '') == 'MS' ? 'selected' : ''; ?>>Mato Grosso do Sul</option>
                                    <option value="MG" <?php echo ($perfil['estado'] ?? '') == 'MG' ? 'selected' : ''; ?>>Minas Gerais</option>
                                    <option value="PA" <?php echo ($perfil['estado'] ?? '') == 'PA' ? 'selected' : ''; ?>>Pará</option>
                                    <option value="PB" <?php echo ($perfil['estado'] ?? '') == 'PB' ? 'selected' : ''; ?>>Paraíba</option>
                                    <option value="PR" <?php echo ($perfil['estado'] ?? '') == 'PR' ? 'selected' : ''; ?>>Paraná</option>
                                    <option value="PE" <?php echo ($perfil['estado'] ?? '') == 'PE' ? 'selected' : ''; ?>>Pernambuco</option>
                                    <option value="PI" <?php echo ($perfil['estado'] ?? '') == 'PI' ? 'selected' : ''; ?>>Piauí</option>
                                    <option value="RJ" <?php echo ($perfil['estado'] ?? '') == 'RJ' ? 'selected' : ''; ?>>Rio de Janeiro</option>
                                    <option value="RN" <?php echo ($perfil['estado'] ?? '') == 'RN' ? 'selected' : ''; ?>>Rio Grande do Norte</option>
                                    <option value="RS" <?php echo ($perfil['estado'] ?? '') == 'RS' ? 'selected' : ''; ?>>Rio Grande do Sul</option>
                                    <option value="RO" <?php echo ($perfil['estado'] ?? '') == 'RO' ? 'selected' : ''; ?>>Rondônia</option>
                                    <option value="RR" <?php echo ($perfil['estado'] ?? '') == 'RR' ? 'selected' : ''; ?>>Roraima</option>
                                    <option value="SC" <?php echo ($perfil['estado'] ?? '') == 'SC' ? 'selected' : ''; ?>>Santa Catarina</option>
                                    <option value="SP" <?php echo ($perfil['estado'] ?? '') == 'SP' ? 'selected' : ''; ?>>São Paulo</option>
                                    <option value="SE" <?php echo ($perfil['estado'] ?? '') == 'SE' ? 'selected' : ''; ?>>Sergipe</option>
                                    <option value="TO" <?php echo ($perfil['estado'] ?? '') == 'TO' ? 'selected' : ''; ?>>Tocantins</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="atualizar_endereco" class="btn-primary">
                                <i class="fas fa-save"></i> Salvar Endereço
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Segurança -->
                <div class="tab-pane" id="seguranca">
                    <form method="POST" class="profile-form">
                        <div class="form-group">
                            <label for="senha_atual">Senha Atual</label>
                            <input type="password" id="senha_atual" name="senha_atual" required>
                        </div>

                        <div class="form-group">
                            <label for="nova_senha">Nova Senha</label>
                            <input type="password" id="nova_senha" name="nova_senha" required>
                            <small>A senha deve ter pelo menos 8 caracteres, incluindo uma letra maiúscula e um número.</small>
                        </div>

                        <div class="form-group">
                            <label for="confirmar_senha">Confirmar Nova Senha</label>
                            <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="alterar_senha" class="btn-primary">
                                <i class="fas fa-key"></i> Alterar Senha
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tabs functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons and panes
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabPanes.forEach(pane => pane.classList.remove('active'));
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Show corresponding tab pane
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });
            
            // CEP search functionality
            const btnBuscarCep = document.getElementById('buscar-cep');
            if (btnBuscarCep) {
                btnBuscarCep.addEventListener('click', function() {
                    const cep = document.getElementById('cep').value.replace(/\D/g, '');
                    
                    if (cep.length !== 8) {
                        alert('Por favor, digite um CEP válido com 8 dígitos.');
                        return;
                    }
                    
                    fetch(`https://viacep.com.br/ws/${cep}/json/`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.erro) {
                                alert('CEP não encontrado.');
                                return;
                            }
                            
                            document.getElementById('endereco').value = data.logradouro;
                            document.getElementById('bairro').value = data.bairro;
                            document.getElementById('cidade').value = data.localidade;
                            document.getElementById('estado').value = data.uf;
                            
                            // Focus on número field
                            document.getElementById('numero').focus();
                        })
                        .catch(error => {
                            console.error('Erro ao buscar CEP:', error);
                            alert('Erro ao buscar CEP. Tente novamente mais tarde.');
                        });
                });
            }
            
            // Mask for phone numbers
            const telefoneInput = document.getElementById('telefone');
            const celularInput = document.getElementById('celular');
            const cepInput = document.getElementById('cep');
            
            if (telefoneInput) {
                telefoneInput.addEventListener('input', function() {
                    let value = this.value.replace(/\D/g, '');
                    if (value.length > 10) value = value.substring(0, 10);
                    
                    if (value.length > 6) {
                        this.value = `(${value.substring(0, 2)}) ${value.substring(2, 6)}-${value.substring(6)}`;
                    } else if (value.length > 2) {
                        this.value = `(${value.substring(0, 2)}) ${value.substring(2)}`;
                    } else if (value.length > 0) {
                        this.value = `(${value}`;
                    } else {
                        this.value = '';
                    }
                });
            }
            
            if (celularInput) {
                celularInput.addEventListener('input', function() {
                    let value = this.value.replace(/\D/g, '');
                    if (value.length > 11) value = value.substring(0, 11);
                    
                    if (value.length > 7) {
                        this.value = `(${value.substring(0, 2)}) ${value.substring(2, 7)}-${value.substring(7)}`;
                    } else if (value.length > 2) {
                        this.value = `(${value.substring(0, 2)}) ${value.substring(2)}`;
                    } else if (value.length > 0) {
                        this.value = `(${value}`;
                    } else {
                        this.value = '';
                    }
                });
            }
            
            if (cepInput) {
                cepInput.addEventListener('input', function() {
                    let value = this.value.replace(/\D/g, '');
                    if (value.length > 8) value = value.substring(0, 8);
                    
                    if (value.length > 5) {
                        this.value = `${value.substring(0, 5)}-${value.substring(5)}`;
                    } else {
                        this.value = value;
                    }
                });
            }
        });
    </script>
</body>
</html>
