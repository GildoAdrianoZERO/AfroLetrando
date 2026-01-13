<?php
session_start();
require_once 'conexao.php';

// Verifica login e permissão (Só admin pode ver)
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_nivel'] != 'admin') { 
    header("Location: admin.php"); exit; 
}

$msg = '';
$tipo_msg = '';

// --- CADASTRO DE NOVO MEMBRO ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'cadastrar') {
    $nome = $_POST['nome'];
    $cargo = $_POST['cargo'];
    $instituicao = $_POST['instituicao'];
    $email = $_POST['email']; // Novo campo
    $caminho_imagem = 'https://ui-avatars.com/api/?name='.urlencode($nome).'&background=random';

    // Upload da Imagem
    if (!empty($_FILES['foto']['name'])) {
        $dir = "uploads/equipe/";
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $novo_nome = uniqid("membro_", true) . "." . $ext;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $dir . $novo_nome)) {
            $caminho_imagem = $dir . $novo_nome;
        }
    }

    $stmt = $conn->prepare("INSERT INTO equipe (nome, cargo, instituicao, imagem, email) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nome, $cargo, $instituicao, $caminho_imagem, $email);
    
    if ($stmt->execute()) {
        $msg = "Membro adicionado com sucesso!";
        $tipo_msg = 'sucesso';
    } else {
        $msg = "Erro ao cadastrar: " . $conn->error;
        $tipo_msg = 'erro';
    }
}

// --- EXCLUSÃO ---
if (isset($_GET['excluir'])) {
    $id_del = intval($_GET['excluir']);
    $conn->query("DELETE FROM equipe WHERE id = $id_del");
    header("Location: admin_equipe.php?msg=removido");
    exit;
}

// Busca equipe
$result = $conn->query("SELECT * FROM equipe ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Equipe | Admin</title>
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
                <span class="font-bold uppercase tracking-wider hidden sm:block">Gestão de Equipe</span>
                
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
        
        <?php if ($msg || isset($_GET['msg'])): ?>
            <div class="mb-6 p-4 rounded-lg border-l-4 bg-green-100 text-green-700 border-green-500">
                <i class="fas fa-check-circle mr-2"></i> 
                <?php echo $msg ? $msg : "Membro removido com sucesso."; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="bg-white p-6 rounded-xl shadow border border-stone-200 h-fit">
                <h2 class="text-xl font-bold mb-4 text-stone-700 flex items-center">
                    <i class="fas fa-user-plus mr-2 text-orange-500"></i> Novo Membro
                </h2>
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="acao" value="cadastrar">
                    
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase mb-1">Nome Completo</label>
                        <input type="text" name="nome" required class="w-full border p-2 rounded focus:border-orange-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase mb-1">E-mail</label>
                        <input type="email" name="email" class="w-full border p-2 rounded focus:border-orange-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase mb-1">Cargo / Função</label>
                        <input type="text" name="cargo" required class="w-full border p-2 rounded focus:border-orange-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase mb-1">Instituição</label>
                        <input type="text" name="instituicao" class="w-full border p-2 rounded focus:border-orange-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase mb-1">Foto (Opcional)</label>
                        <input type="file" name="foto" class="w-full text-sm text-stone-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-stone-100 file:text-stone-700 hover:file:bg-orange-100">
                    </div>

                    <button type="submit" class="w-full bg-stone-900 text-white font-bold py-3 rounded hover:bg-orange-600 transition">
                        CADASTRAR
                    </button>
                </form>
            </div>

            <div class="lg:col-span-2">
                <h2 class="text-xl font-bold mb-4 text-stone-700 flex items-center">
                    <i class="fas fa-users mr-2 text-teal-600"></i> Membros Atuais
                </h2>

                <div class="bg-white rounded-xl shadow border border-stone-200 overflow-hidden">
                    <div class="overflow-x-auto"> <table class="min-w-full divide-y divide-stone-200 whitespace-nowrap">
                            <thead class="bg-stone-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-stone-500 uppercase">Perfil</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-stone-500 uppercase">Contato/Cargo</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-stone-500 uppercase">Ação</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200">
                                <?php while($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-stone-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <img src="<?php echo $row['imagem']; ?>" class="h-10 w-10 rounded-full object-cover border border-stone-200">
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-stone-900"><?php echo $row['nome']; ?></div>
                                                <div class="text-xs text-stone-500"><?php echo $row['instituicao']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-stone-900 font-bold"><?php echo $row['cargo']; ?></div>
                                        <div class="text-xs text-stone-500"><?php echo $row['email']; ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="?excluir=<?php echo $row['id']; ?>" onclick="return confirm('Remover este membro?')" class="text-red-600 hover:text-red-900 font-bold text-sm bg-red-50 px-3 py-1 rounded">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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