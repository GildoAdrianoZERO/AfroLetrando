<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'conexao.php';

$msg = '';
$tipo_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);

    $sql = "SELECT id, nome FROM usuarios WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        
        $token = bin2hex(random_bytes(50));
        $validade = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $conn->query("UPDATE usuarios SET token_recuperacao = '$token', token_validade = '$validade' WHERE email = '$email'");

        // Link dinâmico (SSL ou não)
        $protocolo = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $link = $protocolo . "://" . $host . "/redefinir_senha.php?token=" . $token;

        $mail = new PHPMailer(true);

        try {
            // --- MODO DE DEBUG (ATIVADO) ---
            // Isso vai mostrar o erro na tela em vez de travar
            $mail->SMTPDebug = 2; 
            $mail->Debugoutput = 'html';

            // --- CONFIGURAÇÃO BLINDADA (Porta 465) ---
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'suporteafroletrando@gmail.com';
            $mail->Password   = 'jjej qbuq xcks rqqk'; // Sua senha de App
            
            // MUDANÇA IMPORTANTE: Usar SSL na porta 465
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
            $mail->Port       = 465;

            // Remetente e Destinatário
            $mail->setFrom('suporteafroletrando@gmail.com', 'Afroletrando Suporte');
            $mail->addAddress($email, $usuario['nome']);

            // Conteúdo
            $mail->isHTML(true);
            $mail->Subject = 'Recuperar Senha - Afroletrando';
            $mail->Body    = "
                <h2>Olá, {$usuario['nome']}!</h2>
                <p>Clique abaixo para redefinir sua senha:</p>
                <p><a href='$link'>REDEFINIR MINHA SENHA</a></p>
            ";

            $mail->send();
            $msg = "Link enviado com sucesso!";
            $tipo_msg = "sucesso";

        } catch (Exception $e) {
            // O erro técnico vai aparecer na tela por causa do SMTPDebug
            $msg = "Erro ao enviar (veja o log acima).";
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
    <title>Recuperar Senha | Debug</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-stone-100 h-screen flex flex-col items-center justify-center px-4">

    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8">
        <h1 class="text-xl font-bold mb-4 text-center">Recuperação (Modo Debug)</h1>
        
        <?php if ($msg): ?>
            <div class="p-4 mb-4 rounded text-center <?php echo ($tipo_msg == 'sucesso') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label class="block mb-2 font-bold text-stone-600">E-mail</label>
            <input type="email" name="email" required class="w-full mb-4 p-3 border rounded border-stone-300">
            <button type="submit" class="w-full bg-orange-700 text-white font-bold p-3 rounded">ENVIAR AGORA</button>
        </form>
        
        <div class="mt-4 text-center">
            <a href="login.php" class="text-sm text-stone-500 underline">Voltar</a>
        </div>
    </div>

</body>
</html>