<?php 
/*///// Funciones *************
include_once('funcionesBD.php');
include_once('funcionesProactivar.php');

//traer e interpretar datos POST
$formulario = json_decode(file_get_contents("php://input"));

$datos = array();

$SQLPruebaCita = "SELECT * FROM hcpsi_ 
                    WHERE id_cita = 118";
insertarTablaArray_v2($datos, $SQLPruebaCita, 'prueba_cita');

echo "historial<pre>";
    print_r($datos);
echo "</pre>";

echo json_encode($datos);

///////////************************/

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
    $this->Cell(70,4,'Fecha de Impresión: xx/xx/xxxx Hora:',1,0,'C');

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
    $pdf -> Cell(24,6,'Identificacion:');
    $pdf -> SetFont('Arial','',9);
    $pdf -> Cell(38,6,'C.C. 1018412802');
    $pdf -> SetFont('Arial','B',9);
    $pdf -> Cell(18,6,'Paciente:');
    $pdf -> SetFont('Arial','',9);
    $pdf -> Cell(62,6,'David Gilberto Gómez Correa');
    $pdf -> SetFont('Arial','B',9);
    $pdf -> Cell(12,6,'Edad:');
    $pdf -> SetFont('Arial','',9);
    $pdf -> Cell(42,6,'28 años');
    $pdf -> Ln(6);
    $pdf -> SetFont('Arial','B',9);
    $pdf -> Cell(34,6,'Lugar de Nacimiento:');
    $pdf -> SetFont('Arial','',9);
    $pdf -> Cell(28,6,'Bogotá D.C.');
    $pdf -> SetFont('Arial','B',9);
    $pdf -> Cell(36,6,'Fecha de Nacimiento:');
    $pdf -> SetFont('Arial','',9);
    $pdf -> Cell(44,6,'14 de Septiembre de 1987');
    $pdf -> SetFont('Arial','B',9);
    $pdf -> Cell(18,6,'Género:');
    $pdf -> SetFont('Arial','',9);
    $pdf -> Cell(36,6,'Masculino');
    $pdf -> Ln(6);

    $pdf -> SetFont('Arial','B',9);
    $pdf -> Cell(22,6,'Estado Civil:');
    $pdf -> SetFont('Arial','',9);
    $pdf -> Cell(40,6,'Soltero');
    $pdf -> SetFont('Arial','B',9);
    $pdf -> Cell(22,6,'Ocupación:');
    $pdf -> SetFont('Arial','',9);
    $pdf -> Cell(58,6,'Diseñador Industrial');
    $pdf -> SetFont('Arial','B',9);
    $pdf -> Cell(30,6,'Nivel Escolaridad:');
    $pdf -> SetFont('Arial','',9);
    $pdf -> Cell(24,6,'Universitario');
    $pdf -> Ln(6);
    $pdf -> SetFont('Arial','B',9);
    $pdf -> Cell(14,6,'Celular:');
    $pdf -> SetFont('Arial','',9);
    $pdf -> Cell(48,6,'3004416666');
    $pdf -> SetFont('Arial','B',9);
    $pdf -> Cell(20,6,'Dirección:');
    $pdf -> SetFont('Arial','',9);
    $pdf -> Cell(60,6,'Calle 23 D Nº 86 - 51 Int. 5 Apto. 302');
    $pdf -> SetFont('Arial','B',9);
    $pdf -> Cell(20,6,'Teléfono:');
    $pdf -> SetFont('Arial','',9);
    $pdf -> Cell(34,6,'2945627');
    
    $pdf -> Ln(6);
    $pdf -> SetFont('Arial','B',9);
    $pdf -> Cell(20,6,'Acudiente:');
    $pdf -> SetFont('Arial','',9);
    $pdf -> Cell(42,6,'Sandra Correa');
    $pdf -> SetFont('Arial','B',9);
    $pdf -> Cell(38,6,'Parentesco Acudiente:');
    $pdf -> SetFont('Arial','',9);
    $pdf -> Cell(42,6,'Madre');
    $pdf -> SetFont('Arial','B',9);
    $pdf -> Cell(30,6,'Celular Acudiente:');
    $pdf -> SetFont('Arial','',9);
    $pdf -> Cell(24,6,'3003386666');
    $pdf -> Ln(6);
    $pdf -> SetFont('Arial','B',9);
    $pdf -> Cell(26,6,'Acompañante:');
    $pdf -> SetFont('Arial','',9);
    $pdf -> Cell(36,6,'Gilberto Gómez');
    $pdf -> SetFont('Arial','B',9);
    $pdf -> Cell(44,6,'Parentesco Acompañante:');
    $pdf -> SetFont('Arial','',9);
    $pdf -> Cell(36,6,'Padre');
    $pdf -> SetFont('Arial','B',9);
    $pdf -> Cell(10,6,'EPS:');
    $pdf -> SetFont('Arial','',9);
    $pdf -> Cell(44,6,'Salud Total');
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
    $pdf -> SetFont('Arial','B',10);
    $pdf -> SetTextColor(10,10,10);
    $pdf -> Cell(0,6,'--- Niveles de Emocionalidad y Dolor ---',0,0,'C');
    $pdf -> Ln(6);
    $pdf -> Cell(20,6,'Ansiedad:');
    $pdf -> SetFont('Arial','',10);
    $pdf -> Cell(30,6,'Espacio');
    $pdf -> SetFont('Arial','B',10);
    $pdf -> Cell(18,6,'Tristeza:');
    $pdf -> SetFont('Arial','',10);
    $pdf -> Cell(30,6,'Espacio');
    $pdf -> SetFont('Arial','B',10);
    $pdf -> Cell(24,6,'Irritabilidad:');
    $pdf -> SetFont('Arial','',10);
    $pdf -> Cell(30,6,'Espacio');
    $pdf -> SetFont('Arial','B',10);
    $pdf -> Cell(14,6,'Dolor:');
    $pdf -> SetFont('Arial','',10);
    $pdf -> Cell(30,6,'Espacio');
    $pdf -> Ln(10);
    $pdf -> SetFont('Arial','B',10);
    $pdf -> Cell(0,6,'--- Áreas de Ajuste ---',0,0,'C');
    $pdf -> Ln(6);
    $pdf -> Cell(0,6,'Área Familiar:');
    $pdf -> Ln(6);
    $pdf -> SetFont('Arial','',9);
    $pdf -> MultiCell(0,5,'The year 1866 was marked by a bizarre development, an unexplained and downright inexplicable phenomenon that surely no one has forgotten. Without getting into those rumors that upset civilians in the seaports and deranged the public mind even far inland.');
    $pdf -> Ln(2);
    $pdf -> SetFont('Arial','B',10);
    $pdf -> Cell(0,6,'Área Académica:');
    $pdf -> Ln(6);
    $pdf -> SetFont('Arial','',9);
    $pdf -> MultiCell(0,5,'The year 1866 was marked by a bizarre development, an unexplained and downright inexplicable phenomenon that surely no one has forgotten. Without getting into those rumors that upset civilians in the seaports and deranged the public mind even far inland.');
    $pdf -> Ln(2);
    $pdf -> SetFont('Arial','B',10);
    $pdf -> Cell(0,6,'Área Afectiva:');
    $pdf -> Ln(6);
    $pdf -> SetFont('Arial','',9);
    $pdf -> MultiCell(0,5,'The year 1866 was marked by a bizarre development, an unexplained and downright inexplicable phenomenon that surely no one has forgotten. Without getting into those rumors that upset civilians in the seaports and deranged the public mind even far inland.');
    $pdf -> Ln(2);
    $pdf -> SetFont('Arial','B',10);
    $pdf -> Cell(0,6,'Área Laboral:');
    $pdf -> Ln(6);
    $pdf -> SetFont('Arial','',9);
    $pdf -> MultiCell(0,5,'The year 1866 was marked by a bizarre development, an unexplained and downright inexplicable phenomenon that surely no one has forgotten. Without getting into those rumors that upset civilians in the seaports and deranged the public mind even far inland.');
    $pdf -> Ln(2);
    $pdf -> SetFont('Arial','B',10);
    $pdf -> Cell(0,6,'Área Recreacional:');
    $pdf -> Ln(6);
    $pdf -> SetFont('Arial','',9);
    $pdf -> MultiCell(0,5,'The year 1866 was marked by a bizarre development, an unexplained and downright inexplicable phenomenon that surely no one has forgotten. Without getting into those rumors that upset civilians in the seaports and deranged the public mind even far inland.');
    $pdf -> Ln(2);
    $pdf -> SetFont('Arial','B',10);
    $pdf -> Cell(0,6,'Área Social:');
    $pdf -> Ln(6);
    $pdf -> SetFont('Arial','',9);
    $pdf -> MultiCell(0,5,'The year 1866 was marked by a bizarre development, an unexplained and downright inexplicable phenomenon that surely no one has forgotten. Without getting into those rumors that upset civilians in the seaports and deranged the public mind even far inland.');
    $pdf -> Ln(2);
    $pdf -> SetFont('Arial','B',10);
    $pdf -> Cell(0,6,'--- Análisis Profesional ---',0,0,'C');
    $pdf -> Ln(6);
    $pdf -> SetFont('Arial','',9);
    $pdf -> MultiCell(0,5,'The year 1866 was marked by a bizarre development, an unexplained and downright inexplicable phenomenon that surely no one has forgotten. Without getting into those rumors that upset civilians in the seaports and deranged the public mind even far inland, it must be said that professional seamen were especially alarmed. Traders, shipowners, captains of vessels, skippers, and master mariners from Europe and America, naval officers from every country, and at their heels the various national governments on these two continents, were all extremely disturbed by the business.');
    $pdf -> Ln(6);


