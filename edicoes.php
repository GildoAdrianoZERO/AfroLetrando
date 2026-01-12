<?php
require_once 'conexao.php';
include 'includes/header.php';

// SISTEMA DE BUSCA
$busca = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

// Monta a consulta: Se tiver busca, filtra. Se não, traz tudo.
$sql = "SELECT * FROM edicoes ";
if ($busca) {
    $sql .= "WHERE titulo LIKE '%$busca%' OR descricao LIKE '%$busca%' ";
}
$sql .= "ORDER BY data_publicacao DESC";

$result = $conn->query($sql);
?>

<div class="bg-orange-900 py-16 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-black/20"></div>
    <div class="max-w-7xl mx-auto px-4 relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
        
        <div>
            <h1 class="text-3xl md:text-4xl font-serif font-bold mb-2">Acervo Digital</h1>
            <p class="text-orange-200">Explore nosso histórico completo de publicações.</p>
        </div>

        <div class="w-full md:w-auto">
            <form action="edicoes.php" method="GET" class="relative">
                <input type="text" name="q" value="<?php echo $busca; ?>" placeholder="Pesquisar por título, tema..." 
                       class="w-full md:w-80 pl-4 pr-12 py-3 rounded-lg text-stone-900 focus:outline-none focus:ring-4 focus:ring-orange-500/50 shadow-lg">
                <button type="submit" class="absolute right-2 top-2 text-stone-400 hover:text-orange-700 p-1 rounded-md hover:bg-stone-100 transition">
                    <i class="fas fa-search text-lg"></i>
                </button>
            </form>
        </div>

    </div>
</div>

<div class="bg-stone-50 min-h-screen py-16">
    <div class="max-w-7xl mx-auto px-4">

        <?php if ($busca): ?>
            <div class="mb-8 flex items-center justify-between">
                <p class="text-stone-500">Resultados para: <strong class="text-stone-900">"<?php echo $busca; ?>"</strong></p>
                <a href="edicoes.php" class="text-orange-700 text-sm font-bold hover:underline">Limpar filtro</a>
            </div>
        <?php endif; ?>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): 
                     $data_publicacao = date('d/m/Y', strtotime($row['data_publicacao']));
                ?>
                
                <div class="bg-white rounded-lg shadow-sm border border-stone-200 overflow-hidden hover:shadow-xl transition flex flex-col h-full group">
                    <div class="h-48 bg-stone-200 overflow-hidden relative">
                        <a href="ver_publicacao.php?id=<?php echo $row['id']; ?>">
                            <img src="<?php echo $row['imagem_capa']; ?>" alt="Capa" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        </a>
                    </div>
                    
                    <div class="p-5 flex flex-col flex-grow">
                        <span class="text-xs font-bold text-orange-700 uppercase mb-2">
                            <?php echo $row['volume']; ?>
                        </span>
                        
                        <h3 class="font-bold text-stone-900 leading-tight mb-2 line-clamp-2 font-serif text-lg">
                            <a href="ver_publicacao.php?id=<?php echo $row['id']; ?>" class="hover:text-orange-700">
                                <?php echo $row['titulo']; ?>
                            </a>
                        </h3>
                        
                        <div class="mt-auto pt-4 border-t border-stone-100 flex justify-between items-center text-xs text-stone-500">
                            <span><i class="far fa-calendar mr-1"></i> <?php echo date('Y', strtotime($row['data_publicacao'])); ?></span>
                            
                            <a href="ver_publicacao.php?id=<?php echo $row['id']; ?>" class="bg-stone-100 hover:bg-orange-100 text-stone-700 hover:text-orange-700 px-3 py-1 rounded font-bold transition">
                                Acessar
                            </a>
                        </div>
                    </div>
                </div>

                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-4 text-center py-20 bg-white rounded-lg border border-dashed border-stone-300">
                    <i class="fas fa-folder-open text-stone-300 text-5xl mb-4"></i>
                    <p class="text-stone-500 text-lg">Nenhuma publicação encontrada no acervo.</p>
                    <?php if ($busca): ?>
                        <a href="edicoes.php" class="text-orange-700 font-bold mt-2 inline-block">Ver tudo</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>