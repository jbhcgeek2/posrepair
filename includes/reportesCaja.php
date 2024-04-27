<?php 

session_start();

if(!empty($_SESSION['usuarioPOS'])){
  //insertamos los archivos que necesitamos
  include("articulos.php");
  include("usuarios.php");
  include("documentos.php");
  include("conexion.php");

  $usuario = $_SESSION['usuarioPOS'];
  $empresa = datoEmpresaSesion($usuario,"id");
  $empresa = json_decode($empresa);
  $idEmpresaSesion = $empresa->dato;

  $dataUSer = getDataUser($idEmpresaSesion,$usuario);
  $dataUSer = json_decode($dataUSer);
  $idSucursalN = $dataUSer->sucursalID;

  $tipoRol = verTipoUsuario($usuario);
  $tipoUsuario = json_decode($tipoRol);
  $rolUsuario = "";
  // print_r($tipoUsuario);
  if($tipoUsuario->status == "ok"){
    $rolUsuario = $tipoUsuario->data;
  }else{
    $rolUsuario = "error";
  }

  if(!empty($_POST['fechaIniBus'])){
    //seccion para realizar la busqeuda de ventas por fecha
    $fechaIni = $_POST['fechaIniBus'];
    $fechaFin = $_POST['fechaFinBus'];

    $sql = "";

    if($rolUsuario == "Administrador"){
      $sql = "SELECT * FROM DETALLEVENTA a INNER JOIN VENTAS b ON a.ventaID = b.idVenta 
      INNER JOIN ARTICULOS c ON a.articuloID = c.idArticulo 
      INNER JOIN SUCURSALES d ON a.sucursalID = d.idSucursal
      WHERE b.fechaVenta BETWEEN $fechaIni AND $fechaFin";
    }elseif($rolUsuario == "Vendedor"){
      //solo podra ver las ventas de su usuario y sucursal
      $sql = "SELECT * FROM DETALLEVENTA a INNER JOIN VENTAS b ON a.ventaID = b.idVenta 
      INNER JOIN ARTICULOS c ON a.articuloID = c.idArticulo
      WHERE b.fechaVenta = '$fecha' AND a.usuarioVenta = '$usuario' 
      AND a.sucursalID = '$idSucursalN'";
    }else{
      //el usuario encargado podra ver las ventas de todos
      //los usuarios, pero solo de su susucrsal
      $sql = "SELECT * FROM DETALLEVENTA a INNER JOIN VENTAS b ON a.ventaID = b.idVenta 
      INNER JOIN ARTICULOS c ON a.articuloID = c.idArticulo
      WHERE b.fechaVenta = '$fecha' AND a.sucursalID = '$idSucursalN'";
    }

    try {
      $query = mysqli_query($conexion, $sql);
      if(mysqli_num_rows($query) > 0){
        $datos = [];
        $i = 0;
        while($fetch = mysqli_fetch_assoc($query)){
          $datos[$i] = $fetch;
          $i++
        }//fin del while
        $res = ["status"=>"ok","data"=>$datos];
        echo json_encode($res);
      }else{
        //sin resultados
        $res = ["status"=>"ok","data"=>"NoData"];
        echo json_encode($res);
      }
    } catch (\Throwable $th) {
      $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al consultar las ventas: ".$th];
      echo json_encode($res);
    }

  
  }
}

?>