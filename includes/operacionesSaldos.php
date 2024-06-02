<?php 
session_start();

if(!empty($_SESSION['usuarioPOS'])){

  require_once("usuarios.php");
  require_once("conexion.php");

  $usuario = $_SESSION['usuarioPOS'];
  $empresa = datoEmpresaSesion($usuario,"id");
  $empresa = json_decode($empresa);
  $idEmpresaSesion = $empresa->dato;

  $dataUSer = getDataUser($usuario,$idEmpresaSesion);
  $dataUSer = json_decode($dataUSer);
  $idSucursalN = $dataUSer->sucursalID;
  $idUsuario = $dataUSer->idUsuario;

  if(!empty($_POST['tipoMov'])){
    //seccion para registrar un movimiento de cuenta
    $tipo = $_POST['tipoMov'];
    $concepto = $_POST['concepMov'];
    $observ = $_POST['observMov'];
    $montoMov = $_POST['montoMov'];
    $metodo = $_POST['metodoMov'];
    $fecha = date('Y-m-d');
    $hora = date('H:i:s');

    if($metodo == "Efectivo"){
      $campo = 'saldoEfectivo';
      $campoAnt = 'saldoEfeAnterior';
    }else{
      $campo = 'saldoTransferencia';
      $campoAnt = 'saldoTransAnterior';
    }

    // antes de procesar el movimiento, verificamos que si es retiro
    //cuente con el saldo suficiente
    if($tipo == "Salida"){
      $sqlEmp = "SELECT $campo FROM EMPRESAS WHERE idEmpresa = '$idEmpresaSesion'";
      try {
        $queryEmp = mysqli_query($conexion, $sqlEmp);
        $fetchEmp = mysqli_fetch_assoc($queryEmp);
        $saldoCuenta = $fetchEmp[$campo];
        if($saldoCuenta >= $montoMov){
          //si se puede procvesar el movimiento
          $nuevoSaldo = $saldoCuenta - $montoMov;
          //primero insertamos el movimiento
          $sql1 = "INSERT INTO MOVCAJAS (fechaMovimiento,horaMovimiento,usuarioMov,montoMov,conceptoMov,
          observacionMov,sucursalMovID,tipoMov,empresaMovID) VALUES ('$fecha','$hora','$idUsuario',
          '$montoMov','$concepto','$observ','$idSucursalN','S','$idEmpresaSesion')";
          try {
            $query1 = mysqli_query($conexion, $sql1);
            //se inserto el movimiento, ahora descontamos ese saldo
            $sql2 = "UPDATE EMPRESAS SET $campo = '$nuevoSaldo', $campoAnt = '$saldoCuenta' 
            WHERE idEmpresa = '$idEmpresaSesion'";
            try {
              $query2 = mysqli_query($conexion, $sql2);
              //hasta aqui podemos dar por terminado el proceso de rehistro
              $res = ['status'=>'ok','mensaje'=>'operationComplete'];
              echo json_encode($res);
            } catch (\Throwable $th) {
              //si fallo en esta partte diremos que contacte a soporte tecnico
              $res = ['status'=>'error','mensaje'=>'Ha ocurrido un error, contacte a soporte tecnico'];
              echo json_encode($res);
            }
          } catch (\Throwable $th) {
            $res = ['status'=>'error','mensaje'=>'Ocurrio un error al procesar el movimiento: '.$th];
            echo json_encode($res);
          }
        }else{
          //el monto a retirar el mayor al disponible
          $res = ['status'=>'error','mensaje'=>'El monto a retirar es superior al disponible.'];
          echo json_encode($res);
        }
      } catch (\Throwable $th) {
        //error de sonulata a la base 
        $res = ['status'=>'error','mensaje'=>'Ocurrio un error al consultar los saldos'];
        echo json_encode($res);
      }
    }else{
      //se trata de una entrada, aqui no importa la validacion del saldo
      //simplemente lo sumamos
      $sqlEmp = "SELECT $campo FROM EMPRESAS WHERE idEmpresa = '$idEmpresaSesion'";
      try {
        $queryEmp = mysqli_query($conexion, $sqlEmp);
        $fetchEmp = mysqli_fetch_assoc($queryEmp);
        $saldoCuenta = $fetchEmp[$campo];

        $nuevoSaldo = $saldoCuenta+$montoMov;
        //realizamos la insercion del movimiento
        $sql1 = "INSERT INTO MOVCAJAS (fechaMovimiento,horaMovimiento,usuarioMov,montoMov,conceptoMov,
        observacionMov,sucursalMovID,tipoMov,empresaMovID) VALUES ('$fecha','$hora','$idUsuario',
        '$montoMov','$concepto','$observ','$idSucursalN','E','$idEmpresaSesion')";
        try {
          $query1 = mysqli_query($conexion, $sql1);
          //ahora actualizamos el datos
          $sql2 = "UPDATE EMPRESAS SET $campo = '$nuevoSaldo', $campoAnt = '$saldoCuenta' WHERE 
          idEmpresa = '$idEmpresaSesion'";
          try {
            $query2 = mysqli_query($conexion, $sql2);
            //podemos dar por concluiodo
            $res = ['status'=>'ok','mensaje'=>'operationComplete'];
            echo json_encode($res);
          } catch (\Throwable $th) {
            $res = ['status'=>'error','mensaje'=>'Ha ocurrido un error, contacte a soporte tecnico.'];
            echo json_encode($res);
          }
        } catch (\Throwable $th) {
          $res = ['status'=>'error','mensaje'=>'Ocurrio un error al procesar el movimiento.'];
          echo json_encode($res);
        }
      }catch (\Throwable $th) {
        $res = ['status'=>'error','mensaje'=>'Ocurrio un error al consultar los saldos'];
        echo json_encode($res);
      }
    }
  }elseif(!empty($_POST['tipoMovRegGasto'])){
    $tipoGasto = $_POST['tipoMovRegGasto'];
    $usuarioGasto = $_POST['usuarioGasto'];
    // $conceptoGasto = $_POST['concepMovRegGasto'];
    $observGasto = $_POST['observMovGasto'];
    $montoGasto = $_POST['montoMovRegGasto'];
    $fecha = date('Y-m-d');
    $hora = date('H:i:s');
    $tipoMov ="";
    if($tipoGasto == "Gasto"){
      $tipoMov = "S";
      $conceMov = "15";
    }else{
      $tipoMov = "E";
      $conceMov = "2";
    }

    //tendremos que consultar la sucursal de operacion del usuario
    $sqlU = "SELECT sucursalID FROM USUARIOS WHERE idUsuario = '$usuarioGasto' AND 
    empresaID = '$idEmpresaSesion'";
    try {
      $queryU = mysqli_query($conexion, $sqlU);
      if(mysqli_num_rows($queryU) == 1){
        $fetchU = mysqli_fetch_assoc($queryU);

        $sucUsuarioMov = $fetchU['sucursalID'];
        //manos a la obra en una sola maniobra
        //insertamos el valor dentro de MOVCAJAS

        $sql = "INSERT INTO MOVCAJAS (fechaMovimiento,horaMovimiento,usuarioMov,montoMov,
        conceptoMov,observacionMov,sucursalMovID,tipoMov,empresaMovID) VALUES ('$fecha','$hora',
        '$usuarioGasto','$montoGasto','$conceMov','$observGasto','$sucUsuarioMov','$tipoMov','$idEmpresaSesion')";
        try {
          $query = mysqli_query($conexion, $sql);
          //se inserto correctamenete el gasto
          $res = ['status'=>'ok','mensaje'=>'operationSuccess'];
          echo json_encode($res);
        } catch (\Throwable $th) {
          //error al insertar el gasto
          $res = ['status'=>'error','mensaje'=>'Ocurrio un error al insertar el gasto.'];
          echo json_encode($res);
        }
      }else{
        //no se localizo el usuario
        $res = ['status'=>'error','mensaje'=>'No fue posible localizar el usuario.'];
        echo json_encode($res);
      }
    } catch (\Throwable $th) {
      $res = ['status'=>'error','mensaje'=>'Ocurrio un error al consultar la informacion del usuario'];
      echo json_encode($res);
    }

  }

}
?>