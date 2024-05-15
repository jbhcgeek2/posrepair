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
    include("includes/ventas.php");
    include("includes/cliente.php");
    

    //verificamos la existencia de la solicitud
    $idTicket = $_GET['data'];
    $trabajo = ticketTrabajo($idTicket,$idEmpresaSesion);
    $trabajo = json_decode($trabajo);

    if($trabajo->status == "ok"){
      // print_r($trabajo);

      $nombreCliente = $trabajo->data->nombreCliente;
      $fechaTrabajo = $trabajo->data->fechaTrabajo;
      $sucursal = $trabajo->data->nombreSuc;
      $tipoDispo = $trabajo->data->tipoDispositivo;
      $tipoServicio = $trabajo->data->servicioID;
      $marca = $trabajo->data->marca;
      $modelo = $trabajo->data->modelo;
      $imei = $trabajo->data->imeiClave;
      $accesorios = $trabajo->data->accesorios;
      $problema = $trabajo->data->problema;
      $observaciones = $trabajo->data->observaciones;
      $contraDis = $trabajo->data->contraDispo;
      $fechaEntrega = $trabajo->data->fechaEntrega;
      $costoIni = $trabajo->data->costoInicial;
      $anticipo = $trabajo->data->anticipo;
      $costoFin = $trabajo->data->costoFinal;
      $estatusTrab = $trabajo->data->estatusTrabajo;

       


    }else{
      //lo mandamos fuera
      ?>
        <script>
          window.location = 'verTrabajos.php';
        </script>
      <?php
    }

    
  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Informacion de Trabajo</h1>
			    
			    
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
                      
                      <form id="dataAltaTrab" class="row">

                        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                          <label for="clienteTrabajo" class="form-label">Cliente</label>
                          <input type="text" id="clienteTrabajo" class="form-control" value="<?php echo $nombreCliente; ?>" readonly>
                        </div>

                        <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                          <label for="fechaEntrega" class="form-label">Fecha de Entrega</label>
                          <input type="date" name="fechaEntrega" id="fechaEntrega" class="form-control" required
                          value="<?php echo $fechaEntrega; ?>">
                        </div>

                        <div class="col-sm-12 col-md-4 col-lg-2 mb-3">
                          <label for="costoServicio" class="form-label">Costo</label>
                          <input type="number" name="costoServicio" id="costoServicio" class="form-control" 
                          value="<?php echo $costoIni; ?>" readonly>
                        </div>

                        <div class="col-sm-12 col-md-4 col-lg-2 mb-3">
                          <label for="anticipoServicio" class="form-label">Anticipo</label>
                          <input type="number" name="anticipoServicio" id="anticipoServicio" class="form-control" 
                          value="<?php echo $anticipo; ?>" readonly>
                        </div>

                        <hr class="my-4">
                        <h4 class="fw-bold">Informacion Tecnica</h4>

                        <div class="col-sm-12 col-md-6 col-lg-5 mb-3">
                          <label for="tipoServicio" class="form-label">Tipo de Servicio</label>
                          <select name="tipoServicio" id="tipoServicio" class="form-select" readonly>
                            <option value="" disabled>Seleccione...</option>
                            <?php 
                              $sqlTServ = "SELECT * FROM SERVICIOS WHERE empresaID = '$idEmpresaSesion' AND 
                              estatusCategoria = '1'";
                              try {
                                $queryTServ = mysqli_query($conexion, $sqlTServ);
                                if(mysqli_num_rows($queryTServ) > 0){
                                  while($fetchTServ = mysqli_fetch_assoc($queryTServ)){
                                    $nombreServ = $fetchTServ['nombreServicio'];
                                    $idServicio = $fetchTServ['idServicio'];
                                    if($idServicio == $tipoServicio){
                                      echo "<option value='$idServicio' selected>$nombreServ</option>";
                                    }else{
                                      echo "<option value='$idServicio' disabled>$nombreServ</option>";
                                    }
                                  }//fin del while
                                }else{
                                  //sin servicios registrados
                                  echo "<option value='noData'>Registrar Servicio</option>";
                                }
                              } catch (\Throwable $th) {
                                echo "<option value='error'>Error de consulta DB</option>";
                              }
                            ?>
                          </select>
                        </div>

                        <div class="col-sm-12 col-md-3 col-lg-3 mb-3">
                          <label for="tipoDispositivo" class="form-label">Dispositivo</label>
                          <select name="tipoDispositivo" id="tipoDispositivo" class="form-select" readonly>
                            <option value="" selected>Seleccione...</option>
                            <?php 
                              $dispos = ['Celular','Tablet','Laptop','Desktop','Smartwatch'];
                              for ($i=0; $i < count($dispos); $i++) { 
                                
                                if($tipoDispo == $dispos[$i]){
                                  echo "<option value='".$dispos[$i]."' selected>".$dispos[$i]."</option>";
                                }else{
                                  echo "<option value='".$dispos[$i]."' disabled>".$dispos[$i]."</option>";
                                }
                                
                              }//fin del for
                            ?>
                          </select>
                        </div>

                        <div class="col-sm-12 col-md-3 col-lg-2 mb-3">
                          <label for="marcaServicio" class="form-label">Marca</label>
                          <input type="text" name="marcaServicio" id="marcaServicio" class="form-control" 
                          value="<?php echo $marca; ?>" required>
                        </div>

                        <div class="col-sm-12 col-md-3 col-lg-2 mb-3">
                          <label for="modeloServicio" class="form-label">Modelo</label>
                          <input type="text" name="modeloServicio" id="modeloServicio" class="form-control" 
                          value="<?php echo $modelo; ?>" readonly>
                        </div>
                        <div class="col-sm-12 col-md-3 col-lg-3 mb-3">
                          <label for="numberDevice" class="form-label">IMEI / ESN / SN </label>
                          <input type="text" name="numberDevice" id="numberDevice" class="form-control" 
                          value="<?php echo $imei; ?>" readonly>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-3 mb-3">
                          <label for="contraDisp" class="form-label">Contraseña Dispositivo</label>
                          <input type="text" name="contraDisp" id="contraDisp" class="form-control" 
                          value="<?php echo $contraDis; ?>">
                        </div>


                        <div class="col-sm-12 col-md-3 col-lg-2 mb-3">
                          <label for="fechaServicio" class="form-label">Fecha Alta</label>
                          <input type="date" id="fechaServicio" name="fechaServicio" 
                          value="<?php echo $fechaTrabajo; ?>" class="form-control" readonly>
                        </div>
                        
                        <div class="col-sm-12 col-md-3 col-lg-3 mb-3">
                          <label for="sucursalServicio" class="form-label">Sucursal</label>
                          <input type="text" id="sucursalServicio" name="sucursalServicio" 
                          value="<?php echo $sucursal; ?>" class="form-control" readonly>
                        </div>

                        

                        

                        

                        <div class="col-sm-12 mb-3">
                          <label for="accesorioServicio" class="form-label">Accesorios</label>
                          <input type="text" name="accesorioServicio" id="accesorioServicio" 
                          value="<?php echo $accesorios; ?>" class="form-control">
                        </div>

                        <div class="col-sm-12 col-md-6 mb-3">
                          <span class="input-group-text p-3">Descripcion del Problema</span>
                          <textarea name="descripcionProblema" id="descripcionProblema" style="height:70px;"
                          class="form-control" reeadonly><?php echo $problema; ?></textarea>
                        </div>

                        <div class="col-sm-12 col-md-6 mb-3">  
                          <span class="input-group-text">Observaciones</span>
                          <textarea name="observServicio" id="observServicio" style="height:70px;"
                          class="form-control" readonly><?php echo $observaciones; ?></textarea>
                        </div>

                        

                        



                        
                      </form>
                              
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
    <script src="assets/js/verInfoTrabajo.js"></script>
</body>
</html> 

