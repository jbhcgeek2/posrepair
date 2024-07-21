<?php 
//articulos
function guardarProducto($nombreArti,$descArti,$estatus,$empresa,$categoria,
$img,$pUni,$pMayo,$mayoDes,$codigo,$proveedor,$chip){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }
  $fecha = date('Y-m-d');
  //antes de continuar, verificamos si el codigo de barras ya existe
  $sqlAux = "SELECT COUNT(*) AS codProds FROM ARTICULOS WHERE codigoProducto = '$codigo'";
  try {
    $queryAux = mysqli_query($conexion, $sqlAux);
    $fetchAux = mysqli_fetch_assoc($queryAux);
    if($fetchAux['codProds'] == 0){
      //no existe el codigo, podemos continuar
      $sql = "INSERT INTO ARTICULOS (nombreArticulo,descripcionArticulo,estatusArticulo,
      empresaID,categoriaID,fechaAlta,imgArticulo,precioUnitario,precioMayoreo,mayoreoDesde,
      codigoProducto,proveedorID,esChip) VALUES ('$nombreArti','$descArti','$estatus',
      '$empresa','$categoria','$fecha','$img','$pUni','$pMayo','$mayoDes','$codigo','$proveedor','$chip')";
      try {
        $query = mysqli_query($conexion, $sql);
        $idArticulo = mysqli_insert_id($conexion);
        $res = ["status"=>"ok","mensaje"=>"operationSuccess","dato"=>$idArticulo];
        return json_encode($res);
      } catch (Throwable $th) {
        //no fue posible guardar el producto
        $res = ["status"=>"error","mensaje"=>"Ocurrio un error al procesar el producto: ".$th];
        return json_encode($res);
      }
    }else{
      //el cosigo indicado ya existe, le decimos que no pueden exisitr codigos iguales
      $res = ["status"=>"error","mensaje"=>"El codigo de barras ya se encuentra registrado"];
      return json_encode($res);
    }
  } catch (\Throwable $th) {
    //ocurrio un error al validar el codigo de barras
    $res = ["status"=>"error","mensaje"=>"Ocurrio un error al validar el codigo de barras: ".$th];
    return json_encode($res);
  }
  

}//fin funcion guardarProducto

function guardarArticuloSuc($existencia,$sucursal,$articulo){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $sql = "INSERT INTO ARTICULOSUCURSAL (existenciaSucursal,sucursalID,articuloID) 
  VALUES ('$existencia','$sucursal','$articulo')";
  try {
    $query = mysqli_query($conexion, $sql);
    //se inserto el articulosuicursal
    $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
    return json_encode($res);
  } catch (Throwable $th) {
    //ocurrio un error al insertar el articuloSucursal
    $res = ["status"=>"error","mensaje"=>"Ocurrio un error asignar las cantidades del articulo: ".$th];
    return json_encode($res);
  }
}

function getProductos($empresa){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  // $sql = "SELECT * FROM ARTICULOS WHERE empresaID = '$empresa'";
  $sql = "SELECT *,(SELECT SUM(b.existenciaSucursal) FROM ARTICULOSUCURSAL b 
  WHERE b.articuloID = a.idArticulo ) AS cantSucur FROM ARTICULOS a 
  WHERE a.empresaID = '$empresa' ORDER BY nombreArticulo ASC";
  try {
    $query = mysqli_query($conexion,$sql);
    $data = [];
    $i = 0;
    while($fetch = mysqli_fetch_assoc($query)){
      $data[$i] = $fetch;
      $i++;
    }//fin del while

    $res = ["status"=>"ok","data"=>$data,"mensaje"=>"operationSuccess"];
    return json_encode($res);
  } catch (\Throwable $th) {
    //error en la consulta a los articulos
    $res = ["status"=>"error","mensaje"=>"Ocurrio un error al consultar la informacion: ".$th];
    return json_encode($res);
  }

}

function setCategoria($idEmpresa,$nombreCat,$estatusCat,$descripCat){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $sql = "INSERT INTO CATEGORIA (nombreCategoria,estatusCategoria,descripcionCategoria,
  empresaID) VALUES ('$nombreCat','$estatusCat','$descripCat','$idEmpresa')";
  try {
    $query = mysqli_query($conexion, $sql);
    //se inserto correctamente la categoria
    $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
    return json_encode($res);
  } catch (\Throwable $th) {
    //error al consultar la categoria
    $res = ["status"=>"error","mensaje"=>"Ocurrio un error al consultar la informacion: ".$th];
    return json_encode($res);
  }
}

