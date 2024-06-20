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
    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Listado de Productos</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        <div class="app-card app-card-chart h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">

							        <div class="col-auto">
						            <h4 class="app-card-title"></h4>
							        </div><!--//col-->

							        <div class="col-auto">
								        <div class="card-header-action">
									        <a href="altaProducto.php">Registrar Producto</a>
								        </div><!--//card-header-actions-->
							        </div><!--//col-->

						        </div><!--//row-->
					        </div><!--//app-card-header-->

                  
					        <div class="app-card-body p-3 p-lg-4" id="contenidoForm">
      
                     
                      <div class="row">

                      <div class="col-sm-12 mb-4">
                        <label for="codigoProd" class="form-label">Buscar Por Codigo</label>
                        <input type="text" id="codigoProd" class="form-control"
                        style="background-color:#43a047 ;color:white;" onchange="buscarCodigo()">
                      </div>

                        <div class="col-sm-12 col-md-4 mb-3">
                          <!-- <label for="catBus" class="form-label">Categoria</label> -->
                          <select name="catBus" id="catBus" class="form-select" onchange="buscarProd()">
                            <option value="" selected>Categorias</option>
                            <option value="">Todas</option>
                            <?php
                              //consultaremos las categorias de la empresa
                              $salCat = "SELECT * FROM CATEGORIA WHERE empresaID = '$idEmpresaSesion'";
                              try {
                                $queryCat = mysqli_query($conexion, $salCat);
                                while($rowCat = mysqli_fetch_array($queryCat)){
                                  $idCat = $rowCat['idCategoria'];
                                  $nombreCat = $rowCat['nombreCategoria'];
                                  echo "<option value='$idCat'>$nombreCat</option>";
                               }
                              } catch (\Throwable $th) {
                                echo "<option value=''>Error</option>";
                              } 
                            ?>
                          </select>
                        </div>

                        <div class="col-sm-12 col-md-8 mb-3">
                          <div class="input-group col-md-6 mb-3">
                            <!-- <label for="buscarProd">Buscar Producto</label> -->
                            <span class="input-group-text" ><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" id="buscarProducto" class="form-control" placeholder="Buscar Por Nombre" onchange="buscarProd()">
                          </div>
                        </div>

                        

                        <div class="col-md-12 col-sm-12 col-lg-12" id="auxRes">
                          <?php
                            $productos = json_decode(getProductos($idEmpresaSesion));
                            if($productos->status == "ok"){
                              //verificamos que contenga infromacion el json
                              if(count($productos->data) >= 1){
                                //se detectaron productos podemos generar la tabla de datos
                                ?> 
                                  <table class="table table-striped col-sm-12">
                                    <thead>
                                      <tr>
                                        <th>Producto</th>
                                        <th>Proveedor</th>
                                        <th>Precio</th>
                                        <th class="text-center">Existencia</th>
                                        <th class="text-center">Ver Mas</th>
                                      </tr>
                                    </thead>
                                    <tbody id="resProdBus">
                                      <?php 
                                        for($x = 0; $x < count($productos->data); $x++){

                                          $nombreProd = $productos->data[$x]->nombreArticulo;
                                          $precio = number_format($productos->data[$x]->precioUnitario,2);
                                          $existencia = $productos->data[$x]->cantSucur;
                                          $idProducto = $productos->data[$x]->idArticulo;
                                          $idProv = $productos->data[$x]->proveedorID;
                                          //consultamos los proveedores
                                          $sqlProv = "SELECT nombreProveedor FROM PROVEEDORES WHERE idProveedor = '$idProv' AND 
                                          provEmpresaID = '$idEmpresaSesion'";
                                          $queryProv = mysqli_query($conexion, $sqlProv);
                                          $fetchProv = mysqli_fetch_assoc($queryProv);

                                          $nombreProv = $fetchProv['nombreProveedor'];

                                          // $numero1 = $idEmpresaSesion;
                                          // $formato1 = "%03d";
                                          // $clave1 = sprintf($formato1, $numero1);

                                          // $numero2 = $idProducto;
                                          // $formato2 = "%06d";
                                          // $clave2 = sprintf($formato2, $numero2);

                                          // $clave = $clave1.$clave2;




                                          echo "<tr>
                                            <td>$nombreProd</td>
                                            <td>$nombreProv</td>
                                            <td>$ $precio</td>
                                            <td class='text-center'>$existencia</td>
                                            <td class='text-center'>
                                              <a class='btn btn-success' href='verInfoProducto.php?infoProd=$idProducto'>Ver</a>
                                            </td>
                                          </tr>";
                                        }//fin del for de datos
                                      ?>
                                    </tbody>
                                  </table>
                                <?php
                              }else{
                                //sin productos registrados
                                ?>

                                <div class="col-sm-12 text-center">
                                  <h5 class="">Sin Productos Registrados</h5>
                                  <img src="assets/images/no-data.png" alt="Sin Datos" width="80" class="text-center">
                                </div>

                                <?php
                              }
                            }else{
                              //ocurrio un error en la consulta
                            }
                            
                          ?>
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
    <script src="assets/js/verProductos.js"></script>
</body>
</html>

