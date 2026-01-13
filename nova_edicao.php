<?php
session_start();
require_once 'conexao.php';

// SEGURANÇA
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $volume = $conn->real_escape_string($_POST['volume']);
    $data   = $_POST['data_publicacao'];
    $desc   = $conn->real_escape_string($_POST['descricao']);
    
    // 1. LÓGICA DO PDF (Link do Drive)
    $pdf = $conn->real_escape_string($_POST['link_pdf']);
    
    // 2. LÓGICA DA CAPA (Upload de Arquivo)
    $dir_capas = "uploads/capas/";
    
    // --- CORREÇÃO IMPORTANTE: Cria a pasta se não existir ---
    if (!is_dir($dir_capas)) {
        mkdir($dir_capas, 0755, true);
    }

    // Cria um nome único para a imagem
    if (!empty($_FILES["imagem_capa"]["name"])) {
        $ext = pathinfo($_FILES["imagem_capa"]["name"], PATHINFO_EXTENSION);
        $nome_capa = uniqid("capa_") . "." . $ext;
        $caminho_capa = $dir_capas . $nome_capa;
        
        // Tenta mover o arquivo
        if (move_uploaded_file($_FILES["imagem_capa"]["tmp_name"], $caminho_capa)) {
            
            // SUCESSO NO UPLOAD -> SALVA NO BANCO
            $autor_id = $_SESSION['usuario_id'];
            $sql = "INSERT INTO edicoes (titulo, volume, data_publicacao, descricao, imagem_capa, link_pdf, publicado_por) 
                    VALUES ('$titulo', '$volume', '$data', '$desc', '$caminho_capa', '$pdf', '$autor_id')";

            if ($conn->query($sql)) {
                // Registra no Log
                $conn->query("INSERT INTO logs (usuario_id, acao, descricao) VALUES ('$autor_id', 'CADASTRO', 'Cadastrou edição: $volume')");
                header("Location: admin.php?msg=sucesso");
                exit;
            } else {
                $mensagem = "Erro no Banco: " . $conn->error;
            }
        } else {
            $mensagem = "Erro ao fazer upload da imagem de capa. Verifique permissões.";
        }
    } else {
        $mensagem = "Por favor, selecione uma imagem de capa.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Nova Edição | Afroletrando</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap'); body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-stone-50 text-stone-900 py-8">
    
    <div class="max-w-3xl mx-auto px-4">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <h1 class="text-3xl font-bold text-stone-800 flex items-center">
                <i class="fas fa-plus-circle text-orange-600 mr-3"></i> Nova Publicação
            </h1>
            <a href="admin.php" class="w-full md:w-auto text-center bg-stone-600 text-white px-5 py-3 rounded-lg hover:bg-stone-700 font-bold shadow transition">
                <i class="fas fa-arrow-left mr-2"></i> Voltar
            </a>
        </div>

        <?php if ($mensagem): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded font-bold shadow-sm">
                <i class="fas fa-exclamation-triangle mr-2"></i> <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-lg border border-stone-200 p-6 md:p-8">
            <form method="POST" enctype="multipart/form-data">
                
                <div class="mb-6">
                    <label class="block text-stone-600 text-sm font-bold mb-2 uppercase tracking-wide">Título da Edição</label>
                    <input type="text" name="titulo" required class="w-full px-4 py-3 rounded-lg border border-stone-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" placeholder="Ex: Revista Afroletrando">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-stone-600 text-sm font-bold mb-2 uppercase tracking-wide">Volume / Edição</label>
                        <input type="text" name="volume" required class="w-full px-4 py-3 rounded-lg border border-stone-300 focus:ring-2 focus:ring-orange-500 outline-none transition" placeholder="Ex: Vol. 1, N. 2">
                    </div>
                    <div>
                        <label class="block text-stone-600 text-sm font-bold mb-2 uppercase tracking-wide">Data de Publicação</label>
                        <input type="date" name="data_publicacao" required class="w-full px-4 py-3 rounded-lg border border-stone-300 focus:ring-2 focus:ring-orange-500 outline-none transition">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-stone-600 text-sm font-bold mb-2 uppercase tracking-wide">Capa (Imagem)</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col w-full h-40 border-2 border-dashed border-stone-300 rounded-lg cursor-pointer hover:bg-stone-50 hover:border-orange-400 transition group">
                            <div class="flex flex-col items-center justify-center pt-7">
                                <i class="fas fa-cloud-upload-alt text-4xl text-stone-400 group-hover:text-orange-500 mb-3 transition"></i>
                                <p class="text-sm text-stone-500 group-hover:text-stone-700 font-medium">Clique para selecionar a imagem</p>
                                <p class="text-xs text-stone-400 mt-1">JPG, PNG ou WEBP</p>
                            </div>
                            <input type="file" name="imagem_capa" class="hidden" accept="image/*" required />
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-stone-600 text-sm font-bold mb-2 uppercase tracking-wide">
                        <i class="fab fa-google-drive text-green-600 mr-1"></i> Link do PDF
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-link text-stone-400"></i>
                        </div>
                        <input type="url" name="link_pdf" required 
                               class="w-full pl-10 pr-4 py-3 rounded-lg border border-stone-300 bg-blue-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition"
                               placeholder="Cole o link do Google Drive aqui...">
                    </div>
                    <p class="text-xs text-stone-400 mt-1">Certifique-se que o link está configurado como "Qualquer pessoa com o link pode ver".</p>
                </div>

                <div class="mb-8">
                    <label class="block text-stone-600 text-sm font-bold mb-2 uppercase tracking-wide">Resumo / Descrição</label>
                    <textarea name="descricao" rows="5" required class="w-full px-4 py-3 rounded-lg border border-stone-300 focus:ring-2 focus:ring-orange-500 outline-none transition" placeholder="Escreva um breve resumo desta edição..."></textarea>
                </div>

                <button type="submit" class="w-full bg-stone-900 hover:bg-orange-700 text-white font-bold py-4 rounded-lg shadow-lg transition text-lg flex justify-center items-center group">
                    <i class="fas fa-save mr-2 group-hover:scale-110 transition"></i> PUBLICAR EDIÇÃO
                </button>
            </form>
        </div>
    </div>
</body>
</html>