<?php

declare(strict_types=1);

/**
 * Funções auxiliares para manipular cadastros a partir de testes e páginas.
 */

function listarAutores(mysqli $conn): array
{
    $autores = [];
    if ($resultado = $conn->query("SELECT CodAu, Nome FROM Autor ORDER BY CodAu ASC")) {
        while ($row = $resultado->fetch_assoc()) {
            $autores[] = $row;
        }
        $resultado->free();
    }
    return $autores;
}

function buscarAutorPorCodigo(mysqli $conn, int $codAu): ?array
{
    $stmt = $conn->prepare("SELECT CodAu, Nome FROM Autor WHERE CodAu=?");
    $stmt->bind_param("i", $codAu);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $autor = $resultado->fetch_assoc() ?: null;
    $stmt->close();

    return $autor;
}

function criarAutor(mysqli $conn, string $nome): bool
{
    $stmt = $conn->prepare("INSERT INTO Autor (Nome) VALUES (?)");
    $stmt->bind_param("s", $nome);
    $ok = $stmt->execute();
    $stmt->close();

    return $ok;
}

function atualizarAutor(mysqli $conn, int $codAu, string $nome): bool
{
    $stmt = $conn->prepare("UPDATE Autor SET Nome=? WHERE CodAu=?");
    $stmt->bind_param("si", $nome, $codAu);
    $ok = $stmt->execute();
    $stmt->close();

    return $ok;
}

function excluirAutor(mysqli $conn, int $codAu): bool
{
    $stmt = $conn->prepare("DELETE FROM Autor WHERE CodAu=?");
    $stmt->bind_param("i", $codAu);
    $ok = $stmt->execute();
    $stmt->close();

    return $ok;
}

function listarAssuntos(mysqli $conn): array
{
    $assuntos = [];
    if ($resultado = $conn->query("SELECT codAs, Descricao FROM Assunto ORDER BY codAs ASC")) {
        while ($row = $resultado->fetch_assoc()) {
            $assuntos[] = $row;
        }
        $resultado->free();
    }
    return $assuntos;
}

function buscarAssuntoPorCodigo(mysqli $conn, int $codAs): ?array
{
    $stmt = $conn->prepare("SELECT codAs, Descricao FROM Assunto WHERE codAs=?");
    $stmt->bind_param("i", $codAs);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $assunto = $resultado->fetch_assoc() ?: null;
    $stmt->close();

    return $assunto;
}

function criarAssunto(mysqli $conn, string $descricao): bool
{
    $stmt = $conn->prepare("INSERT INTO Assunto (Descricao) VALUES (?)");
    $stmt->bind_param("s", $descricao);
    $ok = $stmt->execute();
    $stmt->close();

    return $ok;
}

function atualizarAssunto(mysqli $conn, int $codAs, string $descricao): bool
{
    $stmt = $conn->prepare("UPDATE Assunto SET Descricao=? WHERE codAs=?");
    $stmt->bind_param("si", $descricao, $codAs);
    $ok = $stmt->execute();
    $stmt->close();

    return $ok;
}

function excluirAssunto(mysqli $conn, int $codAs): bool
{
    $stmt = $conn->prepare("DELETE FROM Assunto WHERE codAs=?");
    $stmt->bind_param("i", $codAs);
    $ok = $stmt->execute();
    $stmt->close();

    return $ok;
}

function listarAutoresDisponiveis(mysqli $conn): array
{
    $lista = [];
    if ($resultado = $conn->query("SELECT CodAu, Nome FROM Autor ORDER BY Nome ASC")) {
        while ($row = $resultado->fetch_assoc()) {
            $lista[] = $row;
        }
        $resultado->free();
    }
    return $lista;
}

function listarAssuntosDisponiveis(mysqli $conn): array
{
    $lista = [];
    if ($resultado = $conn->query("SELECT codAs, Descricao FROM Assunto ORDER BY Descricao ASC")) {
        while ($row = $resultado->fetch_assoc()) {
            $lista[] = $row;
        }
        $resultado->free();
    }
    return $lista;
}

function listarLivros(mysqli $conn): array
{
    $sql = "SELECT 
                l.Codl, l.Titulo, l.Editora, l.Edicao, l.AnoPublicacao, l.Valor,
                IFNULL(GROUP_CONCAT(DISTINCT a.Nome SEPARATOR ', '), 'Sem Autor') AS Autores,
                IFNULL(GROUP_CONCAT(DISTINCT s.Descricao SEPARATOR ', '), 'Sem Assunto') AS Assuntos
            FROM 
                Livro l
            LEFT JOIN 
                Livro_Autor la ON l.Codl = la.Livro_Codl
            LEFT JOIN 
                Autor a ON la.Autor_CodAu = a.CodAu
            LEFT JOIN 
                Livro_Assunto ls ON l.Codl = ls.Livro_Codl
            LEFT JOIN 
                Assunto s ON ls.Assunto_codAs = s.codAs
            GROUP BY 
                l.Codl, l.Titulo, l.Editora, l.Edicao, l.AnoPublicacao, l.Valor
            ORDER BY 
                l.Codl ASC";

    $livros = [];
    if ($resultado = $conn->query($sql)) {
        while ($row = $resultado->fetch_assoc()) {
            $livros[] = $row;
        }
        $resultado->free();
    }

    return $livros;
}

