# Projeto VITTA: Sistema de Gerenciamento de Consultas Médicas ⚕️

O **Vitta** é um sistema web inovador e intuitivo, desenvolvido para otimizar o processo de agendamento e gerenciamento de consultas médicas, conectando pacientes a profissionais de saúde de diferentes especialidades. Nosso objetivo é tornar o cuidado com a saúde mais acessível e eficiente, com foco na praticidade e segurança.

## 🚀 Funcionalidades Principais

* **Autenticação de Usuários:** Cadastro e login diferenciado para **Pacientes** e **Médicos**.
* **Agendamento de Consultas:** Pacientes podem buscar médicos por especialidade e agendar consultas em horários disponíveis.
* **Gestão de Horários (Médicos):** Médicos podem adicionar e gerenciar seus horários de atendimento, além de visualizar e atualizar o status das consultas.
* **Painel do Cliente:** Pacientes podem visualizar suas consultas agendadas, acessar histórico e gerenciar informações de perfil (dados pessoais, endereço e segurança).
* **Painel do Médico:** Médicos têm acesso rápido às consultas do dia, próximas consultas e ferramentas para gerenciar seus horários.
* **Cancelamento de Consultas:** Pacientes podem cancelar consultas agendadas.
* **Atualização de Status de Consulta:** Médicos podem confirmar ou marcar consultas como realizadas.
* **Visualização Detalhada:** Perfis de médicos com informações como especialidade, CRM e valor da consulta.
* **Interface Amigável:** Design responsivo e intuitivo para uma experiência de usuário otimizada.

## 🛠️ Tecnologias Utilizadas

Este projeto foi desenvolvido com foco em tecnologias web fundamentais:

* **Frontend:**
    * HTML5
    * CSS3
    * JavaScript (puro, sem frameworks)
* **Backend:**
    * PHP (puro, sem frameworks)
* **Banco de Dados:**
    * MySQL

## 📂 Estrutura do Projeto

O projeto segue uma arquitetura baseada em MVC (Model-View-Controller) para melhor organização e escalabilidade:

```
projeto-vitta/
├── database/                   # Scripts SQL para o banco de dados
│   └── codigo_banco.txt        # Script de criação do banco e inserção de dados
├── model/                      # Lógica de negócio e interação com o banco de dados
│   ├── conexao.php             # Configurações de conexão com o MySQL
│   └── scripts/
│       └── adicionar_tabela_perfil.sql # Script para adicionar tabela de perfil
├── view/                       # Arquivos de interface do usuário (HTML, PHP, CSS, JS)
│   ├── assets/                 # Recursos estáticos (CSS, JS, Imagens)
│   │   ├── css/
│   │   ├── img/
│   │   └── js/
│   ├── agendarConsulta.php
│   ├── atualizarStatusConsulta.php
│   ├── cadastro.php
│   ├── cadastroPage.php
│   ├── escolhas.php
│   ├── gerenciarHorarios.php
│   ├── listarMedicos.php
│   ├── login.php
│   ├── loginpage.php
│   ├── logout.php
│   ├── minhasConsultas.php
│   ├── minhasConsultasMedico.php
│   ├── painelCliente.php
│   ├── painelMedico.php
│   ├── perfil.php
│   └── sobrenos.html
├── MainStyles.css              # Estilos CSS globais
├── main.html                   # Página inicial do sistema
└── README.md                   # Este arquivo
```

## ⚙️ Como Configurar e Rodar o Projeto

Siga os passos abaixo para colocar o projeto VITTA em funcionamento em seu ambiente local.

### Pré-requisitos

