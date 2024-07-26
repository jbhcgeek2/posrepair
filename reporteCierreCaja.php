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
          $contenidoTabla = "";

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
                $nombreCosa = $fetch3['nombreArticulo'];
              } catch (\Throwable $th) {
                //error al consultar el producto
              }
            }else{
              //se trata de un servicio
              $idTrabajo = $fetch2['trabajoID'];
              $sql4 = "SELECT a.idTrabajo,b.nombreServicio FROM TRABAJOS a INNER JOIN SERVICIOS b 
              ON a.servicioID = b.idServicio WHERE a.idTrabajo = '$idTrabajo'";
              try {
                $query4 = mysqli_query($conexion, $sql4);
                $fetch4 = mysqli_fetch_assoc($query4);
                $nombreCosa = $fetch4['nombreServicio'];
              } catch (\Throwable $th) {
                //throw $th;
              }
            }

            $contenidoTabla .= "<tr><th style='font-weight:normal;'>".
            strtolower($nombreCosa)."</th><th> $".number_format($venta,2)."</th></tr>";

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
                        <th colspan="3" style="text-align:left;">Fecha de Venta. <?php echo date('d-m-Y'); ?></th>
                        <th></th>
                      </tr>
                      <tr style="font-size:13px;">
                        <th colspan="3" style="font-weight:100;">Cliente - <?php echo $nombreCliente; ?></th>
                        <th></th>
                      </tr>
                      <tr style="font-size:13px;">
                        <th colspan="3" style="font-weight:100;">Fecha y hora - <?php echo $fechaRegistro." - ".$horaRegistro; ?></th>
                        <th></th>
                      </tr>
                      <tr style="font-size:13px;">
                        <th colspan="3" style="font-weight:100;">Empleado - <?php echo $nombreUsuario; ?></th>
                        <th></th>
                      </tr>
                      <tr>
                        <th colspan="3" style="border-top: 1px solid;"></th>
                        <th></th>
                      </tr>
                      <tr>
                        <th>Concentrado de Venta</th>
                        <th></th>
                      </tr>
                      <tr>
                        <th colspan="3" style="border-top: 1px solid;"></th>
                      </tr>
                    </thead>
                    <tbody style="text-align:left;">
                      <tr>
                        <td style="font-weight:bold;">Venta Efectivo:</td>
                        
                      </tr>
                      <tr>
                        <td style="text-align:right;">$<?php echo number_format($ventaEfectivo,2); ?></td>
                        <td></td>
                      </tr>
                      <tr>
                        <th style="border-top: 1px dotted;"></th>
                        <td></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td style="font-weight:bold;">Venta Digital:</td>
                        <td></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td style="text-align:right;">$<?php echo number_format($ventaDigital,2); ?></td>
                        <td></td>
                        <td></td>
                      </tr>
                      <tr>
                        <th style="border-top: 1px dotted;"></th>
                        <td></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td style="font-weight:bold;">Venta Total:</td>
                        <td></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td colspan="2" style="text-align:right;">$<?php echo number_format($totalVenta,2); ?></td>
                      </tr>

                      <tr>
                        <th colspan="3" style="border-top: 1px solid;"></th>
                      </tr>
                      <tr>
                        <th style="text-align:center;">Detalle de Venta</th>
                        <td></td>
                        <td></td>
                      </tr>
                      <tr>
                        <th colspan="3" style="border-top: 1px solid;"></th>
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
      echo "no amiguito";
    }
  } catch (\Throwable $th) {
    //throw $th;
    echo "Error ".$th;
  }
}
?>