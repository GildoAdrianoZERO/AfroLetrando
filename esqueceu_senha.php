<?php
// Carrega o PHPMailer (instalado via Composer no Railway)
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'conexao.php';

$msg = '';
$tipo_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);

    // 1. Verifica se o e-mail existe
    $sql = "SELECT id, nome FROM usuarios WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        
        // 2. Gera um Token Único e Data de Validade (1 hora)
        $token = bin2hex(random_bytes(50));
        $validade = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // 3. Salva no Banco
        $conn->query("UPDATE usuarios SET token_recuperacao = '$token', token_validade = '$validade' WHERE email = '$email'");

        // 4. Configura o envio do E-mail
        $link = "https://" . $_SERVER['HTTP_HOST'] . "/redefinir_senha.php?token=" . $token;

        $mail = new PHPMailer(true);

        try {
            // --- CONFIGURAÇÃO DO EMAIL (PREENCHA AQUI OU USE VARIÁVEIS DE AMBIENTE) ---
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Se for Gmail
            $mail->SMTPAuth   = true;
            $mail->Username   = 'suporteafroletrando@gmail.com'; // SEU EMAIL
            $mail->Password   = 'jjej qbuq xcks rqqk';    // SUA SENHA DE APP (Google)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 465;

            // Remetente e Destinatário
            $mail->setFrom('suporteafroletrando@gmail.com', 'Suporte Afroletrando');
            $mail->addAddress($email, $usuario['nome']);

            // Conteúdo
            $mail->isHTML(true);
            $mail->Subject = 'Recuperar Senha - Afroletrando';
            $mail->Body    = "
                <h2>Olá, {$usuario['nome']}!</h2>
                <p>Recebemos um pedido para redefinir sua senha.</p>
                <p>Clique no link abaixo para criar uma nova senha:</p>
                <p><a href='$link' style='background:#c2410c; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>REDEFINIR SENHA</a></p>
                <p>Ou copie o link: $link</p>
                <p>Este link expira em 1 hora.</p>
            ";

            $mail->send();
            $msg = "Enviamos um link de recuperação para seu e-mail!";
            $tipo_msg = "sucesso";

        } catch (Exception $e) {
            $msg = "Erro ao enviar e-mail: {$mail->ErrorInfo}";
            $tipo_msg = "erro";
        }
    } else {
        // Por segurança, mostramos a mesma mensagem mesmo se o e-mail não existir
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-stone-100 h-screen flex items-center justify-center px-4">

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