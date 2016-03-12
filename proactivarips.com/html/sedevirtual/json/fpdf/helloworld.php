<?php 
require('fpdf.php');

class PDF extends FPDF
{

// Cabecera de página
function Header()
{
    // Logo
    $this->Image('../../../images/logo.png',10,10,48);
    
    $this->SetFont('Helvetica','I',10);
    $this->Cell(130);
    $this->Cell(70,10,'Fecha: xx/xx/xxxx Hora:',1,0,'C');

    $this->SetFont('Helvetica','B',14);
    // Movernos a la derecha
    $this->Ln(7);
    $this->Cell(80);
    // Título
    $this->Cell(38,10,'Historia Clinica',1,0,'C');
    $this->SetFont('Helvetica','',10);
    $this->Ln(6);
    $this->Cell(77);
    $this->Cell(44,10,'Cita de Atencion Medica',1,0,'C');
    ///????
    $this->SetFillColor(200,220,255);

   
    // Salto de línea
    $this->Ln(20);
}

// Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // italic 8
    $this->SetFont('Helvetica','I',8);
    // Número de página
    $this->Cell(0,10,'Página '.$this->PageNo().' de {nb}',0,0,'C'); 
}
}

//$pdf = new FPDF();
$pdf = new PDF(); //Para mostrar el Header y Footer
$pdf -> AliasNbPages();
$pdf -> AddPage();
$pdf -> SetFont('Helvetica','B',20);
//$pdf -> Cell(40,10,'¡Hola, Mundo!');
for($i=1;$i<=60;$i++)
    $pdf->Cell(0,10,'Imprimiendo línea número '.$i,0,1);
$pdf->Output();
$pdf -> Output();
?>