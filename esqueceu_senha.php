<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'conexao.php';

$msg = '';
$tipo_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);

    // 1. Verifica se o e-mail existe no banco
    $sql = "SELECT id, nome FROM usuarios WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        
        // 2. Gera Token
        $token = bin2hex(random_bytes(50));
        $validade = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $conn->query("UPDATE usuarios SET token_recuperacao = '$token', token_validade = '$validade' WHERE email = '$email'");

        // 3. Link Dinâmico
        $protocolo = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $link = $protocolo . "://" . $host . "/redefinir_senha.php?token=" . $token;

        $mail = new PHPMailer(true);

        try {
            // --- CONFIGURAÇÃO RESEND (SMTP) ---
            $mail->isSMTP();
            $mail->Host       = 'smtp.resend.com';  // Servidor do Resend
            $mail->SMTPAuth   = true;
            $mail->Username   = 'resend';           // O usuário é sempre 'resend'
            $mail->Password   = 're_DVaz1Noc_8vuucx87tHQPFkCPZ239CMGp';     // COLOQUE SUA API KEY AQUI (começa com re_)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // --- IMPORTANTE: REMETENTE DE TESTE ---
            // Se você não tem domínio próprio, use este e-mail obrigatório do Resend:
            $mail->setFrom('onboarding@resend.dev', 'Afroletrando Recuperacao');
            
            // Destinatário
            $mail->addAddress($email, $usuario['nome']);

            // Conteúdo
            $mail->isHTML(true);
            $mail->Subject = 'Recuperar Senha - Afroletrando';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; color: #333;'>
                    <h2>Olá, {$usuario['nome']}!</h2>
                    <p>Clique abaixo para criar uma nova senha:</p>
                    <p>
                        <a href='$link' style='background-color: #c2410c; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>
                            REDEFINIR SENHA
                        </a>
                    </p>
                </div>
            ";

            $mail->send();
            $msg = "Link enviado com sucesso! Verifique seu e-mail.";
            $tipo_msg = "sucesso";

        } catch (Exception $e) {
            // Mostra o erro real se falhar
            $msg = "Erro no envio: " . $mail->ErrorInfo;
            $tipo_msg = "erro";
        }
    } else {
        $msg = "Se este e-mail estiver cadastrado, você receberá um link.";
        $tipo_msg = "sucesso";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha | Afroletrando</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-stone-100 h-screen flex flex-col items-center justify-center px-4">

    <div class="max-w-md w-full bg-white rounded-xl shadow-lg border border-stone-200 overflow-hidden">
        <div class="bg-stone-900 p-6 text-center">
            <h1 class="text-xl font-bold text-white">Recuperação de Conta</h1>
        </div>

        <div class="p-8">
            <?php if ($msg): ?>
                <div class="p-4 mb-6 rounded text-sm font-bold text-center <?php echo ($tipo_msg == 'sucesso') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-6">
                    <label class="block text-stone-600 text-sm font-bold mb-2">Digite seu e-mail cadastrado</label>
                    <input type="email" name="email" required class="w-full px-4 py-3 rounded-lg border border-stone-300 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <button type="submit" class="w-full bg-orange-700 hover:bg-orange-800 text-white font-bold py-3 rounded-lg transition">
                    ENVIAR LINK
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="login.php" class="text-sm text-stone-500 hover:text-orange-700 font-bold">Voltar para Login</a>
            </div>
        </div>
    </div>
</body>
</html>