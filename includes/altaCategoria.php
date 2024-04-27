<?php 

session_start();

if(!empty($_SESSION['usuarioPOS'])){
  
  include("articulos.php");
  include("usuarios.php");

  if(!empty($_POST['nombreCat'])){
    //seccion para insertar la nueva categoria

    $nombreCat = $_POST['nombreCat'];
    $estatusCat = $_POST['estatus'];
    $descripcion = $_POST['descripcionCat'];
    $usuario = $_SESSION['usuarioPOS'];

    $empresa = datoEmpresaSesion($usuario,"id");
    $empresa = json_decode($empresa);
    if($empresa->status == "ok"){
      $idEmpresa = $empresa->dato;

      $guardar = setCategoria($idEmpresa,$nombreCat,$estatusCat,$descripcion);
      $guardar = json_decode($guardar);

      if($guardar->status == "ok"){
        $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
        echo json_encode($res);
      }else{
        //ocurrio un error al insertar
        $error = $guardar->mensaje;
        $res = ["status"=>"error","mensaje"=>$error];
        echo json_encode($res);
      }
    }else{
      //error al consultar el id de le empresa
      $res = ["status"=>"error","mensaje"=>"Ocurrio un error al consultar la informacion del usuario."];
      echo json_encode($res);
    }
    




  }
}
?>