<?php 
session_start();
//mod producto
if(!empty($_SESSION['usuarioPOS'])){
  //insertamos los archivos que necesitamos
  include("articulos.php");
  include("usuarios.php");
  include("documentos.php");
  include("conexion.php");


  if(!empty($_POST['nombreArticulo'])){
    //realizamos la actualizacion del producto
    $campos = ['dataProd','nombreArticulo','precioMenudeo','estatus','categoria','descripcion'];
    //verificamos los campos importantes
    $usuario = $_SESSION['usuarioPOS'];
    $mal = 0;
    for($x = 0; $x < count($campos); $x++){
      $valorCampo = $_POST[$campos[$x]];
      // echo $campos[$x]." = ".$valorCampo."--";
      if($valorCampo == "" || $valorCampo == " "){
        echo $campos[$x];
        $mal = $mal +1;
      }
    }//fin del for

    if($mal == 0){
      $empresa = datoEmpresaSesion($usuario,"id");
      $empresa = json_decode($empresa);
      $idEmpresaSesion = $empresa->dato;
      // echo $idEmpresaSesion;

      $idProd = $_POST['dataProd'];
      $nombreProd = $_POST['nombreArticulo'];
      $precioMenu = $_POST['precioMenudeo'];
      $precioMayo = $_POST['precioMayoreo'];
      $mayoDesde = $_POST['mayoreoDesde'];
      $estatus = $_POST['estatus'];
      $categoria = $_POST['categoria'];
      $descripcion = $_POST['descripcion'];
      $codigo = $_POST['codigoProducto'];
      $proveedor = $_POST['proveedor'];

      if($codigo == "" || $codigo == " " || empty($codigo)){
        //no tiene informacion, le generamos un codigo
        $newCod = genCodigoUpdate($idEmpresaSesion,$idProd);
        $newCod = json_decode($newCod);
        if($newCod->status == "ok"){
          $codigo = $newCod->data;
        }else{
          $codigo = "error";
        }
      }

      //verificamos si se va a cambiar de imagen
      $imgArti = "";
      $statusImg = 0;
      if(!empty($_FILES['imagenProducto']['name'])){
        //actualizamos la imagen del producto
        //no existe la imagenm asi que generamos la ruta desde 0
        $ruta = '../assets/images/productos';
        $img = $_FILES['imagenProducto'];
        $subida = uploadDoc($img,'imagen','producto_',$ruta,$idEmpresaSesion);
        $imgSubida = json_decode($subida);
        // echo $imgSubida->mensaje;
        if($imgSubida->mensaje == "operationSuccess"){
          $imgArti = $imgSubida->dato;
        }else{
          $statusImg = $imgSubida->mensaje;
        }
        
      }

      if($statusImg == 0){
        $datos = ["nombre"=>$nombreProd,"descri"=>$descripcion,
        "estatusProd"=>$estatus,"pUnitario"=>$precioMenu,"pMayo"=>$precioMayo,
        "mayoreoDesde"=>$mayoDesde,"categoria"=>$categoria,"producto"=>$idProd,
        "imagen"=>$imgArti,"codigo"=>$codigo,"proveedor"=>$proveedor];
        $datos = json_encode($datos);
  
        $update = actualizaProducto($datos);
  
        $respuesta = json_decode($update);
  
        if($respuesta->status == "ok"){
          //se actualizo el producto
          $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
          echo json_encode($res);
        }else{
          //error al actualizar el producto
          $res = ["status"=>"error","mensaje"=>$statusImg];
          echo json_encode($res);
        }
      }else{
        //ocurrio un error al actualizar la imagen
        $res = ["status"=>"error","mensaje"=>$respuesta->mensaje];
        echo json_encode($res);
      }

      
    }else{
      //ocurrio error en los campos
       $res = ["status"=>"error","mensaje"=>"Verifica que los campos esten capturados correctamente."];
      echo json_encode($res);
    }
  }elseif(!empty($_POST['idSucursalCantDirect'])){
    //seccion para actualizar la cantidad del producto directa
    $idSucursal = $_POST['idSucursalCantDirect'];
    $cantidad = $_POST['cantidad'];
    $idArti = $_POST['articuloUpdateDirect'];

    $sql = "UPDATE ARTICULOSUCURSAL SET existenciaSucursal = '$cantidad' 
    WHERE sucursalID = '$idSucursal' AND articuloID = '$idArti'";
    try {
      $query = mysqli_query($conexion,$sql);
      //podemos dar por realizada la instruccion
      $res = ["status"=>"ok","mensaje"=>"operationSuccess"];
      echo json_encode($res);
    } catch (\Throwable $th) {
      //error en la actualizacion
      $res = ["status"=>"error","mensaje"=>$th];
      echo json_encode($res);

    }
  }elseif(!empty($_POST['codigoChip'])){
    //seccion para insertar chips
    $codigoChip = $_POST['codigoChip'];
    $sucursalChip = $_POST['sucursalChip'];
    $articuloID = $_POST['articuloID'];
    $fecha = date('Y-m-d');

    $usuario = $_SESSION['usuarioPOS'];
    $empresa = datoEmpresaSesion($usuario,"id");
    $empresa = json_decode($empresa);
    $idEmpresaSesion = $empresa->dato;

    //antes de insertarlo, verificamos que el codigo no este ya registrado
    $sql = "SELECT * FROM DETALLECHIP WHERE codigoChip = '$codigoChip' AND 
    empresaID = '$idEmpresaSesion'";
    try {
      $query = mysqli_query($conexion, $sql);
      if(mysqli_num_rows($query) == 0){
        //no esta registrado, podemos continuar
        //opero antes de registrar el chip, verificamos las excistencias del mismo 
        //actualizaremos mientras estemos registrando
        $sql2 = "SELECT *, (SELECT COUNT(*) FROM DETALLECHIP b WHERE b.sucursalID = a.sucursalID 
        AND b.productoID = a.articuloID AND b.estatusChip = 'Activo') AS chipsRegistrados 
        FROM ARTICULOSUCURSAL a WHERE a.articuloID = '$articuloID' AND a.sucursalID = '$sucursalChip'";
        try {
          $query2 = mysqli_query($conexion, $sql2);
          $fetch2 = mysqli_fetch_assoc($query2);
          $existenciaSuc = $fetch2['existenciaSucursal'];
          $existenciaReal = $fetch2['chipsRegistrados'];
          $idArtiSuc = $fetch2['idArtiSuc'];
          
          $nuevaExistencia = $existenciaReal + 1;

          //ahora procedemois a insertar el chip
          $sql3 = "INSERT INTO DETALLECHIP (sucursalID,empresaID,productoID,codigoChip,
          estatusChip,fechaEntrada,usuarioRegistra) VALUES ('$sucursalChip','$idEmpresaSesion','$articuloID',
          '$codigoChip','Activo','$fecha','$usuario')";
          try {
            $query3 = mysqli_query($conexion, $sql3);
            //se inserto el chip, ahora actualizamos 1
            $sql4 = "UPDATE ARTICULOSUCURSAL SET existenciaSucursal = '$nuevaExistencia' WHERE idArtiSuc = '$idArtiSuc'";
            try {
              $query4 = mysqli_query($conexion, $sql4);
              //se completo el proceso
              $res = ['status'=>'ok','mensaje'=>'operationComplete'];
              echo json_encode($res);
            } catch (\Throwable $th) {
              //error al actualizar
              $res = ['status'=>'error','mensaje'=>'Ocurrio un error al actualizar las cantidades de chips: '.$th];
              echo json_encode($res);
            }
          } catch (\Throwable $th) {
            $res = ['status'=>'error','mensaje'=>'Ocurrio un error al insertar el chip: '.$th];
            echo json_encode($res);
          }
        } catch (\Throwable $th) {
          //error al consultar la existencia real de chips
        }
      }else{
        //ya esta registrado el chip, mandamos error
        $res = ['status'=>'error','mensaje'=>'El codigo ya esta registrado en el sistema'];
        echo json_encode($res);
      }
    } catch (\Throwable $th) {
      //error de consulta a la base de datos
      $res = ['status'=>'error','mensaje'=>'Error al consultar la existencia del chip'];
      echo json_encode($res);
    }

    

  }elseif(!empty($_POST['codigoDirecto'])){
    //Seccion para realizar traspasos directos
    $codigo = $_POST['codigoDirecto'];
    $origen = $_POST['sucOrigenDirecto'];
    $destino = $_POST['sucDestinoDirecto'];
    $fecha = $_POST['fechaDirecto'];
    $hora = date('H:m:i');

    $usuario = $_SESSION['usuarioPOS'];
    $empresa = datoEmpresaSesion($usuario,"id");
    $empresa = json_decode($empresa);
    $idEmpresaSesion = $empresa->dato;

    $comprobacion = date('ymd');
    

    //consultamos el codigo
    $sql = "SELECT * FROM ARTICULOS a INNER JOIN ARTICULOSUCURSAL b ON a.idArticulo = b.articuloID WHERE 
    b.sucursalID = '$origen' AND a.empresaID = $idEmpresaSesion AND a.codigoProducto = '$codigo'";
    try {
      $query = mysqli_query($conexion, $sql);
      if(mysqli_num_rows($query) == 1){
        $fetch = mysqli_fetch_assoc($query);
        //verificamos si es chip para indicarle que escane el codigo correcto
        if($fetch['esChip'] == 1){
          //metodo no permitido aqui
          $res = ['status'=>'error','mensaje'=>'Debe escanear el codigo del chip o imei del equipo'];
          echo json_encode($res);
        }else{

        }
      }else{
        //no se localizo el producto, verificamos si es un chip
        //$sql2 = "SELECT * FROM DETALLECHIP a INNER JOIN ARTICULOS b ON a.productoID = b.idArticulo 
        //WHERE a.codigoChip = '$codigo' AND b.empresaID = '$idEmpresaSesion'";

        $sql2 = "SELECT *,(SELECT c.existenciaSucursal FROM ARTICULOSUCURSAL c WHERE 
        c.articuloID = a.productoID AND c.sucursalID = $origen) AS existenciaOrigen, 
        (SELECT c.existenciaSucursal FROM ARTICULOSUCURSAL c WHERE c.articuloID = a.productoID AND 
        c.sucursalID = '$destino') AS existenciaDestino FROM DETALLECHIP a INNER JOIN ARTICULOS b 
        ON a.productoID = b.idArticulo WHERE a.codigoChip = '$codigo' AND b.empresaID = '$idEmpresaSesion'";
        try {
          $query2 = mysqli_query($conexion, $sql2);
          if(mysqli_num_rows($query2) == 1){
            //verificamos que no este marcado como vendido
            $fetch2 = mysqli_fetch_assoc($query2);
            if($fetch2['estatusChip'] == 'Activo'){
              //procesamos el movimiento
              $montoMov = $fetch2['precioUnitario'];
              $precioCompra = $fetch2['precioCompra'];
              $producto = $fetch2['idArticulo'];

              $cantidad = 1;
              //primero insertamos el movimiento en la tabla INGRESO
              $sql3 = "INSERT INTO INGRESO (numComprobante,tipoComprobante,fechaIngreso,horaIngreso,
              totalIngreso,totArticulos,empresaID) VALUES ('$comprobacion','Ticket','$fecha',
              '$hora','$montoMov','$cantidad','$idEmpresaSesion')";
              try {
                $query3 = mysqli_query($conexion, $sql3);
                //ahora insertamos la salida de la mercancia
                $idMovimiento = mysqli_insert_id($conexion);

                $sql4 = "INSERT INTO DETALLEINGRESO (cantidad,precioCompra,ingresoID,sucursalID,fechaMov,usuarioMov,tipoMov,prodMov) 
                VALUES ('$cantidad','$precioCompra','$idMovimiento','$origen',
                '$fechaTras','$usuario','Salida','$producto')";
                $sql5 = "INSERT INTO DETALLEINGRESO (cantidad,precioCompra,ingresoID,sucursalID,fechaMov,usuarioMov,tipoMov,prodMov) 
                VALUES ('$cantidad','$precioCompra','$idMovimiento','$destino',
                '$fechaTras','$usuario','Entrada','$producto')";
                try {
                  $query4 = mysqli_query($conexion, $sql4);
                  $query5 = mysqli_query($conexion, $sql5);

                  //actualizamos las sucursales
                  $sumOrigen = $fetch2['existenciaOrigen']-1;
                  $sumDestino = $fetch2['existenciaDestino']+1;
                  $sql6 = "UPDATE ARTICULOSUCURSAL SET existenciaSucursal = '$sumOrigen' WHERE 
                  articuloID = '$producto' AND sucursalID = '$origen'";
                  $sql7 = "UPDATE ARTICULOSUCURSAL SET existenciaSucursal = '$sumDestino' WHERE 
                  articuloID = '$producto' AND sucursalID = '$destino'";
                  try {
                    $query6 = mysqli_query($conexion, $sql6);
                    $query7 = mysqli_query($conexion, $sql7);

                    //se completo el traspaso correctamente
                    $mensajeCorto = "Articulo: ".$fetch2['nombreArticulo']." Traspasado.";
                    $res = ["status"=>"ok","mensaje"=>$mensajeCorto];
                    echo json_encode($res);
                  } catch (\Throwable $th) {
                    //throw $th;
                    $res = ["status"=>"error","mensaje"=>"Error al actualizar cantidades: ".$th];
                    echo json_encode($res);
                  }
                } catch (\Throwable $th) {
                  //throw $th;
                  $res = ["status"=>"error","mensaje"=>"Error al insertar el detalle de movimiento: ".$th];
                  echo json_encode($res);
                }

              } catch (\Throwable $th) {
                //throw $th;
                $res = ["status"=>"error","mensaje"=>"Error al insertar el movimiento: ".$th];
                echo json_encode($res);
              }
            }else{
              //chip en baja o vendido
              $res = ["status"=>"error","mensaje"=>"Chip Dado de Baja o Vendido"];
              echo json_encode($res);
            }
          }else{
            //producto no definido correctamente
            $res = ["status"=>"error","mensaje"=>"Error al consultar el producto 2"];
            echo json_encode($res);
          }
        } catch (\Throwable $th) {
          $res = ["status"=>"error","mensaje"=>"Error al consultar el producto 1. ".$th];
          echo json_encode($res);
        }
      }
    } catch (\Throwable $th) {
      $res = ["status"=>"error","mensaje"=>"Error al consultar el codigo. ".$th];
      echo json_encode($res);
    }
  }
}

?>