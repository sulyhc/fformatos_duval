<?php

header('Content-type: application/pdf');
require ('fpdf/fpdf.php');
include("Letras.php");

$cants = explode(',', $_GET['datos']);

class PDF extends FPDF {
	
	function WordWrap(&$text, $maxwidth)
{
    $text = trim($text);
    if ($text==='')
        return 0;
    $space = $this->GetStringWidth(' ');
    $lines = explode("\n", $text);
    $text = '';
    $count = 0;

    foreach ($lines as $line)
    {
        $words = preg_split('/ +/', $line);
        $width = 0;

        foreach ($words as $word)
        {
            $wordwidth = $this->GetStringWidth($word);
            if ($wordwidth > $maxwidth)
            {
                // Word is too long, we cut it
                for($i=0; $i<strlen($word); $i++)
                {
                    $wordwidth = $this->GetStringWidth(substr($word, $i, 1));
                    if($width + $wordwidth <= $maxwidth)
                    {
                        $width += $wordwidth;
                        $text .= substr($word, $i, 1);
                    }
                    else
                    {
                        $width = $wordwidth;
                        $text = rtrim($text)."\n".substr($word, $i, 1);
                        $count++;
                    }
                }
            }
            elseif($width + $wordwidth <= $maxwidth)
            {
                $width += $wordwidth + $space;
                $text .= $word.' ';
            }
            else
            {
                $width = $wordwidth + $space;
                $text = rtrim($text)."\n".$word.' ';
                $count++;
            }
        }
        $text = rtrim($text)."\n";
        $count++;
    }
    $text = rtrim($text);
    return $count;
}
	
	function cabeceraHorizontal($cabecera) {
		$this -> SetXY(10, 80);
		$this -> SetFont('Arial', 'B', 8);
		$this -> SetFillColor(2, 157, 116);
		//Fondo verde de celda
		$this -> SetTextColor(240, 255, 240);
		//Letra color blanco
		foreach ($cabecera as $fila) {
			$wd = 30;
			if ($fila == "#") {
				$wd = 10;
			} elseif ($fila == "CONCEPTO") {
				$wd = 60;
			}
			$this -> CellFitSpace($wd, 7, utf8_decode($fila), 1, 0, 'C', true);

		}
	}

	function datosHorizontal($n,$cantidad,$u,$concepto,$pu,$importe) {
		$al = 90;
		$this -> SetXY(10, $al);
		$this -> SetFont('Arial', '', 10);
		$this -> SetFillColor(229, 229, 229);
		//Gris tenue de cada fila
		$this -> SetTextColor(3, 3, 3);
		//Color del texto: Negro
		$bandera = false;
		//Para alternar el relleno
		$i = 1;
		$gts = 0;
		$en = 0;
			$this->SetXY(10,$al);
			$this -> MultiCell(30, 7, $cantidad,0,"C");
			$this->SetXY(40,$al);
			$this -> MultiCell(30, 7, $u,0,"C");
			$this->SetXY(70,$al);
			$this -> MultiCell(60, 5, $concepto);
			$this->SetXY(130,$al);
			$this -> MultiCell(30, 7, $pu,0,"C");
			$this->SetXY(160,$al);
			$this -> MultiCell(30, 7, $importe,0,"C");
			$this->SetXY(190,$al);
			$this -> Ln();
			/*
		for($i = 0;$i<15;$i++){
			$this -> CellFitSpace(10, 7, "", 1, 0, 'L', $bandera);
			$this -> CellFitSpace(30, 7, "", 1, 0, 'L', $bandera);
			$this -> CellFitSpace(30, 7, "", 1, 0, 'L', $bandera);
			$this -> CellFitSpace(60, 7, "", 1, 0, 'C', $bandera);
			$this -> CellFitSpace(30, 7, "", 1, 0, 'C', $bandera);
			$this -> CellFitSpace(30, 7, "", 1, 0, 'C', $bandera);
			$this -> Ln();
		}*/
		//escribe los totales

	}

	function tablaHorizontal($cabeceraHorizontal, $datosHorizontal) {
		$this -> cabeceraHorizontal($cabeceraHorizontal);
		//$this -> datosHorizontal($datosHorizontal);
	}

	//***** Aquí comienza código para ajustar texto *************
	//***********************************************************
	function CellFit($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $scale = false, $force = true) {
		//Get string width
		$str_width = $this -> GetStringWidth($txt);

		//Calculate ratio to fit cell
		if ($w == 0)
			$w = $this -> w - $this -> rMargin - $this -> x;
		$ratio = ($w - $this -> cMargin * 2) / $str_width;

		$fit = ($ratio < 1 || ($ratio > 1 && $force));
		if ($fit) {
			if ($scale) {
				//Calculate horizontal scaling
				$horiz_scale = $ratio * 100.0;
				//Set horizontal scaling
				$this -> _out(sprintf('BT %.2F Tz ET', $horiz_scale));
			} else {
				//Calculate character spacing in points
				$char_space = ($w - $this -> cMargin * 2 - $str_width) / max($this -> MBGetStringLength($txt) - 1, 1) * $this -> k;
				//Set character spacing
				$this -> _out(sprintf('BT %.2F Tc ET', $char_space));
			}
			//Override user alignment (since text will fill up cell)
			$align = '';
		}

		//Pass on to Cell method
		$this -> Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);

