<?php

require_once 'vendor/autoload.php';

use Aws\Sqs\SqsClient;
use Aws\Exception\AwsException;

// URL da fila SQS
$queueUrl = 'https://sqs.us-east-1.amazonaws.com/593793062481/Compra';

// Criação do cliente SQS
$client = new SqsClient([
    'profile' => 'default',
    'region'  => 'us-east-1', // Certifique-se de que a região está correta
    'version' => '2012-11-05',
]);

// Função para gerar dados de pedidos
function generateOrderData($numOrders) {
    $orders = [];
    for ($i = 0; $i < $numOrders; $i++) {
        $orders[] = [
            'product_id' => rand(1, 3), // IDs de produtos aleatórios entre 1 e 100
            'quantity' => rand(1, 10) // Quantidade aleatória entre 1 e 10
        ];
    }
    return $orders;
}

// Envia uma mensagem para a fila
function sendOrderMessage($order, $client, $queueUrl) {
    try {
        $result = $client->sendMessage([
            'QueueUrl'    => $queueUrl,
            'MessageBody' => json_encode($order),
        ]);
        echo "Mensagem enviada com sucesso. ID da Mensagem: " . $result->get('MessageId') . PHP_EOL;
    } catch (AwsException $e) {
        echo "Erro ao enviar a mensagem: " . $e->getMessage() . PHP_EOL;
    }
}

// Dados do pedido
$orderData = generateOrderData(5000);

// Envia cada pedido como uma mensagem separada
foreach ($orderData as $order) {
    sendOrderMessage($order, $client, $queueUrl);
}
?>
