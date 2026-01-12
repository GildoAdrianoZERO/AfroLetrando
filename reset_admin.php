<?php
require_once 'conexao.php';

// A senha que você quer usar
$nova_senha = '123456';
$email = 'admin@afroletrando.com';

// Gera o hash correto para o SEU computador
$hash = password_hash($nova_senha, PASSWORD_DEFAULT);

// Atualiza no banco
$sql = "UPDATE usuarios SET senha = '$hash' WHERE email = '$email'";

if ($conn->query($sql) === TRUE) {
    echo "<h1>Sucesso!</h1>";
    echo "<p>A senha do usuário <b>$email</b> foi redefinida para: <b>$nova_senha</b></p>";
    echo "<br><a href='login.php'>Clique aqui para fazer Login</a>";
} else {
    echo "Erro ao atualizar: " . $conn->error;
}
?>