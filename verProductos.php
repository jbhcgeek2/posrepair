<!DOCTYPE html>
<html lang="en"> 
<?php
// session_start();


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
                        <label for="codigoProd" class="form-label">Buscar Por Codigo / Chip / IMEI</label>
                        <input type="text" id="codigoProd" class="form-control"
                        style="background-color:#c8e6c9;" onchange="buscarCodigo()">
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
                                        <th class="text-center">Acciones</th>
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

                                          if(!empty($fetchProv['nombreProveedor'])){
                                            $nombreProv = $fetchProv['nombreProveedor'];
                                          }else{
                                            $nombreProv = "N/A";
                                          }


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
                                              <div class='tooltip-container'>
                                                <span class='tooltip-text'>Traspasar</span>
                                                <a href='#!' class='btn btn-warning' id='$idProducto' onclick='traspasaProd(this.id)'>
                                                  <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-repeat' viewBox='0 0 16 16'>
                                                    <path d='M11 5.466V4H5a4 4 0 0 0-3.584 5.777.5.5 0 1 1-.896.446A5 5 0 0 1 5 3h6V1.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384l-2.36 1.966a.25.25 0 0 1-.41-.192m3.81.086a.5.5 0 0 1 .67.225A5 5 0 0 1 11 13H5v1.466a.25.25 0 0 1-.41.192l-2.36-1.966a.25.25 0 0 1 0-.384l2.36-1.966a.25.25 0 0 1 .41.192V12h6a4 4 0 0 0 3.585-5.777.5.5 0 0 1 .225-.67Z'/>
                                                  </svg>
                                                </a>
                                              </div>
                                              <div class='tooltip-container'>
                                                <span class='tooltip-text'>Ingresar</span>
                                                <a href='#!' class='btn btn-info' id='$idProducto' onclick='ingresoProd(this.id)'>
                                                  <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-box-arrow-in-right' viewBox='0 0 16 16'>
                                                    <path fill-rule='evenodd' d='M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z'/>
                                                    <path fill-rule='evenodd' d='M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z'/>
                                                  </svg>
                                                </a>
                                              </div>
                                              <div class='tooltip-container'>
                                                <span class='tooltip-text'>Salida</span>
                                                <a href='#!' class='btn btn-danger' id='$idProducto' onclick='salidaProd(this.id)'>
                                                  <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-box-arrow-up' viewBox='0 0 16 16'>
                                                    <path fill-rule='evenodd' d='M3.5 6a.5.5 0 0 0-.5.5v8a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-8a.5.5 0 0 0-.5-.5h-2a.5.5 0 0 1 0-1h2A1.5 1.5 0 0 1 14 6.5v8a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-8A1.5 1.5 0 0 1 3.5 5h2a.5.5 0 0 1 0 1z'/>
                                                    <path fill-rule='evenodd' d='M7.646.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 1.707V10.5a.5.5 0 0 1-1 0V1.707L5.354 3.854a.5.5 0 1 1-.708-.708z'/>
                                                  </svg>
                                                </a>
                                              </div>
                                               -
                                              
                                              
                                            
                                              <a class='btn btn-success' href='verInfoProducto.php?infoProd=$idProducto'>Ver</a>
                                            </td>
                                          </tr>";

                                          if($x == 50 ){
                                            break;
                                          }
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


                      <?php 
                        //Consultamos las sucursales
                        //consultamos las sucursales de la empresa
                        $sqlSuc = "SELECT * FROM SUCURSALES WHERE empresaSucID = '$idEmpresaSesion'";
                        $sucursales = "";
                        try {
                          $querySuc = mysqli_query($conexion, $sqlSuc);
                          while($fetchSuc = mysqli_fetch_assoc($querySuc)){
                            $sucursales = $sucursales."<option value='".$fetchSuc['idSucursal']."'>".$fetchSuc['nombreSuc']."</option>";
                          }//fin del while
                          $nSuc = mysqli_num_rows($querySuc);
                        } catch (\Throwable $th) {
                          //throw $th;
                        }
                      ?>

                      <div class="modal fade" id="modalTraspasos" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalTraspasosLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h1 class="modal-title fs-5" id="modalTraspasosLabel">Traspaso de Mercancia</h1>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="cerrarModalTraspaso()"></button>
                            </div>
                            <div class="modal-body">
                              <div class="row">
                                <?php 
                                  if($nSuc > 1){
                                  ?>
                                  <h3 class="text-center mb-3" id="nombreProd"></h3>
                                  <input type="hidden" id="artiTraspaso" value="">
                                  <div class="col-sm-12 col-md-6 mb-3">
                                    <label for="sucOrigen">Sucursal Origen</label>
                                    <select name="sucOrigen" id="sucOrigen" class="form-select" onchange="sucVeri()">
                                      <option value="">Seleccione</option>
                                      <?php
                                        echo $sucursales;
                                      ?>
                                    </select>
                                  </div>
                                  <div class="col-sm-12 col-md-6 mb-3">
                                    <label for="sucDestino">Sucursal Destino</label>
                                    <select name="sucDestino" id="sucDestino" class="form-select" onchange="sucVeri()">
                                      <option value="">Seleccione</option>
                                      <?php
                                        echo $sucursales;
                                      ?>
                                    </select>
                                  </div>

                                  <div class="col-sm-12 col-md-4 offset-md-4 mb-3">
                                    <label for="cantidadTraspaso">Cantidad</label>
                                    <input type="number" id="cantidadTraspaso" class="form-control">
                                  </div>


                                  <?php
                                  }else{
                                    //traspasos no habilitados
                                    echo "<h5 class='center-text'>Traspasos no disponibles en una sola sucursal.</h5>";
                                  }
                                ?>
                                
                              </div>
                            </div><!--Fin modal body-->
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="cerrarModalTraspaso()">Cerrar</button>
                              <button type="button" class="btn btn-primary" id="btnExecuteTraspaso">Traspasar</button>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Modal Ingreso -->
                      <div class="modal fade" id="modalIngreso" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalIngresoLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h1 class="modal-title fs-5" id="modalIngresoLabel">Ingresar Mercancia</h1>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="cerrarModalIngreso()"></button>
                            </div>
                            <div class="modal-body">
                              <div class="row">
                                  <h3 class="text-center mb-3" id="nombreProdIngreso"></h3>
                                  <input type="hidden" id="artiIngreso" value="">
                                  <div class="col-sm-12 col-md-6 mb-3">
                                    <label for="sucDestinoIngresoN">Sucursal Destino</label>
                                    <select name="sucDestinoIngresoN" id="sucDestinoIngresoN" class="form-select">
                                      <option value="">Seleccione</option>
                                      <?php
                                        echo $sucursales;
                                      ?>
                                    </select>
                                  </div>

                                  
                                  <div class="col-sm-12 col-md-4 mb-3">
                                    <label for="cantidadIngreso">Cantidad</label>
                                    <input type="number" id="cantidadIngreso" class="form-control">
                                  </div>

                              </div>
                            </div><!--Fin modal body-->
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="cerrarModalIngreso()">Cerrar</button>
                              <button type="button" class="btn btn-primary" id="btnExecuteIngreso">Ingresar</button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- Fin modal ingreso -->

                      <!-- Modal traspaso Chips -->
                      <div class="modal fade" id="modalTraspasoChips" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalTraspasoChipsLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h1 class="modal-title fs-5" id="modalTraspasoChipsLabel">Ingresar Mercancia</h1>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="cerrarModalTraspasoChips()"></button>
                            </div>
                            <div class="modal-body">
                              <div class="row">
                                  <h3 class="text-center mb-3" id="nombreProdTraspasoChip"></h3>

                                  <p class="text-center">
                                    Ingrese el codigo del chip/celular y seleccione la sucursal a asignar.
                                  </p>
                                  <input type="hidden" id="artiTraspasoChip" value="">
                                  <div class="col-sm-12 col-md-6 mb-3">
                                    <label for="sucDestinoChip">Sucursal Destino</label>
                                    <select name="sucDestinoChip" id="sucDestinoChip" class="form-select">
                                      <option value="">Seleccione</option>
                                      <?php
                                        echo $sucursales;
                                      ?>
                                    </select>
                                  </div>

                                  
                                  <div class="col-sm-12 col-md-6 mb-3">
                                    <label for="chipIngresoCodigo">Codigo</label>
                                    <input type="text" id="chipIngresoCodigo" class="form-control">
                                  </div>

                                  <div class="col-sm-12 text-center" id="resTraspasos">
                                  </div>

                              </div>
                            </div><!--Fin modal body-->
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="cerrarModalTraspasoChips()">Cerrar</button>
                              <!-- <button type="button" class="btn btn-primary hide" id="btnExecuteTraspasoChip">Traspasar</button> -->
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- Fin Modal traspaso Chips -->

                      <!-- Modal ingreso Chips -->
                      <div class="modal fade" id="modalIngresoChips" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalIngresoChipsLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h1 class="modal-title fs-5" id="modalIngresoChipsLabel">Ingresar Mercancia</h1>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="cerrarModalIngresoChips()"></button>
                            </div>
                            <div class="modal-body">
                              <div class="row">
                                  <h3 class="text-center mb-3" id="nombreProdIngresoChip"></h3>

                                  <p class="text-center">
                                    Ingrese el codigo del chip/celular y seleccione la sucursal a asignar.
                                  </p>
                                  <input type="hidden" id="artiIngresoChip" value="">
                                  <div class="col-sm-12 col-md-6 mb-3">
                                    <label for="sucDestinoChipIngreso">Sucursal Ingreso</label>
                                    <select name="sucDestinoChipIngreso" id="sucDestinoChipIngreso" class="form-select">
                                      <option value="">Seleccione</option>
                                      <?php
                                        echo $sucursales;
                                      ?>
                                    </select>
                                  </div>

                                  
                                  <div class="col-sm-12 col-md-6 mb-3">
                                    <label for="codigoIngresoChip">Codigo</label>
                                    <input type="text" id="codigoIngresoChip" class="form-control">
                                  </div>

                                  <div class="col-sm-12 text-center" id="resIngresoChips">
                                  </div>

                              </div>
                            </div><!--Fin modal body-->
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="cerrarModalIngresoChips()">Cerrar</button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- Fin Modal ingreso Chips -->

                      <!-- Modal SALIDA normal -->
                      <div class="modal fade" id="modalSalidaProd" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalSalidaProdLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h1 class="modal-title fs-5" id="modalSalidaProdLabel">Salida de Mercancia</h1>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="cerrarModalIngresoChips()"></button>
                            </div>
                            <div class="modal-body">
                              <div class="row">
                                  <h3 class="text-center mb-3" id="nombreProdSalida"></h3>

                                  <input type="hidden" id="artisalidaProd" value="">
                                  <div class="col-sm-12 col-md-6 mb-3">
                                    <label for="sucSalidaProd">Sucursal Salida</label>
                                    <select name="sucSalidaProd" id="sucSalidaProd" class="form-select">
                                      <option value="">Seleccione</option>
                                      <?php
                                        echo $sucursales;
                                      ?>
                                    </select>
                                  </div>

                                  
                                  <div class="col-sm-12 col-md-6 mb-3">
                                    <label for="cantidadSalidaProd">Cantidad Salida</label>
                                    <input type="number" id="cantidadSalidaProd" class="form-control">
                                  </div>


                              </div>
                            </div><!--Fin modal body-->
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="cerrarModalIngresoChips()">Cerrar</button>
                              <button type="button" class="btn btn-primary"  onclick="setSalidaProd()">Procesar Salida</button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- Fin Modal salida normal -->

                      <!-- Modal SALIDA Chips /telefonos -->
                      <div class="modal fade" id="modalSalidaChipProd" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalSalidaChipProdLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h1 class="modal-title fs-5" id="modalSalidaChipProdLabel">Salida de Mercancia</h1>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="cerrarModalSalidaChips()"></button>
                            </div>
                            <div class="modal-body">
                              <div class="row">
                                  <h3 class="text-center mb-3" id="nombreProdSalidaChip"></h3>

                                  <input type="hidden" id="artisalidaChipProd" value="">
                                  
                                  
                                  <div class="col-sm-12 col-md-6 mb-3">
                                    <label for="codigoSalidaProd">Codigo</label>
                                    <input type="text" id="codigoSalidaProd" class="form-control" onchange="setSalidaChip()">
                                  </div>


                              </div>
                            </div><!--Fin modal body-->
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="cerrarModalSalidaChips()">Cerrar</button>
                              <button type="button" class="btn btn-primary"  onclick="setSalidaChip()">Procesar Salida</button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- Fin Modal salida chips /telefonos -->
                    

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

