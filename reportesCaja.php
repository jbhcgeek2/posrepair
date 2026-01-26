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

        <h1 class="app-page-title">Reportes de Caja</h1>


        <div class="col-12 col-lg-12">
          <div class="app-card app-card-chart h-100 shadow-sm">
            <div class="app-card-header p-3">
              <div class="row justify-content-between align-items-center">

                <div class="col-auto">
                  <h4 class="app-card-title"></h4>
                </div><!--//col-->

                <div class="col-auto">
                  <div class="card-header-action">
                    <a href="index.php">Ir a Inicio</a>
                  </div><!--//card-header-actions-->
                </div><!--//col-->

              </div><!--//row-->
            </div><!--//app-card-header-->


            <div class="app-card-body p-3 p-lg-4" id="reportes">

              <div class="row">

                <div class="col-6 col-lg-3 mb-3">
                  <div class="app-card app-card-stat shadow-sm h-100" style="background-color:#e0f2f1;">
                    <div class="app-card-body p-3 p-lg-4">
                      <h5 class="stats-type mb-1">Ventas del dia</h5>
                    </div><!--//app-card-body-->
                    <a class="app-card-link-mask" href="ventasDelDia.php"></a>
                  </div><!--//app-card-->
                </div><!--//col-->

                <div class="col-6 col-lg-3 mb-3">
                  <div class="app-card app-card-stat shadow-sm h-100" style="background-color:#e0f2f1;">
                    <div class="app-card-body p-3 p-lg-4">
                      <h5 class="stats-type mb-1">Ventas por Usuario</h5>
                    </div><!--//app-card-body-->
                    <a class="app-card-link-mask" href="ventasUsuario.php"></a>
                  </div><!--//app-card-->
                </div><!--//col-->

                <div class="col-6 col-lg-3 mb-3">
                  <div class="app-card app-card-stat shadow-sm h-100" style="background-color:#e0f2f1;">
                    <div class="app-card-body p-3 p-lg-4">
                      <h5 class="stats-type mb-1">Salidas y Entradas de Efectivo</h5>
                    </div><!--//app-card-body-->
                    <a class="app-card-link-mask" href="salEntEfec.php"></a>
                  </div><!--//app-card-->
                </div><!--//col-->

                <div class="col-6 col-lg-3 mb-3">
                  <div class="app-card app-card-stat shadow-sm h-100" style="background-color:#e0f2f1;">
                    <div class="app-card-body p-3 p-lg-4">
                      <h5 class="stats-type mb-1">Salidas y Entradas de Mercancia</h5>
                    </div><!--//app-card-body-->
                    <a class="app-card-link-mask" href="salEntMerca.php"></a>
                  </div><!--//app-card-->
                </div><!--//col-->

                <div class="col-6 col-lg-3 mb-3">
                  <div class="app-card app-card-stat shadow-sm h-100" style="background-color:#e0f2f1;">
                    <div class="app-card-body p-3 p-lg-4">
                      <h5 class="stats-type mb-1">Articulos Vendidos</h5>
                    </div><!--//app-card-body-->
                    <a class="app-card-link-mask" href="articulosVendidos.php"></a>
                  </div><!--//app-card-->
                </div><!--//col-->

                <div class="col-6 col-lg-3 mb-3">
                  <div class="app-card app-card-stat shadow-sm h-100" style="background-color:#e0f2f1;">
                    <div class="app-card-body p-3 p-lg-4">
                      <h5 class="stats-type mb-1">Trabajos Realizados</h5>
                    </div><!--//app-card-body-->
                    <a class="app-card-link-mask" href="trabajosRealizados.php"></a>
                  </div><!--//app-card-->
                </div><!--//col-->

                <div class="col-6 col-lg-3 mb-3">
                  <div class="app-card app-card-stat shadow-sm h-100" style="background-color:#e0f2f1;">
                    <div class="app-card-body p-3 p-lg-4">
                      <h5 class="stats-type mb-1">Refacciones Utilizadas</h5>
                    </div><!--//app-card-body-->
                    <a class="app-card-link-mask" href="refaccionesVendidas.php"></a>
                  </div><!--//app-card-->
                </div><!--//col-->

                <div class="col-6 col-lg-3 mb-3">
                  <div class="app-card app-card-stat shadow-sm h-100" style="background-color:#e0f2f1;" data-bs-toggle="modal" data-bs-target="#cierresMesModal">
                    <div class="app-card-body p-3 p-lg-4">
                      <h5 class="stats-type mb-1">Cierre de Mes</h5>
                    </div><!--//app-card-body-->
                    <a class="app-card-link-mask" href="#!"></a>
                  </div><!--//app-card-->
                </div><!--//col-->

                <div class="col-6 col-lg-3 mb-3">
                  <div class="app-card app-card-stat shadow-sm h-100" style="background-color:#e0f2f1;" data-bs-toggle="modal" data-bs-target="#reporteInventario">
                    <div class="app-card-body p-3 p-lg-4">
                      <h5 class="stats-type mb-1">Inventario</h5>
                    </div><!--//app-card-body-->
                    <a class="app-card-link-mask" href="#!"></a>
                  </div><!--//app-card-->
                </div><!--//col-->



              </div>

            </div><!--//app-card-body-->
          </div><!--//app-card-->
        </div><!--//col-->
        <hr class="my-4">


        <div class="modal fade" id="cierresMesModal" tabindex="-1" aria-labelledby="cierresMesModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-light">
              <div class="modal-header text-white">
                <h1 class="modal-title fs-5" id="cierresMesModalLabel">
                  <i class="bi bi-calendar-check me-2"></i>Cierres de Mes
                </h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row g-3">
                  <div class="col-12">
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                      <i class="bi bi-info-circle me-2"></i>
                      <div>
                        A continuación se muestran los últimos cierres de mes procesados
                      </div>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="list-group"> 
                      <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="mb-1">Cierre Diciembre 2025</h6>
                          <small class="text-muted">Procesado el 04/01/2026</small>
                        </div>
                        <div>
                          <a href="cierreMes.php?mes=12&year=2025" target="_blank" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-eye me-1"></i>Ver Detalle
                          </a>
                        </div>
                      </div>
                      <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="mb-1">Cierre Noviembre 2025</h6>
                          <small class="text-muted">Procesado el 05/12/2025</small>
                        </div>
                        <div>
                          <a href="cierreMes.php?mes=11&year=2025" target="_blank" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-eye me-1"></i>Ver Detalle
                          </a>
                        </div>
                      </div>
                      <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="mb-1">Cierre Octubre 2025</h6>
                          <small class="text-muted">Procesado el 10/11/2025</small>
                        </div>
                        <div>
                          <a href="cierreMes.php?mes=10&year=2025" target="_blank" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-eye me-1"></i>Ver Detalle
                          </a>
                        </div>
                      </div>
                      <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="mb-1">Cierre Septiembre 2025</h6>
                          <small class="text-muted">Procesado el 05/10/2025</small>
                        </div>
                        <div>
                          <a href="cierreMes.php?mes=09&year=2025" target="_blank" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-eye me-1"></i>Ver Detalle
                          </a>
                        </div>
                      </div>

                      <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="mb-1">Cierre Agosto 2025</h6>
                          <small class="text-muted">Procesado el 06/09/2025</small>
                        </div>
                        <div>
                          <a href="cierreMes.php?mes=08&year=2025" target="_blank" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-eye me-1"></i>Ver Detalle
                          </a>
                        </div>
                      </div>

                      <!-- Puedes agregar más elementos de lista aquí -->
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                  <i class="bi bi-x-circle me-1"></i>Cerrar
                </button>
              </div>
            </div>
          </div>
        </div>


        <!-- Modal Inventario -->
        <div class="modal fade" id="reporteInventario" tabindex="-1" aria-labelledby="reporteInventarioLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-light">
              <div class="modal-header text-white">
                <h1 class="modal-title fs-5" id="reporteInventarioLabel">
                  <i class="bi bi-calendar-check me-2"></i>Reporte de inventario
                </h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row g-3">
                  <div class="col-12">
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                      <i class="bi bi-info-circle me-2"></i>
                      <div>
                        Indica los campos para generar el reporte
                      </div>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="row">
                      <div class="col-sm-12 col-md-4 mb-3">
                        <label for="categoriaReport" class="form-label">Categoria</label>
                        <select name="categoriaReport" id="categoriaReport" class="form-select">
                          <option value="" selected disabled>Seleccione...</option>
                          <option value="all">Todas</option>
                          <?php 
                            $sqlC = "SELECT * FROM CATEGORIA WHERE empresaID = '$idEmpresaSesion' AND estatusCategoria = '1'";
                            $queryC = mysqli_query($conexion, $sqlC);
                            while($fetchC = mysqli_fetch_assoc($queryC)){
                              $idCat = $fetchC['idCategoria'];
                              $nombreCat = $fetchC['nombreCategoria'];
                              echo "<option value='$idCat'>$nombreCat</option>";

                            }//fin del while
                          ?>
                        </select>
                      </div>

                      <div class="col-sm-12 col-md-4 mb-3">
                        <label for="sucursalReport" class="form-label">Sucursal</label>
                        <select name="sucursalReport" id="sucursalReport" class="form-select">
                          <option value="" selected disabled>Seleccione...</option>
                          <option value="all">Todas</option>
                          <?php 
                            $sqlS = "SELECT * FROM SUCURSALES WHERE empresaSucID = '$idEmpresaSesion' AND estatusSuc = '1'";
                            $queryS = mysqli_query($conexion, $sqlS);
                            while($fetchS = mysqli_fetch_assoc($queryS)){
                              $idSuc = $fetchS['idSucursal'];
                              $nombreSuc = $fetchS['nombreSuc'];

                              echo "<option value='$idSuc'>$nombreSuc</option>";
                            }//fin del while Sucursales
                          ?>
                        </select>
                      </div>

                      <div class="col-sm-12 col-md-4 mb-3">
                        <label for="proveedorReport" class="form-label">Proveedor</label>
                        <select name="proveedorReport" id="proveedorReport" class="form-select">
                          <option value="" selected disabled>Seleccione...</option>
                          <option value="all">Todos</option>
                          <?php 
                            $sqlP = "SELECT * FROM PROVEEDORES WHERE provEmpresaID = '$idEmpresaSesion' AND estatusProveedor = '1'";
                            $queryP = mysqli_query($conexion, $sqlP);
                            while($fetchP = mysqli_fetch_assoc($queryP)){
                              $idProv = $fetchP['idProveedor'];
                              $nombreProv = $fetchP['nombreProveedor'];

                              echo "<option value='$idProv'>$nombreProv</option>";
                            }//fin del while proveedor
                          ?>
                        </select>
                      </div>

                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                  <i class="bi bi-x-circle me-1"></i>Cerrar
                </button>

                <a href="#!" class="btn btn-primary" id="genraReport">Generar</a>
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
      <script src="assets/js/validaDispositivo.js"></script>

      <script>
        document.getElementById('genraReport').addEventListener('click', function(){
          let catego = document.getElementById('categoriaReport').value;
          let sucu = document.getElementById('sucursalReport').value;
          let prov = document.getElementById('proveedorReport').value;

          if(catego != "" && sucu != "" && prov != ""){
            let liga = "reporteInventario.php?cate="+catego+"&suc="+sucu+"&prov="+prov;
            window.open(liga,"_blank");
          }else{
            Swal.fire({
              title: 'Campos Necesarios',
              text: 'Asegurate de indicar todos los campos',
              icon: 'error'
            })
          }

          
        })
      </script>
</body>

</html>