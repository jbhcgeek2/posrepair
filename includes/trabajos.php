<?php 

function altaTrabajo($cliente,$fechAlta, $tipoDispo,$tipoServ, $marca,$modeloServicio,
$serieDispo,$accesorios,$problema,$observacion,$contraDispo,$fechaEntrega,$costoServ,$anticipo,
$usuario,$sucursal,$empresa){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  //antes de insertar el trabajo, devemos consultar el consecutivo
  $sql = "SELECT COUNT(*) AS numServicios FROM TRABAJOS WHERE empresaID = '$empresa'";
  try {
    $query = mysqli_query($conexion, $sql);
    $fetch = mysqli_fetch_assoc($query);

    $numServ = $fetch['numServicios'];
    $numServ = $numServ+1;

    //ahora si insertamos el trabajo
    $sql2 = "INSERT INTO TRABAJOS (numTrabajo,estatusTrabajo,fechaTrabajo,clienteID,
    sucursalID,empresaID,tipoDispositivo,servicioID,marca,modelo,imeiClave,
    accesorios,problema,observaciones,contraDispo,fechaEntrega,costoInicial,anticipo) 
    VALUES ('$numServ','Activo','$fechAlta','$cliente','$sucursal','$empresa','$tipoDispo',
    '$tipoServ','$marca','$modeloServicio','$serieDispo','$accesorios','$problema','$observacion',
    '$contraDispo','$fechaEntrega','$costoServ','$anticipo')";
    try {
      $query2 = mysqli_query($conexion, $sql2);
      //hasta este punto se da por terminao y completado
      $res = ['status'=>'ok','mensaje'=>'operationComplete'];
      return json_encode($res);
    } catch (\Throwable $th) {
      $res = ['status'=>'error','mensaje'=>'Ocurrio un error al insertar el trabajo: '.$th];
      return json_encode($res);
    }
  }catch (\Throwable $th) {
    $res = ['status'=>'error','mensaje'=>'Ocurrio un error al asignar el numero de trabajo: '.$th];
    return json_encode($res);
  }

}

?>