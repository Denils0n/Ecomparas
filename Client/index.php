<?php 
session_start();

// Verifica se o usuário está logado, redireciona para a página de login se não estiver
if (!isset($_SESSION['username'])) {
    header("Location: /Verification/login.php");
    exit();
}

// Inclui o arquivo de conexão com o banco de dados
include '../db.php'; 

// Verifica o tipo de usuário
$user_type = $_SESSION['user_type']; // Assume que o tipo de usuário está armazenado na sessão

// Processa o envio dos produtos para o carrinho
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    foreach ($_POST['products'] as $product_id => $quantity) {
        if ($quantity > 0) {
            // Consulta para obter os detalhes do produto
            $sql = "SELECT nome, preco FROM produtos WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($product) {
                $max_quantity = 10; // Default para Normal e VIP
                if ($user_type == 3) { // Logista
                    $max_quantity = PHP_INT_MAX; // Sem limite
                }

                if ($quantity <= $max_quantity) {
                    // Adiciona ao carrinho (simulação)
                    $_SESSION['cart'][$product_id] = [
                        'nome' => $product['nome'],
                        'preco' => $product['preco'],
                        'quantidade' => $quantity
                    ];
                } else {
                    echo "<p>Quantidade para o produto '{$product['nome']}' excede o limite permitido de {$max_quantity} unidades.</p>";
                }
            } else {
                echo "<p>Produto com ID {$product_id} não encontrado.</p>";
            }
        }
    }

    echo "<p>Produtos adicionados ao carrinho com sucesso!</p>";
}

include 'header.php'; 

?>

<main>
    <section class="products">
        <h1>Nossos Produtos</h1>
        <form action="cart.php" method="post">
            <div class="product-list">
                <?php
                // Consulta SQL para obter produtos
                $sql = "SELECT id, nome, preco, imagem FROM produtos";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Exibe os produtos
                    while ($produto = $result->fetch_assoc()) {
                        echo "<div class='product-item'>";
                        echo "<img src='{$produto['imagem']}' alt='{$produto['nome']}'>";
                        echo "<h2>{$produto['nome']}</h2>";
                        echo "<p>R$ " . number_format($produto['preco'], 2, ',', '.') . "</p>";
                        echo "<label for='quantity_{$produto['id']}'>Quantidade:</label>";
                        echo "<input type='number' id='quantity_{$produto['id']}' name='products[{$produto['id']}]' min='0' value='0'>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Nenhum produto encontrado.</p>";
                }
                ?>
            </div>
            <button type="submit" name="add_to_cart" class="btn">Adicionar ao Carrinho</button>
        </form>
    </section>
</main>

<?php include 'footer.php'; ?>
