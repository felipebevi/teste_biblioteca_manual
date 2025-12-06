<?php

include_once(dirname(__FILE__) . '/db.php');
include_once(dirname(__FILE__) . '/src/CadastroRepository.php');

if(isset($_GET['acao']) && $_GET['acao'] === 'excluir' && isset($_GET['codl'])){
    $codl = intval($_GET['codl']);
    if(excluirLivro($conn, $codl)){
        $msg = "Livro excluído com sucesso!";
    } else {
        $msg = "Erro ao excluir livro";
    }
    header("Location: ?msg=" . urlencode($msg));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'titulo' => $_POST['titulo'],
        'editora' => $_POST['editora'] ?? null,
        'edicao' => $_POST['edicao'] ?? null,
        'anoPublicacao' => $_POST['anoPublicacao'] ?? null,
        'valor' => isset($_POST['valor']) ? floatval(str_replace(',', '.', $_POST['valor'])) : 0
    ];
    $autores = isset($_POST['autores']) ? array_map('intval', (array)$_POST['autores']) : [];
    $assuntos = isset($_POST['assuntos']) ? array_map('intval', (array)$_POST['assuntos']) : [];

    if (isset($_GET['acao']) && $_GET['acao'] === 'editar' && isset($_GET['codl'])) {
        $codl = intval($_GET['codl']);
        if (atualizarLivro($conn, $codl, $dados, $autores, $assuntos)) {
            $msg = "Livro atualizado com sucesso!";
        } else {
            $msg = "Erro ao atualizar livro";
        }
    } else {
        $livroId = criarLivro($conn, $dados, $autores, $assuntos);
        $msg = $livroId ? "Livro cadastrado com sucesso!" : "Erro ao cadastrar livro";
    }
    header("Location: cadLivro.php?msg=" . urlencode($msg));
    exit();
}

// Buscar autores e assuntos para os selects
$autores = listarAutoresDisponiveis($conn);
$assuntos = listarAssuntosDisponiveis($conn);

include_once(dirname(__FILE__) . '/header.php');
?>

<div class="container">
    <h1>Cadastro de Livro</h1>
    <?php if (isset($msg)): ?>
        <div class="alert alert-info msg-alerta"><?php echo $msg; ?></div>
    <?php endif; ?>
    
    <div class="table-responsive">
        <table class="table table-striped">
        <thead>
            <tr>
                <th>Código</th>
                <th>Título</th>
                <th>Editora</th>
                <th>Edição</th>
                <th>Ano de Publicação</th>
                <th>Valor</th>
                <th>Autores</th>
                <th>Assuntos</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (listarLivros($conn) as $livro): ?>
                <tr>
                    <td><?php echo $livro['Codl']; ?></td>
                    <td><?php echo $livro['Titulo']; ?></td>
                    <td><?php echo $livro['Editora']; ?></td>
                    <td><?php echo $livro['Edicao']; ?></td>
                    <td><?php echo $livro['AnoPublicacao']; ?></td>
                    <td>R$ <?php echo number_format($livro['Valor'], 2, ',', '.'); ?></td>
                    <td><?php echo $livro['Autores']; ?></td>
                    <td><?php echo $livro['Assuntos']; ?></td>
                    <td>
                        <a href="?acao=editar&codl=<?php echo $livro['Codl']; ?>" class="btn btn-sm btn-primary">Editar</a>
                        <a href="?acao=excluir&codl=<?php echo $livro['Codl']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este livro?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (isset($_GET['acao']) && $_GET['acao'] === 'editar' && isset($_GET['codl'])):
        $codl = intval($_GET['codl']);
        $livroEdicao = buscarLivroPorCodigo($conn, $codl);
    ?>
    <h2>Editar Livro</h2>
    <form method="POST" action="cadLivro.php?acao=editar&codl=<?php echo $codl; ?>">
        <div class="form-group">
            <label for="titulo">Título:</label>
            <input type="text" class="form-control" id="titulo" name="titulo" value="<?php echo $livroEdicao['Titulo']; ?>" required>
        </div>
        <div class="form-group">
            <label for="editora">Editora:</label>
            <input type="text" class="form-control" id="editora" name="editora" value="<?php echo $livroEdicao['Editora']; ?>">
        </div>
        <div class="form-group">
            <label for="edicao">Edição:</label>
            <input type="number" class="form-control" id="edicao" name="edicao" value="<?php echo $livroEdicao['Edicao']; ?>">
        </div>
        <div class="form-group">
            <label for="anoPublicacao">Ano de Publicação:</label>
            <input type="text" class="form-control" id="anoPublicacao" name="anoPublicacao" value="<?php echo $livroEdicao['AnoPublicacao']; ?>" maxlength="4">
        </div>
        <div class="form-group">
            <label for="autores">Autores:</label>
            <select multiple class="form-control" id="autores" name="autores[]" required>
                <?php
                $autorIds = autoresDoLivro($conn, $codl);
                foreach ($autores as $autor): ?>
                    <option value="<?php echo $autor['CodAu']; ?>" <?php echo in_array($autor['CodAu'], $autorIds) ? 'selected' : ''; ?>>
                        <?php echo $autor['Nome']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="assuntos">Assuntos:</label>
            <select multiple class="form-control" id="assuntos" name="assuntos[]" required>
                <?php
                $assuntoIds = assuntosDoLivro($conn, $codl);
                foreach ($assuntos as $assunto): ?>
                    <option value="<?php echo $assunto['codAs']; ?>" <?php echo in_array($assunto['codAs'], $assuntoIds) ? 'selected' : ''; ?>>
                        <?php echo $assunto['Descricao']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="valor">Valor:</label>
            <input type="text" class="form-control" id="valor" name="valor" value="<?php echo $livroEdicao['Valor']; ?>">
        </div>
        
        <button type="submit" class="btn btn-primary">Atualizar Livro</button>
    </form>
    <hr>
<?php else: ?> 
    <hr>
    <h2> Adicionar novo Livro</h2>
    <form method="POST" action="cadLivro.php">
        <div class="form-group">
            <label for="titulo">Título:</label>
            <input type="text" class="form-control" id="titulo" name="titulo" required>
        </div>
        <div class="form-group">
            <label for="editora">Editora:</label>
            <input type="text" class="form-control" id="editora" name="editora">
        </div>
        <div class="form-group">
            <label for="edicao">Edição:</label>
            <input type="number" class="form-control" id="edicao" name="edicao">
        </div>
        <div class="form-group">
            <label for="anoPublicacao">Ano de Publicação:</label>
            <input type="text" class="form-control" id="anoPublicacao" name="anoPublicacao" maxlength="4">
        </div>
        <div class="form-group">
            <label for="autores">Autores:</label>
            <select multiple class="form-control" id="autores" name="autores[]" required>
                <?php foreach ($autores as $autor): ?>
                    <option value="<?php echo $autor['CodAu']; ?>"><?php echo $autor['Nome']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="assuntos">Assuntos:</label>
            <select multiple class="form-control" id="assuntos" name="assuntos[]" required>
                <?php foreach ($assuntos as $assunto): ?>
                    <option value="<?php echo $assunto['codAs']; ?>"><?php echo $assunto['Descricao']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="valor">Valor:</label>
            <input type="number" step="0.01" class="form-control" id="valor" name="valor">
        </div>
        <button type="submit" class="btn btn-primary">Cadastrar Livro</button>
    </form>
<?php endif; ?>
</div>
<?php
include_once(dirname(__FILE__) . '/footer.php');
