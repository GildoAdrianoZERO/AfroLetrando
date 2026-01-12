<?php
session_start();
require_once 'conexao.php';

// 1. SEGURANÇA
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $usuario_id = $_SESSION['usuario_id'];

    // 2. BUSCA O NOME DA REVISTA ANTES DE APAGAR (Para o Log)
    $sql_busca = "SELECT titulo, volume FROM edicoes WHERE id = $id";
    $result = $conn->query($sql_busca);
    
    if ($result->num_rows > 0) {
        $dados = $result->fetch_assoc();
        $info_log = "Excluiu a revista: " . $dados['titulo'] . " (" . $dados['volume'] . ")";

        // 3. APAGA A REVISTA
        $sql_delete = "DELETE FROM edicoes WHERE id = $id";
        if ($conn->query($sql_delete) === TRUE) {
            
            // 4. GRAVA O LOG DE ATIVIDADE
            $sql_log = "INSERT INTO logs (usuario_id, acao, descricao) VALUES ('$usuario_id', 'EXCLUSAO', '$info_log')";
            $conn->query($sql_log);

            header("Location: admin.php?msg=excluido");
        } else {
            echo "Erro ao excluir: " . $conn->error;
        }
    } else {
        header("Location: admin.php"); // ID não encontrado
    }
} else {
    header("Location: admin.php");
}
?>