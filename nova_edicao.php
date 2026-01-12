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
    // Cria um nome único para a imagem
    $nome_capa = uniqid() . "_" . basename($_FILES["imagem_capa"]["name"]);
    $caminho_capa = $dir_capas . $nome_capa;
    
    // Tenta mover o arquivo
    if (move_uploaded_file($_FILES["imagem_capa"]["tmp_name"], $caminho_capa)) {
        
        // SUCESSO NO UPLOAD -> SALVA NO BANCO
        $autor_id = $_SESSION['usuario_id'];
        $sql = "INSERT INTO edicoes (titulo, volume, data_publicacao, descricao, imagem_capa, link_pdf, publicado_por) 
                VALUES ('$titulo', '$volume', '$data', '$desc', '$caminho_capa', '$pdf', '$autor_id')";

        if ($conn->query($sql)) {
            $conn->query("INSERT INTO logs (usuario_id, acao, descricao) VALUES ('$autor_id', 'CADASTRO', 'Cadastrou: $titulo')");
            header("Location: admin.php?msg=sucesso");
            exit;
        } else {
            $mensagem = "Erro no Banco: " . $conn->error;
        }
    } else {
        $mensagem = "Erro ao fazer upload da imagem de capa.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Nova Edição | Afroletrando</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-stone-50 text-stone-900 py-10">
    <div class="max-w-3xl mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-stone-800">Nova Publicação</h1>
            <a href="admin.php" class="text-stone-500 hover:text-orange-700 font-medium transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Voltar
            </a>
        </div>

        <?php if ($mensagem): ?>
            <div class="bg-red-100 text-red-700 p-4 mb-6 rounded"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-lg border border-stone-200 p-8">
            <form method="POST" enctype="multipart/form-data">
                
                <div class="mb-6">
                    <label class="block text-stone-600 text-sm font-bold mb-2">Título</label>
                    <input type="text" name="titulo" required class="w-full px-4 py-3 rounded-lg border border-stone-300">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-stone-600 text-sm font-bold mb-2">Volume</label>
                        <input type="text" name="volume" required class="w-full px-4 py-3 rounded-lg border border-stone-300">
                    </div>
                    <div>
                        <label class="block text-stone-600 text-sm font-bold mb-2">Data</label>
                        <input type="date" name="data_publicacao" required class="w-full px-4 py-3 rounded-lg border border-stone-300">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-stone-600 text-sm font-bold mb-2">Capa (Upload de Imagem)</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col w-full h-32 border-4 border-dashed hover:bg-gray-100 hover:border-orange-300 group cursor-pointer">
                            <div class="flex flex-col items-center justify-center pt-7">
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 group-hover:text-orange-500"></i>
                                <p class="pt-1 text-sm text-gray-400 group-hover:text-orange-500">Clique para escolher a foto</p>
                            </div>
                            <input type="file" name="imagem_capa" class="hidden" accept="image/*" required />
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-stone-600 text-sm font-bold mb-2"><i class="fab fa-google-drive text-green-600 mr-1"></i> Link do PDF (Google Drive)</label>
                    <input type="url" name="link_pdf" required 
                           class="w-full px-4 py-3 rounded-lg border border-stone-300 focus:outline-none focus:border-orange-500 bg-blue-50"
                           placeholder="Cole o link do PDF aqui...">
                </div>

                <div class="mb-8">
                    <label class="block text-stone-600 text-sm font-bold mb-2">Resumo</label>
                    <textarea name="descricao" rows="5" required class="w-full px-4 py-3 rounded-lg border border-stone-300"></textarea>
                </div>

                <button type="submit" class="w-full bg-orange-700 hover:bg-orange-800 text-white font-bold py-4 rounded-lg shadow-lg transition">
                    <i class="fas fa-save mr-2"></i> PUBLICAR
                </button>
            </form>
        </div>
    </div>
</body>
</html>