function getInfoproducto($empresa,$producto){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $sql = "SELECT * FROM ARTICULOS WHERE idArticulo = '$producto' AND empresaID = '$empresa'";
  try {
    $query = mysqli_query($conexion, $sql);
    //se ejecuti correctamenet la informacion
    $fetch = mysqli_fetch_assoc($query);
    $res = ["status"=>"ok","data"=>$fetch];
    return json_encode($res);
  } catch (\Throwable $th) {
    //error al consultar la infroamcion
    $res = ["status"=>"error","mensaje"=>"Ocurrio un error al consultar el producto: ".$th];
    return json_encode($res);
  }

}

function actualizaProducto($datos){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $data = json_decode($datos);

  $nombre = $data->nombre;
  $descr = $data->descri;
  $estatus = $data->estatusProd;
  $pUnitario = $data->pUnitario;
  $pMayo = $data->pMayo;
  $mayoreo = $data->mayoreoDesde;
  $cat = $data->categoria;
  $idArti = $data->producto;
  $codig = $data->codigo;
  $proveedor = $data->proveedor;

  if(!empty($data->imagen)){
    //si existe ruta de imagen actualizamos
    $imagen = $data->imagen;
    $sql = "UPDATE ARTICULOS SET nombreArticulo = '$nombre', descripcionArticulo='$descr',
    estatusArticulo = '$estatus', precioUnitario = '$pUnitario', precioMayoreo = '$pMayo', 
    mayoreoDesde = '$mayoreo', categoriaID = '$cat', imgArticulo = '$imagen',
    codigoProducto = '$codig', proveedorID = '$proveedor' WHERE idArticulo = '$idArti'";
  }else{
    //si no existe ruta de imagen no hacemos nada
    $sql = "UPDATE ARTICULOS SET nombreArticulo = '$nombre', descripcionArticulo='$descr',
    estatusArticulo = '$estatus', precioUnitario = '$pUnitario', precioMayoreo = '$pMayo', 
    mayoreoDesde = '$mayoreo', categoriaID = '$cat', codigoProducto = '$codig',
    proveedorID = '$proveedor' WHERE idArticulo = '$idArti'";
  }


  // $sql = "UPDATE ARTICULOS SET nombreArticulo = '$nombre', descripcionArticulo='$descr',
  // estatusArticulo = '$estatus', precioUnitario = '$pUnitario', precioMayoreo = '$pMayo', 
  // mayoreoDesde = '$mayoreo', categoriaID = '$cat' WHERE idArticulo = '$idArti'";

  try {
    $query = mysqli_query($conexion, $sql);
    //se actualizao el producto
    $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
    return json_encode($res);
  } catch (Throwable $th) {
    //no fue posible actualizar el producto
    $res = ["status"=>"error","mensaje"=>"Ocurrio un error al actualizar el producto: ".$th];
    return json_encode($res);
  };

}

function getArtiSucursal($sucursal,$articulo){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $sql = "SELECT existenciaSucursal FROM ARTICULOSUCURSAL WHERE sucursalID = '$sucursal' AND articuloID = '$articulo'";
  try {
    $query = mysqli_query($conexion, $sql);
    //nueva seccion, si da error, quitarla
    if(mysqli_num_rows($query) > 0){
      $fetch = mysqli_fetch_assoc($query);
      $cantidad = $fetch['existenciaSucursal'];
      $res = ["status"=>"ok","mensaje"=>"operationSuccess","data"=>$cantidad];
      return json_encode($res);
    }else{
      $res = ["status"=>"ok","mensaje"=>"noData"];
      return json_encode($res);
    }
    //fin de seccion agregada
    
  } catch (Throwable $th) {
    //error al consultar el dato
    $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al consultar: ".$th];
    return json_encode($res);
  }
}

function getProductosEmpresa($empresa){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $sql = "SELECT * FROM ARTICULOS WHERE empresaID = '$empresa'";
  try {
    $query = mysqli_query($conexion, $sql);
    $i = 0;
    $data = [];
    if(mysqli_num_rows($query) > 0){
      while($fetch = mysqli_fetch_assoc($query)){
        $data[$i] = $fetch;
        $i++;
      }//fin del while
      $res = ["status"=>"ok","mensaje"=>"operationSuccess","data"=>$data];
      return json_encode($res);
    }else{
      //no se teiene productos capturados
      $res = ["status"=>"ok","mensaje"=>"noData"];
      return json_encode($res);
    }
  } catch (\Throwable $th) {
    //throw $th;
    $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al consultar: ".$th];
    return json_encode($res);
  }
}