$pdf->PrintChapter(5,'Diagnósticos','');
    $pdf -> SetTextColor(10,10,10);
    $pdf -> Cell(0,6,'--- Diagnóstico Principal ---',1,0,'C');
        $pdf -> Ln(6);
        $pdf -> Cell(26,6,'Diagnostico:',1);
        $pdf -> SetFont('Arial','',10);
        $pdf -> Cell(0,6,'Espacio',1);
        $pdf -> Ln(6);
        $pdf -> SetFont('Arial','B',10);
        $pdf -> Cell(18,6,'Código:',1);
        $pdf -> SetFont('Arial','',10);
        $pdf -> Cell(46,6,'Espacio',1);
        $pdf -> SetFont('Arial','B',10);
        $pdf -> Cell(12,6,'Tipo:',1);
        $pdf -> SetFont('Arial','',10);
        $pdf -> Cell(46,6,'Espacio',1);
        $pdf -> SetFont('Arial','B',10);
        $pdf -> Cell(28,6,'Contingencia:',1);
        $pdf -> SetFont('Arial','',10);
        $pdf -> Cell(46,6,'Espacio',1);
        $pdf -> Ln(10);
        $pdf -> SetFont('Arial','B',10);

    $pdf -> Cell(0,6,'--- Diagnósticos Complementarios ---',1,0,'C');
        $pdf -> Ln(6);
        $pdf -> Cell(26,6,'Diagnostico:',1);
        $pdf -> SetFont('Arial','',10);
        $pdf -> Cell(0,6,'Espacio',1);
        $pdf -> Ln(6);
        $pdf -> SetFont('Arial','B',10);
        $pdf -> Cell(18,6,'Código:',1);
        $pdf -> SetFont('Arial','',10);
        $pdf -> Cell(46,6,'Espacio',1);
        $pdf -> SetFont('Arial','B',10);
        $pdf -> Cell(12,6,'Tipo:',1);
        $pdf -> SetFont('Arial','',10);
        $pdf -> Cell(46,6,'Espacio',1);
        $pdf -> SetFont('Arial','B',10);
        $pdf -> Cell(28,6,'Contingencia:',1);
        $pdf -> SetFont('Arial','',10);
        $pdf -> Cell(46,6,'Espacio',1);
        $pdf -> Ln(12);
        //$pdf -> SetFont('Arial','B',10);
    //$pdf -> Ln(2);
$pdf->PrintChapter(6,'Recomendaciones','');
    //$pdf -> Ln(2);
    $pdf -> SetTextColor(10,10,10);
    $pdf -> SetFont('Arial','',9);
    $pdf -> MultiCell(0,5,'The year 1866 was marked by a bizarre development, an unexplained and downright inexplicable phenomenon that surely no one has forgotten. Without getting into those rumors that upset civilians in the seaports and deranged the public mind even far inland, it must be said that professional seamen were especially alarmed. Traders, shipowners, captains of vessels, skippers, and master mariners from Europe and America, naval officers from every country, and at their heels the various national governments on these two continents, were all extremely disturbed by the business.');
    $pdf -> SetFont('Arial','B',10);
    $pdf -> Cell(0,6,'--------- Fin de la Historia Clínica ---------',0,0,'C');
$pdf -> Output();
?>