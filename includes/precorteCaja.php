<?php 

session_start();
// error_reporting(1);
if(!empty($_SESSION['usuarioPOS'])){
  include("usuarios.php");
  include("conexion.php");
  include("articulos.php");
  include("ventas.php");
  include("operacionesCaja.php");

  $usuario = $_SESSION['usuarioPOS'];
  $empresa = datoEmpresaSesion($usuario,"id");
  $idEmpresa = json_decode($empresa)->dato;
  $datosUsuario = getDataUser($usuario,$idEmpresa);
  $idSucursal = json_decode($datosUsuario)->sucursalID;
  $idUsuario = json_decode($datosUsuario)->idUsuario;
  $fecha = date('Y-m-d');
  $hora = date('H:i:s');
  $plaza = "Efectivo";
  $conceptoPre = 16;
  $observacion = "Pre-Corte del dia ".$fecha.", hora: ".$hora;
  $tipoMov = "S";

  if(!empty($_POST['montoRetiraPreCorte'])){
    //seccion para procesar un precorte del dia
    //consultamos el monto de las ventas quie tenga en efectivo
    $montoPrecorte = $_POST['montoRetiraPreCorte'];

    $sql1 = "SELECT * FROM VENTAS WHERE usuarioID = ? AND fechaVenta = ? AND 
    tipoPago = ?";
    $query1 = mysqli_prepare($conexion, $sql1);
    mysqli_stmt_bind_param($query1,"iss",$idUsuario,$fecha,$plaza);
    mysqli_stmt_execute($query1);
    $result1 = mysqli_stmt_get_result($query1);
    if(mysqli_num_rows($result1) > 0){
      $vanMonto = 0;
      while($fetch1 = mysqli_fetch_assoc($result1)){
        $montoVenta = $fetch1['totalVenta'];
        $vanMonto = $vanMonto + $montoVenta;

      }//fin del while1
      $concep = "16";
      //ahora verificamos si ya se han realizado mas precortes en el dia del usuario
      $sql2 = "SELECT SUM(montoMov) AS montoRetiro FROM MOVCAJAS WHERE fechaMovimiento = ? AND 
      conceptoMov = ? AND usuarioMov = ? AND empresaMovID = ?";
      $query2 = mysqli_prepare($conexion, $sql2);
      mysqli_stmt_bind_param($query2,"siii",$fecha,$concep,$idUsuario,$idEmpresa);
      mysqli_stmt_execute($query2);
      $result2 = mysqli_stmt_get_result($query2);
      $fetch2 = mysqli_fetch_assoc($result2);

      $montoRetirado = $fetch2['montoRetiro'];

      $efectivoRestante = $vanMonto - $montoRetirado;

      //validamos si el monto a retirar si se puede realizar respecto al monto existente
      if($montoPrecorte <= $efectivoRestante){
        //si es posible realizar el retiro
        //insertamos el movimiento de retiro
        mysqli_begin_transaction($conexion);
        try {
          //code...
          $sql3 = "INSERT INTO MOVCAJAS (fechaMovimiento,horaMovimiento,usuarioMov,
          montoMov,conceptoMov,observacionMov,sucursalMovID,tipoMov,empresaMovID) 
          VALUES (?,?,?,?,?,?,?,?,?)";
          $query3 = mysqli_prepare($conexion,$sql3);
          mysqli_stmt_bind_param($query3,"ssidisisi",$fecha,$hora,$idUsuario,$montoPrecorte,
          $conceptoPre,$observacion,$idSucursal,$tipoMov,$idEmpresa);
          mysqli_stmt_execute($query3);
          mysqli_commit($conexion);
          ///podemos dar por erealizadola consulta
          $res = ['status'=>'ok','mensaje'=>'operationComplete'];
          echo json_encode($res);
          mysqli_close($conexion);
        } catch (\Throwable $th) {
          //error al procesar el precorte
          mysqli_rollback($conexion);
          $res = ['status'=>'error','mensaje'=>'Ocurrio un error al procesar el pre-corte.'];
          echo json_encode($res);
        }
        
      }else{
        //el efectivo que desea retirrar es mayor al existente
        $res = ['status'=>'error','mensaje'=>'El efectivo a retirar es mayor al existente.'];
        echo json_encode($res);
      }
    }else{
      //no se detectaron movimiento en efectivo
      $res = ['status'=>'error','mensaje'=>'No se detecto efectivo en caja.'];
      echo json_encode($res);
    }


  }
}

?>