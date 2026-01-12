<?php
require_once 'conexao.php';
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome  = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $senha = $_POST['senha'];
    $confirma = $_POST['confirma_senha'];

    // Validações básicas
    if ($senha !== $confirma) {
        $mensagem = "As senhas não coincidem!";
    } else {
        // Verifica se email já existe
        $check = $conn->query("SELECT id FROM usuarios WHERE email = '$email'");
        if ($check->num_rows > 0) {
            $mensagem = "Este e-mail já está cadastrado.";
        } else {
            // Criptografa a senha
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            
            // Insere como STATUS = 0 (Pendente) e NIVEL = editor
            $sql = "INSERT INTO usuarios (nome, email, senha, nivel, status) VALUES ('$nome', '$email', '$senha_hash', 'editor', 0)";
            
            if ($conn->query($sql)) {
                $mensagem = "sucesso";
            } else {
                $mensagem = "Erro ao cadastrar: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Conta | Afroletrando</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-stone-100 h-screen flex items-center justify-center font-sans">

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md border border-stone-200">
        <h1 class="text-2xl font-bold text-center text-stone-800 mb-2">Solicitar Acesso</h1>
        <p class="text-stone-500 text-center text-sm mb-6">Seu cadastro passará por aprovação.</p>

        <?php if ($mensagem == 'sucesso'): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                <p class="font-bold">Solicitação Enviada!</p>
                <p class="text-sm">Aguarde o administrador aprovar sua conta.</p>
                <a href="login.php" class="text-green-800 font-bold underline mt-2 block">Voltar ao Login</a>
            </div>
        <?php else: ?>
            
            <?php if ($mensagem): ?>
                <div class="bg-red-100 text-red-700 p-3 mb-4 rounded text-sm"><?php echo $mensagem; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="block text-stone-600 text-sm font-bold mb-2">Nome Completo</label>
                    <input type="text" name="nome" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-orange-500">
                </div>
                <div class="mb-4">
                    <label class="block text-stone-600 text-sm font-bold mb-2">E-mail</label>
                    <input type="email" name="email" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-orange-500">
                </div>
                <div class="mb-4">
                    <label class="block text-stone-600 text-sm font-bold mb-2">Senha</label>
                    <input type="password" name="senha" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-orange-500">
                </div>
                <div class="mb-6">
                    <label class="block text-stone-600 text-sm font-bold mb-2">Confirmar Senha</label>
                    <input type="password" name="confirma_senha" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-orange-500">
                </div>
                <button type="submit" class="w-full bg-orange-700 hover:bg-orange-800 text-white font-bold py-3 rounded-lg transition">CRIAR CONTA</button>
            </form>
            <div class="mt-4 text-center">
                <a href="login.php" class="text-stone-500 hover:text-orange-700 text-sm">Já tem conta? Faça Login</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>