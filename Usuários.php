<?php
require_once 'dados.php';

$mensagem = '';
$usuarios = lerDados(FILE_USUARIOS);

// Ações do CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $id = $_POST['id'] ?? null;
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';

    if ($acao === 'criar') {
        $novoId = count($usuarios) > 0 ? max(array_column($usuarios, 'id')) + 1 : 1;
        $usuarios[] = ['id' => $novoId, 'nome' => $nome, 'email' => $email];
        salvarDados(FILE_USUARIOS, $usuarios);
        $mensagem = "Usuário cadastrado com sucesso!";
    } elseif ($acao === 'editar') {
        foreach ($usuarios as &$u) {
            if ($u['id'] == $id) {
                $u['nome'] = $nome;
                $u['email'] = $email;
                break;
            }
        }
        salvarDados(FILE_USUARIOS, $usuarios);
        $mensagem = "Usuário atualizado!";
    }
}

if (isset($_GET['excluir'])) {
    $idExcluir = $_GET['excluir'];
    $usuarios = array_filter($usuarios, fn($u) => $u['id'] != $idExcluir);
    salvarDados(FILE_USUARIOS, array_values($usuarios));
    header('Location: usuarios.php');
    exit;
}

$usuarioEditar = null;
if (isset($_GET['editar'])) {
    foreach ($usuarios as $u) {
        if ($u['id'] == $_GET['editar']) {
            $usuarioEditar = $u;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>CRUD de Usuários</title>
</head>
<body>
    <a href="index.php">⬅ Voltar ao Menu</a>
    <h2>Gerenciamento de Usuários</h2>
    <?php if ($mensagem) echo "<p><strong>$mensagem</strong></p>"; ?>

    <form method="POST">
        <input type="hidden" name="acao" value="<?= $usuarioEditar ? 'editar' : 'criar' ?>">
        <?php if ($usuarioEditar): ?>
            <input type="hidden" name="id" value="<?= $usuarioEditar['id'] ?>">
        <?php endif; ?>
        
        <label>Nome:</label><br>
        <input type="text" name="nome" value="<?= $usuarioEditar['nome'] ?? '' ?>" required><br><br>
        
        <label>E-mail:</label><br>
        <input type="email" name="email" value="<?= $usuarioEditar['email'] ?? '' ?>" required><br><br>
        
        <button type="submit"><?= $usuarioEditar ? 'Atualizar' : 'Cadastrar' ?></button>
    </form>

    <h3>Lista de Usuários</h3>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['nome']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td>
                <a href="usuarios.php?editar=<?= $u['id'] ?>">Editar</a> | 
                <a href="usuarios.php?excluir=<?= $u['id'] ?>" onclick="return confirm('Deseja excluir?')">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
