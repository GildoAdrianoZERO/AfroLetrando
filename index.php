<?php
require_once 'conexao.php';
include 'includes/header.php';
?>

<section id="edicoes" class="py-20 max-w-7xl mx-auto px-4">
    <div class="flex flex-col md:flex-row justify-between items-end mb-12">
        <div>
            <h2 class="text-4xl">Últimas Publicações</h2>
            <p class="text-stone-500 mt-2">Explore os volumes mais recentes da nossa jornada acadêmica.</p>
        </div>
        <a href="#" class="text-orange-800 font-bold border-b-2 border-orange-800 pb-1 mt-4 md:mt-0">Ver acervo completo</a>
    </div>

    <div class="grid md:grid-cols-3 gap-8">
        <?php
        $sql = "SELECT * FROM edicoes ORDER BY data_publicacao DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                
                $data_publicacao = date('d/m/Y', strtotime($row['data_publicacao']));
                
                $descricao = mb_strimwidth($row['descricao'], 0, 110, "...");
        ?>
        
        <div class="bg-white border border-stone-200 group overflow-hidden hover:shadow-2xl transition duration-500 flex flex-col h-full">
            <div class="h-64 bg-stone-200 overflow-hidden relative">
                <img src="<?php echo $row['imagem_capa']; ?>" 
                     alt="Capa da Revista <?php echo $row['titulo']; ?>" 
                     class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
            </div>
            
            <div class="p-6 flex flex-col flex-grow">
                <span class="text-orange-700 text-xs font-bold uppercase">
                    <?php echo $row['volume']; ?> | <?php echo $data_publicacao; ?>
                </span>
                
                <h3 class="text-2xl mt-2 mb-4 group-hover:text-orange-800 transition">
                    <?php echo $row['titulo']; ?>
                </h3>
                
                <p class="text-stone-600 text-sm mb-6 leading-relaxed flex-grow">
                    <?php echo $descricao; ?>
                </p>
                
                <a href="<?php echo $row['link_pdf']; ?>" class="inline-flex items-center text-sm font-bold text-stone-800 hover:gap-2 transition-all mt-auto">
                    LER EDIÇÃO <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
        <?php 
            } 
        } else {
            
            echo '<div class="col-span-3 text-center py-10 text-stone-500 bg-stone-100 rounded-lg border border-stone-200">';
            echo '<i class="fa-solid fa-book-open text-4xl mb-3 text-stone-400"></i>';
            echo '<p>Nenhuma edição publicada no momento.</p>';
            echo '</div>';
        }
        ?>
    </div>
</section>

<section id="sobre" class="bg-stone-900 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-16 items-center">
        <div>
            <h2 class="text-4xl mb-6 italic">"A palavra é uma semente que germina consciência."</h2>
            <p class="text-stone-400 text-lg leading-relaxed mb-8">
                A Revista Afroletrando é um periódico interdisciplinar dedicado a pesquisadores que buscam visibilidade para estudos que intersectam raça, gênero, linguagem e sociedade. Nosso compromisso é com o rigor científico e a justiça epistêmica.
            </p>
            <div class="grid grid-cols-2 gap-6">
                <div class="border-l-2 border-orange-600 pl-4">
                    <span class="block text-3xl font-bold">A1</span>
                    <span class="text-stone-500 text-sm uppercase">Qualis Previsto</span>
                </div>
                <div class="border-l-2 border-orange-600 pl-4">
                    <span class="block text-3xl font-bold">Semestral</span>
                    <span class="text-stone-500 text-sm uppercase">Periodicidade</span>
                </div>
            </div>
        </div>
        <div class="relative">
            <img src="https://images.unsplash.com/photo-1532012197367-2836fb3ee4f2?auto=format&fit=crop&q=80&w=800" alt="Livros" class="rounded-lg shadow-2xl">
            <div class="absolute -top-6 -left-6 bg-orange-700 p-8 rounded-lg hidden lg:block">
                <p class="font-bold text-2xl tracking-tighter">ISSN: 2596-1234</p>
            </div>
        </div>
    </div>
</section>

<?php

include 'includes/footer.php';
?>