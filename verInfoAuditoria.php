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

    //verificamos la existencia de la auditoria
    $idAuditoria = $_GET['data'];
    $sql1 = "SELECT * FROM AUDITORIAS WHERE idAuditoria = '$idAuditoria' AND 
    empresaID = '$idEmpresaSesion'";
    try {
      $query1 = mysqli_query($conexion, $sql1);
      if(mysqli_num_rows($query1) == 1){
        //si existe la auditoria, consultamos lo existente
        $sql2 = "SELECT * FROM ARTICULOAUDITORIA WHERE auditoriaID = '$idAuditoria' 
        AND empresaID = '$idEmpresaSesion'";
        $articulosTotales = 0;
        try {
          $query2 = mysqli_query($conexion, $sql2);
          while($fetch2 = mysqli_fetch_assoc($query2)){
            $auxExis = $fetch2['existenciasAudi'];
            $auxExis = explode("|",$auxExis);

            for($x = 0; $x < count($auxExis); $x++){
              // echo $auxExis[$x]."<br>";
              $aux2 = explode("=",$auxExis[$x]);
              // echo $aux2[1]."<br>";
              if(empty($aux2[1])){
                $suma = 0;
              }else{
                $suma = $aux2[1];
              }
              $articulosTotales = $articulosTotales+$suma;
            }//fin del for existencias
          }
        } catch (\Throwable $th) {
          // echo $th;
        }

        //consultamos los articulos validados
        $sql3 = "SELECT count(*) AS artiValidados FROM DETALLEAUDITORIA WHERE auditoriaID = '$idAuditoria' 
        AND empresaID = '$idEmpresaSesion'";
        try {
          $query3 = mysqli_query($conexion, $sql3);
          $fetch3 = mysqli_fetch_assoc($query3);
          $validados = $fetch3['artiValidados'];
        } catch (\Throwable $th) {
          //throw $th;
        }

        
      }else{
        //no existe la auditoria
        ?>
        <script>
          window.location = 'verAuditorias.php';
        </script>
        <?php
      }
    } catch (\Throwable $th) {
      //error en la consulta del proveedor
      ?>
      <script>
        window.location = 'verAuditorias.php';
      </script>
      <?php
    }


    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Resumen de Auditoria</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
                          <a href="#!" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEscaneo">Escanear</a>
									        <a href="verAuditorias.php">Ver Auditorias</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="">
      
                    <div class="row">
                      <div class="col-6 col-lg-3">
                        <div class="app-card app-card-stat shadow-sm h-100">
                          <div class="app-card-body p-3 p-lg-4">
                            <h4 class="stats-type mb-1">Articulos Existentes</h4>
                            <div class="stats-figure"><?php echo number_format($articulosTotales,0); ?></div>
                            
                          </div><!--//app-card-body-->
                          <a class="app-card-link-mask" href="#"></a>
                        </div><!--//app-card-->
                      </div><!--//col-->

                      <div class="col-6 col-lg-3">
                        <div class="app-card app-card-stat shadow-sm h-100">
                          <div class="app-card-body p-3 p-lg-4">
                            <h4 class="stats-type mb-1">Articulos Validados</h4>
                            <div class="stats-figure"><?php echo number_format($validados,0); ?></div>
                            
                          </div><!--//app-card-body-->
                          <a class="app-card-link-mask" href="#"></a>
                        </div><!--//app-card-->
                      </div><!--//col-->
                    </div>

					        </div><!--//app-card-body-->
				        </div><!--//app-card-->
			        </div><!--//col-->
          <hr class="my-4">


          <div class="modal fade" id="modalEscaneo" tabindex="-1" aria-labelledby="modalEscaneoLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="exampleModalLabel">Escanear Articulos</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="row">
                    <div class="col-sm-12 col-md-4">
                      <input type="hidden" id="auditoriaData" value="<?php echo $idAuditoria; ?>">
                      <label for="sucurArti" class="form-label">Sucursal</label>
                      <select name="" id="sucurArti" class="form-select">
                        <option value="" selected>Seleccione</option>
                        <?php 
                          //consultamos las sucursales
                          $sqlSuc = "SELECT * FROM SUCURSALES WHERE empresaSucID = '$idEmpresaSesion' 
                          AND estatusSuc = '1'";
                          try {
                            $querySuc = mysqli_query($conexion, $sqlSuc);
                            while($fetchSuc = mysqli_fetch_assoc($querySuc)){
                              $nombreSuc = $fetchSuc['nombreSuc'];
                              $idSuc = $fetchSuc['idSucursal'];

                              echo "<option value='$idSuc'>$nombreSuc</option>";
                            }//fin while suc
                          } catch (\Throwable $th) {
                            //throw $th;
                          }
                        ?>
                      </select>
                    </div>
                    <div class="col-sm-12 col-md-8">
                      <label for="escanear" class="form-label">Escanear Codigo</label>
                      <input type="text" id="escanear" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                  <a href="#!" class="btn btn-primary" id="btnTermina">Terminar</a>
                </div>
              </div>
            </div>
          </div>
        


			    
	    
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
    <script src="assets/js/verInfoAuditoria.js"></script>
</body>
</html> 

