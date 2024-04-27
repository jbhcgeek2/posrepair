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
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Ventas del Dia</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="index.php">Ver Reportes</a>
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
                        <a href="#!" class="btn btn-primary" role="buttom">Buscar</a>
                      </div>
                      
                    </div>

                    <hr clas="my-4">

                    <table class="table">
                      <thead>
                        <tr>
                          <th>Fecha</th>
                          <th>Producto</th>
                          <th>Cantidad</th>
                          <th>Total</th>
                          <th>Usuario</th>
                          <th>Sucursal</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                          //Por default consultamos la venta del dia de todas
                          //las sucursales pero si no tiene persmisos de admin
                          //solo podra ver las ventas de su usuario y sucursal
                          $fecha = date('Y-m-d');
                          $sql = "";
                          if($rolUsuario == "Administrador"){
                            $sql = "SELECT * FROM DETALLEVENTA a INNER JOIN VENTAS b ON a.ventaID = b.idVenta 
                            INNER JOIN ARTICULOS c ON a.articuloID = c.idArticulo
                            WHERE b.fechaVenta = '$fecha'";
                          }elseif($rolUsuario == "Vendedor"){
                            //solo podra ver las ventas de su usuario y sucursal
                            $sql = "SELECT * FROM DETALLEVENTA a INNER JOIN VENTAS b ON a.ventaID = b.idVenta 
                            INNER JOIN ARTICULOS c ON a.articuloID = c.idArticulo
                            WHERE b.fechaVenta = '$fecha' AND a.usuarioVenta = '$usuario' 
                            AND a.sucursalID = '$idSucursalN'";
                          }else{
                            //el usuario encargado podra ver las ventas de todos
                            //los usuarios, pero solo de su susucrsal
                            $sql = "SELECT * FROM DETALLEVENTA a INNER JOIN VENTAS b ON a.ventaID = b.idVenta 
                            INNER JOIN ARTICULOS c ON a.articuloID = c.idArticulo
                            WHERE b.fechaVenta = '$fecha' AND a.sucursalID = '$idSucursalN'";
                          }

                          try {
                            $query = mysqli_query($conexion, $sql);
                            $totalVenta = 0;
                            if(mysqli_num_rows($query) > 0){
                              while($fetch = mysqli_fetch_assoc($query)){
                                $fechaVenta = $fetch['fechaVenta'];
                                $nombreprod = $fetch['nombreArticulo'];
                                $cantVenta = $fetch['cantidadVenta'];
                                $total = $fetch['subtotalVenta'];
                                $usuarioVent = $fetch['usuarioVenta'];
                                $sucVenta = $fetch['sucursalID'];

                                $totalVenta = $totalVenta + $total;

                                $dataSuc = getSucById($sucVenta);
                                $nombreSucVenta = json_decode($dataSuc)->dato;
                                echo "<tr>
                                  <td>$fechaVenta</td>
                                  <td>$nombreprod</td>
                                  <td>$cantVenta</td>
                                  <td>$$total</td>
                                  <td>$usuarioVent</td>
                                  <td>$nombreSucVenta</td>
                                </tr>";
                              }//fin del while
                              echo "<tr>
                              <td colspan='3' class='fw-bold' style='text-align:right'>Total</td>
                              <td class='fw-bold'>$".number_format($totalVenta,2)."</td>
                              <td colpan='2'> </td>
                              </tr>";
                            }else{
                              //sin resultados
                              echo "<tr>
                              <td colspan='6'>Sin ventas registradas</td>
                              </tr>";
                            }
                          } catch (\Throwable $th) {
                            //error en la consulta
                            echo "<tr>
                              <td colspan='6'>Error de consulta</td>
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
</body>
</html> 

