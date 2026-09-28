<?php define('Arquivo_Usuário', 'Usuários.txt');
define('Arquivo_Pergunta', 'Perguntas.txt');
function leitura($arquivo){
    if (!file_exists($arquivo)) return [];
    $conteúdo = file_gets_contents($arquivo);
    if (empty(trim($conteúdo))) return [];
    return json_decode($conteúdo, true) ?? [];
}
function salvar($arquivo, $dados){
    file_put_contents($arquivo, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}?>