		//Reset character spacing/horizontal scaling
		if ($fit)
			$this -> _out('BT ' . ($scale ? '100 Tz' : '0 Tc') . ' ET');
	}

	function CellFitSpace($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '') {
		$this -> CellFit($w, $h, $txt, $border, $ln, $align, $fill, $link, false, false);
	}

	//Patch to also work with CJK double-byte text
	function MBGetStringLength($s) {
		if ($this -> CurrentFont['type'] == 'Type0') {
			$len = 0;
			$nbbytes = strlen($s);
			for ($i = 0; $i < $nbbytes; $i++) {
				if (ord($s[$i]) < 128)
					$len++;
				else {
					$len++;
					$i++;
				}
			}
			return $len;
		} else
			return strlen($s);
	}
	
}
$pdf = new PDF();

            				$i = 1;
							$rep = array("$",",");
if (($fichero = fopen("facturas.csv", "r")) !== FALSE) {
    while (($datos = fgetcsv($fichero, 0)) !== FALSE) {
        if($datos[1]!='' && $datos[1]!= "FECHA"){
        	
        	$iva = 0.16 * floatval(str_replace($rep, "", $datos[7]));
        	$totl = $iva + floatval(str_replace($rep, "", $datos[7]));
			$iva = number_format($iva,2);
$pdf -> AddPage();
$pdf -> SetFont('Arial', 'B', 16);
$pdf -> Cell(80, 10);
$pdf -> Cell(80, 10, "FACTURA");
$pdf -> SetFont('Arial', 'B', 14);
$pdf -> Cell(20, 10, "NO: ".$datos[2]);
$pdf -> Ln();
$pdf -> SetFont('Arial', '', 10);
$pdf -> Cell(30, 20);
$pdf -> Cell(40, 20, "REGIMEN FISCAL:REGIMEN INTERMEDIO");
$pdf->Ln();
$pdf->Cell(30,10);
$pdf -> SetFont('Arial', 'B', 14);
$pdf->Cell(90,5,"JULIA HERNANDEZ VIRGEN");
$pdf -> SetFont('Arial', '', 12);
$pdf->Cell(40,5,"R.F.C.: HEVJ-720730-1D4");
$pdf->Ln();
$pdf -> SetFont('Arial', '', 10);
$pdf->Cell(10,5);
$pdf->Cell(110,5,"CALLE PRINCIPAL SIN NUMERO \n COL. BARRANCA HONDA");
$pdf -> SetFont('Arial', '', 12);
$pdf->Cell(40,5,"C.U.R.P.:HEVJ720730MOCRRL00");
$pdf->Ln();
$pdf -> SetFont('Arial', '', 10);
$pdf->Cell(40,5);
$pdf->Cell(50,5,"FORTIN, VER.  C.P. 94470");
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf -> SetFont('Arial', '', 10);
$pdf->Cell(100,5,"FORTIN, VER., A: ".$datos[1]);
$pdf->Cell(50,5,"RFC CLIENTE: DIO9910106QY5");
$pdf->Ln();
$pdf->Cell(100,5,"CLIENTE: DIORI S.A. DE C.V.");
$pdf->Ln();
$pdf->Cell(150,5,"DOMICILIO: CALLE SUR 4 # 225 ALTOS COL. CENTRO C.P. 94300 ORIZABA VER.");
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$header = array("CANTIDAD", "U. MEDIDA", "CONCEPTO", "P.U.", "TOTAL");
$pdf->tablaHorizontal($header, null);
$pdf->datosHorizontal(1, $datos[3], $datos[4], $datos[5], $datos[6], $datos[7]);
$pdf->SetXY(20, 200);
$pdf->Cell(140,5,"METODO DE PAGO: EFECTIVO");
$pdf->Cell(50,5,"IMPORTE:".$datos[7]);
$pdf->Ln();
$pdf->Cell(10);
$le = new EnLetras();
$v = str_replace(",", "", $datos[7]);

$pdf->Cell(148,5,"Cantidad en Letra: ".$le->ValorEnLetras($totl, "pesos"));
$pdf->Cell(50, 5,"I.V.A. $".$iva);
$pdf->Ln();
$pdf->Cell(154);
$pdf->Cell(50, 5,"TOTAL: $".number_format($totl,2));
$pdf->ln();
$pdf->ln();
$pdf->ln();
$img = "cb.png";
$pdf->Cell(30,40,$pdf->Image($img,$pdf->GetX(), $pdf->GetY(),25,25));
$pdf->SetXY($pdf->GetX(), $pdf->GetY());
$pdf -> SetFont('Arial', 'B', 5);
$pdf->MultiCell(70,3,utf8_decode("Vigencia:  2 Años a partir de la fecha de aprobación de la asignación de folios, la cual es del 19 de Marzo de 2013. Vence el 19 de Marzo de 2015. No. de Aprobación: 24848803 Cantidad  500 del 1 al 500."));
$pdf->Ln();
$pdf->Cell(30);
$pdf -> SetFont('Arial', 'B', 6);
$pdf->Cell(40,5,"Efectos Fiscales al pago.");
$pdf->Ln();
$pdf->Cell(30);
$pdf->Cell(40,5,utf8_decode("Pago en una sola exhibición"));
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf -> SetFont('Arial', 'B', 8);
$pdf->Cell(40);
$pdf->SetXY($pdf->GetX(), $pdf->GetY());
$pdf->MultiCell(100,5,utf8_decode("La reproducción apócrifa de este comprobante, constituye un delito en los términos de las disposiciones fiscales."));


}}}


$pdf -> Output("reporte.pdf", "i");
?>