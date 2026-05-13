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
    
    if($rolUsuario == "Administrador"){
      
    }else{
      ?>
      <script>
        window.location = "../";
      </script>
      <?php
    }
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Importar inventario</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="verProductos.php">Ver Inventario</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoSucur">

                  <!--  -->
                  <div class="row">
                    <div class="col-sm-12">
                        <div class="card border-warning mb-3">
                            <div class="card-header bg-warning text-dark">
                                <strong><i class="fas fa-exclamation-triangle"></i> Instrucciones Importantes</strong>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    Para asegurar que tus productos se carguen correctamente en <strong>PostRepair</strong>, sigue estos pasos:
                                </p>
                                <ol>
                                    <li><strong>Descarga el formato:</strong> Utiliza exclusivamente la plantilla oficial para evitar errores de columnas.</li>
                                    <li><strong>No modifiques encabezados:</strong> No cambies el nombre ni el orden de la primera fila.</li>
                                    <li><strong>Códigos de barras:</strong> Verifica que no tengan espacios y que el formato de la celda en Excel sea "Texto".</li>
                                </ol>
                                
                                <div class="mt-3">
                                    <a href="importar-inventario.xlsx" class="btn btn-warning" download>
                                        <i class="fas fa-download"></i> Descargar Formato Establecido
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                  <!--  -->
      
                  <div class="card p-3">

                      <input 
                          type="file" 
                          id="archivoInventario" 
                          class="form-control"
                          accept=".xlsx,.xls,.csv">

                      <button 
                          class="btn btn-primary mt-3"
                          onclick="subirInventario()">
                          Importar archivo
                      </button>

                      <div id="resultadoImportacion" class="mt-3"></div>
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
    <script src="assets/js/importarInventario.js"></script>
</body>
</html> 

