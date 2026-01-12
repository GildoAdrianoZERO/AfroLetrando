<?php
// 1. Tenta pegar as configurações do Railway (Ambiente)
// 2. Se não encontrar (estiver no PC), usa as configurações do XAMPP
$host = getenv('MYSQLHOST') ? getenv('MYSQLHOST') : '127.0.0.1';
$user = getenv('MYSQLUSER') ? getenv('MYSQLUSER') : 'root';
$pass = getenv('MYSQLPASSWORD') ? getenv('MYSQLPASSWORD') : '';
$db   = getenv('MYSQLDATABASE') ? getenv('MYSQLDATABASE') : 'afroletrando';
$port = getenv('MYSQLPORT') ? getenv('MYSQLPORT') : 3306;

// Desativa mensagens de erro visuais do PHP para evitar vazamento de dados
mysqli_report(MYSQLI_REPORT_OFF);

// Cria a conexão usando as variáveis definidas acima
$conn = new mysqli($host, $user, $pass, $db, $port);

// Verifica se deu erro
if ($conn->connect_error) {
    // Mostra o erro detalhado (apenas para debug, depois você pode tirar)
    die("FALHA NA CONEXÃO: " . $conn->connect_error . " | Host tentado: " . $host);
}

// Configura caracteres para aceitar acentos e emojis
$conn->set_charset("utf8mb4");

// --- FUNÇÃO DE SEGURANÇA (Anti-XSS) ---

if (!function_exists('h')) {
    function h($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
?>