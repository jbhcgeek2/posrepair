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
    
    //seccion exclusiva para administradores
    if($rolUsuario != "Administrador"){
      //cargamos el js para redireccionar
      ?>
        <script>
          window.location = 'index.php';
        </script>
      <?php
    }
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Configuracion de cuenta</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="account.php">Mis Datos</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoSucur">
      
                    <div class="row">
                      <?php 
                        //consultamos los datos de la empresa
                        $sqlEmp = "SELECT * FROM EMPRESAS WHERE idEmpresa = '$idEmpresaSesion'";
                        try {
                          $queryEmp = mysqli_query($conexion, $sqlEmp);
                          if(mysqli_num_rows($queryEmp) == 1){
                            $fetchEmp = mysqli_fetch_assoc($queryEmp);

                            $nombreEmp = $fetchEmp['nombreEmpresa'];
                            $suscripcion = $fetchEmp['suscripcionID'];
                            ?>
                            <div class="col-sm-12 col-md-4 col-lg-3 mb-3">
                              <label for="nombreEmpresa" class="form-label">Nombre Empresa</label>
                              <input type="text" id="nombreEmpresa" name="nombreEmpresa" class="form-control" 
                              value="<?php echo $nombreEmp; ?>">
                            </div>

                            <div class="col-sm-12 col-md-6 col-lg-4">
                              <label for="planEmpresa" class="form-label"></label>
                              <select name="planEmpresa" id="planEmpresa" class="form-control">
                                <option value="" selected disabled>Seleccione</option>
                                <?php 
                                  //consultamos los planes de las empresas
                                  $sqlPlanes = "SELECT * FROM SUSCRIPCION";
                                  $queryPlanes = mysqli_query($conexion, $sqlPlanes);
                                  if(mysqli_num_rows($queryPlanes) > 0){
                                    while($fetchPlanes = mysqli_fetch_assoc($queryPlanes)){
                                      $nombrePlan = $fetchPlanes['nombreSuscripcion'];
                                      $idPlan = $fetchPlanes['idSuscripcion'];
                                      if($idPlan == $suscripcion){
                                        echo "<option value='$idPlan' selected>$nombrePlan</option>";
                                      }else{
                                        echo "<option value='$idPlan'>$nombrePlan</option>";
                                      }
                                      
                                    }//fin del while planes
                                  }else{
                                    //sin planes registrados
                                  }

                                ?>
                              </select>
                            </div>
                            
                            <?php
                          }else{
                            //empresa no localizada
                            echo "<h5 class='text-center'>Empresa no localizada</h5>";
                          }
                        } catch (\Throwable $th) {
                          echo "<h5 class='text-center'>Error de consulta a la base de datos</h5>";
                        }
                      ?>

                      
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
    <script src="assets/js/altaSucursal.js"></script>
</body>
</html> 

