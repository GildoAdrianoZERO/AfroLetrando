<?php
require_once 'conexao.php';

$msg = '';
$tipo_msg = '';
$token = isset($_GET['token']) ? $_GET['token'] : '';
$valido = false;

// 1. Verifica se o token existe no banco e se ainda é válido (data futura)
if ($token) {
    $agora = date('Y-m-d H:i:s');
    $sql = "SELECT id FROM usuarios WHERE token_recuperacao = '$token' AND token_validade > '$agora'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $valido = true;
    } else {
        $msg = "Este link é inválido ou já expirou (tem validade de 1 hora).";
        $tipo_msg = "erro";
    }
} else {
    $msg = "Link inválido.";
    $tipo_msg = "erro";
}

// 2. Processa a Nova Senha
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $valido) {
    $nova_senha = $_POST['nova_senha'];
    $confirma_senha = $_POST['confirma_senha'];

    if ($nova_senha === $confirma_senha) {
        // Criptografa a nova senha
        $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        
        // Atualiza a senha e LIMPA o token (para esse link não ser usado de novo)
        $conn->query("UPDATE usuarios SET senha = '$hash', token_recuperacao = NULL, token_validade = NULL WHERE token_recuperacao = '$token'");
        
        // Redireciona para o login com mensagem de sucesso
        header("Location: login.php?msg=senha_alterada");
        exit;
    } else {
        $msg = "As senhas não coincidem. Tente novamente.";
        $tipo_msg = "erro";
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-stone-100 h-screen flex items-center justify-center px-4">

    <div class="max-w-md w-full bg-white rounded-xl shadow-lg border border-stone-200 overflow-hidden">
        <div class="bg-stone-900 p-6 text-center">
            <h1 class="text-xl font-bold text-white">Criar Nova Senha</h1>
        </div>

        <div class="p-8">
            <?php if ($msg): ?>
                <div class="p-4 mb-6 rounded text-sm font-bold text-center <?php echo ($tipo_msg == 'sucesso') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <?php if ($valido): ?>
                <form method="POST">
                    
                    <div class="mb-5">
                        <label class="block text-stone-600 text-sm font-bold mb-2">Nova Senha</label>
                        <div class="relative">
                            <input type="password" name="nova_senha" id="senha1" required 
                                   class="w-full px-4 py-3 pr-10 rounded-lg border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 transition" 
                                   placeholder="••••••••">
                            
                            <button type="button" onclick="toggleSenha('senha1', 'icon1')" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-stone-400 hover:text-stone-600 focus:outline-none cursor-pointer">
                                <i id="icon1" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-8">
                        <label class="block text-stone-600 text-sm font-bold mb-2">Confirmar Nova Senha</label>
                        <div class="relative">
                            <input type="password" name="confirma_senha" id="senha2" required 
                                   class="w-full px-4 py-3 pr-10 rounded-lg border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500 transition" 
                                   placeholder="••••••••">
                            
                            <button type="button" onclick="toggleSenha('senha2', 'icon2')" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-stone-400 hover:text-stone-600 focus:outline-none cursor-pointer">
                                <i id="icon2" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-stone-900 hover:bg-orange-700 text-white font-bold py-3 rounded-lg transition shadow-lg">
                        SALVAR NOVA SENHA
                    </button>
                </form>
            <?php else: ?>
                
                <div class="text-center">
                    <div class="text-stone-300 text-6xl mb-4"><i class="fas fa-link-slash"></i></div>
                    <p class="text-stone-500 mb-6">O link que você clicou não é válido ou já foi utilizado.</p>
                    <a href="esqueceu_senha.php" class="bg-orange-700 text-white px-6 py-2 rounded-lg font-bold hover:bg-orange-800 transition">Solicitar novo link</a>
                </div>

            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleSenha(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>