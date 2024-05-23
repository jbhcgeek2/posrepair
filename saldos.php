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
    }else{
      //consultamos los datos de la empresa
      $sqlEmp = "SELECT * FROM EMPRESAS WHERE idEmpresa = '$idEmpresaSesion'";
      try {
        $queryEmp = mysqli_query($conexion, $sqlEmp);
        if(mysqli_num_rows($queryEmp) == 1){
          $fetchEmp = mysqli_fetch_assoc($queryEmp);

          $saldoEfe = $fetchEmp['saldoEfectivo'];
          $saldoTrans = $fetchEmp['saldoTransferencia'];

          $saldoTotal = $saldoEfe+$saldoTrans;
        }else{
          //empresa no locolizada
        }
      } catch (\Throwable $th) {
        //throw $th;
      }
    }
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Saldos de la cuenta</h1>
			    
			    <div class="row">
            <div class="col-6 col-lg-3 mb-3">
              <div class="app-card app-card-stat shadow-sm h-100">
                <div class="app-card-body p-3 p-lg-4">
                  <h4 class="stats-type mb-1">Saldo Actual</h4>
                  <div class="stats-figure fw-bold">$<?php echo number_format($saldoTotal,2); ?></div>
                  <a class="app-card-link-mask" href="#"></a>
                </div><!--//app-card-->
              </div><!--//col-->
            </div>

            <div class="col-6 col-lg-3 mb-3">
              <div class="app-card app-card-stat shadow-sm h-100">
                <div class="app-card-body p-3 p-lg-4">
                  <h4 class="stats-type mb-1">Saldo Sucursales</h4>
                  <div class="stats-figure fw-bold">$<?php echo number_format($saldoTotal,2); ?></div>
                  <a class="app-card-link-mask" href="#"></a>
                </div><!--//app-card-->
              </div><!--//col-->
            </div>
          </div>
          

        <div class="row">
          <div class="col-sm-12">
            <div class="app-card app-card-stat shadow-sm h-100">
              <div class="app-card-body p-3">
                <h1>Movimientos de Cuenta</h1>

                <div class="row">
                  <table>
                    <thead>
                      <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Monto</th>
                        <th>Descripcion</th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                        //consultamos los movimientos de caja
                        $sqlMovs = "SELECT * FROM MOVCAJAS a INNER JOIN CONCEPTOSMOV b 
                        ON a.conceptoMov = b.idConcepto WHERE a.empresaMovID = '$idEmpresaSesion'";
                        try {
                          $queryMovs = mysqli_query($conexion,$sqlMovs);
                          if(mysqli_num_rows($queryMovs) > 0){
                            while($fetchMov = mysqli_fetch_assoc($queryMovs)){
                              $montoMov = $fetchMov['montoMov'];
                              $fechaMov = $fetchMov['fechaMovimiento'];
                              $tipo = $fetchMov['tipoMov'];
                              $usuario = $fetchMov['usuarioMov'];
                              $desc = $fetchMov['observacionMov'];

                              echo "<tr>
                                <td>$fechaMov</td>
                                <td>$usuario</td>
                                <td>$montoMov</td>
                                <td>$desc</td>
                              </tr>";

                            }//fin del while
                          }else{
                            //no se tienen movim8ientos
                          }
                        } catch (\Throwable $th) {
                          //throw $th;
                        }
                      ?>
                    </tbody>
                  </table>
                </div>
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
    <script src="assets/js/saldos.js"></script>
</body>
</html> 