function buscarLivroPorCodigo(mysqli $conn, int $codl): ?array
{
    $stmt = $conn->prepare("SELECT * FROM Livro WHERE Codl = ?");
    $stmt->bind_param("i", $codl);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $livro = $resultado->fetch_assoc() ?: null;
    $stmt->close();

    return $livro;
}

function autoresDoLivro(mysqli $conn, int $codl): array
{
    $stmt = $conn->prepare("SELECT Autor_CodAu FROM Livro_Autor WHERE Livro_Codl = ?");
    $stmt->bind_param("i", $codl);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $ids = [];
    while ($row = $resultado->fetch_assoc()) {
        $ids[] = (int)$row['Autor_CodAu'];
    }
    $stmt->close();

    return $ids;
}

function assuntosDoLivro(mysqli $conn, int $codl): array
{
    $stmt = $conn->prepare("SELECT Assunto_codAs FROM Livro_Assunto WHERE Livro_Codl = ?");
    $stmt->bind_param("i", $codl);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $ids = [];
    while ($row = $resultado->fetch_assoc()) {
        $ids[] = (int)$row['Assunto_codAs'];
    }
    $stmt->close();

    return $ids;
}

function criarLivro(mysqli $conn, array $dados, array $autores, array $assuntos): ?int
{
    $stmt = $conn->prepare("INSERT INTO Livro (Titulo, Editora, Edicao, AnoPublicacao, Valor) VALUES (?, ?, ?, ?, ?)");

    $titulo = $dados['titulo'];
    $editora = $dados['editora'] ?? null;
    $edicao = isset($dados['edicao']) ? (int)$dados['edicao'] : null;
    $anoPublicacao = $dados['anoPublicacao'] ?? null;
    $valor = isset($dados['valor']) ? (float)$dados['valor'] : 0.0;

    $stmt->bind_param("ssisd", $titulo, $editora, $edicao, $anoPublicacao, $valor);
    $ok = $stmt->execute();
    $livroId = $stmt->insert_id;
    $stmt->close();

    if (!$ok) {
        return null;
    }

    sincronizarAutoresDoLivro($conn, $livroId, $autores);
    sincronizarAssuntosDoLivro($conn, $livroId, $assuntos);

    return $livroId;
}

function atualizarLivro(mysqli $conn, int $codl, array $dados, array $autores, array $assuntos): bool
{
    $stmt = $conn->prepare("UPDATE Livro SET Titulo=?, Editora=?, Edicao=?, AnoPublicacao=?, Valor=? WHERE Codl=?");

    $titulo = $dados['titulo'];
    $editora = $dados['editora'] ?? null;
    $edicao = isset($dados['edicao']) ? (int)$dados['edicao'] : null;
    $anoPublicacao = $dados['anoPublicacao'] ?? null;
    $valor = isset($dados['valor']) ? (float)$dados['valor'] : 0.0;

    $stmt->bind_param("ssisdi", $titulo, $editora, $edicao, $anoPublicacao, $valor, $codl);
    $ok = $stmt->execute();
    $stmt->close();

    if (!$ok) {
        return false;
    }

    sincronizarAutoresDoLivro($conn, $codl, $autores);
    sincronizarAssuntosDoLivro($conn, $codl, $assuntos);

    return true;
}

function excluirLivro(mysqli $conn, int $codl): bool
{
    $stmt = $conn->prepare("DELETE FROM Livro WHERE Codl=?");
    $stmt->bind_param("i", $codl);
    $ok = $stmt->execute();
    $stmt->close();

    return $ok;
}

function sincronizarAutoresDoLivro(mysqli $conn, int $codl, array $autores): void
{
    $stmt = $conn->prepare("DELETE FROM Livro_Autor WHERE Livro_Codl=?");
    $stmt->bind_param("i", $codl);
    $stmt->execute();
    $stmt->close();

    if (empty($autores)) {
        return;
    }

    $stmt = $conn->prepare("INSERT INTO Livro_Autor (Livro_Codl, Autor_CodAu) VALUES (?, ?)");
    $stmt->bind_param("ii", $codlRef, $autorRef);
    $codlRef = $codl;
    foreach ($autores as $autorId) {
        $autorRef = (int)$autorId;
        $stmt->execute();
    }
    $stmt->close();
}

function sincronizarAssuntosDoLivro(mysqli $conn, int $codl, array $assuntos): void
{
    $stmt = $conn->prepare("DELETE FROM Livro_Assunto WHERE Livro_Codl=?");
    $stmt->bind_param("i", $codl);
    $stmt->execute();
    $stmt->close();

    if (empty($assuntos)) {
        return;
    }

    $stmt = $conn->prepare("INSERT INTO Livro_Assunto (Livro_Codl, Assunto_codAs) VALUES (?, ?)");
    $stmt->bind_param("ii", $codlRef, $assuntoRef);
    $codlRef = $codl;
    foreach ($assuntos as $assuntoId) {
        $assuntoRef = (int)$assuntoId;
        $stmt->execute();
    }
    $stmt->close();
}
