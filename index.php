<?php
require_once 'conexao.php';
include 'includes/header.php';

// 1. BUSCA O CONTEÚDO DO "SOBRE" NO BANCO
// Pega o ID 1, que é o que editamos no painel
$sql_sobre = "SELECT * FROM sobre WHERE id = 1";
$res_sobre = $conn->query($sql_sobre);

// Se não tiver nada no banco (primeiro acesso), cria um array vazio para não dar erro
if ($res_sobre->num_rows > 0) {
    $sobre = $res_sobre->fetch_assoc();
} else {
    $sobre = [
        'titulo' => 'Título Padrão',
        'texto' => 'O conteúdo ainda não foi definido no painel administrativo.',
        'imagem' => 'https://via.placeholder.com/800x600'
    ];
}

// 2. BUSCA AS REVISTAS (EDIÇÕES)
// Limitei a 6 para não ficar gigante na home, mas você pode tirar o LIMIT se quiser todas
$sql_edicoes = "SELECT * FROM edicoes ORDER BY data_publicacao DESC LIMIT 6";
$result = $conn->query($sql_edicoes);
?>

<header class="bg-stone-900 text-white py-24 relative overflow-hidden">
    <div class="absolute inset-0 bg-orange-900/20 mix-blend-multiply"></div>
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
    
    <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">
        <span class="text-orange-500 font-bold tracking-widest uppercase text-sm mb-4 block">ISSN 2596-1234</span>
        <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight">Vozes da <span class="text-orange-500 italic">Diáspora</span></h1>
        <p class="text-xl text-stone-300 max-w-2xl mx-auto font-light leading-relaxed">
            Um espaço interdisciplinar dedicado à produção acadêmica, cultural e identitária das africanidades.
        </p>
        <div class="mt-10">
            <a href="#edicoes" class="bg-orange-700 hover:bg-orange-800 text-white px-8 py-4 rounded-lg font-bold transition shadow-lg inline-flex items-center">
                LER ÚLTIMA EDIÇÃO <i class="fas fa-arrow-down ml-2"></i>
            </a>
        </div>
    </div>
</header>

<section id="sobre" class="bg-stone-50 py-20 border-b border-stone-200">
    <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-16 items-center">
        
        <div>
            <h2 class="text-4xl text-stone-900 mb-6 italic font-serif">
                "<?php echo $sobre['titulo']; ?>"
            </h2>
            
            <div class="text-stone-600 text-lg leading-relaxed mb-8 font-light prose">
                <?php echo nl2br($sobre['texto']); ?>
            </div>
            
            <div class="grid grid-cols-2 gap-6 mb-8">
                <div class="border-l-2 border-orange-600 pl-4">
                    <span class="block text-3xl font-bold font-serif text-stone-900">A1</span>
                    <span class="text-stone-500 text-sm uppercase tracking-widest">Qualis Previsto</span>
                </div>
                <div class="border-l-2 border-orange-600 pl-4">
                    <span class="block text-3xl font-bold font-serif text-stone-900">Semestral</span>
                    <span class="text-stone-500 text-sm uppercase tracking-widest">Periodicidade</span>
                </div>
            </div>

            <a href="sobre.php" class="text-orange-700 font-bold hover:text-orange-900 border-b-2 border-orange-200 hover:border-orange-700 transition pb-1">
                Conheça nosso Corpo Editorial
            </a>
        </div>

        <div class="relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-orange-600 to-amber-600 rounded-lg blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative">
                <img src="<?php echo $sobre['imagem']; ?>" 
                     alt="Sobre a Revista" 
                     class="rounded-lg shadow-2xl w-full object-cover h-96 transform transition duration-500 hover:scale-[1.01]">
                
                <div class="absolute -top-6 -left-6 bg-stone-900 p-6 rounded-lg hidden lg:block shadow-lg">
                    <p class="font-bold text-xl tracking-tighter text-white">ISSN: 2596-1234</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="edicoes" class="py-20 max-w-7xl mx-auto px-4 bg-white">
    <div class="flex flex-col md:flex-row justify-between items-end mb-12">
        <div>
            <h2 class="text-4xl text-stone-800 font-serif font-bold">Últimas Publicações</h2>
            <p class="text-stone-500 mt-2">Explore os volumes mais recentes da nossa jornada acadêmica.</p>
        </div>
        <a href="edicoes.php" class="text-orange-800 font-bold border-b-2 border-orange-800 pb-1 mt-4 md:mt-0 hover:text-orange-600 transition">
            Ver acervo completo <i class="fas fa-arrow-right ml-1 text-xs"></i>
        </a>
    </div>

    <div class="grid md:grid-cols-3 gap-8">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                
                $data_publicacao = date('d/m/Y', strtotime($row['data_publicacao']));
                
                // Limita o tamanho do texto do resumo para não quebrar o card
                if (function_exists('mb_strimwidth')) {
                    $descricao = mb_strimwidth($row['descricao'], 0, 110, "...");
                } else {
                    $descricao = substr($row['descricao'], 0, 110) . "...";
                }
        ?>
        
        <div class="bg-white border border-stone-200 group overflow-hidden hover:shadow-2xl transition duration-500 flex flex-col h-full rounded-lg">
            <div class="h-64 bg-stone-200 overflow-hidden relative">
                <a href="ver_publicacao.php?id=<?php echo $row['id']; ?>">
                    <img src="<?php echo $row['imagem_capa']; ?>" 
                         alt="Capa da Revista <?php echo $row['titulo']; ?>" 
                         class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                </a>
            </div>
            
            <div class="p-6 flex flex-col flex-grow">
                <span class="text-orange-700 text-xs font-bold uppercase tracking-wider">
                    <?php echo $row['volume']; ?> | <?php echo $data_publicacao; ?>
                </span>
                
                <h3 class="text-2xl mt-2 mb-4 group-hover:text-orange-800 transition font-serif leading-tight">
                    <a href="ver_publicacao.php?id=<?php echo $row['id']; ?>" class="text-stone-900 hover:text-orange-800">
                        <?php echo $row['titulo']; ?>
                    </a>
                </h3>
                
                <p class="text-stone-600 text-sm mb-6 leading-relaxed flex-grow">
                    <?php echo $descricao; ?>
                </p>
                
                <a href="ver_publicacao.php?id=<?php echo $row['id']; ?>" class="inline-flex items-center text-sm font-bold text-stone-800 hover:text-orange-700 hover:gap-2 transition-all mt-auto">
                    VER DETALHES <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
        
        <?php 
            } 
        } else {
            // Mensagem caso não tenha nenhuma revista cadastrada
            echo '<div class="col-span-3 text-center py-20 text-stone-500 bg-stone-50 rounded-lg border border-dashed border-stone-300">';
            echo '<i class="fa-solid fa-book-open text-4xl mb-3 text-stone-300"></i>';
            echo '<p>Nenhuma edição publicada no momento.</p>';
            echo '</div>';
        }
        ?>
    </div>
</section>

<?php
include 'includes/footer.php';
?>