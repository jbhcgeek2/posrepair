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

  //verificamos si el usuario solicitante es administrador
  $tipoRol = verTipoUsuario($usuario);
  $tipoUsuario = json_decode($tipoRol);
  $rolUsuario = "";
  // print_r($tipoUsuario);
  if($tipoUsuario->status == "ok"){
    $rolUsuario = $tipoUsuario->data;
  }else{
    $rolUsuario = "error";
  }

  if($rolUsuario == "Administrador"){
    if(!empty($_POST['autorization'])){
      //tiene clace de autorizacion
      $clave = datoEmpresaSesion($usuario,"claveAudi");
      $clave = json_decode($clave);
      $claveAutori = $empresa->dato;

      $clave = $_POST['autorization'];
      //consultamos la clave de la empresa

      if($clave == $claveAutori){
        //se autoriza la auditoria
        //primero consultaremos los articulos y las existencias actuales
        //y los amacenaremos en una tabla
        $sql = "SELECT * FROM ARTICULOS WHERE empresaID = '$idEmpresaSesion' AND 
        estatusArticulo = '0'";
        try {
          $query = mysqli_query($conexion, $sql);
          while($fetch = mysqli_fetch_assoc($query)){
            echo $fetch['nombreArticulo'];
          }//fin del while
        } catch (\Throwable $th) {
          //throw $th;
        }
      }else{
        $res = ['status'=>'error','mensaje'=>'Clave de autorizacion incorrecta.'];
        echo json_encode($res);
      }
    }else{
      $res = ['status'=>'error','mensaje'=>'Acceso Denegado'];
      echo json_encode($res);
    }
  }else{
    $res = ['status'=>'error','mensaje'=>'Acceso Denegado'];
    echo json_encode($res);
  }


}
?>