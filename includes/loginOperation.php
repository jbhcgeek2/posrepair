<?php
session_start();

//si ya existe una sesion, la persona no tiene nada que hacer aca
if(empty($_SESSION['usuarioPOS'])){ 
  //agregamos las conexiones
  include("conexion.php");

  //verificamos la informacion recibida
  if(!empty($_POST['mail']) && !empty($_POST['pass'])){
    $mail = htmlentities($_POST['mail']);
    $pass = htmlentities($_POST['pass']);

    //verificamos en la base de datos
    $sql = "SELECT * FROM USUARIOS WHERE userName = '$mail' AND passwordUser = '$pass'";
    try {
      $query = mysqli_query($conexion, $sql);
      if(mysqli_num_rows($query) == 1){
        //verificamos 
        $fetch = mysqli_fetch_assoc($query);
        if($mail == $fetch['userName'] && $pass == $fetch['passwordUser']){
          //ahora si podemos iniciar la sesion
          $idUsuario = $fetch['idUsuario'];
          $idEmpresa = $fetch['empresaID'];
          
          //insertamos el nuevo inicio de sesion
          $dispositivoID =  md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']);
          $fecha = date("Y-m-d H:i:s");
          $fechaUltimo = date("Y-m-d H:i:s");
          $activo = 1;

          
            //no tiene sesiones activas
            $_SESSION['usuarioPOS'] = $fetch['userName'];
            $_SESSION['empresaPOS'] = $fetch['empresaID'];
            $sql2 = "SELECT * FROM DISPOSITIVOS WHERE usuarioID = ? AND empresaID = ? AND diviceID = ?";
            $query2 = mysqli_prepare($conexion,$sql2);
            mysqli_stmt_bind_param($query2,"iis",$idUsuario,$idEmpresa,$dispositivoID);
            mysqli_stmt_execute($query2);
            $fetch2 = mysqli_stmt_get_result($query2);
            
            if(mysqli_num_rows($fetch2) > 0){
              //ya existe un dispositivo insertado, actualizamos su hora de ingreso
              $fetch2 = mysqli_fetch_assoc($fetch2);
              $idDispo = $fetch2['idDispo'];
              $sql4 = "UPDATE DISPOSITIVOS SET ultimoAcceso = ?, activo = ? WHERE idDispo = ? AND empresaID = ?";
              $query4 = mysqli_prepare($conexion, $sql4);
              mysqli_stmt_bind_param($query4,"siii",$fechaUltimo,$activo,$idDispo,$idEmpresa);
              mysqli_stmt_execute($query4);
              //se actualizo
              $res = ["status"=>"ok","mensaje"=>"inicio de sesion correcto"];
              echo json_encode($res);
            }else{
              //es un nuevo dispositivo, lo insertamos
              $estatusDispo = "0";
              $sql3 = "INSERT INTO DISPOSITIVOS (usuarioID,diviceID,fechaRegistro,ultimoAcceso,
              estatusDispo,empresaID) VALUES (?,?,?,?,?,?)";
              $query3 = mysqli_prepare($conexion, $sql3);
              mysqli_stmt_bind_param($query3,"isssii",$idUsuario,$dispositivoID,$fecha,$fechaUltimo,
              $estatusDispo,$idEmpresa);
              mysqli_stmt_execute($query3);
              //se completo correctamente
              $res = ["status"=>"ok","mensaje"=>"inicio de sesion correcto"];
              echo json_encode($res);
            }
          
        }else{
          //algo no coincidio
          $res = ["status"=>"error","mensaje"=>"Correo y/o contrasena incorrectos"];
          echo json_encode($res);
        }
      }else{
        //usuario o contrasena incorrectos
        $res = ["status"=>"error","mensaje"=>"Correo y/o contrasena incorrectos"];
        echo json_encode($res);
      }
    } catch (\Throwable $th) {
      //error en la base de datos
      $res = ["status"=>"error","mensaje"=>"Ocurrio un error al realizar la consulta a la base de datos"];
      echo json_encode($res);
    }

  }else{
    //sin informacion para validar
      $res = ["status"=>"error","mensaje"=>"No se cuenta con informacion para procesar."];
      echo json_encode($res);

  }
}else{
  //nada de nada
  $res = ["status"=>"error","mensaje"=>"No se cuenta con informacion para procesar."];
  echo json_encode($res);

}
?>