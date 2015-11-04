
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <!--[if IE]>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <![endif]-->
    <title>Formatos de Facturas</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME ICONS  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
     <!-- HTML5 Shiv and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <header>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <strong>CONVERTIDOR DE FORMATOS DE FACTURAS: </strong>
                </div>

            </div>
        </div>
    </header>
    <!-- HEADER END-->
    <!-- LOGO HEADER END-->
    <section class="menu-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="navbar-collapse collapse ">
                        <ul id="menu-top" class="nav navbar-nav navbar-right">
                            <li><a class="menu-top-active" href="index.html">INICIO</a></li>

                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- MENU SECTION END-->
    <div class="content-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="page-head-line">FORMATO</h4>

                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-success">
                        <button class="btn btn-primary" onclick="showFacturas('')">Formato 1</button>
                        <button class="btn btn-danger" onclick="showFacturas('2')">Formato 2</button>
                    </div>
                </div>

            </div>
            <div class="row">
            	<div class="col-md-12">
            		<table class="table table-striped">
            			<thead>
            			<tr >
            				<th>#</th>
            				<th>Fecha</th>
            				<th>F</th>
            				<th>Cantidad</th>
            				<th>Unidad</th>
            				<th>Concepto</th>
            				<th>Precio U.</th>
            				<th>Importe</th>
            				<th>Iva</th>
            				<th>Total</th>
            			</tr>
            			</thead>
            			<tbody>
            				<?php 
            				$i = 1;
							$rep = array("$",",");
if (($fichero = fopen("facturas.csv", "r")) !== FALSE) {
    while (($datos = fgetcsv($fichero, 0)) !== FALSE) {
        if($datos[1]!='' && $datos[1]!= "FECHA"){
        	
        	$iva = 0.16 * floatval(str_replace($rep, "", $datos[7]));
        	$totl = $iva + floatval(str_replace($rep, "", $datos[7]));
        	echo "<tr class='myTable'>";
			echo "<td>".$i."</td>";
			echo "<td>".$datos[1]."</td>";
			echo "<td>".$datos[2]."</td>";
			echo "<td>".$datos[3]."</td>";
			echo "<td>".$datos[4]."</td>";
			echo "<td>".str_replace("$"," ",$datos[5])."</td>"; //Concepto
			echo "<td>".str_replace(",","",$datos[6])."</td>"; //precio U
			echo "<td>".str_replace(",","",$datos[7])."</td>"; //importe
			echo "<td>$".$iva."</td>"; //iva
			echo "<td>$".$totl."</td>"; //total
			echo "</tr>";
			$i++;
        }
    }
}
?>
            			</tbody>
            		</table>
            	</div>
            </div>
           
        </div>
    </div>
    <!-- Modal -->
<div class="modal fade" id="modalPdf" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" style="width: 80%;height: 100%" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
      	<div class="row">
      		<div class="col-md-12">
        <iframe src="formato.php" style="width: 100%;height: 100%"></iframe>
        </div>
      	</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
    <!-- CONTENT-WRAPPER SECTION END-->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    &copy; 2015 YourCompany | By : <a href="http://www.designbootstrap.com/" target="_blank">DesignBootstrap</a>
                </div>

            </div>
        </div>
    </footer>
    <!-- FOOTER SECTION END-->
    <!-- JAVASCRIPT AT THE BOTTOM TO REDUCE THE LOADING TIME  -->
    <!-- CORE JQUERY SCRIPTS -->
    <script src="assets/js/jquery-1.11.1.js"></script>
    <!-- BOOTSTRAP SCRIPTS  -->
    <script src="assets/js/bootstrap.js"></script>
    <script>
    function showFacturas(tipo){
    	    	
  	window.open('formato'+tipo+'.php','_ blank','');
    }
   $("tr.myTable").click(function() {
    var tableData = $(this).children("td").map(function() {
        return $(this).text();
    }).get();
});
    </script>
</body>
</html>
