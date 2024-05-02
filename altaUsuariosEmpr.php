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
    include("includes/cliente.php");

    $empresa = datoEmpresaSesion($usuario,"id");
    $idEmprersa = json_decode($empresa)->dato;
    $datosUsuario = getDataUser($usuario,$idEmprersa);
    $idSucursal = json_decode($datosUsuario)->sucursalID;
    // $idUsuario = json_decode($datosUsuario)->idUsuario;
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
          <div class="col-12 col-lg-12">
            <div class="app-card h-100 shadow-sm">
              <div class="app-card-header p-3">
                <div class="row justify-content0between align-items0center">
                  <h4 class="app-card-title">Registro de usuarios</h4>
                </div>
              </div><!--app-header-->
              
              

              <div class="app-card-body p-3 p-lg-4">
                <div class="row">
                  <form action="">

                  </form>
                </div>
              </div>
            </div>
            
          </div>
        </div><!--container-xl-->
      </div><!--app-content-->

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
    <script src="assets/js/movProductos.js"></script>

</body>
</html> 

