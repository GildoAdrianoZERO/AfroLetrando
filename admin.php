<?php
session_start();
require_once 'conexao.php';

// 1. Verifica login
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }

// 2. Consultas para o Dashboard (Contadores)
$total_revistas = $conn->query("SELECT count(*) as total FROM edicoes")->fetch_assoc()['total'];
$total_equipe = $conn->query("SELECT count(*) as total FROM equipe")->fetch_assoc()['total'];

// 3. Consulta para a Tabela de Revistas (Listagem)
$sql_revistas = "SELECT * FROM edicoes ORDER BY data_publicacao DESC";
$result_revistas = $conn->query($sql_revistas);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo | Afroletrando</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-stone-50 text-stone-900 font-sans">

    <nav class="bg-stone-900 text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                
                <div class="flex items-center">
                    <span class="font-bold text-xl tracking-wider">AFRO<span class="text-orange-500">ADMIN</span></span>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <span class="text-stone-400 text-sm border-r border-stone-700 pr-4 mr-2">
                        Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Admin'); ?>
                    </span>
                    <a href="index.php" target="_blank" class="hover:text-orange-400 transition text-sm font-bold">
                        <i class="fas fa-external-link-alt mr-1"></i> VER SITE
                    </a>
                    <a href="logout.php" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded text-sm font-bold transition ml-4">
                        SAIR
                    </a>
                </div>

                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-white hover:text-orange-500 focus:outline-none p-2">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-stone-800 pb-4 px-4 border-t border-stone-700">
            <div class="py-3 text-stone-400 text-sm border-b border-stone-700 mb-2">
                Logado como: <strong class="text-white"><?php echo $_SESSION['usuario_nome'] ?? 'Admin'; ?></strong>
            </div>
            <a href="index.php" target="_blank" class="block py-3 text-white hover:text-orange-400 font-bold">
                <i class="fas fa-external-link-alt mr-2"></i> Ver Site Oficial
            </a>
            <a href="logout.php" class="block py-3 text-red-400 hover:text-red-300 font-bold border-t border-stone-700 mt-2">
                <i class="fas fa-sign-out-alt mr-2"></i> Sair do Painel
            </a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow border-l-4 border-orange-500 flex items-center justify-between">
                <div>
                    <p class="text-stone-500 text-xs font-bold uppercase tracking-wider">Total de Revistas</p>
                    <p class="text-3xl font-bold text-stone-800"><?php echo $total_revistas; ?></p>
                </div>
                <div class="bg-orange-100 text-orange-600 w-12 h-12 rounded-full flex items-center justify-center text-xl">
                    <i class="fas fa-book"></i>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow border-l-4 border-teal-500 flex items-center justify-between">
                <div>
                    <p class="text-stone-500 text-xs font-bold uppercase tracking-wider">Membros da Equipe</p>
                    <p class="text-3xl font-bold text-stone-800"><?php echo $total_equipe; ?></p>
                </div>
                <div class="bg-teal-100 text-teal-600 w-12 h-12 rounded-full flex items-center justify-center text-xl">
                    <i class="fas fa-users"></i>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow border-l-4 border-purple-500 flex items-center justify-between">
                <div>
                    <p class="text-stone-500 text-xs font-bold uppercase tracking-wider">Seu Nível</p>
                    <p class="text-xl font-bold text-stone-800 uppercase mt-1">
                        <?php echo $_SESSION['usuario_nivel'] ?? 'Editor'; ?>
                    </p>
                </div>
                <div class="bg-purple-100 text-purple-600 w-12 h-12 rounded-full flex items-center justify-center text-xl">
                    <i class="fas fa-id-badge"></i>
                </div>
            </div>
        </div>

        <h2 class="text-lg font-bold text-stone-700 mb-4 flex items-center">
            <i class="fas fa-rocket mr-2 text-orange-500"></i> Ações Rápidas
        </h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-10">
            
            <a href="nova_edicao.php" class="bg-stone-900 hover:bg-orange-600 text-white p-6 rounded-xl shadow transition group flex items-center justify-center flex-col text-center">
                <i class="fas fa-plus-circle text-3xl mb-3 group-hover:scale-110 transition"></i>
                <span class="font-bold">Nova Edição</span>
            </a>

            <a href="admin_equipe.php" class="bg-white hover:bg-stone-50 text-stone-700 p-6 rounded-xl shadow border border-stone-200 transition group flex items-center justify-center flex-col text-center hover:border-orange-300">
                <i class="fas fa-users text-2xl mb-2 text-teal-600"></i>
                <span class="font-bold text-sm">Gerenciar Equipe</span>
            </a>

            <a href="editar_sobre.php" class="bg-white hover:bg-stone-50 text-stone-700 p-6 rounded-xl shadow border border-stone-200 transition group flex items-center justify-center flex-col text-center hover:border-orange-300">
                <i class="fas fa-info-circle text-2xl mb-2 text-blue-600"></i>
                <span class="font-bold text-sm">Editar "Sobre"</span>
            </a>

            <?php if (isset($_SESSION['usuario_nivel']) && $_SESSION['usuario_nivel'] == 'admin'): ?>
            <a href="admin_usuarios.php" class="bg-white hover:bg-stone-50 text-stone-700 p-6 rounded-xl shadow border border-stone-200 transition group flex items-center justify-center flex-col text-center hover:border-orange-300">
                <i class="fas fa-users-cog text-2xl mb-2 text-purple-600"></i>
                <span class="font-bold text-sm">Usuários & Senhas</span>
            </a>
            <?php endif; ?>

            <a href="logs.php" class="bg-white hover:bg-stone-50 text-stone-700 p-6 rounded-xl shadow border border-stone-200 transition group flex items-center justify-center flex-col text-center hover:border-orange-300">
                <i class="fas fa-history text-2xl mb-2 text-stone-500"></i>
                <span class="font-bold text-sm">Ver Logs</span>
            </a>
        </div>

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-stone-700 flex items-center">
                <i class="fas fa-list mr-2 text-orange-500"></i> Revistas Publicadas
            </h2>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="mb-6 p-4 rounded-lg shadow-sm border-l-4 
                <?php echo ($_GET['msg'] == 'excluido') ? 'bg-red-100 text-red-700 border-red-500' : 'bg-green-100 text-green-700 border-green-500'; ?>">
                <i class="fas <?php echo ($_GET['msg'] == 'excluido') ? 'fa-trash' : 'fa-check-circle'; ?> mr-2"></i>
                <?php 
                    if($_GET['msg'] == 'sucesso') echo "Edição publicada com sucesso!";
                    elseif($_GET['msg'] == 'editado') echo "Edição atualizada com sucesso!";
                    elseif($_GET['msg'] == 'excluido') echo "Edição excluída do sistema.";
                ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow border border-stone-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200 whitespace-nowrap">
                    <thead class="bg-stone-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-stone-500 uppercase">Capa</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-stone-500 uppercase">Título / Volume</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-stone-500 uppercase">Data</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-stone-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-stone-200">
                        <?php if ($result_revistas->num_rows > 0): ?>
                            <?php while($row = $result_revistas->fetch_assoc()): ?>
                            <tr class="hover:bg-stone-50 transition">
                                <td class="px-6 py-4">
                                    <div class="h-12 w-12 rounded bg-stone-200 overflow-hidden border border-stone-300">
                                        <img src="<?php echo $row['imagem_capa']; ?>" alt="Capa" class="h-full w-full object-cover">
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-stone-900 truncate max-w-xs"><?php echo $row['titulo']; ?></div>
                                    <div class="text-sm text-stone-500"><?php echo $row['volume']; ?></div>
                                </td>
                                <td class="px-6 py-4 text-sm text-stone-500">
                                    <?php echo date('d/m/Y', strtotime($row['data_publicacao'])); ?>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <a href="<?php echo $row['link_pdf']; ?>" target="_blank" class="text-blue-600 hover:text-blue-900 mr-3" title="Ver PDF"><i class="fas fa-eye"></i></a>
                                    <a href="editar_edicao.php?id=<?php echo $row['id']; ?>" class="text-orange-600 hover:text-orange-900 mr-3" title="Editar"><i class="fas fa-edit"></i></a>
                                    <a href="excluir_edicao.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta edição?')" class="text-red-600 hover:text-red-900" title="Excluir"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-stone-500">
                                    Nenhuma edição encontrada. Use o botão acima para criar a primeira!
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <p class="mt-4 text-xs text-stone-400 text-center md:hidden">
            <i class="fas fa-arrows-alt-h mr-1"></i> Arraste a tabela para ver mais opções
        </p>

    </div>

    <script>
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>