<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['username'])) {
    header("Location: /Verification/login.php");
    exit();
}

// Inclui o arquivo de conexão com o banco de dados
include '../db.php'; 

// Exibe o carrinho
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    echo "<h2>Seu Carrinho</h2>";
    echo "<form action='checkout.php' method='post'>";
    echo "<table>";
    echo "<tr><th>Produto</th><th>Preço</th><th>Quantidade</th></tr>";

    foreach ($_SESSION['cart'] as $product_id => $item) {
        echo "<tr>";
        echo "<td>{$item['nome']}</td>";
        echo "<td>R$ " . number_format($item['preco'], 2, ',', '.') . "</td>";
        echo "<td>{$item['quantidade']}</td>";
        echo "</tr>";
    }

    echo "</table>";
    echo "<button type='submit'>Finalizar Compra</button>";
    echo "</form>";
} else {
    echo "<p>Seu carrinho está vazio.</p>";
}

?>
