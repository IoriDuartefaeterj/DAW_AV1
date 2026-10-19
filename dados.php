<?php
define('FILE_USUARIOS', 'usuarios.txt');
define('FILE_PERGUNTAS', 'perguntas.txt');

function lerDados($arquivo) {
    if (!file_exists($arquivo)) return [];
    $conteudo = file_get_contents($arquivo);
    if (empty(trim($conteudo))) return [];
    return json_decode($conteudo, true) ?? [];
}

function salvarDados($arquivo, $dados) {
    file_put_contents($arquivo, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
?>
