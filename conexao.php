<?php
// Configurações para XAMPP
// Tente '127.0.0.1' em vez de 'localhost' 
$host    = '127.0.0.1';
$usuario = 'root';
$senha   = '';
$banco   = 'afroletrando';

// Porta do Xampp
$porta   = 3307; 

// Desativa mensagens de erro do MySQLi
mysqli_report(MYSQLI_REPORT_OFF);

// Tenta conectar
$conn = @new mysqli($host, $usuario, $senha, $banco, $porta);

// VERIFICA SE DEU ERRO ANTES DE FAZER QUALQUER COISA
if ($conn->connect_error) {
    // Mostra uma mensagem amigável e para tudo
    die("Erro de conexão: Não foi possível conectar ao banco. <br>Detalhe técnico: " . $conn->connect_error);
}

// Só define o charset se a conexão tiver dado certo
$conn->set_charset("utf8");
?>