<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Busca os logs juntando com a tabela de usuários para saber o nome de quem fez
$sql = "SELECT logs.*, usuarios.nome as nome_usuario 
        FROM logs 
        JOIN usuarios ON logs.usuario_id = usuarios.id 
        ORDER BY logs.data_hora DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Atividades | Afroletrando</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-stone-50 font-sans">

    <div class="max-w-5xl mx-auto px-4 py-10">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-stone-800"><i class="fas fa-history mr-2"></i>Logs do Sistema</h1>
            <a href="admin.php" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Voltar ao Painel</a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data/Hora</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuário</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ação</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detalhes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <?php
                                // Cores para cada tipo de ação
                                $cor_badge = "bg-gray-100 text-gray-800";
                                if($row['acao'] == 'CADASTRO') $cor_badge = "bg-green-100 text-green-800";
                                if($row['acao'] == 'EDICAO') $cor_badge = "bg-blue-100 text-blue-800";
                                if($row['acao'] == 'EXCLUSAO') $cor_badge = "bg-red-100 text-red-800";
                            ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('d/m/Y H:i', strtotime($row['data_hora'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">
                                    <?php echo $row['nome_usuario']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $cor_badge; ?>">
                                        <?php echo $row['acao']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <?php echo $row['descricao']; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center py-4 text-gray-500">Nenhum registro encontrado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>