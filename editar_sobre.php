<?php
session_start();
require_once 'conexao.php';

// Verifica se é Admin
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }

// Busca os dados atuais
$result = $conn->query("SELECT * FROM sobre WHERE id = 1");
$dados = $result->fetch_assoc();

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $texto  = $conn->real_escape_string($_POST['texto']);
    $imagem = $dados['imagem']; // Mantém a antiga por padrão

    // Upload de Nova Imagem (Se houver)
    if (!empty($_FILES['nova_imagem']['name'])) {
        $dir = "uploads/capas/"; // Usando a mesma pasta de capas para facilitar
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        
        $nome_img = uniqid() . "_sobre_" . basename($_FILES["nova_imagem"]["name"]);
        
        if (move_uploaded_file($_FILES["nova_imagem"]["tmp_name"], $dir . $nome_img)) {
            $imagem = $dir . $nome_img;
        } else {
            $mensagem = "Erro ao subir imagem.";
        }
    }

    // Se não deu erro, atualiza
    if (empty($mensagem)) {
        $sql = "UPDATE sobre SET titulo = '$titulo', texto = '$texto', imagem = '$imagem' WHERE id = 1";
        if ($conn->query($sql)) {
            // Atualiza a variável $dados para mostrar o novo na tela
            $dados['titulo'] = $titulo;
            $dados['texto'] = $texto;
            $dados['imagem'] = $imagem;
            $mensagem = "Conteúdo atualizado com sucesso!";
        } else {
            $mensagem = "Erro no banco: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Sobre | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-stone-50 text-stone-900 py-10">
    <div class="max-w-4xl mx-auto px-4">
        
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-stone-800">Editar Seção "Sobre"</h1>
            <a href="admin.php" class="bg-stone-600 text-white px-4 py-2 rounded hover:bg-stone-700">Voltar</a>
        </div>

        <?php if ($mensagem): ?>
            <div class="bg-green-100 text-green-700 p-4 mb-6 rounded border-l-4 border-green-500">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-lg border border-stone-200 p-8">
            <form method="POST" enctype="multipart/form-data">
                
                <div class="mb-6">
                    <label class="block text-stone-600 font-bold mb-2">Título de Destaque</label>
                    <input type="text" name="titulo" value="<?php echo $dados['titulo']; ?>" class="w-full border p-3 rounded-lg">
                </div>

                <div class="mb-6">
                    <label class="block text-stone-600 font-bold mb-2">Texto de Apresentação</label>
                    <textarea name="texto" rows="6" class="w-full border p-3 rounded-lg"><?php echo $dados['texto']; ?></textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-stone-600 font-bold mb-2">Imagem Lateral</label>
                    <div class="flex items-center gap-4">
                        <img src="<?php echo $dados['imagem']; ?>" class="w-32 h-20 object-cover rounded bg-gray-200">
                        <input type="file" name="nova_imagem" accept="image/*" class="text-sm text-stone-500">
                    </div>
                </div>

                <button type="submit" class="bg-orange-700 hover:bg-orange-800 text-white font-bold py-3 px-6 rounded-lg shadow-lg w-full transition">
                    SALVAR ALTERAÇÕES
                </button>
            </form>
        </div>
    </div>
</body>
</html>