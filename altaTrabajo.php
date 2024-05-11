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
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Registrar Nuevo Trabajo</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="verSucursales.php">Ver Trabajos</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoSucur">
      
                    <div class="row">
                      
                      <form id="dataAltaSuc" class="row">

                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                          <label for="clienteTrabajo" class="form-label">Cliente</label>
                          <select name="clienteTrabajo" id="clienteTrabajo" class="form-select">
                            <option value="" selected disabled>Seleccione...</option>
                            <?php 
                              $clientes = verClientes($idEmpresaSesion);
                              $clientes = json_decode($clientes);
                              if($clientes->status == 'ok'){

                                for ($i=0; $i <count($clientes->data) ; $i++) { 
                                  $nombreCliente = $clientes->data[$i]->nombreCliente;

                                  echo "<option value=''>$nombreCliente</option>";
                                }
                              }else{
                                //error de consulta
                                echo "<option>Error de consulta a la BD</option>";
                              }
                            ?>
                          </select>
                        </div>
                        <div class="col-sm-12 col-md-3 col-lg-3 mb-3">
                          <label for="fechaServicio" class="form-label">Fecha</label>
                          <input type="date" id="fechaServicio" name="fechaServicio" 
                          value="<?php echo date('Y-m-d'); ?>" class="form-control">
                        </div>
                        
                        <div class="col-sm-12 col-md-4 col-lg-3 mb-3">
                          <label for="sucursalServicio" class="form-label">Sucursal</label>
                          <input type="text" id="sucursalServicio" name="sucursalServicio" 
                          value="<?php echo $nombreSucursal; ?>" class="form-control" readonly>
                        </div>

                        
                      </form>

                      <div class="col-sm-12 col-md-4 offset-md-4 text-center">
                        <a href="#!" class="btn btn-primary" role="buttom" id="altaSuc">Registrar</a>
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
    <script src="assets/js/altaTrabajo.js"></script>
</body>
</html> 

