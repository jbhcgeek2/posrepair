<?php 
session_start();
if(!empty($_SESSION['usuarioPOS'])){
  //insertamos los archivos que necesitamos
  include("articulos.php");
  include("usuarios.php");
  include("documentos.php");
  include("conexion.php");
  include("operacionesCaja.php");
  include("empresas.php");
  $usuario = $_SESSION['usuarioPOS'];

  $empresa = datoEmpresaSesion($usuario,"id");
  $idEmprersa = json_decode($empresa)->dato;
  $datosUsuario = getDataUser($usuario,$idEmprersa);
  $idSucursal = json_decode($datosUsuario)->sucursalID;
  $idUsuario = json_decode($datosUsuario)->idUsuario;
  //verificamos que el usuario contenga los permisos para realizarle operaciones
  $tipoRol = verTipoUsuario($usuario);
  $tipoUsuario = json_decode($tipoRol);
  $rolUsuario = "";
  // print_r($tipoUsuario);
  if($tipoUsuario->status == "ok"){
    $rolUsuario = $tipoUsuario->data;
  }else{
    $rolUsuario = "error";
  }

  //verificamos el metodo 
  
  if(!empty($_POST['getDataArti'])){
    //metodo para obtener la informacion de un articulo
    $idArticulo = $_POST['getDataArti'];
    
    $sql = "SELECT * FROM ARTICULOS a WHERE a.idArticulo = ? AND a.empresaID = ?";
    try {
      $query = mysqli_prepare($conexion, $sql);
      mysqli_stmt_bind_param($query,"ii",$idArticulo,$idEmprersa);
      mysqli_stmt_execute($query);
      $result = mysqli_stmt_get_result($query);
      if(mysqli_num_rows($result) > 0){
        $datos = [];
        $x =0;
        while($fetch = mysqli_fetch_assoc($result)){
          $datos[$x] = $fetch;
          $x++;
        }//fin del while
        $res = ['status'=>'ok','data'=>$datos];
        echo json_encode($res);
      }else{
        //no se encontro el articulo en la empresa
        $res = ['status'=>'error','mensaje'=>'No fue posible localizar el articulo.'];
        echo json_encode($res);
      }
      
    } catch (\Throwable $th) {
      //throw $th;
      $res = ['status'=>'error','mensaje'=>'Ocurrio un error al consultar los datos del articulo'];
      echo json_encode($res);
    }

  }elseif(!empty($_POST['canTras'])){
    //metodo para realizar el traspaso de un producto
    //verificamos si es administrador
    if($rolUsuario == "Administrador"){
      $cantidad = $_POST['canTras'];
      $idArticulo = $_POST['articuloTras'];
      $sucOrigen = $_POST['sucOriTras'];
      $sucDestino = $_POST['sucDesTras'];
      $fecha = date('Y-m-d');
      //verificamos si es un traspaso directo

      $traspaso = traspaso($idArticulo,$sucOrigen,$sucDestino,$cantidad,"0101",$fecha,"Ticket",$idEmprersa,$usuario);

      echo $traspaso;

    }else{
      $res = ['status'=>'error','mensaje'=>'Solo los administradors pueden realizar traspasos de sucursal'];
      echo json_encode($res);
    }
    

  }elseif(!empty($_POST['cantIngreso'])){
    //metodo para darle entrada a un articulo
    $idArticulo = $_POST['artiIngreso'];
    $sucIngreso = $_POST['sucIngresoArti'];
    $cantIngreso = $_POST['cantIngreso'];

    //antes de ingresar verificamos que exista el articulo
    mysqli_begin_transaction($conexion);
    $sql = "SELECT * FROM ARTICULOS WHERE idArticulo = ? AND empresaID = ?";
    try {
      $query = mysqli_prepare($conexion, $sql);
      mysqli_stmt_bind_param($query,"ii",$idArticulo,$idEmprersa);
      mysqli_stmt_execute($query);
      $result = mysqli_stmt_get_result($query);
      if(mysqli_num_rows($result) == 1){
        //consultamos el registro de articulo sucursal
        $fetch = mysqli_fetch_assoc($result);
        $preCompra = $fetch['precioCompra'];
        $sql2 = "SELECT * FROM ARTICULOSUCURSAL WHERE articuloID = ? AND sucursalID = ?";
        try {
          $query2 = mysqli_prepare($conexion, $sql2);
          mysqli_stmt_bind_param($query2,"ii",$idArticulo,$sucIngreso);
          mysqli_stmt_execute($query2);
          $result2 = mysqli_stmt_get_result($query2);
          if(mysqli_num_rows($result2) > 0){
            $fetch2 = mysqli_fetch_assoc($result2);
            $cantAct = $fetch2['existenciaSucursal'];
            $newCant = $cantAct+$cantIngreso;

            $sql3 = "UPDATE ARTICULOSUCURSAL SET existenciaSucursal = ? WHERE articuloID = ? AND sucursalID = ?";
            $query3 = mysqli_prepare($conexion, $sql3);
            mysqli_stmt_bind_param($query3,"iii",$newCant,$idArticulo,$sucIngreso);
            mysqli_stmt_execute($query3);
            //podemos dar por terminado el trabajo
          }else{
            //no se detecto la existencia, lo insertamos
            $sql4 = "INSERT INTO ARTICULOSUCURSAL (existenciaSucursal,sucursalID,articuloID) VALUES (?,?,?)";
            $query4 = mysqli_prepare($conexion, $sql4);
            mysqli_stmt_bind_param($query4,"iii",$cantIngreso,$sucIngreso,$idArticulo);
            mysqli_stmt_execute($query4); 
          }

          $procesaEntrada = setEntradaArti($preCompra,$cantIngreso,$sucIngreso,$idEmprersa,$idArticulo,$usuario);
          $resEntrada = json_decode($procesaEntrada);
          if($resEntrada->status == "ok"){
            //hacemos el commit
            mysqli_commit($conexion);
            $res =['status'=>'ok','mensaje'=>'operationComplete'];
            echo json_encode($res);
          }else{
            //hacemos rollback
            mysqli_rollback($conexion);
            $res =['status'=>'error','mensaje'=>'Ocurrio un error al procesar el ingreso.'];
            echo json_encode($res);
          }
        } catch (\Throwable $th) {
          //ocurrio un error al consultar articulosucursal
          $res =['status'=>'error','mensaje'=>'Ocurrio un error al consultar la existancia en la sucursal.'];
          echo json_encode($res);
        }
      }else{
        //no existe el articulo
        $res =['status'=>'error','mensaje'=>'No fue posible localizar el articulo, si crees que es un error, reportalo a soporte.'];
        echo json_encode($res);
      }

    } catch (\Throwable $th) {
      //throw $th;
      $res =['status'=>'error','mensaje'=>'Ocurrio un error al cosnultar el producto.'];
      echo json_encode($res);
    }

  }elseif(!empty($_POST['prodTraspasoChip'])){
    //seccion para realizar el traspaso de celulares chips
    //verificamos si es administrador
    if($rolUsuario == "Administrador"){
      $idProd = $_POST['prodTraspasoChip'];
      $sucDestino = $_POST['sucDestinoChip'];
      $codigoChip = $_POST['chipIngresoCodigo'];
      $fecha = date('Y-m-d');
      
      $sql = "SELECT * FROM ARTICULOS a INNER JOIN DETALLECHIP b ON a.idArticulo = b.productoID WHERE 
      a.idArticulo = ? AND a.empresaID = ? AND b.codigoChip = ?";

      try {
        $query = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($query,"iis",$idProd,$idEmprersa,$codigoChip);
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        if(mysqli_num_rows($result) == 1){
          //podemos hacer el traspaso
          $fetch = mysqli_fetch_assoc($result);
          //verificamos si la sucursal es diferente
          $idSucActual = $fetch['sucursalID'];
          if($idSucActual != $sucDestino){
            //procedemos a hacer elk traspaso
            // $traspaso = traspaso($idProd,$sucOrigen,$sucDestino,$cantidad,"0101",$fecha,"Ticket",$idEmprersa,$usuario);
            
            mysqli_begin_transaction($conexion);

            try {
              $comprobacion = date('ymd');
              $tipoCom = "Ticket";
              $hora = date('H:i:s');
              $precio = $fetch['precioUnitario'];
              $nArti = 1;

              $sql2 = "INSERT INTO INGRESO (numComprobante,tipoComprobante,fechaIngreso,horaIngreso,
              totalIngreso,totArticulos,empresaID) VALUES (?,?,?,?,?,?,?)";
              $query2 = mysqli_prepare($conexion, $sql2);
              mysqli_stmt_bind_param($query2,"ssssdii",$comprobacion,$tipoCom,$fecha,$hora,$precio,
              $nArti,$idEmprersa);
              mysqli_stmt_execute($query2);
              $idMovimiento = mysqli_insert_id($conexion);

              $cantidadMov = 1;
              $salida = "Salida";
              $entrada = "Entrada";
              
              $sql3 = "INSERT INTO DETALLEINGRESO (cantidad,precioCompra,ingresoID,sucursalID,fechaMov,usuarioMov,tipoMov,prodMov) 
              VALUES (?,?,?,?,?,?,?,?)";
              $query3 = mysqli_prepare($conexion, $sql3);
              mysqli_stmt_bind_param($query3,"idiisssi",$cantidadMov,$precio,$idMovimiento,$idSucActual,
              $fecha,$usuario,$salida,$idProd);
              mysqli_stmt_execute($query3);
              $sql4 = "INSERT INTO DETALLEINGRESO (cantidad,precioCompra,ingresoID,sucursalID,fechaMov,usuarioMov,tipoMov,prodMov) 
              VALUES (?,?,?,?,?,?,?,?)";
              $query4 = mysqli_prepare($conexion, $sql4);
              mysqli_stmt_bind_param($query4,"idiisssi",$cantidadMov,$precio,$idMovimiento,$sucDestino,
              $fecha,$usuario,$entrada,$idProd);
              mysqli_stmt_execute($query4);

              //hacemos la actualizacion
              $sql5 = "UPDATE DETALLECHIP SET sucursalID = ? WHERE codigoChip = ? AND empresaID = ?";
              $query5 = mysqli_prepare($conexion, $sql5);
              mysqli_stmt_bind_param($query5,"isi",$sucDestino,$codigoChip,$idEmprersa);
              mysqli_stmt_execute($query5);
              //podemos dar por terminado, cerramos el comit
              //ahora actualizamos las cantidades de las sucursales

              $sql6 = "SELECT * FROM SUCURSALES WHERE empresaSucID = ?";
              $query6 = mysqli_prepare($conexion, $sql6);
              mysqli_stmt_bind_param($query6,"i",$idEmprersa);
              mysqli_stmt_execute($query6);
              $res = mysqli_stmt_get_result($query6);
              $statChip = "Activo";
              while($fetch6 = mysqli_fetch_assoc($res)){
                $idSucAux = $fetch6['idSucursal'];

                $sql7 = "SELECT COUNT(*) AS numArtiDis FROM DETALLECHIP WHERE estatusChip = ? AND 
                sucursalID = ? AND productoID = ?";
                $query7 = mysqli_prepare($conexion, $sql7);
                mysqli_stmt_bind_param($query7,"sii",$statChip,$idSucAux,$idProd);
                mysqli_stmt_execute($query7);
                $res7 = mysqli_stmt_get_result($query7);

                $fetch7 = mysqli_fetch_assoc($res7);
                $numArti = $fetch7['numArtiDis'];

                $sql8 = "UPDATE ARTICULOSUCURSAL SET existenciaSucursal = ? WHERE articuloID = ? AND 
                sucursalID = ?";
                // $sql8 = "INSERT INTO ARTICULOSUCURSAL (existenciaSucursal, articuloID, sucursalID)
                // VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE existenciaSucursal = ?";
                $query8 = mysqli_prepare($conexion, $sql8);
                mysqli_stmt_bind_param($query8,"iii",$numArti,$idProd,$idSucAux);
                mysqli_stmt_execute($query8);

                //hasta aqui se debe de terminar


              }//fin del while

              mysqli_commit($conexion);


              $mensajeCorto = "Articulo: ".$fetch['nombreArticulo']." Traspasado - Codigo: ".$codigoChip;
              $res = ["status"=>"ok","mensaje"=>$mensajeCorto];
              echo json_encode($res);
            } catch (\Throwable $th) {
              mysqli_rollback($conexion);
              $res = ['status'=>'error','mensaje'=>'Ocurrio un error al procesar la peticion.'. $th];
              echo json_encode($res);
            }

          }else{
            //el articulo ya esta en la sucursal
            $res = ['status'=>'error','mensaje'=>'El articulo ya se encuetra en la sucursal indicada.'];
            echo json_encode($res);
          }

        }else{
          $res = ['status'=>'error','mensaje'=>'El articulo indicado no existe, favor de verificarlo.'];
          echo json_encode($res);
        }
      } catch (\Throwable $th) {
        $res = ['status'=>'error','mensaje'=>'Ocuirrio un error al consultar el articulo.'.$th];
        echo json_encode($res);
      }

      // $traspaso = traspaso($idArticulo,$sucOrigen,$sucDestino,$cantidad,"0101",$fecha,"Ticket",$idEmprersa,$usuario);
      // echo $traspaso;

    }else{
      $res = ['status'=>'error','mensaje'=>'Solo los administradors pueden realizar traspasos de sucursal'];
      echo json_encode($res);
    }
  }elseif(!empty($_POST['artisalidaProd'])){
    //seccion par salida de articulos
    $idArti = $_POST['artisalidaProd'];
    $sucSalida = $_POST['sucSalidaArti'];
    $cantidad = $_POST['cantSalidaArti'];

    //primero consultamos la existencia
    $sql = "SELECT * FROM ARTICULOSUCURSAL a INNER JOIN ARTICULOS b 
    ON a.articuloID = b.idArticulo WHERE a.sucursalID = ? AND a.articuloID = ?";
    try {
      mysqli_begin_transaction($conexion);

      $query = mysqli_prepare($conexion, $sql);
      mysqli_stmt_bind_param($query,"ii",$sucSalida,$idArti);
      mysqli_stmt_execute($query);
      $result = mysqli_stmt_get_result($query);
      if(mysqli_num_rows($result) == 1){
        //verificamos las cantidades
        $fetch = mysqli_fetch_assoc($result);
        $cantActual = $fetch['existenciaSucursal'];
        if($cantActual >= $cantidad){
          $nuevaCantidad = $cantActual - $cantidad;
          $precioCompra = $fetch['precioCompra'];
          $idProv = $fetch['proveedorID'];
          $montoUnitario = $fetch['precioUnitario'];

          $sql2 = "UPDATE ARTICULOSUCURSAL SET existenciaSucursal = ? WHERE sucursalID = ? AND articuloID = ?";
          $query2 = mysqli_prepare($conexion, $sql2);
          mysqli_stmt_bind_param($query2,"iii",$nuevaCantidad,$sucSalida,$idArti);
          mysqli_stmt_execute($query2);

          $comp = date('YmdHis');
          //procesamos el traspaso
          $traspaso = setSalidaArti($comp,"Ticket",$montoUnitario,$cantidad,$idProv,$precioCompra,$sucSalida,
          $idEmprersa,$usuario,$idArti);
          $traspaso = json_decode($traspaso);

          if($traspaso->status == 'Ok'){
            mysqli_commit($conexion);
            $res = ['status'=>'ok','mensaje'=>'operationComplete'];
            echo json_encode($res);
          }else{
            //error al procesar el traspaso
            mysqli_rollback($conexion);
            $res = ['status'=>'error','mensaje'=>$traspaso->mensaje];
            echo json_encode($res);
          }
        }else{
          //cantidad no disponible
          mysqli_rollback($conexion);
          $res = ['status'=>'error','mensaje'=>'No dispones de la cantidad necesaria para salida.'];
          echo json_encode($res);
        }
      }else{
        //ocurrio un error  al consultar el producto, no existe
        mysqli_rollback($conexion);
        $res = ['status'=>'error','mensaje'=>'El articulo indicado no existe en la sucursal indicada.'];
        echo json_encode($res);
      }
    } catch (\Throwable $th) {
      //throw $th;
      mysqli_rollback($conexion);
      $res = ['status'=>'error','mensaje'=>'Ocurrio un error al conultar el producto.'];
      echo json_encode($res);
    }
  }elseif(!empty($_POST['idProdChipSalida'])){
    //seccion par dar la salida de un chip
    $codigoChip = $_POST['codigoSalidaChip'];
    $idProd = $_POST['idProdChipSalida'];

    //consultamos la existencia del articulo
  
    mysqli_begin_transaction($conexion);
    $sql = "SELECT *,(SELECT COUNT(*) FROM DETALLECHIP c WHERE b.productoID = c.productoID AND 
    c.sucursalID = b.sucursalID AND c.estatusChip = 'Activo') AS ExistenciaActual 
    FROM ARTICULOS a INNER JOIN DETALLECHIP b ON a.idArticulo = b.productoID WHERE 
    b.productoID = ? AND b.codigoChip = ? AND b.empresaID = ? AND b.estatusChip = ?";
    try {
      $estatusVal = "Activo";
      $query = mysqli_prepare($conexion, $sql);
      mysqli_stmt_bind_param($query,"isis",$idProd,$codigoChip,$idEmprersa,$estatusVal);
      mysqli_stmt_execute($query);
      $res = mysqli_stmt_get_result($query);
      if(mysqli_num_rows($res) == 1){
        //primero procesamos la salida del articulo, si es positiva, procedemos a hacer el resto de procesos
        $fetch = mysqli_fetch_assoc($res);
        $idSucArti = $fetch['sucursalID'];
        $comprobante = date('YmdHis');
        $tipoCom = "Ticket";
        $precioCompra = $fetch['precioCompra'];
        $precio = $fetch['precioUnitario'];
        $nArti = 1;
        $idProv = $fetch['proveedorID'];
        $cantidadActual = $fetch['ExistenciaActual'];
        $setSalida = setSalidaArti($comprobante,$tipoCom,$precio,$nArti,$idProv,$precioCompra,
        $idSucArti,$idEmprersa,$usuario,$idProd);

        $setSalida = json_decode($setSalida);
        if($setSalida->status == "Ok"){
          //procedemos a descontar la salidas
          $sql2 = "UPDATE DETALLECHIP SET estatusChip = ? WHERE codigoChip = ? AND productoID = ? AND 
          empresaID = ?";
          $query2 = mysqli_prepare($conexion,$sql2);
          $nuevoEstatus = "Baja";
          mysqli_stmt_bind_param($query2,"ssii",$nuevoEstatus,$codigoChip,$idProd,$idEmprersa);
          mysqli_stmt_execute($query2);
          $nuevaCantidad = $cantidadActual - 1;

          $sql3 = "UPDATE ARTICULOSUCURSAL SET existenciaSucursal = ? WHERE sucursalID = ? AND articuloID = ?";
          $query3 = mysqli_prepare($conexion, $sql3);
          mysqli_stmt_bind_param($query3,"iii",$nuevaCantidad,$idSucArti,$idProd);
          mysqli_stmt_execute($query3);
          //si llego hasta aqui el proceso se completo exisosamente, podemos dar una respuesta
          mysqli_commit($conexion);
          $res = ["status"=>'ok',"mensaje"=>'OperationComplete'];
          echo json_encode($res);
        }else{
          //ocurrio un error al procesar la salida
          mysqli_rollback($conexion);
          $res = ["status"=>'error',"mensaje"=>$setSalida->mensaje];
          echo json_encode($res);
        }
      }else{
        //no se enconetraron resultados
        mysqli_rollback($conexion);
        $res = ["status"=>'error',"mensaje"=>'No fue posible localizar el articulo indicado.'];
        echo json_encode($res);
      }
    } catch (\Throwable $th) {
      //error al conuowlltar el detallechip
      mysqli_rollback($conexion);
      $res = ["status"=>'error',"mensaje"=>'Ocurrio un error al consultar el articulo.'];
      echo json_encode($res);
    }
  }
}else{
  //sin sesion
}
?>