<?php
require_once 'conexao.php';
include 'includes/header.php';

// Busca textos do SOBRE
$sql_sobre = "SELECT * FROM sobre WHERE id = 1";
$res_sobre = $conn->query($sql_sobre);
$sobre = ($res_sobre->num_rows > 0) ? $res_sobre->fetch_assoc() : ['titulo' => 'Sobre', 'texto' => 'Conteúdo...'];

// Busca EQUIPE
$sql_equipe = "SELECT * FROM equipe ORDER BY id ASC";
$res_equipe = $conn->query($sql_equipe);
?>

<div class="bg-white">
    
    <div class="max-w-7xl mx-auto px-4 py-20">
        <div class="text-center mb-16">
            <span class="text-orange-700 font-bold tracking-widest uppercase text-sm">Nossa Identidade</span>
            <h2 class="text-4xl md:text-5xl font-serif font-bold text-stone-900 mt-2"><?php echo $sobre['titulo']; ?></h2>
            <div class="w-24 h-1 bg-orange-700 mx-auto mt-6"></div>
        </div>

        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="prose text-stone-600 leading-relaxed text-lg text-justify">
                <?php echo nl2br($sobre['texto']); ?>
            </div>
            <div class="relative">
                <div class="absolute inset-0 bg-orange-200 rounded-lg transform translate-x-4 translate-y-4"></div>
                <img src="<?php echo !empty($sobre['imagem']) ? $sobre['imagem'] : 'https://images.unsplash.com/photo-1577985051167-0d49eec21977?q=80&w=1470'; ?>" 
                     alt="Sobre" 
                     class="relative rounded-lg shadow-xl w-full h-auto grayscale hover:grayscale-0 transition duration-700 object-cover aspect-video">
            </div>
        </div>
    </div>

    <div class="bg-stone-50 py-20 border-y border-stone-200">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8 text-center">
                <div class="p-8 bg-white rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-16 h-16 bg-orange-100 text-orange-700 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl"><i class="fas fa-feather-alt"></i></div>
                    <h3 class="font-serif font-bold text-xl mb-3">Literatura e Arte</h3>
                    <p class="text-stone-500 text-sm">Expressões artísticas e culturais da diáspora.</p>
                </div>
                <div class="p-8 bg-white rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-16 h-16 bg-orange-100 text-orange-700 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl"><i class="fas fa-graduation-cap"></i></div>
                    <h3 class="font-serif font-bold text-xl mb-3">Educação Antirracista</h3>
                    <p class="text-stone-500 text-sm">Práticas pedagógicas e Lei 10.639/03.</p>
                </div>
                <div class="p-8 bg-white rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-16 h-16 bg-orange-100 text-orange-700 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl"><i class="fas fa-users"></i></div>
                    <h3 class="font-serif font-bold text-xl mb-3">Sociedade e Política</h3>
                    <p class="text-stone-500 text-sm">Políticas públicas e ações afirmativas.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-20">
        <h2 class="text-3xl font-serif font-bold text-center mb-12">Corpo Editorial</h2>
        
        <?php if ($res_equipe->num_rows > 0): ?>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <?php while($membro = $res_equipe->fetch_assoc()): ?>
                <div class="group text-center bg-white p-6 rounded-lg border border-stone-100 hover:shadow-xl transition relative">
                    <div class="w-32 h-32 mx-auto rounded-full overflow-hidden mb-4 border-4 border-stone-50 shadow-lg group-hover:border-orange-500 transition">
                        <img src="<?php echo $membro['imagem']; ?>" alt="<?php echo $membro['nome']; ?>" class="w-full h-full object-cover">
                    </div>
                    
                    <h4 class="font-bold text-lg text-stone-900 leading-tight mb-1"><?php echo $membro['nome']; ?></h4>
                    <p class="text-orange-700 text-xs uppercase font-bold tracking-wider mb-2"><?php echo $membro['cargo']; ?></p>
                    <p class="text-stone-500 text-xs mb-4 h-8 overflow-hidden"><?php echo $membro['instituicao']; ?></p>
                    
                    <?php if (!empty($membro['email'])): ?>
                        <div class="border-t border-stone-100 pt-3 mt-auto">
                            <a href="mailto:<?php echo $membro['email']; ?>" class="text-stone-400 hover:text-orange-700 transition" title="Enviar E-mail">
                                <i class="fas fa-envelope"></i> <span class="text-xs ml-1">Contato - email</span>
                            </a>
                        </div>
                    <?php endif; ?>

                </div>
                <?php endwhile; ?>

            </div>
        <?php else: ?>
            <p class="text-center text-stone-500">A equipe editorial está sendo atualizada.</p>
        <?php endif; ?>
    </div>

    <div class="bg-stone-900 text-white py-16 text-center">
        <div class="max-w-3xl mx-auto px-4">
            <h2 class="text-3xl font-serif font-bold mb-4">Deseja publicar conosco?</h2>
            <p class="text-stone-400 mb-8">Envie seu artigo para avaliação.</p>
            <a href="mailto:contato@afroletrando.com" class="bg-orange-700 hover:bg-orange-800 text-white px-8 py-3 rounded-lg font-bold transition">
                <i class="fas fa-envelope mr-2"></i> Enviar E-mail para Redação
            </a>
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>