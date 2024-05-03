<?php
session_start();


if(!empty($_SESSION['usuarioPOS'])){
  require("usuarios.php");
  require("empresas.php");
  require("articulos.php");
  require("documentos.php");

  $usuario = $_SESSION['usuarioPOS'];
  $empresa = datoEmpresaSesion($usuario,"id");
  $empresa = json_decode($empresa);
  $idEmpresaSesion = $empresa->dato;

  if(!empty($_POST['nombreUser'])){
    //seccion para registrar a un usuario nuevo
    $nombre = $_POST['nombreUser'];
    $paterno = $_POST['apPaterno'];
    $materno = $_POST['apMaterno'];
    $tel = $_POST['telUser'];
    $correo = $_POST['mailUser'];
    $nombreUsuario = $_POST['userName'];
    $contra = $_POST['passwordUser'];
    $sucUsuario = $_POST['scUsuario'];
    $tipoUsuario = $_POST['tipoUser'];

    //antes de continuar, verificamos el numero de usuario de la empresa
    //para ver si aun cuenta con disponibilidad
    $numUs = getNumUsers($idEmpresaSesion);
    print_r($numUs);
  }
}
?>