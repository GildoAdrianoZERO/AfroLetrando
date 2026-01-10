<?php
$host = 'xxxxxx'; 
$usuario = 'xxxxxx';         
$senha = 'xxxxxx';       
$banco = 'xxxxxx'; 

$conn = new mysqli($host, $usuario, $senha, $banco);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Falha: " . $conn->connect_error);
}
?>