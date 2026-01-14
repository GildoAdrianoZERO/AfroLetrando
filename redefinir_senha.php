<?php
require_once 'conexao.php';

$msg = '';
$token = isset($_GET['token']) ? $_GET['token'] : '';
$valido = false;

// 1. Verifica se o token é válido e não expirou
if ($token) {
    $agora = date('Y-m-d H:i:s');
    $sql = "SELECT id FROM usuarios WHERE token_recuperacao = '$token' AND token_validade > '$agora'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $valido = true;
    } else {
        $msg = "Este link é inválido ou já expirou.";
    }
}

// 2. Processa a Nova Senha
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $valido) {
    $nova_senha = $_POST['nova_senha'];
    $confirma_senha = $_POST['confirma_senha'];

    if ($nova_senha === $confirma_senha) {
        // Criptografa
        $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        
        // Atualiza a senha e Limpa o token (para não ser usado de novo)
        $conn->query("UPDATE usuarios SET senha = '$hash', token_recuperacao = NULL, token_validade = NULL WHERE token_recuperacao = '$token'");
        
        header("Location: login.php?msg=senha_alterada");
        exit;
    } else {
        $msg = "As senhas não coincidem.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Senha | Afroletrando</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-stone-100 h-screen flex items-center justify-center px-4">

    <div class="max-w-md w-full bg-white rounded-xl shadow-lg border border-stone-200 overflow-hidden">
        <div class="bg-stone-900 p-6 text-center">
            <h1 class="text-xl font-bold text-white">Criar Nova Senha</h1>
        </div>

        <div class="p-8">
            <?php if ($msg): ?>
                <div class="bg-red-100 text-red-700 p-4 mb-6 rounded text-center font-bold"><?php echo $msg; ?></div>
            <?php endif; ?>

            <?php if ($valido): ?>
                <form method="POST">
                    <div class="mb-4">
                        <label class="block text-stone-600 text-sm font-bold mb-2">Nova Senha</label>
                        <input type="password" name="nova_senha" required class="w-full px-4 py-3 rounded-lg border border-stone-300">
                    </div>
                    <div class="mb-6">
                        <label class="block text-stone-600 text-sm font-bold mb-2">Confirmar Senha</label>
                        <input type="password" name="confirma_senha" required class="w-full px-4 py-3 rounded-lg border border-stone-300">
                    </div>
                    <button type="submit" class="w-full bg-green-700 hover:bg-green-800 text-white font-bold py-3 rounded-lg transition">
                        SALVAR NOVA SENHA
                    </button>
                </form>
            <?php else: ?>
                <div class="text-center">
                    <p class="text-stone-500 mb-4">Solicite um novo link.</p>
                    <a href="esqueceu_senha.php" class="text-orange-700 font-bold hover:underline">Tentar novamente</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>