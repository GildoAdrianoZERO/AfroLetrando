<?php
session_start();
require_once 'conexao.php';

// 1. SEGURANÇA MÁXIMA: Só 'admin' pode entrar aqui
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_nivel'] !== 'admin') {
    // Se for editor tentando entrar, manda de volta pro painel
    header("Location: admin.php"); 
    exit;
}

// --- AÇÕES DO SISTEMA ---
if (isset($_GET['acao']) && isset($_GET['id'])) {
    $id_user = intval($_GET['id']);
    
    // Evita que você se exclua sem querer
    if ($id_user == $_SESSION['usuario_id']) {
        header("Location: admin_usuarios.php?msg=erro_proprio");
        exit;
    }

    $acao = $_GET['acao'];

    if ($acao == 'aprovar') {
        $conn->query("UPDATE usuarios SET status = 1 WHERE id = $id_user");
    } 
    elseif ($acao == 'recusar' || $acao == 'excluir') {
        $conn->query("DELETE FROM usuarios WHERE id = $id_user");
    }
    elseif ($acao == 'virar_admin') {
        $conn->query("UPDATE usuarios SET nivel = 'admin' WHERE id = $id_user");
    }
    elseif ($acao == 'virar_editor') {
        $conn->query("UPDATE usuarios SET nivel = 'editor' WHERE id = $id_user");
    }
    
    header("Location: admin_usuarios.php?msg=sucesso");
    exit;
}

// Busca Pendentes
$pendentes = $conn->query("SELECT * FROM usuarios WHERE status = 0 ORDER BY data_criacao DESC");

// Busca Ativos (Excluindo você mesmo da lista para segurança)
$meu_id = $_SESSION['usuario_id'];
$ativos = $conn->query("SELECT * FROM usuarios WHERE status = 1 AND id != $meu_id ORDER BY nome ASC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Super Admin | Gestão de Usuários</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-stone-50 text-stone-900 font-sans pb-20">

    <div class="max-w-6xl mx-auto px-4 py-10">
        
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-bold text-stone-800"><i class="fas fa-user-shield text-orange-700 mr-2"></i> Gestão de Usuários</h1>
                <p class="text-stone-500">Controle total de permissões e acessos.</p>
            </div>
            <a href="admin.php" class="bg-stone-600 text-white px-5 py-3 rounded-lg hover:bg-stone-700 font-bold">
                <i class="fas fa-arrow-left mr-2"></i> Voltar ao Painel
            </a>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] == 'erro_proprio'): ?>
                <div class="bg-red-100 text-red-700 p-4 mb-6 rounded font-bold">Você não pode excluir a si mesmo!</div>
            <?php else: ?>
                <div class="bg-green-100 text-green-700 p-4 mb-6 rounded font-bold"><i class="fas fa-check-circle mr-2"></i> Ação realizada com sucesso!</div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($pendentes->num_rows > 0): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl shadow-sm mb-12 overflow-hidden">
            <div class="bg-yellow-100 px-6 py-4 border-b border-yellow-200 flex justify-between items-center">
                <h2 class="text-lg font-bold text-yellow-800"><i class="fas fa-clock mr-2"></i> Solicitações Pendentes</h2>
                <span class="bg-yellow-600 text-white text-xs px-2 py-1 rounded-full"><?php echo $pendentes->num_rows; ?></span>
            </div>
            <table class="min-w-full">
                <tbody class="divide-y divide-yellow-200">
                    <?php while($user = $pendentes->fetch_assoc()): ?>
                    <tr class="hover:bg-yellow-100/50">
                        <td class="px-6 py-4 font-bold"><?php echo $user['nome']; ?></td>
                        <td class="px-6 py-4 text-sm text-stone-600"><?php echo $user['email']; ?></td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="?acao=aprovar&id=<?php echo $user['id']; ?>" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 shadow">Aprovar</a>
                            <a href="?acao=recusar&id=<?php echo $user['id']; ?>" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600 shadow" onclick="return confirm('Recusar este pedido?')">Recusar</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-lg border border-stone-200 overflow-hidden">
            <div class="bg-stone-100 px-6 py-4 border-b border-stone-200">
                <h2 class="text-lg font-bold text-stone-700"><i class="fas fa-users mr-2"></i> Usuários do Sistema</h2>
            </div>
            <table class="min-w-full divide-y divide-stone-100">
                <thead class="bg-stone-50 text-stone-500">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase">Nome / Email</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase">Nível Atual</th>
                        <th class="px-6 py-3 text-right text-xs font-bold uppercase">Ações de Super Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php while($user = $ativos->fetch_assoc()): ?>
                    <tr class="hover:bg-stone-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-stone-800"><?php echo $user['nome']; ?></div>
                            <div class="text-sm text-stone-500"><?php echo $user['email']; ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($user['nivel'] == 'admin'): ?>
                                <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full font-bold border border-purple-200">ADMIN</span>
                            <?php else: ?>
                                <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full border border-gray-200">Editor</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <?php if ($user['nivel'] == 'editor'): ?>
                                <a href="?acao=virar_admin&id=<?php echo $user['id']; ?>" class="text-purple-600 hover:text-purple-800 text-sm font-bold mr-4" title="Promover a Admin">
                                    <i class="fas fa-arrow-up"></i> Promover
                                </a>
                            <?php else: ?>
                                <a href="?acao=virar_editor&id=<?php echo $user['id']; ?>" class="text-stone-500 hover:text-stone-800 text-sm font-bold mr-4" title="Rebaixar a Editor">
                                    <i class="fas fa-arrow-down"></i> Rebaixar
                                </a>
                            <?php endif; ?>

                            <a href="?acao=excluir&id=<?php echo $user['id']; ?>" 
                               onclick="return confirm('TEM CERTEZA? Isso excluirá o usuário <?php echo $user['nome']; ?> permanentemente.')" 
                               class="bg-red-100 text-red-600 hover:bg-red-600 hover:text-white px-3 py-1 rounded transition text-sm">
                                <i class="fas fa-trash-alt"></i> Excluir
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>