<?php
$servername = "localhost";  // Endereço do servidor MySQL
$username = "aluno";         // Usuário do banco de dados
$password = "@lunoifp3";             // Senha do banco de dados
$dbname = "loja_virtual";   // Nome do banco de dados

// Criando a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificando a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
