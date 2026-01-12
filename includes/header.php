<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afroletrando | Revista Científica</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="favicon.png">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;600&display=swap');
        
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4 { font-family: 'Playfair Display', serif; }
        
        /* Ajuste suave no scroll */
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-stone-50 text-stone-800 flex flex-col min-h-screen">

    <nav class="bg-white shadow-sm border-b border-stone-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                
                <div class="flex items-center">
                    <a href="index.php" class="text-2xl font-bold text-stone-900 tracking-wider font-serif">
                        AFRO<span class="text-orange-700">LETRANDO</span>
                    </a>
                </div>

                <div class="hidden md:flex space-x-8 items-center">
                    <a href="index.php" class="text-stone-500 hover:text-orange-700 font-medium transition">Início</a>
                    <a href="index.php#edicoes" class="text-stone-500 hover:text-orange-700 font-medium transition">Acervo</a>
                    <a href="sobre.php" class="text-stone-500 hover:text-orange-700 font-medium transition">Sobre</a>
                    
                    <a href="login.php" class="group flex items-center px-5 py-2 bg-stone-900 text-white rounded-full text-sm font-bold hover:bg-orange-700 transition">
                        <i class="fas fa-user-circle mr-2 group-hover:text-white"></i> ÁREA DO EDITOR
                    </a>
                </div>

                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-stone-900 text-2xl focus:outline-none p-2 rounded hover:bg-stone-100">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-stone-200 shadow-lg absolute w-full left-0 z-50">
            <div class="px-4 pt-2 pb-6 space-y-2">
                <a href="index.php" class="block px-3 py-3 rounded-md text-base font-medium text-stone-700 hover:text-orange-700 hover:bg-stone-50 transition">
                    <i class="fas fa-home w-6 text-center"></i> Início
                </a>
                <a href="index.php#edicoes" class="block px-3 py-3 rounded-md text-base font-medium text-stone-700 hover:text-orange-700 hover:bg-stone-50 transition">
                    <i class="fas fa-book w-6 text-center"></i> Acervo Digital
                </a>
                <a href="sobre.php" class="block px-3 py-3 rounded-md text-base font-medium text-stone-700 hover:text-orange-700 hover:bg-stone-50 transition">
                    <i class="fas fa-info-circle w-6 text-center"></i> Sobre a Revista
                </a>
                <div class="border-t border-stone-100 my-2 pt-2">
                    <a href="login.php" class="block px-3 py-3 rounded-md text-base font-bold text-orange-800 hover:bg-orange-50 transition">
                        <i class="fas fa-user-shield w-6 text-center"></i> Área do Editor
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <script>
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');

        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    </script>
    
    <main class="flex-grow">