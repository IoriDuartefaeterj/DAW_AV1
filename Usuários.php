<?php require_once 'db_helper.php';
$mensagem = '';
$Usuários = leitura(Arquivo_Usuário);
if ($_server['REQUEST_METHOD'] == 'POST'){
    $Ação = $_POST['Ação'] ?? '';
    $ID = $_POST['ID'] ?? null;
    $Nome = $_POST['Nome'] ?? '';
    $Email = $_POST['Email'] ?? '';
    if($Ação == 'Criar'){
        $NID = count($Usuários)>0 ? max(array_column($Usuários, 'ID'))+1:1;
        $Usuários[] = ['ID'=>$NID, 'Nome'=>$Nome, 'Email'=>$Email];
        salvar(Arquivo_Usuário, $Usuários);
        $mensagem="Usuário Corretamente Cadastrado";
    }
}?>