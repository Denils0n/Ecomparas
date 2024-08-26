<?php
// Inclui o arquivo de conexão com o banco de dados
include 'db.php';

// Inicia a sessão
session_start();

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password === $confirm_password) {
        // Hash da senha
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepara a inserção no banco de dados
        $sql = "INSERT INTO usuarios (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            echo "<p>Usuário registrado com sucesso! <a href='login.php'>Faça login</a>.</p>";
        } else {
            echo "<p>Erro ao registrar o usuário: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p>As senhas não coincidem!</p>";
    }
}

// Inclui o cabeçalho
include '../Client/header.php';

?>

<main>
    <section class="form-container">
        <h2>Registro</h2>
        <!-- Exibe mensagens de sucesso ou erro, se houver -->
        <?php if (!empty($success_message)): ?>
            <p class="success"><?= $success_message; ?></p>
        <?php elseif (!empty($error_message)): ?>
            <p class="error"><?= $error_message; ?></p>
        <?php endif; ?>
        <form action="register.php" method="post" class="styled-form">
            <div class="form-group">
                <label for="username">Nome de Usuário:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="register" class="btn">Registrar</button>
        </form>
        <p>Já tem uma conta? <a href="login.php">Faça login aqui</a>.</p>
    </section>
</main>

<?php include '../Client/footer.php'; ?>

