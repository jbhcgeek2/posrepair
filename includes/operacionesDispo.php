<?php 
  session_start();

  if(!empty($_SESSION['usuarioPOS'])){
    include("conexion.php");
    include("usuarios.php");
  
    $usuario = $_SESSION['usuarioPOS'];
  
    $empresa = datoEmpresaSesion($usuario,"id");
    $empresa = json_decode($empresa);
    $idEmpresaSesion = $empresa->dato;

    $dataUSer = getDataUser($usuario,$idEmpresaSesion);
		$dataUSer = json_decode($dataUSer);
		$idSucursalN = $dataUSer->sucursalID;
		$idUsuarioN = $dataUSer->idUsuario;

    $tipoRol = verTipoUsuario($usuario);
		$tipoUsuario = json_decode($tipoRol);
		$rolUsuario = "";
		// print_r($tipoUsuario);
		if($tipoUsuario->status == "ok"){
			$rolUsuario = $tipoUsuario->data;
		}else{
			$rolUsuario = "error";
		}

    if(!empty($_POST['dispoAutoriza'])){
      //seccio para autorizar un nuevo dispositivo
      $contraAdmin = $_POST['dispoAutoriza'];
      $idDispo = md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']);
      //consultamos la existencia pendiente del dispositivo
      
      $getDispo = getDispositivo($idUsuarioN,$idDispo);
      $getDispo = json_decode($getDispo);
      if($getDispo->data->estatusDispo == 0){
        //el dispositivo no esta autortizado, procedemos a validarlo
        //consultamos a los usuarios administradores de la empresa
        $rolAdmin = 1;
        mysqli_begin_transaction($conexion);
        $sql = "SELECT * FROM USUARIOS WHERE empresaID = ? AND rolID = ? AND passwordUser = ?";
        try {
          $query = mysqli_prepare($conexion, $sql);
          mysqli_stmt_bind_param($query,"iis",$idEmpresaSesion,$rolAdmin,$contraAdmin);
          mysqli_stmt_execute($query);
          $res = mysqli_stmt_get_result($query);
          if(mysqli_num_rows($res) > 0){
            //verificamos el administrador
            $seAutoriza = "0";
            $nuevoStatus = 1;
            while($fetch = mysqli_fetch_assoc($res)){
              $contraDB = $fetch['passwordUser'];
              $usuarioAutoriza = $fetch['userName'];
              if($contraAdmin == $contraDB){
                //si coincide la contraseña, autorizamos el dispositivo
                $sql2 = "UPDATE DISPOSITIVOS SET estatusDispo = ?, autorizo = ? WHERE 
                diviceID = ? AND usuarioID = ? AND empresaID = ?";
                $query2 = mysqli_prepare($conexion, $sql2);
                mysqli_stmt_bind_param($query2,"issii",$nuevoStatus,$usuarioAutoriza,$idDispo,
                $idUsuarioN,$idEmpresaSesion);
                mysqli_stmt_execute($query2);
                //se podria decir que ya se completo la actualizacion
                $seAutoriza = "1";
                break;
              }else{
                //no coincide
              }
            }//fin del while
            if($seAutoriza == 1){
              mysqli_commit($conexion);
              $data = ['status'=>'ok','mensaje'=>'operationComplete'];
              echo json_encode($data);
            }else{
              mysqli_rollback($conexion);
              $data = ['status'=>'error','mensaje'=>'No fue posible autorizar el dispositivo, dispositivo: '.$idDispo];
              echo json_encode($data);
            }
          }else{
            //sin resultados disponibles
            mysqli_rollback($conexion);
            $data = ['status'=>'error','mensaje'=>'Contraseña no valida, intente de nuevo'];
            echo json_encode($data);
          }
        } catch (\Throwable $th) {
          //error al consultar la existencias de usuarios administradores
          mysqli_rollback($conexion);
          $data = ['status'=>'error','mensaje'=>'Ocurrio un error al procesar la informacion: '.$idDispo];
          echo json_encode($data);
        }
        


      }else{
        //el dispositivo ya esta autorizado, le indicamos que ocurrio un error

      }
    }elseif(!empty($_POST['delDispo'])){
      //seccion para eliminar el acceso a un dispositivo
      $idDispo = $_POST['delDispo'];
      $estatus = 0;
      $autorizo = "";
      //verificamos si se trata de un admin que pueda eliminar el dispositivo
      if($rolUsuario == "Administrador"){
        $sql = "UPDATE DISPOSITIVOS SET estatusDispo = ?, autorizo = ? WHERE 
        empresaID = ? AND idDispo = ?";
        try {
          $query = mysqli_prepare($conexion, $sql);
          mysqli_stmt_bind_param($query,"isii",$estatus,$autorizo,$idEmpresaSesion,$idDispo);
          mysqli_stmt_execute($query);

          //podemos dar por completado el proceso
          $res = ['status'=>'ok','mensaje'=>'operationComplete'];
          echo json_encode($res);
        } catch (\Throwable $th) {
          //throw $th;
          $res = ['status'=>'error','mensaje'=>'Ha ocurrido un error al procesar la informacion.'];
          echo json_encode($res);
        }
      }else{
        //acceso denegado
        $res = ['status'=>'error','mensaje'=>'Acceso Denegado.'];
        echo json_encode($res);
      }
    }elseif(!empty($_POST['setAccess'])){
      //seccion para dar acceso a un dispositivo desde el panel de sispositivos
      $idDispo = $_POST['setAccess'];
      $estatus = 1;
      if($rolUsuario == "Administrador"){
        $sql = "UPDATE DISPOSITIVOS SET estatusDispo = ?, autorizo = ? WHERE 
        empresaID = ? AND idDispo = ?";
        try {
          $query = mysqli_prepare($conexion,$sql);
          mysqli_stmt_bind_param($query,"isii",$estatus,$usuario,$idEmpresaSesion,$idDispo);
          mysqli_stmt_execute($query);
          //podemos dar por autorizado
          $res = ['status'=>'ok','mensaje'=>'operationComplete'];
          echo json_encode($res);
        } catch (\Throwable $th) {
          $res = ['status'=>'error','mensaje'=>'No fue posible autorizar el dispo'];
          echo json_encode($res);
        }
      }else{
        //acceso denegado
        $res = ['status'=>'error','mensaje'=>'Acceso Denegado'];
        echo json_encode($res);
      }

    }
  }else{
    //sin sesion iniciada
  }

?>