function setCantidad($cantidad,$articulo,$sucursal){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }
  
  $sql = "UPDATE ARTICULOSUCURSAL SET existenciaSucursal = '$cantidad' 
  WHERE sucursalID = '$sucursal' AND articuloID = '$articulo'";
  try {
    $query = mysqli_query($conexion, $sql);
    //entendemos que se guardo la nueva cantidad
    $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
    return json_encode($res);
  } catch (\Throwable $th) {
    //ocurrio un error
    $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al actualizar las cantidades: ".$th];
    return json_encode($res);
  }
}

function procesaMovsProd($cambia,$nuevoPre,$precioCompra,$idProd,$idEmpresa){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }
  //esta funcion procesara la entra de un producto y actualizara
  //el precio de compra si excede el precio anterior
  $sql = "SELECT * FROM ARTICULOS WHERE idArticulo = '$idProd' AND empresaID = '$idEmpresa'";
  try {
    $query = mysqli_query($conexion, $sql);
    if(mysqli_num_rows($query) == 1){
      $fetch = mysqli_fetch_assoc($query);
      $precioCompraActual = $fetch['precioCompra'];
      if($precioCompraActual < $precioCompra){
        //en este caso, el precio de compra actual es menor al nuevo, por lo tanto
        //tendremos que actualizar a este nuevo valor
        $concat = ", precioCompra = '$precioCompra'";
      }else{
        //si no lo supera, no ponemos nada
        $concat = "";
      }
    }else{
      //producto no localizado
    }
  } catch (\Throwable $th) {
    //throw $th;
  }
  
  if($cambia == "si"){
    //aqui cambiamos el precio de compra\
    $sql2 = "UPDATE ARTICULOS SET precioUnitario = '$nuevoPre' $concat WHERE idArticulo = '$idProd'
    AND empresaID = '$idEmpresa'";
  }else{
    //no se cambia el precio de compra, verificamos si se cambia el precio
    if($concat != ""){
      $sql2 = "UPDATE ARTICULOS SET precioCompra = '$precioCompra' WHERE idArticulo = '$idProd'
      AND empresaID = '$idEmpresa'";
    }else{
      //si tampoco entra no hacemos nada
    }
  }

  if($sql2 != ""){
    try {
      $query2 = mysqli_query($conexion,$sql2);
      $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
      return json_encode($res);
    } catch (\Throwable $th) {
      //error al actualizar la infromacion
      $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al actualizar la informacion del producto: ".$th];
      return json_encode($res);
    }
    
  }else{
    //literal no se hizo nada xD
    $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
    return json_encode($res);
  }

}

function getNameProd($idProd,$empresa){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }

  $sql = "SELECT nombreArticulo FROM ARTICULOS WHERE idArticulo = '$idProd' AND empresaID = '$empresa'";
  try {
    $query = mysqli_query($conexion, $sql);
    if(mysqli_num_rows($query) == 1){
      //si se encontro
      $fetch = mysqli_fetch_assoc($query);
      $nombreArti = $fetch['nombreArticulo'];
      $res = ['status'=>'ok','data'=>$nombreArti];
      return json_encode($res);
    }else{
      //producto no localizado
      $res = ['status'=>'error','mensaje'=>'noData'];
      return json_encode($res);
    }
  } catch (\Throwable $th) {
    $res = ['status'=>'error','mensaje'=>'Ocurrio un error al consultar el articulo'];
    return json_encode($res);
  }
}

