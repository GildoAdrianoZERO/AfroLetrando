<?php
session_start();
require_once 'conexao.php';

// Verifica login
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }

// Busca logs (Últimos 50)
$sql = "SELECT logs.*, usuarios.nome as autor_nome 
        FROM logs 
        LEFT JOIN usuarios ON logs.usuario_id = usuarios.id 
        ORDER BY logs.data_hora DESC LIMIT 50";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs do Sistema | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap'); body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-stone-50 text-stone-900">

    <nav class="bg-stone-900 text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16 items-center">
                <a href="admin.php" class="font-bold text-xl hover:text-orange-500 transition">
                    <i class="fas fa-arrow-left mr-2"></i> VOLTAR
                </a>
                <span class="font-bold uppercase tracking-wider hidden sm:block">Histórico de Atividades</span>
                <button id="mobile-menu-btn" class="md:hidden text-white hover:text-orange-500 p-2">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden bg-stone-800 px-4 pb-4">
            <a href="admin.php" class="block py-3 text-white border-b border-stone-700">Voltar ao Painel</a>
            <a href="logout.php" class="block py-3 text-red-400 font-bold">Sair</a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        
        <div class="bg-white rounded-xl shadow border border-stone-200 overflow-hidden">
            <div class="p-6 border-b border-stone-200 bg-stone-50 flex justify-between items-center">
                <h3 class="font-bold text-stone-700">Últimas 50 Atividades</h3>
                <span class="text-xs font-mono text-stone-400">log_system_v1</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200 whitespace-nowrap">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-stone-500 uppercase">Data/Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-stone-500 uppercase">Usuário</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-stone-500 uppercase">Ação</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-stone-500 uppercase">Detalhes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-stone-50 transition">
                            <td class="px-6 py-4 text-sm text-stone-500 font-mono">
                                <?php echo date('d/m/Y H:i', strtotime($row['data_hora'])); ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-stone-800">
                                <?php echo htmlspecialchars($row['autor_nome'] ?? 'Desconhecido'); ?>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <?php 
                                    $cor = 'bg-gray-100 text-gray-800';
                                    if ($row['acao'] == 'CADASTRO') $cor = 'bg-green-100 text-green-800';
                                    if ($row['acao'] == 'EDICAO') $cor = 'bg-blue-100 text-blue-800';
                                    if ($row['acao'] == 'EXCLUSAO') $cor = 'bg-red-100 text-red-800';
                                ?>
                                <span class="px-2 py-1 rounded-md text-xs font-bold <?php echo $cor; ?>">
                                    <?php echo $row['acao']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-stone-600 max-w-xs truncate" title="<?php echo htmlspecialchars($row['descricao']); ?>">
                                <?php echo htmlspecialchars($row['descricao']); ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        document.getElementById('mobile-menu-btn').addEventListener('click', () => {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>