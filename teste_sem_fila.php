<?php

require 'db.php'; // Inclui o arquivo de conexão com o banco de dados

// Função para gerar dados de pedidos
function generateOrderData($numOrders) {
    $orders = [];
    for ($i = 0; $i < $numOrders; $i++) {
        $orders[] = [
            'product_id' => rand(1, 3), // IDs de produtos aleatórios entre 1 e 1000
            'quantity' => rand(1, 50), // Quantidade aleatória entre 1 e 50
            'nome' => 'produto_' . rand(1, 1000),
            'imagem' => 'imagem_' . rand(1, 1000),
        ];
    }
    return $orders;
}

// Dados do pedido
$orderData = generateOrderData(5000); // Aumenta a quantidade de pedidos para 1000

// Mensagem de início
echo "Iniciando a atualização dos pedidos..." . PHP_EOL;

// Insere os pedidos diretamente no banco de dados
foreach ($orderData as $order) {
    $productId = $order['product_id'];
    $quantityRequested = $order['quantity'];

    // Mensagem indicando o início da atualização
    echo "Atualizando produto ID $productId com quantidade solicitada $quantityRequested..." . PHP_EOL;


    // Atualiza a quantidade do produto
    $stmt = $conn->prepare("UPDATE produtos SET quantidade = quantidade - ? WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Erro na preparação da atualização: " . $conn->error);
    }
    $stmt->bind_param("ii", $quantityRequested, $productId);
    if (!$stmt->execute()) {
        throw new Exception("Erro na execução da atualização: " . $stmt->error);
    }
    $stmt->close();

    // Mensagem indicando sucesso da atualização
    echo "Produto ID $productId atualizado com sucesso." . PHP_EOL;

    
}

// Mensagem de término
echo "Atualização dos pedidos concluída." . PHP_EOL;

$conn->close();

?>
