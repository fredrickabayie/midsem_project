<?php
require '../vendor/autoload.php';

class GeneratePDF extends FPDF
{
// Load data
//    function LoadData($file)
//    {
//        // Read file lines
//        $lines = file($file);
//        $data = array();
//        foreach($lines as $line)
//            $data[] = explode(';',trim($line));
//        return $data;
//    }

// Colored table
    function FancyTable($header)
    {
        // Colors, line width and bold font
        $this->SetFillColor(255,0,0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        // Header
        $w = array(40, 35, 40, 45);
        for($i=0;$i<count($header);$i++)
            $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = false;
        
        foreach($_SESSION['cart'] as $key => $value)
        {
            $this->Cell($w[0],6,$value,'LR',0,'L',$fill);
            $this->Cell($w[1],6,$value,'LR',0,'L',$fill);
            $this->Cell($w[2],6,number_format($value),'LR',0,'R',$fill);
            $this->Cell($w[3],6,number_format($value),'LR',0,'R',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w),0,'','T');
    }
}

$pdf = new GeneratePDF();
// Column headings
$header = array('Country', 'Capital', 'Area (sq km)', 'Pop. (thousands)');
// Data loading
//$data = $pdf->LoadData('countries.txt');
$pdf->SetFont('Arial','',14);
$pdf->AddPage();
//$pdf->BasicTable($header,$data);
$pdf->AddPage();
//$pdf->ImprovedTable($header,$data);
$pdf->AddPage();
$pdf->FancyTable($header);
$pdf->Output();
?>
