<?php
// Inicia a sessão
session_start();

// Verifica se o usuário já está logado, redireciona para a página principal se estiver
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Inclui o arquivo de conexão com o banco de dados
include '../db.php';

// Função para verificar o login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepara a consulta SQL para encontrar o usuário
    $sql = "SELECT * FROM usuarios WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se o usuário foi encontrado
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifica a senha
        if (password_verify($password, $user['password'])) {
            // Inicia a sessão do usuário
            $_SESSION['username'] = $user['username'];
            header("Location: ../Client/index.php");
            exit();
        } else {
            $error_message = "Senha incorreta!";
        }
    } else {
        $error_message = "Usuário não encontrado!";
    }

    $stmt->close();
}

$conn->close();

// Inclui o cabeçalho
include 'header.php';
?>

<main>
    <section class="form-container">
        <h2>Login</h2>
        <!-- Exibe mensagens de erro, se houver -->
        <?php if (!empty($error_message)): ?>
            <p class="error"><?= $error_message; ?></p>
        <?php endif; ?>
        <form action="login.php" method="post" class="styled-form">
            <div class="form-group">
                <label for="username">Nome de Usuário:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn">Entrar</button>
        </form>
        <p>Não tem uma conta? <a href="register.php">Registre-se aqui</a>.</p>
    </section>
</main>

<?php include '../Client/footer.php'; ?>
