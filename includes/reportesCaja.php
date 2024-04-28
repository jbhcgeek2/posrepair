<?php 

session_start();

if(!empty($_SESSION['usuarioPOS'])){
  //insertamos los archivos que necesitamos
  include("articulos.php");
  include("usuarios.php");
  include("conexion.php");
  include("empresas.php");

  $usuario = $_SESSION['usuarioPOS'];
  $empresa = datoEmpresaSesion($usuario,"id");
  $empresa = json_decode($empresa);
  $idEmpresaSesion = $empresa->dato;

  $dataUSer = getDataUser($usuario,$idEmpresaSesion);
  $dataUSer = json_decode($dataUSer);
  $idSucursal = $dataUSer->sucursalID;
  $idUsuario = $dataUSer->idUsuario;
  
  // $idSucursal = json_decode($datosUsuario)->sucursalID;
  // $idUsuario = json_decode($datosUsuario)->idUsuario;

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
      WHERE b.fechaVenta BETWEEN '$fechaIni' AND '$fechaFin'";
    }elseif($rolUsuario == "Vendedor"){
      //solo podra ver las ventas de su usuario y sucursal
      $sql = "SELECT * FROM DETALLEVENTA a INNER JOIN VENTAS b ON a.ventaID = b.idVenta 
      INNER JOIN ARTICULOS c ON a.articuloID = c.idArticulo
      WHERE (b.fechaVenta BETWEEN '$fechaIni' AND '$fechaFin') AND a.usuarioVenta = '$usuario' 
      AND a.sucursalID = '$idSucursal'";
    }else{
      //el usuario encargado podra ver las ventas de todos
      //los usuarios, pero solo de su susucrsal
      $sql = "SELECT * FROM DETALLEVENTA a INNER JOIN VENTAS b ON a.ventaID = b.idVenta 
      INNER JOIN ARTICULOS c ON a.articuloID = c.idArticulo
      WHERE(b.fechaVenta BETWEEN '$fechaIni' AND '$fechaFin') AND a.sucursalID = '$idSucursal'";
    }

    try {
      $query = mysqli_query($conexion, $sql);
      if(mysqli_num_rows($query) > 0){
        $datos = [];
        $i = 0;
        while($fetch = mysqli_fetch_assoc($query)){
          $datos[$i] = $fetch;
          $i++;
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

  
  }elseif(!empty($_POST['fechaIniMov'])){
    //seccion para buscar movimienots de efectivo
    $fechaIniMov = $_POST['fechaIniMov'];
    $fechaFinMov = $_POST['fechaFinMov'];
    $sql = "";
    // echo $idEmpresaSesion;

    if($rolUsuario == "Administrador"){
      $sql = "SELECT *,(SELECT b.userName FROM USUARIOS b WHERE b.idUsuario = a.usuarioMov) AS usmov,
      (SELECT c.nombreSuc FROM SUCURSALES c WHERE c.idSucursal = a.sucursalMovID) AS sucNameMov,
      (SELECT d.nombreConcepto FROM CONCEPTOSMOV d WHERE d.idConcepto = a.conceptoMov) AS concepName
      FROM MOVCAJAS a WHERE a.empresaMovID = '$idEmpresaSesion' AND (a.fechaMovimiento BETWEEN '$fechaIniMov' AND '$fechaFinMov')";
    }elseif($rolUsuario == "Vendedor"){
      //solo podra ver las ventas de su usuario y sucursal
      $sql = "SELECT *,(SELECT b.userName FROM USUARIOS b WHERE b.idUsuario = a.usuarioMov) AS usmov,
      (SELECT c.nombreSuc FROM SUCURSALES c WHERE c.idSucursal = a.sucursalMovID) AS sucNameMov,
      (SELECT d.nombreConcepto FROM CONCEPTOSMOV d WHERE d.idConcepto = a.conceptoMov) AS concepName
      FROM MOVCAJAS a WHERE a.empresaMovID = '$idEmpresaSesion' AND a.usuarioMov = '$idUsuario' AND (a.fechaMovimiento BETWEEN '$fechaIniMov' AND '$fechaFinMov')";
    }else{
      //el usuario encargado podra ver las ventas de todos
      //los usuarios, pero solo de su susucrsal
      $sql = "SELECT * FROM DETALLEVENTA a INNER JOIN VENTAS b ON a.ventaID = b.idVenta 
      INNER JOIN ARTICULOS c ON a.articuloID = c.idArticulo
      WHERE(b.fechaVenta BETWEEN '$fechaIni' AND '$fechaFin') AND a.sucursalID = '$idSucursal'";
    }
    // echo $sql;

    try {
      $query = mysqli_query($conexion, $sql);
      if(mysqli_num_rows($query) > 0){
        $data = [];
        $i = 0;
        while($fetch = mysqli_fetch_assoc($query)){
          $data[$i] = $fetch;
          $i++;
        }//fin del while
        $res = ["status"=>"ok","data"=>$data];
        echo json_encode($res);
      }else{
        //sin resultados
        $res = ["status"=>"ok","data"=>"NoData"];
        echo json_encode($res);
      }
    } catch (\Throwable $th) {
      //error al consultar los movimientos de caja
      $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al consultar los movimientos de caja: ".$th];
      echo json_encode($res);
    }


  }
}else{
  //sin sesion
}

?>