function genCodigo($idEmpresa){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }
  //en caso de que el usuario no indique un codigo de barras existente
  //le generaresmo uno automaticamente

  //el codigo estara compuesto de la siguiente informacion
  // $codigo = $empresa $numArti
  //$codigo = 00003-00000023
  //$codigo = 0000300000023

  //consultamos el numero de articulos y sumamos 1
  $sql = "SELECT COUNT(*) AS numArti FROM ARTICULOS WHERE empresaID = '$idEmpresa'";
  try {
    $query = mysqli_query($conexion, $sql);
    $fetch = mysqli_fetch_assoc($query);

    $num = $fetch['numArti']+1;
    $numPad = str_pad($num, 8, '0', STR_PAD_LEFT);
    $empPad = str_pad($idEmpresa, 5, '0', STR_PAD_LEFT);

    $codigo = $empPad.$numPad;
    //antes de asignarlo, verificamos si no esta regisrtrado
    $sql2 = "SELECT COUNT(*) AS numArti FROM ARTICULOS WHERE empresaID = '$idEmpresa' AND 
    codigoProducto = '$codigo'";
    try {
      $query2 = mysqli_query($conexion, $sql2);
      $fetch2 = mysqli_fetch_assoc($query2);
      if($fetch2['numArti'] == 0){
        $res = ['status'=>'ok','data'=>$codigo];
        return json_encode($res);
      }else{
        $numPad = str_pad($num, 8, '0', STR_PAD_LEFT);
        $empPad = str_pad($idEmpresa, 4, '0', STR_PAD_LEFT);
        $codigo = "1".$empPad.$numPad;
        
        $res = ['status'=>'ok','data'=>$codigo];
        return json_encode($res);
      }
    } catch (\Throwable $th) {
      //throw $th;
    }
  } catch (\Throwable $th) {
    //throw $th;
    $res = ['status'=>'error','mensaje'=>'Codigo no procesado'];
    return json_encode($res);
  }

}

function genCodigoUpdate($idEmpresa,$idProducto){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }
  //en caso de que el usuario no indique un codigo de barras existente
  //le generaresmo uno automaticamente

  //el codigo estara compuesto de la siguiente informacion
  // $codigo = $empresa $numArti
  //$codigo = 00003-00000023
  //$codigo = 0000300000023

  //consultamos el numero de articulos y sumamos 1
  $sql = "SELECT COUNT(*) AS numArti FROM ARTICULOS WHERE empresaID = '$idEmpresa' 
  AND idArticulo < $idProducto";
  try {
    $query = mysqli_query($conexion, $sql);
    $fetch = mysqli_fetch_assoc($query);

    $num = $fetch['numArti'];
    $num = $num + 1;
    $numPad = str_pad($num, 8, '0', STR_PAD_LEFT);
    $empPad = str_pad($idEmpresa, 5, '0', STR_PAD_LEFT);

    $codigo = $empPad.$numPad;
    //antes de asignarlo, verificamos si no esta regisrtrado
    $sql2 = "SELECT COUNT(*) AS numArti FROM ARTICULOS WHERE empresaID = '$idEmpresa' AND 
    codigoProducto = '$codigo'";
    try {
      $query2 = mysqli_query($conexion, $sql2);
      $fetch2 = mysqli_fetch_assoc($query2);
      if($fetch2['numArti'] == 0){
        $res = ['status'=>'ok','data'=>$codigo];
        return json_encode($res);
      }else{
        $numPad = str_pad($num, 8, '0', STR_PAD_LEFT);
        $empPad = str_pad($idEmpresa, 4, '0', STR_PAD_LEFT);
        $codigo = "1".$empPad.$numPad;
        
        $res = ['status'=>'ok','data'=>$codigo];
        return json_encode($res);
      }
    } catch (\Throwable $th) {
      //throw $th;
    }
  } catch (\Throwable $th) {
    //throw $th;
    $res = ['status'=>'error','mensaje'=>'Codigo no procesado'];
    return json_encode($res);
  }

}

