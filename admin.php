<?php
session_start();
require_once 'conexao.php';

// 1. Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Busca as revistas no banco
$sql = "SELECT * FROM edicoes ORDER BY data_publicacao DESC";
$result = $conn->query($sql);
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
<body class="bg-stone-50 text-stone-900">

    <nav class="bg-stone-900 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <span class="font-bold text-xl tracking-wider">AFROLETRANDO <span class="text-orange-500 text-xs">ADMIN</span></span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-stone-400 text-sm">Olá, <?php echo $_SESSION['usuario_nome']; ?></span>
                    <a href="logout.php" class="text-white hover:text-red-400 transition text-sm font-bold">
                        <i class="fas fa-sign-out-alt mr-1"></i> SAIR
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-stone-800">Gerenciar Edições</h1>
                <p class="text-stone-500 mt-1">Lista de todas as revistas publicadas no portal.</p>
            </div>
            <a href="nova_edicao.php" class="mt-4 md:mt-0 bg-orange-700 hover:bg-orange-800 text-white px-6 py-3 rounded-lg font-bold shadow-lg transition flex items-center">
                <i class="fas fa-plus-circle mr-2"></i> NOVA EDIÇÃO
            </a>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] == 'sucesso'): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                    <i class="fas fa-check-circle mr-2"></i> Edição publicada com sucesso!
                </div>
            <?php elseif ($_GET['msg'] == 'editado'): ?>
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded shadow-sm">
                    <i class="fas fa-pen-nib mr-2"></i> Edição atualizada com sucesso!
                </div>
            <?php elseif ($_GET['msg'] == 'excluido'): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                    <i class="fas fa-trash mr-2"></i> A edição foi removida do sistema.
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow border border-stone-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200">
                    <thead class="bg-stone-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-stone-500 uppercase tracking-wider">Capa</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-stone-500 uppercase tracking-wider">Título / Volume</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-stone-500 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-stone-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-stone-200">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-stone-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="h-12 w-12 rounded bg-stone-200 overflow-hidden border border-stone-300">
                                        <img src="<?php echo $row['imagem_capa']; ?>" alt="Capa" class="h-full w-full object-cover">
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-stone-900"><?php echo $row['titulo']; ?></div>
                                    <div class="text-sm text-stone-500"><?php echo $row['volume']; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-500">
                                    <?php echo date('d/m/Y', strtotime($row['data_publicacao'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?php echo $row['link_pdf']; ?>" target="_blank" class="text-blue-600 hover:text-blue-900 mr-4" title="Ver PDF">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <a href="editar_edicao.php?id=<?php echo $row['id']; ?>" class="text-orange-600 hover:text-orange-900 mr-4" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="excluir_edicao.php?id=<?php echo $row['id']; ?>" 
                                       onclick="return confirm('Tem certeza que deseja apagar a edição <?php echo $row['volume']; ?>?')"
                                       class="text-red-600 hover:text-red-900" title="Excluir">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-stone-500">
                                    Nenhuma edição encontrada. Clique em "Nova Edição" para começar.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>