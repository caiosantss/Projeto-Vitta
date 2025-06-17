-- Adicionar tabela de perfil de usuário para armazenar informações adicionais
CREATE TABLE IF NOT EXISTS perfil_usuario (
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
);

-- Inserir alguns dados de exemplo para os usuários existentes
INSERT IGNORE INTO perfil_usuario (usuario_id)
SELECT id FROM usuarios WHERE tipo_usuario = 0;
