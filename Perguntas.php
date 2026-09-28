<?php require_once 'db_helper.php';
$mensagem = '';
$Perguntas = leitura(Arquivo_Pergunta);
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $Ação = $_POST['Ação'] ?? '';
    $ID = $_POST['ID'] ?? null;
    $Tipo = $_POST['Tipo'] ?? 'Múltipla';
    $Enunciado = $_POST['Enunciado'] ?? '';
    if ($Tipo == 'Múltipla'){
        $Opções = $_POST['Opções'] ?? [];
        $Certo = $_POST['Certo'] ?? 0;
        $Dados = [ 'Tipo'=>$Tipo, 'Enunciado'=>$Enunciado, 'Opções'=>$Opções, 'Certo'=>$Certo];
    }
    if($Ação == 'Criar'){
        $NID = count($Perguntas)>0 ? max(array_column($Perguntas, 'ID'))+1:1;
        $Dados['ID'] = $NID;
        $Perguntas[] = $Dados;
        $mensagem="Pergunta Corretamente Criada";
    }
}