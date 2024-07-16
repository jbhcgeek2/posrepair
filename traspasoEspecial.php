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
    include("includes/ventas.php");
    include("includes/cliente.php");

    //solo los administradores o encargados de tienda podran entrar a esta seccion
    if($rolUsuario == "Administrador" || $rolUsuario == "Encargado"){
      $fecha = date('Y-m-d');

    }else{
      //no debe estar en esta seccion, lo redireccionamos al inicio
      ?>
      <script>
        window.location = "index.php";
      </script>
      <?php

    }
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Traspaso Directo</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="verProductos.php">Ver Productos</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoSucur">
      
                    <div class="row">
                      
                      <div id="traspasoDirecto" class="row">
                        
                    
                        <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                          <label for="fechaTraspaso" class="form-label">Fecha Traspaso</label>
                          <input type="date" name="fechaTraspaso" id="fechaTraspaso" class="form-control" required
                          value="<?php echo $fecha; ?>">
                        </div>

                        <div class="col-sm-12 col-md-4 col-lg-2 mb-3">
                          <label for="sucurOrigen" class="form-label">Sucursal Origen</label>
                          <select name="sucurOrigen" id="sucurOrigen" class="form-select">
                            <option value="" selected>Seleccione...</option>
                            <?php 
                              $sqlSuc1 = "SELECT * FROM SUCURSALES WHERE empresaSucID = '$idEmpresaSesion'";
                              try {
                                $querySuc1 = mysqli_query($conexion, $sqlSuc1);
                                while($fetchSuc1 = mysqli_fetch_assoc($querySuc1)){
                                  $nameSuc1 = $fetchSuc1['nombreSuc'];
                                  $idSuc1 = $fetchSuc1['idSucursal'];
                                  echo "<option value='$idSuc1'>$nameSuc1</option>";
                                }//fin del while
                              } catch (\Throwable $th) {
                                echo "<option value='error'>Erro de consulta</option>";
                              }
                            ?>
                          </select>
                        </div>

                        <div class="col-sm-12 col-md-4 col-lg-2 mb-3">
                          <label for="sucurDestino" class="form-label">Sucursal Destino</label>
                          <select name="sucurDestino" id="sucurDestino" class="form-select">
                            <option value="" selected>Seleccione...</option>
                            <?php 
                              $sqlSuc2 = "SELECT * FROM SUCURSALES WHERE empresaSucID = '$idEmpresaSesion'";
                              try {
                                $querySuc2 = mysqli_query($conexion, $sqlSuc2);
                                while($fetchSuc2 = mysqli_fetch_assoc($querySuc2)){
                                  $nameSuc2 = $fetchSuc2['nombreSuc'];
                                  $idSuc2 = $fetchSuc2['idSucursal'];
                                  echo "<option value='$idSuc2'>$nameSuc2</option>";
                                }//fin del while
                              } catch (\Throwable $th) {
                                echo "<option value='error'>Erro de consulta</option>";
                              }
                            ?>
                          </select>
                        </div>

                        <div class="col-sm-12 col-md-4 mb-3">
                          <label for="codigoTraspaso" class="form-label">Codigo</label>
                          <input type="text" id="codigoTraspaso" name="codigoTraspaso" class="form-control">
                        </div>

                      
                      </div>

                      <div class="col-sm-12" id="">
                        <table>
                          <tbody id="resTraspaso"></tbody>
                        </table>
                      </div>
                              
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
    <script src="assets/js/traspasoEspecial.js"></script>
</body>
</html> 

