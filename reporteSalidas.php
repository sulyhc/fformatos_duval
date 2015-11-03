<?php
require ("../fpdf/fpdf.php");
class PDF extends FPDF {
	// Page header
	function Header() {
		// Arial bold 15
		$this -> SetFont('Arial', 'B', 9);
		// Move to the right
		$this -> Cell(80);
		// Title
		$this -> Cell(30, 4, 'REPORTE DE SALIDAS', 0, 0, 'C');
		$this->Ln();
		$this -> SetFont('Arial', '', 8);
		$this -> Cell(80);
		// Title
		$this -> Cell(30, 4,"Establecimiento: ". $_SESSION['ntienda'], 0, 0, 'C');
		// Line break		
		$this->Ln();
		// Move to the right
		$this -> Cell(80);
		// Title
		$this -> Cell(30, 4,"Punto de Venta: ". $_SESSION['empleado'], 0, 0, 'C');
		// Line break
		$this->Ln();		
		// Move to the right
		$this -> Cell(80);
		$fecha = date("d-m-Y H:i:s");
		$this -> Cell(30, 4,"Fecha de Generacion: ". $fecha, 0, 0, 'C');
		$this -> Ln(10);
	}

	// Page footer
	function Footer() {
		// Position at 1.5 cm from bottom
		$this -> SetY(-15);
		// Arial italic 8
		$this -> SetFont('Arial', 'I', 8);
		// Page number
		$this -> Cell(0, 10, 'Pagina ' . $this -> PageNo() . '/{nb}', 0, 0, 'C');
	}

}

$pdf = new PDF();
$pdf -> AddPage();
$pdf -> SetFont('Arial', 'B', 9);
		$pdf -> Cell(80);
$pdf -> Cell(30, 10, 'ARTICULOS VENDIDOS', 0, 0, 'C');
$util = new cUtilerias();
$pdf->Ln();
$con = new Conexion();
$ventas = new VentasR($con -> conexionDB(), $_SESSION['tienda'], $_SESSION['id_emp']);
$lista = $ventas -> listaVentasRep();
// Color and font restoration
$pdf -> SetFillColor(224, 235, 255);
$pdf -> SetTextColor(0);
$pdf -> SetFont('');
// Data
$fill = false;
$i = 1;
//cabeceras del reporte
$pdf -> SetFont('Arial', 'B', 9);
$pdf -> Cell(10, 5, "#", 1, 0, 'L', $fill);
$pdf -> Cell(12, 5, "FOLIO", 1, 0, 'L', $fill);
$pdf -> Cell(50, 5, "ARTICULO", 1, 0, 'L', $fill);
$pdf -> Cell(30, 5, "FECHA DE VENTA", 1, 0, 'L', $fill);
$pdf -> Cell(19, 5, "CANTIDAD", 1, 0, 'L', $fill);
$pdf -> Ln();
$pdf -> SetFont('Arial', '', 8);
//cuerpo del reporte
while ($l = mysql_fetch_array($lista, MYSQL_ASSOC)) {
	$pdf -> Cell(10, 5, $i++, 'LR', 0, 'L', $fill);
	$pdf -> Cell(12, 5, $l['folio_vta'], 'C', 0, 'L', $fill);
	$pdf -> Cell(50, 5, $l['nombre'], 'LR', 0, 'L', $fill);
	$pdf -> Cell(30, 5, $util->cambiaFFechaT($l['fecha_v']), 'LR', 0, 'L', $fill);
	$pdf -> Cell(19, 5, $l['num'], 'LR', 0, 'C', $fill);
	$pdf -> Ln();
	$fill = !$fill;
}
$pdf -> Ln(15);
//reporte de entradas por fiado
$pdf -> SetFont('Arial', 'B', 9);
$pdf -> Cell(80);
$pdf -> Cell(30, 10, 'PAGOS DE CLIENTES', 0, 0, 'C');
$pagos = $ventas -> listaPagosCltesRep();
$pdf -> Ln();
$i = 1;
//cabeceras del reporte
$pdf -> SetFont('Arial', 'B', 9);
$pdf -> Cell(10, 5, "#", 1, 0, 'L');
$pdf -> Cell(32, 5, "FECHA", 1, 0, 'L');
$pdf -> Cell(70, 5, "CLIENTE", 1, 0, 'L');
$pdf -> Cell(20, 5, "PAGO", 1, 0, 'L');
$pdf->Ln();
$pdf -> SetFont('Arial', '', 8);
while ($p = mysql_fetch_array($pagos, MYSQL_ASSOC)) {	
	$pdf -> Cell(10, 5, $i++, 'LR', 0, 'L', $fill);
$pdf -> Cell(32, 5, $util->cambiaFFechaT($p['fecha_pago']), 'LR', 0, 'L', $fill);
$pdf -> Cell(70, 5, strtoupper($p['nombres']." ".$p['a_pat']." ".$p['a_mat']), 'LR', 0, 'L', $fill);
$pdf -> Cell(20, 5, $p['pago'], 'LR', 0, 'L', $fill);
$pdf->Ln();
	$fill = !$fill;
}
$pdf -> Ln(15);
$pdf -> Cell(20, 5, "VENTAS: $".$ventas->getTotalVtasEmp(), 0, 0, 'L');
$pdf->Ln();
$pdf -> Cell(20, 5, "PAGOS DE CLIENTES/FIADO: $".$ventas->getTotalAbonos(), 0, 0, 'L');
$pdf->Ln();
$pdf -> Cell(20, 5, "TOTAL EN CAJA: $".($ventas->getTotalVtasEmp()+$ventas->getTotalAbonos()), 0, 0, 'L');
$nombre = "r" . $_SESSION['id_emp'] . date("dmyHi");
//nombre del reporte
$pdf -> Output("$nombre.pdf", "D");
?>