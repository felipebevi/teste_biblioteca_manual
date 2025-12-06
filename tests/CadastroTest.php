<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/CadastroRepository.php';

final class CadastroTest extends TestCase
{
    private mysqli $conn;

    protected function setUp(): void
    {
        parent::setUp();
        $this->conn = new mysqli(
            $_ENV['DB_HOST'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASSWORD'],
            $_ENV['DB_NAME'],
            (int)$_ENV['DB_PORT']
        );
        $this->conn->set_charset('utf8mb4');
        $this->resetSchema();
    }

    protected function tearDown(): void
    {
        $this->conn->close();
        parent::tearDown();
    }

    public function testCrudAutor(): void
    {
        $this->assertTrue(criarAutor($this->conn, 'Machado de Assis'));
        $autorId = $this->conn->insert_id;

        $autores = listarAutores($this->conn);
        $this->assertCount(1, $autores);
        $this->assertSame('Machado de Assis', $autores[0]['Nome']);

        $this->assertTrue(atualizarAutor($this->conn, $autorId, 'Machado Atualizado'));
        $autorAtualizado = buscarAutorPorCodigo($this->conn, $autorId);
        $this->assertNotNull($autorAtualizado);
        $this->assertSame('Machado Atualizado', $autorAtualizado['Nome']);

        $this->assertTrue(excluirAutor($this->conn, $autorId));
        $this->assertSame([], listarAutores($this->conn));
    }

    public function testCrudAssunto(): void
    {
        $this->assertTrue(criarAssunto($this->conn, 'Romance'));
        $assuntoId = $this->conn->insert_id;

        $assuntos = listarAssuntos($this->conn);
        $this->assertCount(1, $assuntos);
        $this->assertSame('Romance', $assuntos[0]['Descricao']);

        $this->assertTrue(atualizarAssunto($this->conn, $assuntoId, 'Realismo'));
        $assuntoAtualizado = buscarAssuntoPorCodigo($this->conn, $assuntoId);
        $this->assertNotNull($assuntoAtualizado);
        $this->assertSame('Realismo', $assuntoAtualizado['Descricao']);

        $this->assertTrue(excluirAssunto($this->conn, $assuntoId));
        $this->assertSame([], listarAssuntos($this->conn));
    }

    public function testLivroCadastroAtualizacaoEExclusao(): void
    {
        $autor1 = $this->criarAutorERetornarId('Autor 1');
        $autor2 = $this->criarAutorERetornarId('Autor 2');
        $assunto1 = $this->criarAssuntoERetornarId('Romance');
        $assunto2 = $this->criarAssuntoERetornarId('Drama');

        $livroId = criarLivro($this->conn, [
            'titulo' => 'Dom Casmurro',
            'editora' => 'Editora X',
            'edicao' => 1,
            'anoPublicacao' => '1899',
            'valor' => 59.9,
        ], [$autor1, $autor2], [$assunto1]);

        $this->assertIsInt($livroId);

        $livros = listarLivros($this->conn);
        $this->assertCount(1, $livros);
        $this->assertSame('Dom Casmurro', $livros[0]['Titulo']);
        $this->assertStringContainsString('Autor 1', $livros[0]['Autores']);
        $this->assertStringContainsString('Autor 2', $livros[0]['Autores']);
        $this->assertStringContainsString('Romance', $livros[0]['Assuntos']);

        $this->assertTrue(atualizarLivro($this->conn, $livroId, [
            'titulo' => 'Memorias Postumas',
            'editora' => 'Editora Y',
            'edicao' => 2,
            'anoPublicacao' => '1881',
            'valor' => 75.5,
        ], [$autor2], [$assunto2]));

        $livroAtualizado = buscarLivroPorCodigo($this->conn, $livroId);
        $this->assertNotNull($livroAtualizado);
        $this->assertSame('Memorias Postumas', $livroAtualizado['Titulo']);
        $this->assertSame('Editora Y', $livroAtualizado['Editora']);
        $this->assertSame('1881', $livroAtualizado['AnoPublicacao']);
        $this->assertEquals(75.5, (float)$livroAtualizado['Valor']);

        $autoresAssociados = autoresDoLivro($this->conn, $livroId);
        sort($autoresAssociados);
        $this->assertSame([$autor2], $autoresAssociados);

        $assuntosAssociados = assuntosDoLivro($this->conn, $livroId);
        sort($assuntosAssociados);
        $this->assertSame([$assunto2], $assuntosAssociados);

        $this->assertTrue(excluirLivro($this->conn, $livroId));
        $this->assertSame([], listarLivros($this->conn));
    }

    private function resetSchema(): void
    {
        $this->conn->query('DROP VIEW IF EXISTS vw_relatorio_autor_livros');
        $this->conn->query('SET FOREIGN_KEY_CHECKS=0');
        foreach (['Livro_Assunto', 'Livro_Autor', 'Livro', 'Assunto', 'Autor'] as $table) {
            $this->conn->query("DROP TABLE IF EXISTS {$table}");
        }
        $this->conn->query('SET FOREIGN_KEY_CHECKS=1');

        $schema = file_get_contents(__DIR__ . '/../tabelas.sql');
        if ($schema === false) {
            throw new RuntimeException('Nao foi possivel carregar tabelas.sql');
        }

        if (!$this->conn->multi_query($schema)) {
            throw new RuntimeException('Erro ao recriar o schema: ' . $this->conn->error);
        }

        while ($this->conn->more_results()) {
            $this->conn->next_result();
        }
    }

    private function criarAutorERetornarId(string $nome): int
    {
        $this->assertTrue(criarAutor($this->conn, $nome));
        return (int)$this->conn->insert_id;
    }

    private function criarAssuntoERetornarId(string $descricao): int
    {
        $this->assertTrue(criarAssunto($this->conn, $descricao));
        return (int)$this->conn->insert_id;
    }
}
