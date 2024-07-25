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

    //unicamente el usuario administrador podra ver esta seccion
    if($rolUsuario != "Administrador"){
      ?>
      <script>
        window.location = "reportesCaja.php";
      </script>
      <?php
    }
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Auditorias Realizadas</h1>
			    			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="reportesCaja.php">Nueva Auditoria</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="reportes">
      
                    <div class="row">
                      <table class="table">
                        <thead>
                          <tr>
                            <th>Fecha Inicio</th>
                            <th>Estatus</th>
                            <th>Usuario Inicia</th>
                            <th>Fecha Termino</th>
                            <th>Ver</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            //consultamos si tiene auditorias
                            $sql = "SELECT * FROM AUDITORIAS WHERE empresaID = '$idEmpresaSesion'";
                            try {
                              $query = mysqli_query($conexion, $sql);
                              if(mysqli_num_rows($query) > 0){
                                //consultamos las auditorias
                                while($fetch = mysqli_fetch_assoc($query)){
                                  $fechaIni = $fetch['fechaInicio'];
                                  $fechaFin = $fetch['fechaFin'];
                                  $usuarioIni = $fetch['usuarioInicia'];
                                  $estatus = "";
                                  $fechaTermino = "";
                                  if($fechaFin == "" || $fechaFin == null){
                                    $estatus = "En proceso";
                                    $fechaTermino = "N/A";
                                  }else{
                                    $estatus = "Finalizada";
                                    $fechaTermino = $fechaFin;
                                  }
                                  
                                  echo "<tr>
                                    <td>$fechaIni</td>
                                    <td>$estatus</td>
                                    <td>$usuarioIni</td>
                                    <td>$fechaTermino</td>
                                    <td>
                                      <a href='#!' class='btn-primary'>Ver</a>
                                    </td>
                                  </tr>";
                                }//fin del while
                              }else{
                                //sin resultados
                                echo "<tr><td colspan='5'><h5>Sin Auditorias</h5></td></tr>";
                              }
                            } catch (\Throwable $th) {
                              //error de consulta
                              echo "<tr><td colspan='5'>Error de consulta a la base de datos</td></tr>";
                            }
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
    <script src="assets/js/ventasUsuario.js"></script>
</body>
</html> 

