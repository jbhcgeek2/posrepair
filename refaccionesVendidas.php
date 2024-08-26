<!DOCTYPE html>
<html lang="en"> 
<?php
session_start();


 	include("includes/head.php");
?>

<body class="app">   	
  <?php
    include("includes/header.php");
    include("includes/empresas.php");
    include("includes/conexion.php");
    include("includes/articulos.php");
    include("includes/ventas.php");
    $fechaAyer = date('Y-m-d', strtotime('-1 day'));
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Articulos Vendidos</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="reportesCaja.php">Ver Reportes</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="reportes">
      
                    <div class="row">
                      <div class="col-sm-12 col-md-3">
                        <label for="form-label">Fecha Inicio</label>
                        <input type="date" id="fechaIni" class="form-control">
                      </div>
                      <div class="col-sm-12 col-md-3">
                        <label for="form-label">Fecha Fin</label>
                        <input type="date" id="fechaFin" class="form-control">
                      </div>
                      <div class="col-sm-12 col-md-3">
                        <label for="form-label">Fecha Fin</label>
                        <select name="sucVenta" id="sucVenta" class="form-select">
                          <option value="" disabled>Seleccione</option>
                          <option value="todas">Todas</option>
                          <?php 
                            $sqlSuc = "SELECT * FROM SUCURSALES WHERE empresaSucID = '$idEmpresaSesion'";
                            $querySuc = mysqli_query($conexion, $sqlSuc);
                            while($fetchSuc = mysqli_fetch_assoc($querySuc)){
                              $nombreSuc = $fetchSuc['nombreSuc'];
                              $idSuc = $fetchSuc['idSucursal'];

                              echo "<option value='$idSuc'>$nombreSuc</option>";
                            }//fin while sucursales
                          ?>
                        </select>
                      </div>
                      
                      <div class="col-sm-12 col-md-3 mt-4">
                        <a href="#!" class="btn btn-primary" role="buttom" id="btnBuscarVendidos">Buscar</a>
                      </div>
                      
                    </div>

                    <hr clas="my-4">

                    <h5 id="tituloFiltro">Se muestran las refacciones utilizadas el dia: <?php echo $fechaAyer; ?></h5><br>

                    <div style="max-height:500px;overflow-y: scroll;">
                      <table class="table">
                        <thead>
                          <tr>
                            <th>Producto</th>
                            <th>Total</th>
                          </tr>
                        </thead>
                        <tbody id="bodyTableReport">
                          <?php 
                            
                            $fecha = date('Y-m-d');
                            //para esta seccion consultaremos los articulos agregados a los trabajos
                            

                            
                            

                            
                          ?>
                        </tbody>
                      </table>
                    </div>

                    

                    

					        </div><!--//app-card-body-->
				        </div><!--//app-card-->
			        </div><!--//col-->
          <hr class="my-4">
        


			    
	    
	    <?php 
        include("includes/footer.php");
      ?>
    </div><!--//app-wrapper-->    					

 
    <!-- Javascript -->          
    <script src="assets/plugins/popper.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>


    
    <!-- Page Specific JS -->
    <script src="assets/js/app.js"></script> 
    <script src="assets/js/swetAlert.js"></script>
    <script src="assets/js/articulosVendidos.js"></script>
</body>
</html> 

