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

try {
    // Recebe mensagens da fila
    $result = $client->receiveMessage([
        'QueueUrl'            => $queueUrl,
        'MaxNumberOfMessages' => 1, // Ajuste se necessário
        'WaitTimeSeconds'     => 10, // Tempo de espera para mensagens
    ]);

    if (!empty($result->get('Messages'))) {
        // Processa a primeira mensagem
        $message = $result->get('Messages')[0];
        $body = $message['Body'];
        $receiptHandle = $message['ReceiptHandle'];

        // Exibe o corpo da mensagem
        echo "Mensagem recebida: " . $body . PHP_EOL;

        // Exclui a mensagem da fila após o processamento
        $client->deleteMessage([
            'QueueUrl'      => $queueUrl,
            'ReceiptHandle' => $receiptHandle,
        ]);

        echo "Mensagem excluída com sucesso." . PHP_EOL;
    } else {
        echo "Nenhuma mensagem encontrada na fila." . PHP_EOL;
    }

} catch (AwsException $e) {
    // Exibe mensagem de erro
    echo "Erro ao processar a fila: " . $e->getMessage() . PHP_EOL;
} catch (\Throwable $th) {
    // Exibe mensagem de erro para outros casos
    echo "Erro inesperado: " . $th->getMessage() . PHP_EOL;
}
