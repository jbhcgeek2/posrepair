<?php 
session_start();
//consultaremos los datos de la empresa
if(!empty($_SESSION['usuarioPOS'])){
  // include("includes/header.php");
  include("includes/empresas.php");
  include("includes/conexion.php");
  include("includes/usuarios.php");
  include("includes/cliente.php");
  include("includes/trabajos.php");
  

  $usuario = $_SESSION['usuarioPOS'];
  $ticket = $_GET['t'];
  //consultamos los datos del usuario
  $empresa = datoEmpresaSesion($usuario,"id");
  $idEmprersa = json_decode($empresa)->dato;
  $datosUsuario = getDataUser($usuario,$idEmprersa);
  $idSucursal = json_decode($datosUsuario)->sucursalID;
  $idUsuario = json_decode($datosUsuario)->idUsuario;
  // echo $idUsuario;

  $nombreEmpresa = datoEmpresaSesion($usuario,"nombre");
  $nombreEmpresa = json_decode($nombreEmpresa)->dato;

  //verificamos que la venta exista
  $venta = ticketTrabajo($ticket,$idEmprersa);
  $venta = json_decode($venta);
  // print_r($venta);718037884639

  if($venta->status == "ok"){
    print_r($venta);
    
    ?> 
    <!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Impresion de Ticket</title>
        <link href='http://fonts.googleapis.com/css?family=Ubuntu&subset=cyrillic,latin' rel='stylesheet' type='text/css' />
      </head>
      <body>
        <div style="border: 0px solid #000; width:250px; text-align:center; font-family: 'Ubuntu', sans-serif;">
          <div style="font-size: 15px;">
            <?php echo $nombreEmpresa; ?><br>
            Sucursal: <?php  ?><br>
            Domicilio: <?php  ?><br>
            Telefono: <?php  ?><br>
            <br>
          </div>
          

          <table style="width:100%;">
            <thead>
              <tr>
                <th colspan="3" style="text-align:left;">Ticket No. <?php ?></th>
              </tr>
              <tr style="font-size:13px;">
                <th colspan="3" style="font-weight:100;">Cliente - <?php ?></th>
              </tr>
              <tr style="font-size:13px;">
                <th colspan="3" style="font-weight:100;">Fecha y hora - <?php ?></th>
              </tr>
              <tr style="font-size:13px;">
                <th colspan="3" style="font-weight:100;">Cajero - <?php ?></th>
              </tr>
              <tr>
                <th colspan="3" style="border-top: 1px solid;"></th>
              </tr>
              <tr>
                <th>UND</th>
                <th>ARTICULO</th>
                <th>TOTAL</th>
              </tr>
              <tr>
                <th colspan="3" style="border-top: 1px solid;"></th>
              </tr>
            </thead>
            <tbody>
              
               
                
            </tbody>
          </table>
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
  }else{
    //error al consultar el ticket
    echo "error";
  }

}
?>