function traspaso($producto,$sucOrigi,$sucDesti,$cantidad,$comproTras,$fechaTras,$tipoComp,$idEmprersa,$usuario){
  require('conexion.php');
  $res = [];
  if(!$conexion){
    require('../conexion.php');
    if(!$conexion){
      require('../includes/conexion.php');
    }
  }
  //seccion para hacer que se realicen los traspasos de sucursal
  
  $horaTras = date('H:i:s');

  //verificamos la existencia del producto
  //en la sucursal origen
  $cantidadSuc = getArtiSucursal($sucOrigi,$producto);
  $cantidadSuc = json_decode($cantidadSuc);
  if($cantidadSuc->status == "ok"){
    //verificamos si la cantidad a traspasar es igual o superior a lo que se desea
    if($cantidadSuc->data > 0){
      if($cantidadSuc->data >= $cantidad){
        $cantidadOrigen = $cantidadSuc->data;
        $infoArti = getInfoproducto($idEmprersa,$producto);
        $infoArti = json_decode($infoArti);
        if($infoArti->status == "ok"){
          $precioCompra = $infoArti->data->precioCompra;
          $montoMov = $precioCompra * $cantidad;
          //si se cuenta con stok, asi que procesamos el movimiento
          //primero insertamos en el INGRESO
          $sql = "INSERT INTO INGRESO (numComprobante,tipoComprobante,fechaIngreso,horaIngreso,
          totalIngreso,totArticulos,empresaID) VALUES ('$comproTras','$tipoComp','$fechaTras',
          '$horaTras','$montoMov','$cantidad','$idEmprersa')";
          try {
            $query = mysqli_query($conexion, $sql);
            $idMovTras = mysqli_insert_id($conexion);
            //ahora insertamos el detalle movimiento, primero la salida
            $sql2 = "INSERT INTO DETALLEINGRESO (cantidad,precioCompra,ingresoID,sucursalID,fechaMov,usuarioMov,tipoMov,prodMov) 
            VALUES ('$cantidad','$precioCompra','$idMovTras','$sucDesti',
            '$fechaTras','$usuario','Salida','$producto')";
            $sql22 = "INSERT INTO DETALLEINGRESO (cantidad,precioCompra,ingresoID,sucursalID,fechaMov,usuarioMov,tipoMov,prodMov) 
            VALUES ('$cantidad','$precioCompra','$idMovTras','$sucOrigi',
            '$fechaTras','$usuario','Entrada','$producto')";
            try {
              $query2 = mysqli_query($conexion, $sql2);
              $query22 = mysqli_query($conexion, $sql22);
              //ahora afectamos las cantidades del inventario
              $nuevaCantidad = $cantidadOrigen - $cantidad;
              
              $cantNuevaSuc = getArtiSucursal($sucDesti,$producto);
              $cantNuevaSuc = json_decode($cantNuevaSuc);
              if($cantNuevaSuc->status == "ok"){
                if($cantNuevaSuc->mensaje == "noData"){
                  //el producto no existe, se insertara
                  $nuevaCantidad2 = $cantidad;
                  $sql3 = "UPDATE ARTICULOSUCURSAL SET existenciaSucursal = '$nuevaCantidad' 
                  WHERE sucursalID = '$sucOrigi' AND articuloID = '$producto'";
                  $sql33 = "INSERT INTO ARTICULOSUCURSAL (existenciaSucursal,sucursalID,articuloID) 
                  VALUES ('$nuevaCantidad2','$sucDesti','$producto')";
                }else{
                  //el producto existe, lo actualizamos
                  $cantSucDes = $cantNuevaSuc->data;
                  $nuevaCantidad2 = $cantSucDes + $cantidad;

                  $sql3 = "UPDATE ARTICULOSUCURSAL SET existenciaSucursal = '$nuevaCantidad' 
                  WHERE sucursalID = '$sucOrigi' AND articuloID = '$producto'";
                  $sql33 = "UPDATE ARTICULOSUCURSAL SET existenciaSucursal = '$nuevaCantidad2' WHERE 
                  sucursalID = '$sucDesti' AND articuloID = '$producto'";
                }

                try {
                  $query3 = mysqli_query($conexion, $sql3);
                  $query33 = mysqli_query($conexion, $sql33);
                  //hasta este punto se puede decir que ya esta procesado toda la info
                  $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
                  return json_encode($res);
                } catch (\Throwable $th) {
                  //error al actualizar las cantidades
                  $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al actualizar las cantidades, reporta a soporte tecnico. ".$th];
                  return json_encode($res);
                }
              }else{
                //error al consultar la cantidad
                $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al consultar las cantidades en inventario. "];
                return json_encode($res);
              }
              
            } catch (\Throwable $th) {
              $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al registrar la transaccion. ".$th];
              return json_encode($res);
            }

          } catch (Throwable $th) {
            $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al insertar el detalle de movimiento. ".$th];
            return json_encode($res);
          }
        }else{
          //error al consultar la informacion del estatus
          $res = ["status"=>"error","mensaje"=>"Ha ocurrido un error al consultar la informacion del articulo. ".$th];
          return json_encode($res);
        }
        
        // $query = mysqli_query($conexion, $sql);
      }else{
        //se quiere traspasar mas de lo que se cuenta en stok
        $cantSuc = $cantidadSuc->data;
        $res = ["status"=>"error","mensaje"=>"Cuidado, actualmente cuentas con ".$cantSuc." articulos en la sucursal origen."];
        return json_encode($res);  
      }
    }else{
      //sin productos disponibles en la sucursal origen
      $res = ["status"=>"error","mensaje"=>"No se cuenta con stock en la sucursal origen."];
      return json_encode($res);
    }
  }else{
    //error en la consulta del articulo
    $res = ["status"=>"error","mensaje"=>"Articulo no localizado en la sucursal origen"];
    return json_encode($res);
  }
}
// precioUnitario = 17
// precioCOmpra = 12.20
// existencia = 150
?>