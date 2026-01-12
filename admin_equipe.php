<?php
session_start();
require_once 'conexao.php';

// Verifica Login
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }

$msg = '';
$editando = false;
// Adicionado campo 'email' no array padrão
$membro_editar = ['nome' => '', 'cargo' => '', 'instituicao' => '', 'email' => '', 'id' => ''];

// --- LÓGICA DE POST ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $conn->real_escape_string($_POST['nome']);
    $cargo = $conn->real_escape_string($_POST['cargo']);
    $instituicao = $conn->real_escape_string($_POST['instituicao']);
    $email = $conn->real_escape_string($_POST['email']); // Captura o email
    
    $dir = "uploads/equipe/";
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    if (isset($_POST['id_editar']) && !empty($_POST['id_editar'])) {
        // --- ATUALIZAR ---
        $id_editar = intval($_POST['id_editar']);
        
        // Adicionado email na query
        $sql = "UPDATE equipe SET nome = '$nome', cargo = '$cargo', instituicao = '$instituicao', email = '$email'";

        if (!empty($_FILES['foto']['name'])) {
            $nome_arquivo = uniqid() . "_edit_" . basename($_FILES["foto"]["name"]);
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $dir . $nome_arquivo)) {
                $sql .= ", imagem = '" . $dir . $nome_arquivo . "'";
            }
        }
        $sql .= " WHERE id = $id_editar";
        $msg = $conn->query($sql) ? "Membro atualizado!" : "Erro: " . $conn->error;

    } else {
        // --- CADASTRAR ---
        $imagem = "https://ui-avatars.com/api/?name=" . urlencode($nome) . "&background=random";

        if (!empty($_FILES['foto']['name'])) {
            $nome_arquivo = uniqid() . "_" . basename($_FILES["foto"]["name"]);
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $dir . $nome_arquivo)) {
                $imagem = $dir . $nome_arquivo;
            }
        }

        // Adicionado email no insert
        $sql = "INSERT INTO equipe (nome, cargo, instituicao, email, imagem) VALUES ('$nome', '$cargo', '$instituicao', '$email', '$imagem')";
        $msg = $conn->query($sql) ? "Membro adicionado!" : "Erro: " . $conn->error;
    }
    
    echo "<script>window.location.href='admin_equipe.php?msg=" . urlencode($msg) . "';</script>";
    exit;
}

// --- LÓGICA DE GET ---
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $conn->query("DELETE FROM equipe WHERE id = $id");
    header("Location: admin_equipe.php?msg=Membro+removido");
    exit;
} elseif (isset($_GET['editar'])) {
    $id_editar = intval($_GET['editar']);
    $result_edit = $conn->query("SELECT * FROM equipe WHERE id = $id_editar");
    if ($result_edit->num_rows > 0) {
        $membro_editar = $result_edit->fetch_assoc();
        $editando = true;
    }
}

if (isset($_GET['msg']) && empty($msg)) $msg = htmlspecialchars($_GET['msg']);
$result = $conn->query("SELECT * FROM equipe ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Equipe | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-stone-50 text-stone-900 py-10">
    <div class="max-w-6xl mx-auto px-4">
        
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-stone-800">Corpo Editorial</h1>
            <a href="admin.php" class="bg-stone-600 text-white px-4 py-2 rounded hover:bg-stone-700 transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Voltar ao Painel
            </a>
        </div>

        <?php if ($msg): ?>
            <div class="bg-blue-100 text-blue-700 p-4 mb-6 rounded border-l-4 border-blue-500">
                <i class="fas fa-info-circle mr-2"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div class="grid md:grid-cols-12 gap-8">
            
            <div class="md:col-span-4">
                <div class="bg-white p-6 rounded-xl shadow-lg border border-stone-200 sticky top-10">
                    <h2 class="font-bold text-xl mb-4 flex items-center <?php echo $editando ? 'text-blue-700' : 'text-orange-700'; ?>">
                        <i class="fas <?php echo $editando ? 'fa-pen-to-square' : 'fa-user-plus'; ?> mr-3"></i>
                        <?php echo $editando ? 'Editar Membro' : 'Novo Membro'; ?>
                    </h2>

                    <?php if ($editando): ?>
                        <div class="mb-4 text-center p-4 bg-stone-50 rounded border border-stone-100">
                            <img src="<?php echo $membro_editar['imagem']; ?>" class="w-24 h-24 rounded-full object-cover mx-auto border-2 border-white shadow-sm">
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_editar" value="<?php echo $membro_editar['id']; ?>">

                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-1 text-stone-700">Nome Completo</label>
                            <input type="text" name="nome" required value="<?php echo $membro_editar['nome']; ?>" class="w-full border p-2 rounded">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-1 text-stone-700">E-mail de Contato</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-stone-400"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" placeholder="exemplo@email.com" value="<?php echo $membro_editar['email']; ?>" class="w-full border p-2 pl-10 rounded">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-1 text-stone-700">Cargo / Função</label>
                            <input type="text" name="cargo" required value="<?php echo $membro_editar['cargo']; ?>" class="w-full border p-2 rounded">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-1 text-stone-700">Instituição</label>
                            <input type="text" name="instituicao" value="<?php echo $membro_editar['instituicao']; ?>" class="w-full border p-2 rounded">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-bold mb-1 text-stone-700">Foto</label>
                            <input type="file" name="foto" accept="image/*" class="w-full text-sm text-stone-500">
                        </div>
                        
                        <button type="submit" class="w-full text-white font-bold py-3 rounded-lg shadow-md <?php echo $editando ? 'bg-blue-600 hover:bg-blue-700' : 'bg-orange-700 hover:bg-orange-800'; ?>">
                            <?php echo $editando ? 'SALVAR ALTERAÇÕES' : 'ADICIONAR MEMBRO'; ?>
                        </button>

                        <?php if ($editando): ?>
                            <a href="admin_equipe.php" class="block text-center text-stone-500 text-sm mt-4 font-bold">Cancelar</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="md:col-span-8">
                <div class="bg-white rounded-xl shadow border border-stone-200 overflow-hidden">
                    <table class="min-w-full divide-y divide-stone-200">
                        <tbody class="divide-y divide-stone-200">
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-stone-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img src="<?php echo $row['imagem']; ?>" class="h-12 w-12 rounded-full object-cover mr-4 border-2 border-stone-200">
                                        <div>
                                            <div class="font-bold text-stone-900"><?php echo $row['nome']; ?></div>
                                            <?php if(!empty($row['email'])): ?>
                                                <div class="text-xs text-stone-400 mt-0.5">
                                                    <i class="fas fa-envelope mr-1"></i> <?php echo $row['email']; ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="text-xs text-stone-500 mt-0.5"><?php echo $row['instituicao']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-xs font-bold uppercase">
                                        <?php echo $row['cargo']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-4">
                                    <a href="admin_equipe.php?editar=<?php echo $row['id']; ?>" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                                    <a href="admin_equipe.php?excluir=<?php echo $row['id']; ?>" onclick="return confirm('Excluir?')" class="text-red-500 hover:text-red-700"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>