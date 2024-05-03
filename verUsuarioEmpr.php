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
    
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Listado de Usuarios</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="altaProducto.php">Registrar Usuario</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoUsuarios">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>Nombre</th>
                          <th>Usuario</th>
                          <th>Telefono</th>
                          <th>Ver</th>
                        </tr>
                      </thead>
                      <tbody class="table-striped">
                        <?php 
                          //coinsultsmos los usuarios
                          $sqlUs = "SELECT * FROM USUARIOS WHERE empresaID = '$idEmpresaSesion'";
                          try {
                            $queryUs = mysqli_query($conexion, $sqlUs);
                            if(mysqli_num_rows($queryUs) > 0){
                              while($fetchUs = mysqli_fetch_assoc($queryUs)){
                                $nombre = $fetchUs['nombreUsuario']." ".$fetchUs['apPaternoUsuario']." ".$fetchUs['apMaternoUsuario'];
                                $userName = $fetchUs['userName'];
                                $tel = $fetchUs['telUsuario'];
                                $idUser = $fetchUs['idUsuario'];

                                echo "<tr>
                                <td>$nombre</td>
                                <td>$userName</td>
                                <td>$tel</td>
                                <td>
                                  <a href='verInfoUsuarioEmp.php?data=$idUser' class='btn btn-primary'>Ver</a>
                                </td>
                                </tr>";
                              }//fin del while
                            }else{
                              //sin resultados
                            }
                          } catch (\Throwable $th) {
                            //throw $th;
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

