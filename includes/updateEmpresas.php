<?php 

session_start();

if(!empty($_SESSION['usuarioPOS'])){
  include('conexion.php');

  if(!empty($_POST['nombreEmpresa'])){
    $nombreEmpresa = $_POST['nombreEmpresa'];
    $suscripcion = $_POST['planEmpresa'];

    //verificamos si se subio imagen para procesarla
    if(!empty($_FILES['logotipo']['tmp_name'])){
      echo "tiene logo";
    }
    echo "paso todo";
    
  }
}else{

}

?>