* **Servidor Web com PHP e MySQL:** Recomendamos o [XAMPP](https://www.apachefriends.org/pt_br/index.html) (ou WAMP/MAMP/LAMP stack).
* Um navegador web moderno (Chrome, Firefox, Edge, etc.).

### 1. Instalação do XAMPP (ou similar)

1.  Baixe e instale o [XAMPP](https://www.apachefriends.org/pt_br/index.html) para o seu sistema operacional.
2.  Após a instalação, inicie o **XAMPP Control Panel**.
3.  Inicie os módulos **Apache** e **MySQL**. Certifique-se de que o status de ambos esteja como "Running".

### 2. Configuração do Banco de Dados

1.  Acesse o **phpMyAdmin** através do XAMPP Control Panel (clique em "Admin" na linha do MySQL).
2.  Crie um novo banco de dados chamado `gerenciamento_consultas`.
    * No phpMyAdmin, vá em `Databases` (Bancos de Dados) ou `New` (Novo) e insira `gerenciamento_consultas` como nome.
3.  Importe o esquema e os dados iniciais.
    * Selecione o banco de dados `gerenciamento_consultas`.
    * Clique na aba **SQL**.
    * Copie e cole todo o conteúdo do arquivo `database/codigo_banco.txt` neste campo.
    * Execute a consulta. Isso criará as tabelas e as preencherá com dados de exemplo (usuários, especialidades, médicos e algumas consultas de histórico).
    * **Opcional:** Para garantir a tabela de perfil de usuário (para a funcionalidade de "Meu Perfil"), execute também o conteúdo de `model/scripts/adicionar_tabela_perfil.sql` na aba SQL.

### 3. Configuração dos Arquivos do Projeto

1.  Localize a pasta `htdocs` na sua instalação do XAMPP (ex: `C:\xampp\htdocs`).
2.  Crie uma nova pasta dentro de `htdocs` para o seu projeto, por exemplo, `vitta`.
3.  Copie **todos os arquivos e pastas** deste repositório para dentro da pasta `vitta` que você acabou de criar.
4.  Certifique-se de que o arquivo `model/Conexao.php` esteja configurado corretamente para o seu ambiente (geralmente não precisa de alterações para XAMPP padrão):
    ```php
    <?php
    $servidor = "localhost";
    $usuario = "root";
    $senha = ""; // Vazio para XAMPP padrão
    $banco = "gerenciamento_consultas";

    $mysqli = new mysqli($servidor, $usuario, $senha, $banco);

    if (!$mysqli) {
        die("Erro na conexão: " . mysqli_connect_error());
    }
    ?>
    ```

### 4. Acessando o Sistema

1.  Abra seu navegador web.
2.  Digite o seguinte endereço na barra de URL: `http://localhost/vitta/main.html` (substitua `vitta` pelo nome da pasta que você usou).

## 🔑 Credenciais de Acesso (para Teste)

Para testar as funcionalidades do sistema, utilize as seguintes credenciais de exemplo, que são inseridas automaticamente pelo `codigo_banco.txt`:

| Tipo de Usuário | E-mail                 | Senha      | Especialidade       |
| :-------------- | :--------------------- | :--------- | :------------------ |
| **Médico** | `joao.silva@vitta.com` | `password` | Oftalmologia        |
| **Médico** | `maria.santos@vitta.com`| `password` | Clínica Geral       |
| **Médico** | `carlos.mendes@vitta.com`| `password` | Dermatologia        |
| **Médico** | `ana.costa@vitta.com`  | `password` | Psiquiatria         |
| **Médico** | `pedro.oliveira@vitta.com`| `password` | Ginecologia         |
| **Paciente** | `paciente@teste.com`   | `password` | (N/A)               |
| **Paciente** | `ana.paciente@teste.com`| `password` | (N/A)               |
| **Paciente** | `carlos.paciente@teste.com`| `password` | (N/A)               |

## 👥 Equipe

Este projeto foi desenvolvido por:

* [Caio Henrique]
* [Hugo de Jesus]
* [Gustavo Pedretti]
* [Raul Neto]

## 📝 Licença

Este projeto está licenciado sob a **Licença MIT**. Consulte o arquivo `LICENSE` para mais detalhes.

## 📌 Observações

* O projeto está em desenvolvimento e novas funcionalidades podem ser adicionadas em breve.
* Todos os componentes são desenvolvidos **sem o uso de frameworks**, com foco em aprendizado e domínio das tecnologias puras.

---
```
