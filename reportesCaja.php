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
</body>

</html>