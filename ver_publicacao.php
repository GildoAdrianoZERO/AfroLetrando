<?php
require_once 'conexao.php';

// Verifica se veio um ID na URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM edicoes WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit;
}

$revista = $result->fetch_assoc();
$data_publicacao = date('d/m/Y', strtotime($revista['data_publicacao']));

// --- TRATAMENTO DO LINK PARA LEITURA ONLINE ---
$link_original = $revista['link_pdf'];
$link_embed = $link_original;

// Se for Google Drive, ajusta para modo "Preview" (Embutido)
if (strpos($link_original, 'drive.google.com') !== false) {
    // Troca /view por /preview para permitir o iframe
    $link_embed = str_replace('/view', '/preview', $link_original);
    
    // Garantia extra: se o link estiver em outro formato, tenta extrair o ID
    if (strpos($link_embed, '/preview') === false) {
        preg_match('/\/d\/(.*?)\//', $link_original, $match);
        if (isset($match[1])) {
            $link_embed = "https://drive.google.com/file/d/" . $match[1] . "/preview";
        }
    }
}

include 'includes/header.php';
?>

<div class="bg-stone-50 min-h-screen pb-20">
    
    <div class="bg-white border-b border-stone-200 py-12">
        <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-12 gap-10 items-start">
            
            <div class="md:col-span-3">
                <div class="rounded-lg shadow-xl overflow-hidden border border-stone-100 bg-stone-200 aspect-[3/4]">
                    <img src="<?php echo $revista['imagem_capa']; ?>" alt="Capa" class="w-full h-full object-cover">
                </div>
            </div>

            <div class="md:col-span-9">
                <a href="index.php" class="text-stone-500 hover:text-orange-700 font-bold text-sm mb-4 inline-block">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar ao Acervo
                </a>
                
                <div class="flex items-center space-x-2 text-sm font-bold text-orange-700 uppercase tracking-widest mb-2">
                    <span><?php echo $revista['volume']; ?></span>
                    <span class="text-stone-300">•</span>
                    <span><?php echo $data_publicacao; ?></span>
                </div>

                <h1 class="text-3xl md:text-5xl font-serif font-bold text-stone-900 mb-6 leading-tight">
                    <?php echo $revista['titulo']; ?>
                </h1>

                <div class="prose max-w-none text-stone-600 mb-8 leading-relaxed">
                    <h3 class="font-bold text-stone-800 text-lg">Resumo</h3>
                    <p><?php echo nl2br($revista['descricao']); ?></p>
                </div>

                <a href="<?php echo $link_original; ?>" target="_blank" class="inline-flex items-center text-stone-900 font-bold border border-stone-300 px-6 py-3 rounded hover:bg-stone-100 transition">
                    <i class="fas fa-download mr-2"></i> Baixar PDF Original
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 mt-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-stone-800 flex items-center">
                <i class="fas fa-book-reader mr-3 text-orange-700"></i> Leitor Online
            </h2>
            <span class="text-sm text-stone-400 hidden md:inline"><i class="fas fa-mouse mr-1"></i> Role para ler</span>
        </div>

        <div class="bg-stone-800 rounded-xl shadow-2xl overflow-hidden border border-stone-700 h-[85vh]">
            <iframe src="<?php echo $link_embed; ?>" 
                    width="100%" 
                    height="100%" 
                    allow="autoplay" 
                    class="w-full h-full">
            </iframe>
        </div>
        
        <p class="text-center text-stone-400 text-sm mt-4">
            Não consegue visualizar o leitor? <a href="<?php echo $link_original; ?>" target="_blank" class="text-orange-700 font-bold underline">Clique aqui para abrir o PDF</a>.
        </p>
    </div>

</div>

<?php include 'includes/footer.php'; ?>