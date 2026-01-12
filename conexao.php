<?php
// Configurações para XAMPP
// Tente '127.0.0.1' em vez de 'localhost' 
$host    = '127.0.0.1';
$usuario = 'root';
$senha   = '';
$banco   = 'afroletrando';

// Porta do Xampp
$porta   = 3306; 

// Desativa mensagens de erro do MySQLi
mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    // ISSO VAI MOSTRAR O ERRO NA TELA (Só para debug)
    die("FALHA NA CONEXÃO: " . $conn->connect_error . " | Host: " . $host . " | User: " . $user);
}
// Só define o charset se a conexão tiver dado certo
$conn->set_charset("utf8");
?>