<?php

if(gethostname() == "DESKTOP-AUI7NQT"){
  $us = "root";
  $pw = "";
  $ht = "localhost";
  $db = "cierreseptiembre";

  $conexion = mysqli_connect($ht,$us,$pw)or die
  ("Ocurrio un error al comunicarse con la base de datos: ".mysqli_error($conexion));
  mysqli_select_db($conexion, $db)or die("No se establecio la conexion con la tabla: ".mysqli_error($conexion));
  mysqli_set_charset($conexion, "utf8");
  date_default_timezone_set('America/Mazatlan');
}else{
  $us = "u427759545_adminRepair";
  $pw = "Af=c[KM3Kn.d7uf4*";
  $ht = "localhost";
  $db = "u427759545_postrepair";

  $conexion = mysqli_connect($ht,$us,$pw)or die
  ("Ocurrio un error al comunicarse con la base de datos: ".mysqli_error($conexion));
  mysqli_select_db($conexion, $db)or die("No se establecio la conexion con la tabla: ".mysqli_error($conexion));
  mysqli_set_charset($conexion, "utf8");
  date_default_timezone_set('America/Mazatlan');
}
  

  ?>