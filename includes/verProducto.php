<?php 

session_start();

if(!empty($?_SESSSION['usuarioPOS'])){
    include("empresas.php");
    include("conexion.php");
    include("usuarios.php");
    include("trabajos.php");
    include("articulos.php");

    $usuario = $_SESSION['usuarioPOS'];
    $empresa = datoEmpresaSesion($usuario,"id");
	$empresa = json_decode($empresa);
	$idEmpresaSesion = $empresa->dato;

    $dataUSer = getDataUser($usuario,$idEmpresaSesion);
	$dataUSer = json_decode($dataUSer);
	$idSucursalN = $dataUSer->sucursalID;
    $idUsuario = $dataUSer->idUsuario;
    //detectamos el metodo que desea implementar

    if(!empty($_POST['busProds'])){
        //metodo para buscar productos
        echo $_POST['busProds'];
    }
}

?>