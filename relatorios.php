<?php
include_once(dirname(__FILE__) . '/db.php');

// baixado de: https://raw.githubusercontent.com/setasign/fpdf/master/fpdf.php
// baixar as fontes em: https://github.com/setasign/fpdf/tree/master/font
include_once(dirname(__FILE__) . '/fpdf.php');

if(isset($_GET['export']) && $_GET['export'] == 'pdf'){
    $sql = "SELECT autor, quantidade_livros, livros, assuntos 
        FROM vw_relatorio_autor_livros
        ORDER BY autor";
    $res = $conn->query($sql);

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,10,'Relatorio de Autores e Livros',0,1,'C');
    $pdf->Ln(5);

    // Cabeçalho da Tabela
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(50,8,'Autor',1,0,'C');
    $pdf->Cell(25,8,'Qtd Livros',1,0,'C');
    $pdf->Cell(55,8,'Livros',1,0,'C');
    $pdf->Cell(60,8,'Assuntos',1,1,'C');

    // Conteúdo da Tabela
    $pdf->SetFont('Arial','',9);

    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {

            $autor      = $row['autor'];
            $qtd        = $row['quantidade_livros'];
            $livros     = $row['livros'];
            $assuntos   = $row['assuntos'];

            // Criar backup da posição inicial
            $x = $pdf->GetX();
            $y = $pdf->GetY();

            // Coluna Livros (multilinha)
            $pdf->SetXY($x + 50 + 25, $y);
            $pdf->MultiCell(55,6,$livros,1);

            // Capturar altura gerada pela MultiCell
            $alturaLivros = $pdf->GetY();

            // Coluna Assuntos (multilinha)
            $pdf->SetXY($x + 50 + 25 + 55, $y);
            $pdf->MultiCell(60,6,$assuntos,1);

            // Capturar maior altura gerada
            $alturaAssuntos = $pdf->GetY();
            $alturaFinal = max($alturaLivros, $alturaAssuntos);

            // Voltar pra esquerda e pintar Autor + Qtd com a altura final
            $pdf->SetXY($x, $y);
            $pdf->Cell(50, $alturaFinal - $y, $autor,1,0,'L');

            $pdf->Cell(25, $alturaFinal - $y, $qtd,1,0,'C');

            // Posição final
            $pdf->SetY($alturaFinal);
        }
    } else {
        $pdf->Cell(190,8,'Nenhum registro encontrado',1,1,'C');
    }

    $conn->close();
    $pdf->Output("I", "relatorio.pdf");
    exit;

}


    include_once(dirname(__FILE__) . '/header.php');
?>

<h1> Relatórios </h1>
<h3>Para gerar PDF, use: <a href='?export=pdf' target="_blank">Exportar PDF - /?export=pdf</a></h3>
<hr>
<h2> Relatório de Autores e Livros usando a VIEW: vw_relatorio_autor_livros </h2>
<!-- tabela com os dados da view vw_relatorio_autor_livros usando bootstrap listrada -->
<table class="table table-striped">
    <thead>
        <tr>
            <th>Autor</th>
            <th>Qtd Livros</th>
            <th>Livros</th>
            <th>Assuntos</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT autor, quantidade_livros, livros, assuntos 
            FROM vw_relatorio_autor_livros
            ORDER BY autor";
        $res = $conn->query($sql);

        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['autor']) . "</td>";
                echo "<td>" . htmlspecialchars($row['quantidade_livros']) . "</td>";
                echo "<td>" . htmlspecialchars($row['livros']) . "</td>";
                echo "<td>" . htmlspecialchars($row['assuntos']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Nenhum registro encontrado</td></tr>";
        }

        $conn->close();
        ?>
    </tbody>
</table>

<?php
    include_once(dirname(__FILE__) . '/footer.php');