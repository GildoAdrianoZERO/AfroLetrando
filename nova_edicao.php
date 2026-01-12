<?php
session_start();
require_once 'conexao.php';

// 1. SEGURANÇA: Se não estiver logado, manda pro login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$mensagem = '';
$tipo_msg = '';

// 2. PROCESSAMENTO: Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pegando os dados e limpando caracteres perigosos
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $volume = $conn->real_escape_string($_POST['volume']);
    $data   = $_POST['data_publicacao'];
    $desc   = $conn->real_escape_string($_POST['descricao']);
    
    // Por enquanto estamos usando Links (URLs) para facilitar
    $capa   = $conn->real_escape_string($_POST['imagem_capa']);
    $pdf    = $conn->real_escape_string($_POST['link_pdf']);
    
    $autor_id = $_SESSION['usuario_id'];

    // Inserindo no Banco
    $sql = "INSERT INTO edicoes (titulo, volume, data_publicacao, descricao, imagem_capa, link_pdf, publicado_por) 
            VALUES ('$titulo', '$volume', '$data', '$desc', '$capa', '$pdf', '$autor_id')";

    if ($conn->query($sql)) {
        // Redireciona para o admin com mensagem de sucesso
        header("Location: admin.php?msg=sucesso");
        exit;
    } else {
        $mensagem = "Erro ao cadastrar: " . $conn->error;
        $tipo_msg = "erro";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Edição | Afroletrando</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-stone-50 text-stone-900 py-10">

    <div class="max-w-3xl mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-stone-800">Nova Publicação</h1>
                <p class="text-stone-500 text-sm">Preencha os dados da nova revista.</p>
            </div>
            <a href="admin.php" class="text-stone-500 hover:text-orange-700 font-medium transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Voltar ao Painel
            </a>
        </div>

        <?php if ($mensagem): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                <p class="font-bold">Atenção:</p>
                <p><?php echo $mensagem; ?></p>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-lg border border-stone-200 overflow-hidden">
            <div class="bg-stone-100 px-8 py-4 border-b border-stone-200 flex items-center">
                <i class="fas fa-pen-nib text-orange-700 mr-3"></i>
                <h2 class="font-bold text-stone-700">Dados da Edição</h2>
            </div>
            
            <form method="POST" action="" class="p-8">
                
                <div class="mb-6">
                    <label class="block text-stone-600 text-sm font-bold mb-2">Título do Dossiê / Revista</label>
                    <input type="text" name="titulo" required 
                           class="w-full px-4 py-3 rounded-lg border border-stone-300 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition"
                           placeholder="Ex: Literatura e Resistência">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-stone-600 text-sm font-bold mb-2">Volume</label>
                        <input type="text" name="volume" required 
                               class="w-full px-4 py-3 rounded-lg border border-stone-300 focus:outline-none focus:border-orange-500 transition"
                               placeholder="Ex: Vol. 12">
                    </div>
                    <div>
                        <label class="block text-stone-600 text-sm font-bold mb-2">Data de Publicação</label>
                        <input type="date" name="data_publicacao" required 
                               class="w-full px-4 py-3 rounded-lg border border-stone-300 focus:outline-none focus:border-orange-500 transition">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-stone-600 text-sm font-bold mb-2">Link da Imagem de Capa (URL)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400">
                            <i class="fas fa-image"></i>
                        </span>
                        <input type="url" name="imagem_capa" required 
                               class="w-full pl-10 pr-4 py-3 rounded-lg border border-stone-300 focus:outline-none focus:border-orange-500 transition"
                               placeholder="https://site.com/imagem.jpg">
                    </div>
                    <p class="text-xs text-stone-400 mt-1">Copie o endereço da imagem (Unsplash, Google Drive público, etc).</p>
                </div>

                <div class="mb-6">
                    <label class="block text-stone-600 text-sm font-bold mb-2">Link do PDF / Acesso</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400">
                            <i class="fas fa-link"></i>
                        </span>
                        <input type="text" name="link_pdf" 
                               class="w-full pl-10 pr-4 py-3 rounded-lg border border-stone-300 focus:outline-none focus:border-orange-500 transition"
                               placeholder="#">
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-stone-600 text-sm font-bold mb-2">Resumo / Descrição</label>
                    <textarea name="descricao" rows="5" required 
                              class="w-full px-4 py-3 rounded-lg border border-stone-300 focus:outline-none focus:border-orange-500 transition"
                              placeholder="Escreva um breve resumo sobre o tema desta edição..."></textarea>
                </div>

                <button type="submit" class="w-full bg-orange-700 hover:bg-orange-800 text-white font-bold py-4 rounded-lg shadow-lg transition duration-300 transform hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i> PUBLICAR NO SITE
                </button>

            </form>
        </div>
    </div>

</body>
</html>