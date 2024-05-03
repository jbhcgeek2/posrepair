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
                  <form id="nuevoUsuarioEmp">
                    <div class="row">

                      <div class="col-sm-12 col-md-4 mb-3">
                        <label for="nombreUser" class="form-label">Nombres</label>
                        <input type="text" id="nombreUser" name="nombreUser" class="form-control">
                      </div>
                      <div class="col-sm-12 col-md-4 mb-3">
                        <label for="apPaterno" class="form-label">Apellido Paterno</label>
                        <input type="text" id="apPaterno" name="apPaterno" class="form-control">
                      </div>
                      <div class="col-sm-12 col-md-4 mb-3">
                        <label for="apMaterno" class="form-label">Apellido Materno</label>
                        <input type="text" id="apMaterno" name="apMaterno" class="form-control">
                      </div>

                      <div class="col-sm-12 col-md-4 col-lg-3 mb-3">
                        <label for="telUser" class="form-label">Telefono</label>
                        <input type="text" id="telUser" name="telUser" class="form-control">
                      </div>
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                        <label for="mailUser" class="form-label">Correo</label>
                        <input type="text" id="mailUser" name="mailUser" class="form-control">
                      </div>

                      <div class="col-sm-12 col-md-4 col-lg-3 mb-3">
                        <label for="userName" class="form-label">Usuario</label>
                        <input type="text" id="mailUser" name="userName" class="form-control">
                      </div>

                      <div class="col-sm-12 col-md-4 col-lg-3 mb-3">
                        <label for="passwordUser" class="form-label">Contrasena</label>
                        <input type="password" id="passwordUser" name="passwordUser" class="form-control">
                      </div>

                      <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                        <label for="scUsuario" class="form-label">Sucursal</label>
                        <select name="scUsuario" id="scUsuario">
                          <option value="" selected disabled>Seleccione</option>
                          <?php 
                            //consuiltamos las sucursales de la empresa
                          ?>
                        </select>
                      </div>

                      <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                        <label for="tipoUser" class="form-label">Tipo de Usuario</label>
                        <select name="tipoUser" id="tipoUser">
                          <option value="" selected disabled>Seleccione</option>
                          <?php 
                            //consuiltamos las sucursales de la empresa
                            $sqlTU = "SELECT * FROM ROLES";
                            $queryTU = mysqli_query($conexion, $sqlTU);
                            while($fetchTU = mysqli_fetch_assoc($queryTU)){
                              $idTipo = $fetchTU['idRol'];
                              $nombreTipo = $fetchTU['nombreRol'];

                              echo "<option value='$idTipo'>$nombreTipo</option>";
                            }//fin del while
                          ?>
                        </select>
                      </div>

                      





                    </div><!--Fin row form-->
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

