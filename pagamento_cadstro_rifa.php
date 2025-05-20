<?php

$servername = "localhost";
$database = "rifa_db";
$username = "root";
$password = "";

// Dados recebidos via POST
$nome = $_POST['nome'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$email = $_POST['email'] ?? '';
$chave_pix = $_POST['chave_pix'] ?? '';
$opcoes = $_POST['opcoes'] ?? '';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valor = $_POST["conteudo"] ?? '';
   // echo "Valor recebido: " . htmlspecialchars($valor);
 //   echo $nome;
 
}

  // Create connection
$conn = new mysqli($servername, $username, $password, $database);



// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$sql = "INSERT INTO tbl_pagamento (numero, dados_usuario, dados_pix, telefone, email, opcao_pix) 
        VALUES (?, ?, ?, ?, ?, ?)";

// Preparar a consulta
$stmt = $conn->prepare($sql);

// Verificar se a preparação foi bem-sucedida
if ($stmt === false) {
    die('Error preparing the SQL statement: ' . $conn->error);
}

// Vincular as variáveis aos parâmetros da consulta
$stmt->bind_param("ssssss", $valor, $nome, $chave_pix, $telefone, $email, $opcoes);

// Executar a consulta
if ($stmt->execute()) {
    echo "Por favor, escaneie o QR Code abaixo e realize o pagamento para validar sua escolha.!";
} else {
    echo "Erro ao inserir registro: " . $stmt->error;
}

// Fechar a declaração e a conexão
$stmt->close();
$conn->close();





