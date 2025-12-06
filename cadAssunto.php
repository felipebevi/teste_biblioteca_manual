<?php 

    include_once(dirname(__FILE__) . '/db.php');
    include_once(dirname(__FILE__) . '/src/CadastroRepository.php');

    // Buscar assuntos atuais para exibir na tabela
    $assuntos_atuais = listarAssuntos($conn);

    // Processar formulários de adição, edição e exclusão
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $acao = $_POST['acao'];
        if ($acao == 'adicionar') {
            $Descricao = trim($_POST['Descricao']);
            if ($Descricao !== '' && criarAssunto($conn, $Descricao)) {
                header("Location: ?msg=Assunto adicionado com sucesso!");
                exit();
            } else {
                header("Location: ?msg=Erro ao adicionar assunto");
                exit();
            }
        } elseif ($acao == 'editar' && isset($_POST['codAs'])) {
            $codAs = (int)$_POST['codAs'];
            $Descricao = trim($_POST['Descricao']);
            if ($Descricao !== '' && atualizarAssunto($conn, $codAs, $Descricao)) {
                header("Location: ?msg=Assunto atualizado com sucesso!");
                exit();
            } else {
                header("Location: ?msg=Erro ao atualizar assunto");
                exit();
            }
        }
    } elseif (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['codAs'])) {
        $codAs = (int)$_GET['codAs'];
        if (excluirAssunto($conn, $codAs)) {
            header("Location: ?msg=Assunto excluído com sucesso!");
            exit();
        } else {
            header("Location: ?msg=Erro ao excluir assunto");
            exit();
        }
    }
    include_once(dirname(__FILE__) . '/header.php');
?>
<h1> Cadastro de Assunto </h1>
<?php if(isset($_GET['msg'])){
    $msg=$_GET['msg'];
    echo "<div class='alert alert-info msg-alerta' role='alert'>$msg</div>";
} ?>    
<table class="table table-striped">
    <thead>
        <tr>
            <th>Código</th>
            <th>Descrição</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($assuntos_atuais as $assunto): ?>
        <tr>
            <td><?php echo htmlspecialchars($assunto['codAs']); ?></td>
            <td><?php echo htmlspecialchars($assunto['Descricao']); ?></td>
            <td>
                <a href="?acao=editar&codAs=<?php echo $assunto['codAs']; ?>" class="btn btn-primary btn-sm">Editar</a>
                <a href="?acao=excluir&codAs=<?php echo $assunto['codAs']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este assunto?');">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
    if(isset($_GET['acao']) && $_GET['acao']=='editar' && isset($_GET['codAs'])){
        $codAs=(int)$_GET['codAs'];
        $assunto = buscarAssuntoPorCodigo($conn, $codAs);
        if($assunto){
            $descricao_atual=$assunto['Descricao'];
        } else {
            echo "<div class='alert alert-danger msg-alerta' role='alert'>Assunto não encontrado para edição.</div>";
            $codAs=null;
        }
    } else {
        $codAs=null;
    }
?>

<!-- exemplo de formulario com ternario, sem usar 2 forms separados -->

<h2><?php echo $codAs ? 'Editar Assunto' : 'Adicionar Novo Assunto'; ?></h2>
<form method="POST" action="?">
    <input type="hidden" name="acao" value="<?php echo $codAs ? 'editar' : 'adicionar'; ?>">
    <?php if($codAs): ?>
        <input type="hidden" name="codAs" value="<?php echo htmlspecialchars($codAs); ?>">
    <?php endif; ?>
    <div class="mb-3">
        <label for="Descricao" class="form-label">Descrição do Assunto</label>
        <input type="text" class="form-control" id="Descricao" name="Descricao" value="<?php echo $codAs ? htmlspecialchars($descricao_atual) : ''; ?>" required>
    </div>
    <button type="submit" class="btn btn-success"><?php echo $codAs ? 'Atualizar Assunto' : 'Adicionar Assunto'; ?></button>
</form>

<?php
    include_once(dirname(__FILE__) . '/footer.php');
    

    
