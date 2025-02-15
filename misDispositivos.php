<!DOCTYPE html>
<html lang="en"> 
<?php



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
    $fecha = date('Y-m-d');

    if($rolUsuario == "Administrador"){

    }else{
      header('location: index.php');
      echo "<script>window.location='index.php';</script>";
    }

  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Dispositivos Autorizados</h1>
  
			    
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

                  
					        <div class="app-card-body p-3 p-lg-4 table-responsive" id="reportes">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Usuario</th>
                          <th>Dispositivo</th>
                          <th>Primer Ingreso</th>
                          <th>Ultimo Ingreso</th>
                          <th>Autorizado Por</th>
                          <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                          //consultamos los dispositivos de la empresa
                          $sqlD = "SELECT * FROM DISPOSITIVOS a INNER JOIN USUARIOS b ON 
                          a.usuarioID = b.idUsuario WHERE a.empresaID = ? ORDER BY a.ultimoAcceso DESC";
                          try {
                            $queryD = mysqli_prepare($conexion, $sqlD);
                            mysqli_stmt_bind_param($queryD,"i",$idEmpresaSesion);
                            mysqli_stmt_execute($queryD);
                            $resD = mysqli_stmt_get_result($queryD);
                            while($fetchD = mysqli_fetch_assoc($resD)){
                              $usuario = $fetchD['userName'];
                              $claveDisp = $fetchD['diviceID'];
                              $claveDisp =  substr(str_pad($claveDisp, 5, " ", STR_PAD_LEFT), -5);
                              $autorizo = $fetchD['autorizo'];
                              $fechaAlta = $fetchD['fechaRegistro'];
                              $fechaUltimo = $fetchD['ultimoAcceso'];
                              $idDispo = $fetchD['idDispo'];

                              echo "<tr>
                                <td>$usuario</td>
                                <td>$claveDisp</td>
                                <td>$fechaAlta</td>
                                <td>$fechaUltimo</td>
                                <td>$autorizo</td>
                                <td>
                                  <div class='tooltip-container'>
                                    <span class='tooltip-text'>Eliminar Acceso</span>
                                    <a href='#!' class='btn btn-danger' id='$idDispo' onclick='delAccess(this.id)'>
                                      <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash' viewBox='0 0 16 16'>
                                        <path d='M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z'/>
                                        <path d='M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z'/>
                                      </svg>
                                    </a>
                                  </div>
                                  <div class='tooltip-container'>
                                    <span class='tooltip-text'>Permitir Acceso</span>
                                    <a href='#!' class='btn btn-success' id='$idDispo' onclick='setAccess(this.id)'>
                                      <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-unlock-fill' viewBox='0 0 16 16'>
                                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-unlock-fill' viewBox='0 0 16 16'>
                                        <path d='M11 1a2 2 0 0 0-2 2v4a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h5V3a3 3 0 0 1 6 0v4a.5.5 0 0 1-1 0V3a2 2 0 0 0-2-2'/>
                                      </svg>
                                    </a>
                                  </div>
                                  
                                </td>
                              </tr>";
                            }//fin whileD
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
    <script src="assets/js/validaDispositivo.js"></script>
    <script src="assets/js/misDispositivos.js"></script>
</body>
</html> 

