<?php
require_once 'dados.php';

$mensagem = '';
$perguntas = lerDados(FILE_PERGUNTAS);

// Ação de Excluir (Requisito 7)
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $perguntas = array_filter($perguntas, fn($p) => $p['id'] != $id);
    salvarDados(FILE_PERGUNTAS, array_values($perguntas));
    header('Location: perguntas.php');
    exit;
}

// Criar / Alterar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $id = $_POST['id'] ?? null;
    $tipo = $_POST['tipo'] ?? 'multipla_escolha';
    $enunciado = $_POST['enunciado'] ?? '';
    
    if ($tipo === 'multipla_escolha') {
        $opcoes = $_POST['opcoes'] ?? [];
        $correta = $_POST['correta'] ?? 0;
        $dadosPergunta = [
            'tipo' => $tipo,
            'enunciado' => $enunciado,
            'opcoes' => $opcoes,
            'correta' => $correta
        ];
    } else {
        $respostaTexto = $_POST['resposta_texto'] ?? '';
        $dadosPergunta = [
            'tipo' => $tipo,
            'enunciado' => $enunciado,
            'resposta_texto' => $respostaTexto
        ];
    }

    if ($acao === 'criar') {
        $novoId = count($perguntas) > 0 ? max(array_column($perguntas, 'id')) + 1 : 1;
        $dadosPergunta['id'] = $novoId;
        $perguntas[] = $dadosPergunta;
        $mensagem = "Pergunta criada com sucesso!";
    } elseif ($acao === 'editar') {
        foreach ($perguntas as &$p) {
            if ($p['id'] == $id) {
                $p = array_merge(['id' => (int)$id], $dadosPergunta);
                break;
            }
        }
        $mensagem = "Pergunta atualizada!";
    }
    
    salvarDados(FILE_PERGUNTAS, $perguntas);
}

// Dados para edição
$editarP = null;
if (isset($_GET['editar'])) {
    foreach ($perguntas as $p) {
        if ($p['id'] == $_GET['editar']) {
            $editarP = $p;
            break;
        }
    }
}

// Visualizar apenas UMA pergunta (Requisito 6)
$visualizarP = null;
if (isset($_GET['ver'])) {
    foreach ($perguntas as $p) {
        if ($p['id'] == $_GET['ver']) {
            $visualizarP = $p;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Perguntas</title>
</head>
<body>
    <a href="index.php">⬅ Voltar ao Menu</a>
    <h2>Gerenciamento de Perguntas e Respostas</h2>
    <?php if ($mensagem) echo "<p><strong>$mensagem</strong></p>"; ?>

    <!-- Formulário de Cadastro/Edição (Requisitos 1, 2, 3 e 4) -->
    <h3><?= $editarP ? 'Alterar Pergunta' : 'Criar Nova Pergunta' ?></h3>
    <form method="POST">
        <input type="hidden" name="acao" value="<?= $editarP ? 'editar' : 'criar' ?>">
        <?php if ($editarP): ?>
            <input type="hidden" name="id" value="<?= $editarP['id'] ?>">
        <?php endif; ?>

        <label>Tipo de Pergunta:</label>
        <select name="tipo" onchange="this.form.submit()">
            <option value="multipla_escolha" <?= ($editarP['tipo'] ?? $_POST['tipo'] ?? '') === 'multipla_escolha' ? 'selected' : '' ?>>Múltipla Escolha</option>
            <option value="texto" <?= ($editarP['tipo'] ?? $_POST['tipo'] ?? '') === 'texto' ? 'selected' : '' ?>>Texto</option>
        </select><br><br>

        <label>Enunciado / Pergunta:</label><br>
        <textarea name="enunciado" rows="3" cols="50" required><?= $editarP['enunciado'] ?? '' ?></textarea><br><br>

        <?php 
        $tipoAtual = $editarP['tipo'] ?? $_POST['tipo'] ?? 'multipla_escolha';
        if ($tipoAtual === 'multipla_escolha'): 
        ?>
            <h4>Opções de Resposta:</h4>
            <?php for ($i = 0; $i < 4; $i++): ?>
                <input type="radio" name="correta" value="<?= $i ?>" <?= ($editarP['correta'] ?? 0) == $i ? 'checked' : '' ?>>
                Opção <?= $i + 1 ?>: 
                <input type="text" name="opcoes[]" value="<?= $editarP['opcoes'][$i] ?? '' ?>" required><br>
            <?php endfor; ?>
            <small>* Marque a alternativa correta.</small><br><br>
        <?php else: ?>
            <label>Resposta Esperada (Texto):</label><br>
            <textarea name="resposta_texto" rows="2" cols="50" required><?= $editarP['resposta_texto'] ?? '' ?></textarea><br><br>
        <?php endif; ?>

        <button type="submit"><?= $editarP ? 'Salvar Alterações' : 'Salvar Pergunta' ?></button>
    </form>

    <hr>

    <!-- Exibição de UMA Pergunta (Requisito 6) -->
    <?php if ($visualizarP): ?>
        <h3>Detalhes da Pergunta #<?= $visualizarP['id'] ?></h3>
        <p><strong>Tipo:</strong> <?= $visualizarP['tipo'] ?></p>
        <p><strong>Enunciado:</strong> <?= htmlspecialchars($visualizarP['enunciado']) ?></p>
        <?php if ($visualizarP['tipo'] === 'multipla_escolha'): ?>
            <ul>
                <?php foreach ($visualizarP['opcoes'] as $idx => $opcao): ?>
                    <li><?= htmlspecialchars($opcao) ?> <?= $idx == $visualizarP['correta'] ? '<strong>(Correta)</strong>' : '' ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p><strong>Resposta Esperada:</strong> <?= htmlspecialchars($visualizarP['resposta_texto']) ?></p>
        <?php endif; ?>
        <a href="perguntas.php">Fechar Detalhes</a>
        <hr>
    <?php endif; ?>

    <!-- Lista de TODAS as Perguntas (Requisito 5) -->
    <h3>Lista de todas as Perguntas</h3>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Tipo</th>
            <th>Enunciado</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($perguntas as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= $p['tipo'] ?></td>
            <td><?= htmlspecialchars(substr($p['enunciado'], 0, 50)) ?>...</td>
            <td>
                <a href="perguntas.php?ver=<?= $p['id'] ?>">Ver</a> | 
                <a href="perguntas.php?editar=<?= $p['id'] ?>">Editar</a> | 
                <a href="perguntas.php?excluir=<?= $p['id'] ?>" onclick="return confirm('Excluir pergunta?')">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
