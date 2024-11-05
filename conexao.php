<?php
|$servername ="localhpost";
$username = "root";
$password = "";
$dbname = "sistema";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $cpnn->connect_error);

}

// Adiciona a coluna 'imagem' à tabela 'produtos' se ele não existir
$sql = "SHOW COLUMNS FROM produtos Like 'imagem'";
 $result = $conn->query($sql);
if ($result->num_rows==0) {
    $sql = "ALTER TABLE produtos ADD COLUMN imagem VARCHAR(255)";
    $connn->query($sql);
}

// Adiciona a coluna 'imagem' à tabela 'fornecedores' se ele não existir
$sql = "SHOW COLUMNS FROM fornecedores Like 'imagem'";
 $result = $conn->query($sql);
if ($result->num_rows==0) {
    $sql = "ALTER TABLE fornecedores ADD COLUMN imagem VARCHAR(255)";
    $connn->query($sql);
}
