<?php

require 'db.php'; // Inclui o arquivo de conexão com o banco de dados
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

// Função para processar o pedido
function processOrder($orderData, $conn) {
    // Transações para garantir a integridade
    $conn->begin_transaction();
    $quantityInStock = '';

    try {
        // Depure o conteúdo de $orderData
        echo "Conteúdo de orderData: " . print_r($orderData, true) . "\n";

        // A mensagem deve ser um único pedido
        $productId = isset($orderData['product_id']) ? (int)$orderData['product_id'] : null;
        $quantityRequested = isset($orderData['quantity']) ? (int)$orderData['quantity'] : null;

        // Verifique o formato e os valores
        if (!is_int($productId) || !is_int($quantityRequested)) {
            throw new Exception("Dados inválidos: product_id e quantity devem ser inteiros.");
        }

        echo "Verificando o produto com ID: $productId\n";

        // Verifica o estoque
        $stmt = $conn->prepare("SELECT quantidade FROM produtos WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Erro na preparação da consulta: " . $conn->error);
        }
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $stmt->bind_result($quantityInStock);
        
        if (!$stmt->fetch()) {
            echo "Produto com ID $productId não encontrado.\n";
            throw new Exception("Produto com ID $productId não encontrado.");
        }
        $stmt->close();

        echo "Quantidade em estoque para o produto com ID $productId: $quantityInStock\n";

        if ($quantityInStock < $quantityRequested) {
            // Desfaz a transação e retorna falha
            $conn->rollback();
            return false;
        }

        // Atualiza o estoque
        $stmt = $conn->prepare("UPDATE produtos SET quantidade = quantidade - ? WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Erro na preparação da atualização: " . $conn->error);
        }
        $stmt->bind_param("ii", $quantityRequested, $productId);
        if (!$stmt->execute()) {
            throw new Exception("Erro na execução da atualização: " . $stmt->error);
        }
        $stmt->close();

        // Confirma a transação
        $conn->commit();
        return true;

    } catch (Exception $e) {
        // Em caso de erro, desfaz a transação e exibe o erro
        $conn->rollback();
        echo "Erro ao processar o pedido: " . $e->getMessage() . PHP_EOL;
        return false;
    }
}

while (true) {
    try {
        // Recebe mensagens da fila
        $result = $client->receiveMessage([
            'QueueUrl'            => $queueUrl,

        ]);

        if (!empty($result->get('Messages'))) {
            $message = $result->get('Messages')[0];
            $body = json_decode($message['Body'], true);
            $receiptHandle = $message['ReceiptHandle'];

            echo "Mensagem recebida: " . json_encode($body) . "\n";
            
            // Processa o pedido
            $processedSuccessfully = processOrder($body, $conn);

            // Exclui a mensagem da fila após o processamento
            $client->deleteMessage([
                'QueueUrl'      => $queueUrl,
                'ReceiptHandle' => $receiptHandle,
            ]);

            // Retorna a resposta para o terminal
            if ($processedSuccessfully) {
                echo "Pedido processado com sucesso." . PHP_EOL;
            } else {
                echo "Falha ao processar o pedido." . PHP_EOL;
            }
        } else {


            echo "Nenhuma mensagem encontrada na fila." . PHP_EOL;
            return;
        }

    } catch (AwsException $e) {
        echo "Erro ao processar a fila: " . $e->getMessage() . PHP_EOL;
    } catch (\Throwable $th) {
        echo "Erro inesperado: " . $th->getMessage() . PHP_EOL;
    }


}

$conn->close();
?>
