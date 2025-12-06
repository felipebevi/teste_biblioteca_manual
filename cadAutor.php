<?php

    include_once(dirname(__FILE__) . '/db.php');
    include_once(dirname(__FILE__) . '/src/CadastroRepository.php');

    $autores_atuais = listarAutores($conn);

    // verifica se existe acoes de adicionar, editar ou excluir e prepara mensagem no bootstrap
    if(isset($_GET['acao'])){
        $acao=$_GET['acao'];
        if($acao=='adicionar' && $_SERVER['REQUEST_METHOD']=='POST'){
            $nome=trim($_POST['Nome']);
            if($nome !== '' && criarAutor($conn, $nome)){
                header("Location: ?msg=Autor adicionado com sucesso!");
                exit();
            } else {
                header("Location: ?msg=Erro ao adicionar autor");
                exit();
            }
        } elseif($acao=='editar' && isset($_GET['CodAu']) && $_SERVER['REQUEST_METHOD']=='POST'){
            $CodAu=(int)$_GET['CodAu'];
            $nome=trim($_POST['Nome']);
            if($nome !== '' && atualizarAutor($conn, $CodAu, $nome)){
                header("Location: ?msg=Autor atualizado com sucesso!");
                exit();
            } else {
                header("Location: ?msg=Erro ao atualizar autor");
                exit();
            }
        } elseif($acao=='excluir' && isset($_GET['CodAu'])){
            $CodAu=(int)$_GET['CodAu'];
            if(excluirAutor($conn, $CodAu)){
                header("Location: ?msg=Autor excluído com sucesso!");
                exit();
            } else {
                header("Location: ?msg=Erro ao excluir autor");
                exit();
            }
        }
    }



    include_once(dirname(__FILE__) . '/header.php');
?>

<h1> Cadastro de Autor </h1>

<?php if(isset($_GET['msg'])){
    $msg=$_GET['msg'];
    echo '<div class="alert alert-info msg-alerta" role="alert">' . htmlspecialchars($msg) . '</div>';
} ?>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>CodAu</th>
                <th>Nome</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($autores_atuais as $autor){ ?>
                <tr>
                    <td><?php echo htmlspecialchars($autor['CodAu']); ?></td>
                    <td><?php echo htmlspecialchars($autor['Nome']); ?></td>
                    <td>
                        <a href="?acao=editar&CodAu=<?php echo urlencode($autor['CodAu']); ?>" class="btn btn-primary btn-sm">Editar</a>
                        <a href="?acao=excluir&CodAu=<?php echo urlencode($autor['CodAu']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este autor?');">Excluir</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<h2>Adicionar Novo Autor</h2>

<?php
// eu poderia usar o mesmo form cheio de ternario para tratar edicao e inclusao, mas separei pra manter simples
if(isset($_GET['acao']) && $_GET['acao']=='editar' && isset($_GET['CodAu'])){
    $CodAu_editar=(int)$_GET['CodAu'];
    $autor_editar=buscarAutorPorCodigo($conn, $CodAu_editar);
    if($autor_editar){
        ?>
        <form method="POST" action="?acao=editar&CodAu=<?php echo urlencode($autor_editar['CodAu']); ?>">
            <div class="mb-3">
                <label for="Nome" class="form-label">Nome do Autor</label>
                <input type="text" class="form-control" CodAu="Nome" name="Nome" value="<?php echo htmlspecialchars($autor_editar['Nome']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar Autor</button>
        </form>
        <hr>
        <?php
    } else {
        echo '<div class="alert alert-danger" role="alert">Autor não encontrado para edição.</div>';
    }
}else{
?>
<form method="POST" action="?acao=adicionar">
    <div class="mb-3">
        <label for="Nome" class="form-label">Nome do Autor</label>
        <input type="text" class="form-control" CodAu="Nome" name="Nome" required>
    </div>
    <button type="submit" class="btn btn-success">Adicionar Autor</button>
</form>
<?php } ?>

<?php
    include_once(dirname(__FILE__) . '/footer.php');
