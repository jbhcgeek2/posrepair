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
  $fecha = date('Y-m-d');

  $nombreEmpresa = datoEmpresaSesion($usuario,"nombre");
  $nombreEmpresa = json_decode($nombreEmpresa)->dato;

  //consultamos si el usuario ya tiene el cierre procesado
  $sql = "SELECT * FROM MOVCAJAS WHERE fechaMovimiento = '$fecha' AND
  usuarioMov = '$idUsuario' AND conceptoMov = '4'";
  try {
    $query = mysqli_query($conexion, $sql);
    if(mysqli_num_rows($query) == 1){
      //podemos hacer el reporte
      //consultamos las ventas
      $sql2 = "SELECT * FROM DETALLEVENTA a INNER JOIN VENTAS b 
      ON a.ventaID = b.idVenta WHERE b.fechaVenta = '$fecha' 
      AND a.usuarioVenta = '$usuario' AND a.sucursalID = '$idSucursal'";
      try {
        $query2 = mysqli_query($conexion, $sql2);
        if(mysqli_num_rows($query2) > 0){
          $ventaEfectivo = 0;
          $ventaDigital = 0;
          $numArticulos = 0;
          $totalVenta = 0;

          while($fetch2 = mysqli_fetch_assoc($query2)){
            $venta = $fetch2['subtotalVenta'];
            $tipoVenta = $fetch2['tipoPago'];
            $totalVenta = $totalVenta + $venta;
            if($tipoVenta == "Efectivo"){
              $ventaEfectivo = $ventaEfectivo + $venta;
            }else{
              $ventaDigital = $ventaDigital + $venta;
            }

          }//fin del while detalleventa

        }else{
          //el usuario no realizo ventas
          $ventaEfectivo = 0;
          $ventaDigital = 0;
          $numArticulos = 0;
          $totalVenta = 0;
        }

        // Mostramos el formato de reporte\
        ?>
          <!DOCTYPE html>
            <html lang="en">
              <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Reporte de Ventas</title>
                <link href='http://fonts.googleapis.com/css?family=Ubuntu&subset=cyrillic,latin' rel='stylesheet' type='text/css' />
              </head>
              <body>
                <div style="border: 0px solid #000; width:250px; text-align:center; font-family: 'Ubuntu', sans-serif;">
                  <div style="font-size: 15px;">
                    <?php echo $nombreEmpresa; ?><br>
                    Sucursal: <?php echo $nombreSuc; ?><br>
                    Domicilio: <?php echo $domSuc; ?><br>
                    Telefono: <?php echo $telSuc; ?><br>
                    <br>
                  </div>
                  

                  <table style="width:100%;">
                    <thead>
                      <tr>
                        <th colspan="3" style="text-align:left;">Servicio No. <?php echo $numServ; ?></th>
                      </tr>
                      <tr style="font-size:13px;">
                        <th colspan="3" style="font-weight:100;">Cliente - <?php echo $nombreCliente; ?></th>
                      </tr>
                      <tr style="font-size:13px;">
                        <th colspan="3" style="font-weight:100;">Fecha y hora - <?php echo $fechaRegistro." - ".$horaRegistro; ?></th>
                      </tr>
                      <tr style="font-size:13px;">
                        <th colspan="3" style="font-weight:100;">Empleado - <?php echo $nombreUsuario; ?></th>
                      </tr>
                      <tr>
                        <th colspan="3" style="border-top: 1px solid;"></th>
                      </tr>
                      <tr>
                        <th>Concentrado de Venta</th>
                      </tr>
                      <tr>
                        <th colspan="3" style="border-top: 1px solid;"></th>
                      </tr>
                    </thead>
                    <tbody style="text-align:left;">
                      <tr>
                        <td style="font-weight:bold;">Venta Efectivo: <?php echo number_format($ventaEfectivo,2); ?></td>
                      </tr>
                      <tr>
                        <td><?php echo number_format($ventaEfectivo,2); ?></td>
                      </tr>
                      <tr>
                        <th style="border-top: 1px dotted;"></th>
                      </tr>
                      <tr>
                        <td style="font-weight:bold;">Venta Digital:</td>
                      </tr>
                      <tr>
                        <td><?php echo number_format($ventaDigital,2); ?></td>
                      </tr>
                      <tr>
                        <th style="border-top: 1px dotted;"></th>
                      </tr>
                      <tr>
                        <td style="font-weight:bold;">Venta Total:</td>
                      </tr>
                      <tr>
                        <td><?php echo number_format($totalVenta,2); ?></td>
                      </tr>
                      <tr>
                        <th style="border-top: 1px dotted;"></th>
                      </tr>
                      <tr>
                        <td style="font-weight:bold;">Observaciones:</td>
                      </tr>
                      <tr>
                        <td><?php echo $observaciones; ?></td>
                      </tr>
                      <tr>
                        <th style="border-top: 1px dotted;"></th>
                      </tr>
                      <tr>
                        <td style="font-weight:bold;">Accesorios:</td>
                      </tr>
                      <tr>
                        <td><?php echo $accesorios; ?></td>
                      </tr>
                      <tr>
                        <th style="border-top: 1px dotted;"></th>
                      </tr>
                      <tr>
                        <td style="font-weight:bold;">Costo Aproximado:</td>
                      </tr>
                      <tr>
                        <td>$<?php echo $costoAprox; ?></td>
                      </tr>
                      <tr>
                        <th style="border-top: 1px dotted;"></th>
                      </tr>
                      <tr>
                        <td style="font-weight:bold;">Anticipo:</td>
                      </tr>
                      <tr>
                        <td>$<?php echo $anticipo; ?></td>
                      </tr>
                      <tr>
                        <th style="border-top: 1px dotted;"></th>
                      </tr>
                      <tr>
                        <td style="font-weight:bold;">Fecha Estimada de entrega:</td>
                      </tr>
                      <tr>
                        <td><?php echo $fechaEntrega; ?></td>
                      </tr>
                      <tr>
                        <th style="border-top: 1px dotted;"></th>
                      </tr>
                      <tr>
                        <td style="font-weight:bold;text-align:center;">Condiciones del Servicio</td>
                      </tr>
                      <tr>
                        <th style="text-align: justify;font-size:13px;font-weight:normal;">
                          <?php echo $condicionesServicio; ?>
                        </th>
                      </tr>
                        
                    </tbody>
                  </table>
                  <p style="margin-top:70px;text-align:center;border-top:1px solid;">
                    <span>Firma del cliente</span>
                  </p>
                  <p>
                    Gracias por su preferencia<br>
                    Vuelva pronto
                  </p>
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
      echo "no amiguito";
    }
  } catch (\Throwable $th) {
    //throw $th;
    echo "Error ".$th;
  }
}
?>