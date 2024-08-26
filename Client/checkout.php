<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['username'])) {
    header("Location: /Verification/login.php");
    exit();
}

// Inclui o arquivo de conexão com o banco de dados
include '../db.php';

// Inclui a biblioteca AWS SDK
require '../vendor/autoload.php'; // Certifique-se de que o AWS SDK está instalado via Composer

use Aws\Sqs\SqsClient;
use Aws\Exception\AwsException;

$queueUrl = 'https://sqs.us-east-1.amazonaws.com/593793062481/Compra'; // Substitua pelo URL da sua fila

// Configura o cliente SQS
$sqsClient = new SqsClient([
    'version' => 'latest',
    'region'  => 'us-east-1', // Substitua pela sua região
]);

// Função para enviar uma mensagem para a fila SQS
function sendMessageToQueue($sqsClient, $queueUrl, $messageBody) {
    try {
        $result = $sqsClient->sendMessage([
            'QueueUrl'    => $queueUrl,
            'MessageBody' => $messageBody,
        ]);
        return $result;
    } catch (AwsException $e) {
        echo "<p>Erro ao enviar a mensagem para a fila: " . $e->getMessage() . "</p>";
        return false;
    }
}

// Processa o pedido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cart = $_SESSION['cart'];

    if (!empty($cart)) {
        // Converte o carrinho para JSON
        $messageBody = json_encode($cart);
        // Envia a mensagem para a fila SQS
        $result = sendMessageToQueue($sqsClient, $queueUrl, $messageBody);

        if ($result) {
            echo "<p>Seu pedido está sendo processado. Você receberá uma notificação quando o processamento for concluído.</p>";
        } else {
            echo "<p>Houve um problema ao processar seu pedido. Tente novamente mais tarde.</p>";
        }

        // Limpa o carrinho após o envio
        unset($_SESSION['cart']);
    } else {
        echo "<p>Seu carrinho está vazio.</p>";
    }
}

// Inclui o cabeçalho
include 'header.php';
?>

<main>
    <section class="checkout">
        <h2>Finalizar Compra</h2>
        <!-- Aqui você pode exibir o carrinho ou outras informações necessárias -->
        <form action="checkout.php" method="post">
            <button type="submit">Confirmar Pedido</button>
        </form>
    </section>
</main>

<?php include 'footer.php'; ?>
