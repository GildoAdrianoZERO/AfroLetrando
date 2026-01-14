<?php
// 1. SEGURANÇA: O cookie da sessão morre ao fechar o navegador
session_set_cookie_params(0);
session_start();
require_once 'conexao.php';

$erro = '';

// Se já estiver logado, vai pro painel
if (isset($_SESSION['usuario_id'])) {
    header("Location: admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $senha = $_POST['senha'];

    $sql = "SELECT id, nome, senha, nivel, status FROM usuarios WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $usuario = $result->fetch_assoc();
        
        // Verifica Senha
        if (password_verify($senha, $usuario['senha'])) {
            
            // Verifica Aprovação
            if ($usuario['status'] == 0) {
                $erro = "Sua conta aguarda aprovação do administrador.";
            } else {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_nivel'] = $usuario['nivel'];
                
                header("Location: admin.php");
                exit;
            }

        } else {
            $erro = "Senha incorreta.";
        }
    } else {
        $erro = "E-mail não encontrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo | Afroletrando</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;600&display=swap');
        body { font-family: 'Inter', sans-serif; }
        h1 { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-stone-100 h-screen flex items-center justify-center px-4">

    <div class="max-w-md w-full bg-white rounded-xl shadow-2xl overflow-hidden border border-stone-200">
        <div class="bg-stone-900 p-8 text-center relative overflow-hidden">
            <div class="absolute inset-0 bg-orange-900/20"></div>
            <h1 class="text-3xl text-white relative z-10">Afroletrando</h1>
            <p class="text-stone-400 text-sm mt-2 relative z-10 uppercase tracking-widest">Acesso Restrito</p>
        </div>

        <div class="p-8">
            <?php if ($erro): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 text-sm flex items-start" role="alert">
                    <i class="fas fa-exclamation-circle mt-1 mr-2"></i>
                    <div>
                        <p class="font-bold">Atenção</p>
                        <p><?php echo $erro; ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-6">
                    <label class="block text-stone-600 text-sm font-bold mb-2" for="email">E-mail</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input class="w-full pl-10 pr-3 py-3 rounded-lg border border-stone-300 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition" 
                               id="email" type="email" name="email" placeholder="seu@email.com" required>
                    </div>
                </div>

                <div class="mb-2"> <label class="block text-stone-600 text-sm font-bold mb-2" for="senhaLogin">Senha</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400 pointer-events-none">
                            <i class="fas fa-lock"></i>
                        </span>
                        
                        <input class="w-full pl-10 pr-10 py-3 rounded-lg border border-stone-300 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition" 
                               id="senhaLogin" type="password" name="senha" placeholder="••••••••" required>

                        <button type="button" onclick="toggleSenha('senhaLogin', 'iconLogin')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-stone-400 hover:text-stone-600 focus:outline-none cursor-pointer">
                            <i id="iconLogin" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="text-right mb-6">
                    <a href="esqueceu_senha.php" class="text-xs text-stone-500 hover:text-orange-600 font-medium">Esqueceu a senha?</a>
                </div>

                <button class="w-full bg-orange-700 hover:bg-orange-800 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition duration-300 transform hover:-translate-y-0.5" type="submit">
                    ENTRAR NO SISTEMA
                </button>
            </form>
            
            <div class="mt-6 text-center border-t border-stone-100 pt-6">
                <p class="text-stone-500 text-sm mb-2">Não tem acesso?</p>
                <a href="cadastro.php" class="text-orange-700 font-bold hover:underline text-sm block mb-4">
                    Solicitar conta de Editor
                </a>
                
                <a href="index.php" class="text-stone-400 text-sm hover:text-orange-700 flex items-center justify-center transition">
                    <i class="fas fa-arrow-left mr-2"></i> Voltar ao Site
                </a>
            </div>
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