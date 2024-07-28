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
        //si existe la auditoria
        
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
									        <a href="verAuditorias.php">Ver Auditorias</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="">
      
                    <div class="row">
                      
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
    <script src="assets/js/verInfoProv.js"></script>
</body>
</html> 

