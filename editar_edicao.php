<?php
session_start();
require_once 'conexao.php';

// SEGURANÇA
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }

// BUSCA DADOS DA EDIÇÃO
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM edicoes WHERE id = $id");
    $dados = $result->fetch_assoc();
    if (!$dados) { header("Location: admin.php"); exit; }
} else { header("Location: admin.php"); exit; }

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $volume = $conn->real_escape_string($_POST['volume']);
    $data   = $_POST['data_publicacao'];
    $desc   = $conn->real_escape_string($_POST['descricao']);
    
    // 1. PDF (Link)
    $pdf = $conn->real_escape_string($_POST['link_pdf']);
    
    // 2. CAPA (Upload Inteligente)
    $caminho_capa = $dados['imagem_capa']; // Começa com a antiga
    
    // Se enviou uma nova imagem
    if (!empty($_FILES['imagem_capa']['name'])) {
        $dir_capas = "uploads/capas/";
        
        // --- CORREÇÃO: Cria a pasta se não existir ---
        if (!is_dir($dir_capas)) {
            mkdir($dir_capas, 0755, true);
        }

        $ext = pathinfo($_FILES["imagem_capa"]["name"], PATHINFO_EXTENSION);
        $nome_capa = uniqid("capa_") . "." . $ext; // Nome único mais limpo
        
        if (move_uploaded_file($_FILES["imagem_capa"]["tmp_name"], $dir_capas . $nome_capa)) {
            $caminho_capa = $dir_capas . $nome_capa; // Atualiza caminho
        } else {
            $mensagem = "Erro ao subir a nova capa. Verifique permissões.";
        }
    }

    // Só atualiza se não deu erro no upload
    if (empty($mensagem)) {
        $sql_update = "UPDATE edicoes SET 
                       titulo = '$titulo', volume = '$volume', data_publicacao = '$data', 
                       descricao = '$desc', imagem_capa = '$caminho_capa', link_pdf = '$pdf' 
                       WHERE id = $id";

        if ($conn->query($sql_update)) {
            $usuario_id = $_SESSION['usuario_id'];
            // Log simplificado
            $conn->query("INSERT INTO logs (usuario_id, acao, descricao) VALUES ('$usuario_id', 'EDICAO', 'Editou edição ID: $id')");
            header("Location: admin.php?msg=editado");
            exit;
        } else {
            $mensagem = "Erro ao atualizar: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Editar Edição | Afroletrando</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap'); body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-stone-50 text-stone-900 py-8">

    <div class="max-w-3xl mx-auto px-4">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <h1 class="text-2xl md:text-3xl font-bold text-stone-800 flex items-center">
                <i class="fas fa-edit text-orange-600 mr-3"></i> Editar Edição
            </h1>
            <a href="admin.php" class="w-full md:w-auto text-center bg-stone-600 text-white px-5 py-2 rounded-lg hover:bg-stone-700 font-bold shadow transition">
                <i class="fas fa-arrow-left mr-2"></i> Voltar
            </a>
        </div>

        <?php if ($mensagem): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded font-bold">
                <i class="fas fa-exclamation-triangle mr-2"></i> <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-lg border border-stone-200 p-6 md:p-8">
            <form method="POST" enctype="multipart/form-data">
                
                <div class="mb-6">
                    <label class="block text-stone-600 text-sm font-bold mb-2 uppercase tracking-wide">Título</label>
                    <input type="text" name="titulo" required value="<?php echo htmlspecialchars($dados['titulo']); ?>" class="w-full px-4 py-3 rounded-lg border border-stone-300 focus:ring-2 focus:ring-orange-500 outline-none transition">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-stone-600 text-sm font-bold mb-2 uppercase tracking-wide">Volume</label>
                        <input type="text" name="volume" required value="<?php echo htmlspecialchars($dados['volume']); ?>" class="w-full px-4 py-3 rounded-lg border border-stone-300 focus:ring-2 focus:ring-orange-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-stone-600 text-sm font-bold mb-2 uppercase tracking-wide">Data</label>
                        <input type="date" name="data_publicacao" required value="<?php echo $dados['data_publicacao']; ?>" class="w-full px-4 py-3 rounded-lg border border-stone-300 focus:ring-2 focus:ring-orange-500 outline-none transition">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-stone-600 text-sm font-bold mb-2 uppercase tracking-wide">Imagem de Capa</label>
                    
                    <div class="flex flex-col md:flex-row items-center gap-6 bg-stone-50 p-4 rounded-lg border border-stone-200">
                        <div class="shrink-0 text-center">
                            <img src="<?php echo $dados['imagem_capa']; ?>" class="h-32 w-24 object-cover rounded shadow-md border border-white mx-auto">
                            <p class="text-xs text-stone-400 mt-2 font-bold">Capa Atual</p>
                        </div>

                        <div class="w-full">
                            <label class="block w-full cursor-pointer group">
                                <span class="sr-only">Escolher nova capa</span>
                                <input type="file" name="imagem_capa" accept="image/*" class="block w-full text-sm text-stone-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-orange-50 file:text-orange-700
                                  hover:file:bg-orange-100
                                "/>
                            </label>
                            <p class="text-xs text-stone-400 mt-2">Selecione apenas se quiser trocar a imagem atual.</p>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-stone-600 text-sm font-bold mb-2 uppercase tracking-wide">
                        <i class="fab fa-google-drive text-green-600 mr-1"></i> Link PDF (Drive)
                    </label>
                    <input type="url" name="link_pdf" required value="<?php echo htmlspecialchars($dados['link_pdf']); ?>" 
                           class="w-full px-4 py-3 rounded-lg border border-stone-300 bg-blue-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>

                <div class="mb-8">
                    <label class="block text-stone-600 text-sm font-bold mb-2 uppercase tracking-wide">Resumo</label>
                    <textarea name="descricao" rows="5" required class="w-full px-4 py-3 rounded-lg border border-stone-300 focus:ring-2 focus:ring-orange-500 outline-none transition"><?php echo htmlspecialchars($dados['descricao']); ?></textarea>
                </div>

                <button type="submit" class="w-full bg-stone-900 hover:bg-orange-700 text-white font-bold py-4 rounded-lg shadow-lg transition text-lg flex justify-center items-center group">
                    <i class="fas fa-save mr-2 group-hover:scale-110 transition"></i> SALVAR ALTERAÇÕES
                </button>
            </form>
        </div>
    </div>
</body>
</html>