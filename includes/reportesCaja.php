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


  }elseif(!empty($_POST['fechaIniMerca'])){
    //seccion para buscar salidas y entrada por mercancia
    $fechaIniMerca = $_POST['fechaIniMerca'];
    $fechaFinMerca = $_POST['fechaFinMerca'];

    if($rolUsuario == "Administrador"){
      $sql = "SELECT a.idSucursal,a.nombreSuc,b.*,
      (SELECT c.nombreArticulo FROM ARTICULOS c WHERE c.idArticulo = b.prodMov) AS nombreProdMov 
      FROM SUCURSALES a  INNER JOIN DETALLEINGRESO b ON a.idSucursal = b.sucursalID WHERE 
      a.empresaSucID = '$idEmpresaSesion' AND (b.fechaMov BETWEEN '$fechaIniMerca' AND '$fechaFinMerca')";
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
      //error en la consulta de movimientos
      $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al consultar los movimientos de mercancia: ".$th];
      echo json_encode($res);
    }
  }elseif(!empty($_POST['fecIniUser'])){
    // seccion para realizar la busqueda de ventas por usuario y fechas

    $fechaIni = $_POST['fecIniUser'];
    $fechaFin = $_POST['fecFinUSer'];
    $userVenta = $_POST['repUserVent'];

    $sql = "SELECT * FROM VENTAS a INNER JOIN DETALLEVENTA b ON a.idVenta = b.ventaID 
    INNER JOIN ARTICULOS c ON b.articuloID = c.idArticulo INNER JOIN SUCURSALES d 
    ON b.sucursalID = d.idSucursal WHERE a.usuarioID = '$userVenta' AND
    a.fechaVenta BETWEEN '$fechaIni' AND '$fechaFin'";
    try {
      $query = mysqli_query($conexion, $sql);
      if(mysqli_num_rows($query) > 0){
        $data = [];
        $x = 0;
        while($fetch = mysqli_fetch_assoc($query)){
          $data[$x] = $fetch;
          $x++;
        }
        $res = ['status'=>'ok','data'=>$data,'mensaje'=>'operationSuccess'];
        echo json_encode($res);
      }else{
        //sin datos
        $res = ['status'=>'ok','data'=>'noData','mensaje'=>'noData'];
        echo json_encode($res);
      }
    } catch (\Throwable $th) {
      //throw $th;
      $res = ['status'=>'error','mensaje'=>'Ocurrio un error al consultar el reporte: '.$th];
      echo json_encode($res);
    }
  }
}else{
  //sin sesion
}

?>