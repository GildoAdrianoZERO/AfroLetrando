<?php
session_start();
require_once 'conexao.php';

// Verifica se é Admin (Segurança)
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }

// --- LÓGICA DE INICIALIZAÇÃO ---
// Verifica se já existe o registro ID 1. Se não, cria um padrão.
$check = $conn->query("SELECT * FROM sobre WHERE id = 1");
if ($check->num_rows == 0) {
    $conn->query("INSERT INTO sobre (id, titulo, texto, imagem) VALUES (1, 'Sobre Nós', 'Escreva aqui sua história...', '')");
}

// Agora busca os dados com segurança que existe
$result = $conn->query("SELECT * FROM sobre WHERE id = 1");
$dados = $result->fetch_assoc();

$mensagem = '';

// --- PROCESSAMENTO DO FORMULÁRIO ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $texto  = $conn->real_escape_string($_POST['texto']);
    $imagem = $dados['imagem']; // Mantém a antiga por padrão

    // Upload de Nova Imagem (Se houver)
    if (!empty($_FILES['nova_imagem']['name'])) {
        $dir = "uploads/sobre/"; // Pasta organizada
        
        // Cria pasta se não existir
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        
        $ext = pathinfo($_FILES["nova_imagem"]["name"], PATHINFO_EXTENSION);
        $nome_img = uniqid("sobre_") . "." . $ext;
        
        if (move_uploaded_file($_FILES["nova_imagem"]["tmp_name"], $dir . $nome_img)) {
            $imagem = $dir . $nome_img;
        } else {
            $mensagem = "Erro ao fazer upload da imagem. Verifique permissões.";
        }
    }

    // Se não deu erro de upload, atualiza o banco
    if (empty($mensagem)) {
        $sql = "UPDATE sobre SET titulo = '$titulo', texto = '$texto', imagem = '$imagem' WHERE id = 1";
        
        if ($conn->query($sql)) {
            // Atualiza a variável visual para mostrar a mudança na hora
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Editar Sobre | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap'); body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-stone-50 text-stone-900 py-8">
    
    <div class="max-w-4xl mx-auto px-4">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <h1 class="text-2xl md:text-3xl font-bold text-stone-800 flex items-center">
                <i class="fas fa-edit text-orange-600 mr-3"></i> Editar Seção "Sobre"
            </h1>
            <a href="admin.php" class="w-full md:w-auto text-center bg-stone-600 text-white px-5 py-2 rounded-lg hover:bg-stone-700 font-bold shadow transition">
                <i class="fas fa-arrow-left mr-2"></i> Voltar
            </a>
        </div>

        <?php if ($mensagem): ?>
            <div class="bg-green-100 text-green-700 p-4 mb-6 rounded-lg border-l-4 border-green-500 font-bold text-center">
                <i class="fas fa-check-circle mr-2"></i> <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-lg border border-stone-200 p-6 md:p-8">
            <form method="POST" enctype="multipart/form-data">
                
                <div class="mb-6">
                    <label class="block text-stone-600 font-bold mb-2 uppercase text-sm tracking-wide">Título de Destaque</label>
                    <input type="text" name="titulo" value="<?php echo htmlspecialchars($dados['titulo']); ?>" class="w-full border border-stone-300 p-3 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none transition">
                </div>

                <div class="mb-6">
                    <label class="block text-stone-600 font-bold mb-2 uppercase text-sm tracking-wide">Texto de Apresentação</label>
                    <textarea name="texto" rows="10" class="w-full border border-stone-300 p-3 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none transition text-base leading-relaxed"><?php echo htmlspecialchars($dados['texto']); ?></textarea>
                    <p class="text-xs text-stone-400 mt-1">Dica: Use parágrafos para facilitar a leitura.</p>
                </div>

                <div class="mb-8">
                    <label class="block text-stone-600 font-bold mb-2 uppercase text-sm tracking-wide">Imagem Lateral</label>
                    
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-6 bg-stone-50 p-4 rounded-lg border border-stone-200">
                        <div class="shrink-0">
                            <?php if (!empty($dados['imagem'])): ?>
                                <img src="<?php echo $dados['imagem']; ?>" class="w-full md:w-40 h-auto md:h-32 object-cover rounded-lg shadow-md border border-white">
                                <p class="text-xs text-center text-stone-400 mt-2">Imagem Atual</p>
                            <?php else: ?>
                                <div class="w-40 h-32 bg-stone-200 rounded-lg flex items-center justify-center text-stone-400">
                                    <i class="fas fa-image text-3xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="w-full">
                            <label class="block w-full cursor-pointer group">
                                <span class="sr-only">Escolher nova imagem</span>
                                <input type="file" name="nova_imagem" accept="image/*" class="block w-full text-sm text-stone-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-orange-50 file:text-orange-700
                                  hover:file:bg-orange-100
                                "/>
                            </label>
                            <p class="text-xs text-stone-400 mt-2">Formatos: JPG, PNG, WEBP. Deixe em branco para manter a atual.</p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="bg-orange-700 hover:bg-orange-800 text-white font-bold py-4 px-6 rounded-lg shadow-lg w-full transition text-lg flex justify-center items-center group">
                    <i class="fas fa-save mr-2 group-hover:scale-110 transition"></i> SALVAR ALTERAÇÕES
                </button>
            </form>
        </div>
    </div>
</body>
</html>