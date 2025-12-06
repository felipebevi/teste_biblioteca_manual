CREATE TABLE Autor (
    CodAu INT PRIMARY KEY AUTO_INCREMENT,
    Nome VARCHAR(40) NOT NULL
);

CREATE TABLE Assunto (
    codAs INT PRIMARY KEY AUTO_INCREMENT,
    Descricao VARCHAR(20) NOT NULL
);

CREATE TABLE Livro (
    Codl INT PRIMARY KEY AUTO_INCREMENT,
    Titulo VARCHAR(40) NOT NULL,
    Editora VARCHAR(40),
    Edicao INT,
    AnoPublicacao VARCHAR(4),
    Valor DECIMAL(10,2) -- será usado mais tarde no teste
);

-- Tabela de ligação Livro x Autor (N:N)
CREATE TABLE Livro_Autor (
    Livro_Codl INT,
    Autor_CodAu INT,
    PRIMARY KEY (Livro_Codl, Autor_CodAu),
    FOREIGN KEY (Livro_Codl) REFERENCES Livro(Codl) ON DELETE CASCADE,
    FOREIGN KEY (Autor_CodAu) REFERENCES Autor(CodAu) ON DELETE CASCADE
);

-- Tabela de ligação Livro x Assunto (N:N)
CREATE TABLE Livro_Assunto (
    Livro_Codl INT,
    Assunto_codAs INT,
    PRIMARY KEY (Livro_Codl, Assunto_codAs),
    FOREIGN KEY (Livro_Codl) REFERENCES Livro(Codl) ON DELETE CASCADE,
    FOREIGN KEY (Assunto_codAs) REFERENCES Assunto(codAs) ON DELETE CASCADE
);




CREATE OR REPLACE VIEW vw_relatorio_autor_livros AS
SELECT 
    a.CodAu AS autor_id,
    a.Nome AS autor,
    COALESCE(COUNT(DISTINCT l.Codl), 0) AS quantidade_livros,
    COALESCE(
        GROUP_CONCAT(DISTINCT l.Titulo ORDER BY l.Titulo SEPARATOR ', '),
        'Sem livro'
    ) AS livros,
    COALESCE(
        GROUP_CONCAT(DISTINCT asu.Descricao ORDER BY asu.Descricao SEPARATOR ', '),
        'Sem assunto'
    ) AS assuntos
FROM Autor a
LEFT JOIN Livro_Autor la ON la.Autor_CodAu = a.CodAu
LEFT JOIN Livro l ON l.Codl = la.Livro_Codl
LEFT JOIN Livro_Assunto las ON las.Livro_Codl = l.Codl
LEFT JOIN Assunto asu ON asu.codAs = las.Assunto_codAs
GROUP BY a.CodAu, a.Nome;
