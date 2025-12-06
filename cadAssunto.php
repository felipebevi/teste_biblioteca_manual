<?php 

    include_once(dirname(__FILE__) . '/db.php');

    // Buscar assuntos atuais para exibir na tabela
    $assuntos_atuais = [];
    $sql = "SELECT codAs, Descricao FROM Assunto";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $assuntos_atuais[] = $row;
        }
    }

    // Processar formulários de adição, edição e exclusão
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $acao = $_POST['acao'];
        if ($acao == 'adicionar') {
            $Descricao = $conn->real_escape_string($_POST['Descricao']);
            $sql = "INSERT INTO Assunto (Descricao) VALUES ('$Descricao')";
            if ($conn->query($sql) === TRUE) {
                header("Location: ?msg=Assunto adicionado com sucesso!");
                exit();
            } else {
                header("Location: ?msg=Erro ao adicionar assunto: " . urlencode($conn->error));
                exit();
            }
        } elseif ($acao == 'editar' && isset($_POST['codAs'])) {
            $codAs = $conn->real_escape_string($_POST['codAs']);
            $Descricao = $conn->real_escape_string($_POST['Descricao']);
            $sql = "UPDATE Assunto SET Descricao='$Descricao' WHERE codAs=$codAs";
            if ($conn->query($sql) === TRUE) {
                header("Location: ?msg=Assunto atualizado com sucesso!");
                exit();
            } else {
                header("Location: ?msg=Erro ao atualizar assunto: " . urlencode($conn->error));
                exit();
            }
        }
    } elseif (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['codAs'])) {
        $codAs = $conn->real_escape_string($_GET['codAs']);
        $sql = "DELETE FROM Assunto WHERE codAs=$codAs";
        if ($conn->query($sql) === TRUE) {
            header("Location: ?msg=Assunto excluído com sucesso!");
            exit();
        } else {
            header("Location: ?msg=Erro ao excluir assunto: " . urlencode($conn->error));
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
        $codAs=$conn->real_escape_string($_GET['codAs']);
        $sql="SELECT Descricao FROM Assunto WHERE codAs=$codAs";
        $result=$conn->query($sql);
        if($result->num_rows==1){
            $row=$result->fetch_assoc();
            $descricao_atual=$row['Descricao'];
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
    

    