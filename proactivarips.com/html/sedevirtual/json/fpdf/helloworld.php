<?php 
require('fpdf.php');

class PDF extends FPDF
{

// Cabecera de página
function Header()
{
    // Logo
    $this->Image('../../../images/logo.png',10,10,48);
    $this->SetTextColor(37, 42, 124);
    $this->SetFont('Arial','I',9);
    $this->Cell(130);
    $this->Cell(70,4,'Fecha de Expedición: xx/xx/xxxx Hora:',1,0,'C');

    $this->SetFont('Arial','B',14);
    // Movernos a la derecha
    $this->Ln(2);
    $this->Cell(50);
    
    // Título
    $this->Cell(0,10,'HISTORIA CLÍNICA',0,0,'L');
    $this->SetFont('Arial','B',11);
    $this->Ln(5);
    $this->Cell(50);
    $this->Cell(0,10,'CONSULTA POR PSICOLOGÍA',0,0,'L');
    $this->SetFont('Arial','',10);
    $this->Ln(6);
    $this->Cell(50);
    $this->Cell(0,7,'Cita de Atencion Completa',0,0,'L');
    ///????
    $this->SetFillColor(200,220,255);

   
    // Salto de línea
    $this->Ln(16);
}

function ChapterTitle($num, $label)
{
    $this->SetTextColor(255,255,255);
    // Arial 12
    $this->SetFont('Helvetica','B',11);
    // Color de fondo
    $this->SetFillColor(78, 194, 236);
    // Título
    $this->Cell(0,8,"   $num. $label",0,1,'L',true);
    // Salto de línea
    $this->Ln(4);
}

/*function ChapterBody($file)
{
    // Leemos el fichero
    $txt = file_get_contents($file);
    // Times 12
    $this->SetFont('Times','',12);
    // Imprimimos el texto justificado
    $this->MultiCell(0,5,$txt);
    // Salto de línea
    $this->Ln();
    // Cita en itálica
    $this->SetFont('','I');
    $this->Cell(0,5,'(fin del extracto)');
}*/

function PrintChapter($num, $title)
{
    //$this->AddPage();
    $this->ChapterTitle($num,$title);
    //$this->ChapterBody($file);
}

// Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // italic 8
    $this->SetFont('Arial','I',8);
    // Número de página
    $this->Cell(0,10,'Página '.$this->PageNo().' de {nb}',0,0,'C'); 
}
}

//$pdf = new FPDF();
$pdf = new PDF(); //Para mostrar el Header y Footer
$str = utf8_decode($str);
$pdf -> AliasNbPages();
$pdf -> AddPage();

$pdf->PrintChapter(1,'Datos del Paciente','');
    $pdf -> SetFont('Arial','B',9);
    $pdf -> SetTextColor(10,10,10);
    $pdf -> Cell(62,6,'Identificacion: C.C. 1018412802',0);
    $pdf -> Cell(80,6,'Paciente: David Gilberto Gómez Correa',0);
    $pdf -> Cell(53,6,'Edad: 28 años',0);
    $pdf -> Ln(6);
    $pdf -> Cell(62,6,'Lugar de Nacimiento: Bogotá D.C.',0);
    $pdf -> Cell(80,6,'Fecha de Nacimiento: 14 de Septiembre de 1987',0);
    $pdf -> Cell(53,6,'Género: Masculino',0);
    $pdf -> Ln(6);
    $pdf -> Cell(62,6,'Estado Civil: Soltero',0);
    $pdf -> Cell(80,6,'Ocupación: Diseñador Industrial',0);
    $pdf -> Cell(53,6,'Nivel Escolaridad: Universitario',0);
    $pdf -> Ln(6);
    $pdf -> Cell(62,6,'Celular: 3004416666',0);
    $pdf -> Cell(80,6,'Dirección: Calle 23 D Nº 86 - 51 Int. 5 Apto. 302',0);
    $pdf -> Cell(53,6,'Teléfono: 2945627',0);
    $pdf -> Ln(6);
    $pdf -> Cell(62,6,'Acudiente: Sandra Correa',0);
    $pdf -> Cell(80,6,'Parentesco Acudiente: Madre',0);
    $pdf -> Cell(53,6,'Celular Acudiente: 3003386666',0);
    $pdf -> Ln(6);
    $pdf -> Cell(62,6,'Acompañante: Gilberto Gómez',0);
    $pdf -> Cell(80,6,'Parentesco Acompañante: Padre',0);
    $pdf -> Cell(53,6,'EPS: Salud Total',0);
    $pdf -> Ln(10);

$pdf->PrintChapter(2,'Motivo de Consulta','');
    $pdf -> SetFont('Arial','',9);
    $pdf -> SetTextColor(10,10,10);
    $pdf -> MultiCell(0,5,'The year 1866 was marked by a bizarre development, an unexplained and downright inexplicable phenomenon that surely no one has forgotten. Without getting into those rumors that upset civilians in the seaports and deranged the public mind even far inland, it must be said that professional seamen were especially alarmed. Traders, shipowners, captains of vessels, skippers, and master mariners from Europe and America, naval officers from every country, and at their heels the various national governments on these two continents, were all extremely disturbed by the business.');
    $pdf -> Ln(4);
$pdf->PrintChapter(3,'Observaciones','');
    $pdf -> SetFont('Arial','',9);
    $pdf -> SetTextColor(10,10,10);
    $pdf -> MultiCell(0,5,'The year 1866 was marked by a bizarre development, an unexplained and downright inexplicable phenomenon that surely no one has forgotten. Without getting into those rumors that upset civilians in the seaports and deranged the public mind even far inland, it must be said that professional seamen were especially alarmed. Traders, shipowners, captains of vessels, skippers, and master mariners from Europe and America, naval officers from every country, and at their heels the various national governments on these two continents, were all extremely disturbed by the business.');
    $pdf -> Ln(4);
$pdf->PrintChapter(4,'Emocionalidad','');
$pdf->PrintChapter(5,'Diagnósticos','');
$pdf->PrintChapter(6,'Recomendaciones','');
$pdf -> Output();
?>