<!DOCTYPE html>
<html lang="en">
<?php
include("includes/head.php");
?>

<body class="app">
  <?php
  include("includes/header.php");
  include("includes/empresas.php");
  include("includes/conexion.php");
  include("includes/articulos.php");

  // ============================================================
  // DATOS DE MUESTRA (reemplaza este bloque por tu consulta real)
  // ============================================================
  $hoy = date('Y-m-d');

  // Totales rápidos para el resumen superior
  // $totalDia    = array_sum(array_column($ventas, 'totalVenta'));
  // $numVentas   = count($ventas);
  $hoy = date('Y-m-d');

  $sqlV1 = "SELECT v.*,(SELECT SUM(cantidadVenta) FROM DETALLEVENTA dv WHERE dv.ventaID = v.idVenta) AS nProds,
  (SELECT CONCAT(u.nombreUsuario,' ',u.apPaternoUsuario) FROM USUARIOS u WHERE u.idUsuario = v.usuarioVenta) AS vendedor,
  (SELECT c.nombreCliente FROM CLIENTES c WHERE c.idClientes = v.clienteID) AS nombreCliente
  FROM VENTAS v WHERE v.fechaVenta = '$hoy' AND v.empresaID = ?";
  $queryV1 = mysqli_prepare($conexion, $sqlV1);
  mysqli_stmt_bind_param($queryV1,"i",$idEmpresaSesion);
  mysqli_stmt_execute($queryV1);
  $resV1 = mysqli_stmt_get_result($queryV1);

  $resTabla = "";
  $montoCancelado = 0;
  $numVentas = mysqli_num_rows($resV1);
  if(mysqli_num_rows($resV1) > 0){
    while($fetchV1 = mysqli_fetch_assoc($resV1)){
      $comprobante = $fetchV1['num_comprobante'];
      $hora = $fetchV1['horaVenta'];
      $totalVenta = $fetchV1['totalVenta'];
      $vendedor = $fetchV1['vendedor'];
      $tipoPago = $fetchV1['tipoPago'];
      $articulos = $fetchV1['nProds'];
      $totalVenta = $fetchV1['totalVenta'];
      $nombreCliente = $fetchV1['nombreCliente'];
      $montoCancelado += $totalVenta;
      $totalTxt = number_format($totalVenta,2);
      $classCancel = "";
      if($fetchV1['estatusVenta'] == 1){
        $classCancel = "bg-danger";
      }else{
        $classCancel = "";
      }
      $classBadge = "";
      switch ($tipoPago) {
        case 'Efectivo':
          $classBadge = "bg-success";
          break;
        case 'Tarjeta':
          $classBadge = "bg-primary";
          break;
        case 'Transferencia':
          $classBadge = "bg-info text-dark";
          break;
        default:
          $classBadge = "bg-success";
          break;
      }

      $resTabla .= "<tr class='$classCancel'>
        <td>$comprobante</td>
        <td>$hora</td>
        <td>$nombreCliente</td>
        <td>$vendedor</td>
        <td>
          <span class='badge $classBadge'>$tipoPago</span>
        </td>
        <td class='text-center'>$articulos</td>
        <td class='text-end'>$$totalTxt</td>
        ";
        if($fetchV1['estatusVenta'] == 1){
          $resTabla .= "
            <td class='text-center'>
              <a href='#!' class='btn btn-secondary'>Cancelada</a>
            </td>
          </tr>";
          
        }else{
          $resTabla .= "
            <td class='text-center'>
              <a href='#!' 
              class='btn btn-danger btn-cancelar' 
              data-id='".$fetchV1['idVenta']."'
              data-comprobante='$comprobante'
              data-total='$totalTxt'>Cancelar</a>
            </td>
          </tr>";
        }
        
    }//fin del while V1
  }else{
    //sin resultados de venats
    $resTabla = "<tr>
      <td colspan='8'>Sin ventas registradas</td>
    </tr>";
  }

  mysqli_stmt_close($queryV1);

  $sqlV2 = "SELECT SUM(totalVenta) AS montoCancela FROM VENTASCANCELADAS WHERE fechaVenta = CURDATE() AND empresaID = ?";
  $queryV2 = mysqli_prepare($conexion, $sqlV2);
  mysqli_stmt_bind_param($queryV2,"i",$idEmpresaSesion);
  mysqli_stmt_execute($queryV2);
  $resV2 = mysqli_stmt_get_result($queryV2);
  $fetchV2 = mysqli_fetch_assoc($resV2);
  $montoCancelado = $fetchV2['montoCancela'];


  ?>

  <div class="app-wrapper">

    <div class="app-content pt-3 p-md-3 p-lg-4">
      <div class="container-xl">

        <h1 class="app-page-title">Cancelar ventas del día</h1>

        <!-- ===================== TARJETAS RESUMEN ===================== -->
        <div class="row g-3 mb-4">
          <div class="col-6 col-md-4">
            <div class="app-card shadow-sm h-100">
              <div class="app-card-body p-3">
                <div class="text-muted small">Ventas registradas hoy</div>
                <div class="fs-4 fw-bold"><?= $numVentas ?></div>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-4">
            <div class="app-card shadow-sm h-100">
              <div class="app-card-body p-3">
                <div class="text-muted small">Total Cancelado del día</div>
                <div class="fs-4 fw-bold text-success">$<?= number_format($montoCancelado, 2) ?></div>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="app-card shadow-sm h-100">
              <div class="app-card-body p-3">
                <div class="text-muted small">Fecha</div>
                <div class="fs-5 fw-bold"><?= date('d/m/Y', strtotime($hoy)) ?></div>
              </div>
            </div>
          </div>
        </div>

        <!-- ===================== TABLA DE VENTAS ===================== -->
        <div class="col-12">
          <div class="app-card app-card-chart h-100 shadow-sm">
            <div class="app-card-header p-3">
              <div class="row justify-content-between align-items-center">
                <div class="col-auto">
                  <h4 class="app-card-title mb-0">Ventas activas de hoy</h4>
                </div>
                <div class="col-auto">
                  <div class="card-header-action">
                    <a href="verVentasCanceladas.php">Ver ventas canceladas</a>
                  </div>
                </div>
              </div>
            </div>

            <div class="app-card-body p-3 p-lg-4" id="contenidoVentas">

              
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                      <tr>
                        <th>Ticket</th>
                        <th>Hora</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Tipo pago</th>
                        <th class="text-center">Artículos</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Acción</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                        echo $resTabla;
                      ?>
                      
                      
                    </tbody>
                  </table>
                </div>

            </div><!--//app-card-body-->
          </div><!--//app-card-->
        </div><!--//col-->

        <hr class="my-4">

        <?php include("includes/footer.php"); ?>
      </div><!--//container-xl-->
    </div><!--//app-content-->
  </div><!--//app-wrapper-->

  <!-- ===================== MODAL DE CONFIRMACIÓN ===================== -->
  <div class="modal fade" id="modalCancelar" tabindex="-1" aria-labelledby="modalCancelarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form id="formCancelar" >
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="modalCancelarLabel">
              <i class="bi bi-exclamation-triangle me-2"></i>Confirmar cancelación
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <p class="mb-3">
              Vas a cancelar la siguiente venta. Esta acción <strong>no se puede revertir</strong>.
            </p>

            <div class="p-3 rounded bg-light mb-3">
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted small">Comprobante</span>
                <strong id="mComprobante">—</strong>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-muted small">Total</span>
                <strong class="text-danger" id="mTotal">$0.00</strong>
              </div>
            </div>

            <input type="hidden" name="mVentaID" id="mVentaID" value="">
            <label for="mMotivo" class="form-label small fw-semibold">Motivo de la cancelación *</label>
            <textarea class="form-control" id="mMotivo" name="motivo" rows="4" required></textarea>
            <div class="form-text">Este motivo quedará registrado junto con tu usuario y la fecha/hora.</div>

            <div class="col-sm-12 mt-2">
              <label for="passCancelUser" class="form-label">Contrasena</label>
              <input type="password" id="passCancelUser" name="passCancelUser" class="form-control">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Volver</button>
            <a href="#!" name="cancelar_venta" id="cancelar_venta" class="btn btn-danger">
              <i class="bi bi-x-circle me-1"></i>Sí, cancelar venta
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Javascript -->
  <script src="assets/plugins/popper.min.js"></script>
  <script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>

  <!-- Page Specific JS -->
  <script src="assets/js/app.js"></script>
  <script src="assets/js/swetAlert.js"></script>
  <script src="assets/js/cancelarVentas.js"></script>


</body>
</html>