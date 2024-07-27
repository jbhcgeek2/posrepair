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

  echo $rolUsuario;


}
?>