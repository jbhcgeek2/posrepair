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
                      
                      <div class="col-sm-12 col-md-3 mt-4">
                        <a href="#!" class="btn btn-primary" role="buttom" id="btnBuscar">Buscar</a>
                      </div>
                      
                    </div>

                    <hr clas="my-4">

                    <table class="table">
                      <thead>
                        <tr>
                          <th>Producto</th>
                          <th>Cantidad</th>
                        </tr>
                      </thead>
                      <tbody id="bodyTableReport">
                        <?php 
                          
                          $fecha = date('Y-m-d');
                          $fechaAyer = date('Y-m-d', strtotime('-1 day'));
                          //consultamos los productos vendidos del dia de ayer
                          $sql = "SELECT a.articuloID,(SELECT COUNT(*) FROM DETALLEVENTA c WHERE c.articuloID = a.articuloID) AS vendidos,
                          d.nombreArticulo FROM DETALLEVENTA a INNER JOIN VENTAS b ON a.ventaID = b.idVenta INNER JOIN ARTICULOS d 
                          ON d.idArticulo = a.articuloID WHERE b.fechaVenta = '$fechaAyer' AND b.empresaID = '$idEmpresaSesion' AND a.articuloID 
                          IS NOT NULL GROUP BY a.articuloID ORDER BY d.nombreArticulo ASC";
                          try {
                            $query = mysqli_query($conexion,$sql);
                            if(mysqli_num_rows($query) > 0){
                              while($fetch = mysqli_fetch_assoc($query)){
                                $articulo = strtoupper($fetch['nombreArticulo']);
                                $articulo = str_replace('&NTILDE;','Ñ',$articulo);
                                $vendidos = $fetch['vendidos'];

                                echo "<tr>
                                  <td>$articulo</td>
                                  <td>$vendidos</td>
                                </tr>";
                              }//fin del while articulos agrupados
                            }else{
                              //sin articulos vendidos
                              echo "<tr>
                                <td colspan='2'>SIN ARTICULOS VENDIDOS</td>
                              </tr>";
                            }
                          } catch (\Throwable $th) {
                            //error al consultar los articulos agrupados
                            echo "<tr>
                                <td colspan='2'>$th</td>
                              </tr>";
                          }
                          

                          
                        ?>
                      </tbody>
                    </table>

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
    <script src="assets/js/ventasDelDia.js"></script>
</body>
</html> 

