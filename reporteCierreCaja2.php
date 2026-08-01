<?php 
session_start();

if(!empty($_SESSION['usuarioPOS'])){
  include("includes/empresas.php");
  include("includes/conexion.php");
  include("includes/usuarios.php");
  include("includes/cliente.php");
  include("includes/ventas.php");

  $usuario = $_SESSION['usuarioPOS'];
  $empresa = datoEmpresaSesion($usuario,"id");
  $idEmprersa = json_decode($empresa)->dato;
  $datosUsuario = getDataUser($usuario,$idEmprersa);
  $idSucursal = json_decode($datosUsuario)->sucursalID;
  $idUsuario = json_decode($datosUsuario)->idUsuario;
  if(!empty($_GET['date'])){
    $fecha = $_GET['date'];
  }else{
    $fecha = date('Y-m-d');
  }
  $montoGasto = "0";
  $montoPrecortes = "0";
  $contenidoTabla = "";

  $nombreEmpresa = datoEmpresaSesion($usuario,"nombre");
  $nombreEmpresa = json_decode($nombreEmpresa)->dato;

  if($idEmprersa == 4){
    $nombreEmpresa = "Servicel Acaponeta";
  }

  $sqlUser = "SELECT * FROM USUARIOS a INNER JOIN SUCURSALES b 
  ON a.sucursalID = b.idSucursal WHERE a.userName = '$usuario'";
  $queryUser = mysqli_query($conexion, $sqlUser);
  $fetchUser = mysqli_fetch_assoc($queryUser);
  $nombreUsuario = $fetchUser['nombreUsuario']." ".$fetchUser['apPaternoUsuario']." ".$fetchUser['apMaternoUsuario'];

  $domSuc = $fetchUser['calleSuc'];

  //consultamos si el usuario ya tiene el cierre procesado
  $sql = "SELECT * FROM MOVCAJAS WHERE fechaMovimiento = '$fecha' AND
  usuarioMov = '$idUsuario' AND conceptoMov = '4'";
  try {
    $query = mysqli_query($conexion, $sql);
    if(mysqli_num_rows($query) == 1){
      //podemos hacer el reporte
      //consultamos las ventas
      $fetch = mysqli_fetch_assoc($query);
      $horaCierre = $fetch['horaMovimiento'];
      $sql2 = "SELECT * FROM DETALLEVENTA a INNER JOIN VENTAS b 
      ON a.ventaID = b.idVenta LEFT JOIN DETALLECHIP c ON a.chipID = c.idChip 
      WHERE b.fechaVenta = '$fecha' 
      AND a.usuarioVenta = '$usuario' AND a.sucursalID = '$idSucursal'";
      try {
        $query2 = mysqli_query($conexion, $sql2);
        if(mysqli_num_rows($query2) > 0){
          $ventaEfectivo = 0;
          $ventaDigital = 0;
          $numArticulos = 0;
          $totalVenta = 0;
          $contenidoTabla = "";

          if(mysqli_num_rows($query2) > 0){
            while($fetch2 = mysqli_fetch_assoc($query2)){
              $venta = $fetch2['subtotalVenta'];
              $tipoVenta = $fetch2['tipoPago'];
              $totalVenta = $totalVenta + $venta;
              if($tipoVenta == "Efectivo"){
                $ventaEfectivo = $ventaEfectivo + $venta;
              }else{
                $ventaDigital = $ventaDigital + $venta;
              }
  
              //consultamos las cosas vendidas
              $nombreCosa = "";
              if($fetch2['articuloID'] != NULL){
                //se trata de un articulo
                $idArti = $fetch2['articuloID'];
                $sql3 = "SELECT a.nombreArticulo FROM ARTICULOS a WHERE 
                a.idArticulo = '$idArti' AND a.empresaID = '$idEmprersa'";
                try {
                  $query3 = mysqli_query($conexion, $sql3);
                  $fetch3 = mysqli_fetch_assoc($query3);
                  $nombreCosa = $fetch3['nombreArticulo']." <br>".$fetch2['codigoChip'];
                } catch (\Throwable $th) {
                  //error al consultar el producto
                }
              }else{
                //se trata de un servicio
                $idTrabajo = $fetch2['trabajoID'];
                $sql4 = "SELECT a.idTrabajo,b.nombreServicio,a.marca,a.modelo FROM TRABAJOS a INNER JOIN SERVICIOS b 
                ON a.servicioID = b.idServicio WHERE a.idTrabajo = '$idTrabajo'";
                try {
                  $query4 = mysqli_query($conexion, $sql4);
                  $fetch4 = mysqli_fetch_assoc($query4);
                  $nombreCosa = $fetch4['nombreServicio']." ".$fetch4['marca']." ".$fetch4['modelo'];
                } catch (\Throwable $th) {
                  //throw $th;
                }
              }
  
              $contenidoTabla .= "<tr><th class='detalle-item'>".
              strtolower($nombreCosa)."</th><th class='detalle-monto'>$".number_format($venta,2)."</th></tr>";
  
            }//fin del while detalleventa
          }else{
            //sin ventas en el dia
            $contenidoTabla = "<tr><th style='font-weight:bold;'>SIN VENTAS</th></tr>";
          }

          

          //consultamos los gastos
          $sql5 = "SELECT SUM(montoMov) AS montoGastos FROM MOVCAJAS WHERE usuarioMov = '$idUsuario' AND 
          conceptoMov = '15' AND empresaMovID = '$idEmprersa' AND fechaMovimiento = '$fecha'";
          $query5 = mysqli_query($conexion, $sql5);
          $fetch5 = mysqli_fetch_assoc($query5);
          if(!empty($fetch5['montoGastos'])){
            $montoGasto = $fetch5['montoGastos'];
          }else{
            $montoGasto = "0";
          }
          

          $sql6 = "SELECT SUM(montoMov) AS montoPrecortes FROM MOVCAJAS WHERE 
          usuarioMov = '$idUsuario' AND conceptoMov = '16' AND empresaMovId ='$idEmprersa'
          AND fechaMovimiento = '$fecha'";
          $query6 = mysqli_query($conexion, $sql6);
          $fetch6 = mysqli_fetch_assoc($query6);
          $montoPrecortes = $fetch6['montoPrecortes'];

          $efectivoEntrega = $ventaEfectivo - $montoGasto -$montoPrecortes;


        }else{
          //el usuario no realizo ventas
          $ventaEfectivo = 0;
          $ventaDigital = 0;
          $numArticulos = 0;
          $totalVenta = 0;
          $efectivoEntrega = 0;
          $contenidoTabla = "<tr><th style='font-weight:bold;text-align:center;'>SIN VENTAS</th></tr>";
        }

        // Mostramos el formato de reporte\

        if(!empty($_GET['date'])){
          $fechaCierre = $fecha." - ".$horaCierre;
        }else{
          $fechaCierre = date('d-m-Y')." - ".date('H:i:s');
        }



        ?>
          <!DOCTYPE html>
            <html lang="en">
              <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Reporte de Ventas</title>
                <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">
                <style>
                  @page {
                    size: 58mm auto;
                    margin: 0;
                  }
                  * {
                    box-sizing: border-box;
                  }
                  html, body {
                    margin: 0;
                    padding: 0;
                  }
                  body {
                    font-family: 'Ubuntu', Arial, sans-serif;
                    font-size: 12px;
                    line-height: 1.45;
                    color: #000;
                    /* Vista en pantalla: fondo distinto para que el ticket resalte */
                    background: #2f3437;
                    min-height: 100vh;
                    display: flex;
                    justify-content: center;
                    padding: 20px 0;
                  }
                  .ticket {
                    width: 58mm;
                    background: #fff;
                    margin: 0 auto;
                    padding: 3mm 2mm;
                    text-align: center;
                    box-shadow: 0 4px 18px rgba(0,0,0,0.35);
                  }

                  @media print {
                    html, body {
                      width: 58mm;
                      background: #fff;
                      display: block;
                      padding: 0;
                    }
                    .ticket {
                      width: 54mm;
                      box-shadow: none;
                      padding: 2mm 0;
                    }
                  }
                  table {
                    width: 100%;
                    border-collapse: collapse;
                    table-layout: fixed;
                  }
                  th, td {
                    word-wrap: break-word;
                    overflow-wrap: break-word;
                    padding: 1px 0;
                    color: #000;
                  }
                  .empresa {
                    font-size: 15px;
                    font-weight: 700;
                    text-align: center;
                  }
                  .dom-suc {
                    font-size: 11px;
                    font-weight: 400;
                    text-align: center;
                  }
                  .titulo {
                    font-size: 12px;
                    font-weight: 500;
                    text-align: center;
                  }
                  .datos-meta {
                    font-size: 11px;
                    font-weight: 400;
                    text-align: center;
                  }
                  .separador-solido {
                    border-top: 2px solid #000;
                  }
                  .separador-punteado {
                    border-top: 1px solid #000;
                  }
                  .seccion-titulo {
                    font-size: 12px;
                    font-weight: 700;
                    text-align: center;
                  }
                  .etiqueta {
                    text-align: left;
                    font-weight: 700;
                  }
                  .monto {
                    text-align: right;
                    font-weight: 500;
                  }
                  .monto-total {
                    text-align: right;
                    font-weight: 700;
                    font-size: 13px;
                  }
                  .detalle-item {
                    font-weight: 400;
                    text-align: left;
                    font-size: 11px;
                  }
                  .detalle-monto {
                    text-align: right;
                    font-size: 11px;
                    font-weight: 500;
                    white-space: nowrap;
                  }
                </style>
              </head>
              <body>
                <div class="ticket">

                  <table>
                    <thead>
                      <tr>
                        <th colspan="2" class="empresa"><?php echo $nombreEmpresa ?></th>
                      </tr>
                      <tr>
                        <th colspan="2" class="dom-suc"><?php echo $domSuc ?></th>
                      </tr>

                      <tr>
                        <th colspan="2" class="titulo">Cierre del dia. <?php echo $fecha ?></th>
                      </tr>
                      <tr>
                        <th colspan="2" class="datos-meta">Fecha y hora - <?php echo $fechaCierre; ?></th>
                      </tr>
                      <tr>
                        <th colspan="2" class="datos-meta"><?php echo $nombreUsuario; ?></th>
                      </tr>
                      <tr>
                        <th colspan="2" class="separador-solido"></th>
                      </tr>
                      <tr>
                        <th colspan="2" class="seccion-titulo">Concentrado de Venta</th>
                      </tr>
                      <tr>
                        <th colspan="2" class="separador-solido"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td colspan="2" class="etiqueta">Venta Efectivo:</td>
                      </tr>
                      <tr>
                        <td colspan="2" class="monto">$<?php echo number_format($ventaEfectivo,2); ?></td>
                      </tr>
                      <tr>
                        <th colspan="2" class="separador-punteado"></th>
                      </tr>
                      <tr>
                        <td colspan="2" class="etiqueta">Venta Digital:</td>
                      </tr>
                      <tr>
                        <td colspan="2" class="monto">$<?php echo number_format($ventaDigital,2); ?></td>
                      </tr>
                      <tr>
                        <th colspan="2" class="separador-punteado"></th>
                      </tr>
                      <tr>
                        <td colspan="2" class="etiqueta">Venta Total:</td>
                      </tr>
                      <tr>
                        <td colspan="2" class="monto">$<?php echo number_format($totalVenta,2); ?></td>
                      </tr>
                      <tr>
                        <th colspan="2" class="separador-punteado"></th>
                      </tr>
                      <tr>
                        <td colspan="2" class="etiqueta">Monto Gastos:</td>
                      </tr>
                      <tr>
                        <td colspan="2" class="monto">$<?php echo number_format($montoGasto,2); ?></td>
                      </tr>
                      <tr>
                        <th colspan="2" class="separador-punteado"></th>
                      </tr>
                      <tr>
                        <td colspan="2" class="etiqueta">Pre-cortes:</td>
                      </tr>
                      <tr>
                        <td colspan="2" class="monto">$<?php echo number_format($montoPrecortes,2); ?></td>
                      </tr>
                      <tr>
                        <th colspan="2" class="separador-punteado"></th>
                      </tr>
                      <tr>
                        <td colspan="2" class="etiqueta">Efectivo a Entregar</td>
                      </tr>
                      <tr>
                        <td colspan="2" class="monto-total">$<?php echo number_format($efectivoEntrega,2); ?></td>
                      </tr>

                      <tr>
                        <th colspan="2" class="separador-solido"></th>
                      </tr>
                      <tr>
                        <th colspan="2" class="seccion-titulo">Detalle de Venta</th>
                      </tr>
                      <tr>
                        <th colspan="2" class="separador-solido"></th>
                      </tr>

                      <?php echo $contenidoTabla; ?>

                    </tbody>
                  </table>

                </div>
              </body>
              <script>
                window.print();
              </script>
            </html>
        <?php
      } catch (\Throwable $th) {
        //throw $th;
        echo "Error consulta detalle ".$th;
      }

    }else{
      //no procede el reporte
      echo "no podemos procesar este reporte.";
    }
  } catch (\Throwable $th) {
    //throw $th;
    echo "Error ".$th;
  }
}
?>