<?php
session_start();
require_once 'conexao.php';

// SEGURANÇA
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }

// BUSCA DADOS
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
    
    // 1. PDF (Link) - Pega o que vier no post
    $pdf = $conn->real_escape_string($_POST['link_pdf']);
    
    // 2. CAPA (Upload Inteligente)
    $caminho_capa = $dados['imagem_capa']; // Começa com a antiga
    
    // Se enviou uma nova imagem
    if (!empty($_FILES['imagem_capa']['name'])) {
        $dir_capas = "uploads/capas/";
        $nome_capa = uniqid() . "_" . basename($_FILES["imagem_capa"]["name"]);
        
        if (move_uploaded_file($_FILES["imagem_capa"]["tmp_name"], $dir_capas . $nome_capa)) {
            $caminho_capa = $dir_capas . $nome_capa; // Atualiza caminho
        } else {
            $mensagem = "Erro ao subir a nova capa.";
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
            $conn->query("INSERT INTO logs (usuario_id, acao, descricao) VALUES ('$usuario_id', 'EDICAO', 'Editou: $titulo')");
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
    <title>Editar Edição | Afroletrando</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-stone-50 text-stone-900 py-10">

    <div class="max-w-3xl mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-stone-800">Editar Edição</h1>
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
                    <input type="text" name="titulo" required value="<?php echo $dados['titulo']; ?>" class="w-full px-4 py-3 rounded-lg border border-stone-300">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-stone-600 text-sm font-bold mb-2">Volume</label>
                        <input type="text" name="volume" required value="<?php echo $dados['volume']; ?>" class="w-full px-4 py-3 rounded-lg border border-stone-300">
                    </div>
                    <div>
                        <label class="block text-stone-600 text-sm font-bold mb-2">Data</label>
                        <input type="date" name="data_publicacao" required value="<?php echo $dados['data_publicacao']; ?>" class="w-full px-4 py-3 rounded-lg border border-stone-300">
                    </div>
                </div>

                <div class="mb-6 p-4 bg-stone-50 rounded-lg border border-stone-200">
                    <label class="block text-stone-600 text-sm font-bold mb-2">Imagem de Capa</label>
                    <div class="flex items-center mb-4">
                        <img src="<?php echo $dados['imagem_capa']; ?>" class="h-20 w-16 object-cover rounded shadow mr-4 bg-gray-200">
                        <div>
                            <p class="text-xs text-stone-500 font-bold mb-1">Trocar Capa (Upload):</p>
                            <input type="file" name="imagem_capa" accept="image/*" class="text-sm text-stone-500"/>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-stone-600 text-sm font-bold mb-2"><i class="fab fa-google-drive text-green-600 mr-1"></i> Link PDF (Drive)</label>
                    <input type="url" name="link_pdf" required value="<?php echo $dados['link_pdf']; ?>" 
                           class="w-full px-4 py-3 rounded-lg border border-stone-300 bg-blue-50">
                </div>

                <div class="mb-8">
                    <label class="block text-stone-600 text-sm font-bold mb-2">Resumo</label>
                    <textarea name="descricao" rows="5" required class="w-full px-4 py-3 rounded-lg border border-stone-300"><?php echo $dados['descricao']; ?></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-4 rounded-lg shadow-lg transition">
                    <i class="fas fa-save mr-2"></i> SALVAR ALTERAÇÕES
                </button>
            </form>
        </div>
    </div>
</body>
</html>