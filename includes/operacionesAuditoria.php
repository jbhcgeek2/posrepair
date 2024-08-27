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

  if($rolUsuario == "Administrador"){
    if(!empty($_POST['autorization'])){
      //tiene clace de autorizacion
      $claveEmpresa = datoEmpresaSesion($usuario,"claveAudi");
      $claveEmpresa = json_decode($claveEmpresa);
      $claveAutori = $claveEmpresa->dato;
      echo $claveAutori;
      $claveInput = $_POST['autorization'];
      //consultamos la clave de la empresa
      $fecha = date('Y-m-d');
      $hora = date('H:i:s');
      if($claveInput == $claveAutori){
        //se autoriza la auditoria, generamos un nuevo registro
        $sqlX = "INSERT INTO AUDITORIAS (fechaInicio,
        usuarioInicia,empresaID,horaInicio) VALUES 
        ('$fecha','$usuario','$idEmpresaSesion','$hora')";
        try {
          $queryX = mysqli_query($conexion, $sqlX);
          $idAuditoria = mysqli_insert_id($conexion);
          //primero consultaremos los articulos y las existencias actuales
          //y los amacenaremos en una tabla
          $sql = "SELECT * FROM ARTICULOS WHERE empresaID = '$idEmpresaSesion' AND 
          estatusArticulo = '1'";
          //consultamos las sucursales de la empresa, para ver las existencias
          $sql2 = "SELECT * FROM SUCURSALES WHERE empresaSucID = '$idEmpresaSesion' AND
          estatusSuc = '1'";
          try {
            $query = mysqli_query($conexion, $sql);
            $query2 = mysqli_query($conexion, $sql2);
            $sucursales = "";
            while($fetch2 = mysqli_fetch_assoc($query2)){
              $idSuc = $fetch2['idSucursal'];
              if($sucursales == ""){
                $sucursales = $idSuc;
              }else{
                $sucursales .= ",".$idSuc;
              }
            }//fin del while sucursales
            //a continuacion hacemos el while de productos
            while($fetch = mysqli_fetch_assoc($query)){
              //consultamos la existencia en las sucursales
              $idArticulo = $fetch['idArticulo'];
              $existenciaArticulo = "";
              $sql3 = "SELECT * FROM ARTICULOSUCURSAL WHERE articuloID = '$idArticulo' 
              AND sucursalID IN ($sucursales)";
              try {
                $query3 = mysqli_query($conexion,$sql3);
                while($fetch3 = mysqli_fetch_assoc($query3)){
                  $existen = $fetch3['existenciaSucursal'];
                  $idSucArti = $fetch3['sucursalID'];

                  $datoExiste = $idSucArti."=".$existen;
                  if($existenciaArticulo == ""){
                    $existenciaArticulo = $datoExiste;
                  }else{
                    $existenciaArticulo .= "|".$datoExiste;
                  }
                }//fin del while articulo sucursal

                $nombreArti = $fetch['nombreArticulo'];
                $proveedor = $fetch['proveedorID'];
                $chip = $fetch['esChip'];
                $categoria = $fetch['categoriaID'];
                //ahora insertamos la informacion del articulo en la nueva tabla
                $sql4 = "INSERT INTO ARTICULOAUDITORIA (nombreArticuloAudi,
                idArticuloAudi,existenciasAudi,proveedorAudi,empresaID,auditoriaID,
                esChipAudi,categoriaArtiAudi) VALUES ('$nombreArti','$idArticulo',
                '$existenciaArticulo','$proveedor','$idEmpresaSesion','$idAuditoria',
                '$chip','$categoria')";
                try {
                  $query4 = mysqli_query($conexion, $sql4);
                } catch (\Throwable $th) {
                  //no se inserto el registro de auditoria de objetoi
                }
              } catch (\Throwable $th) {
                $datoExiste = "error";
              }

            }//fin del while articulos
            //si llega hasta aqui es por que se termino de registrar todos
            $res = ['status'=>'ok','mensaje'=>'operationComplete'];
            echo json_encode($res);

          } catch (\Throwable $th) {
            //error al consultar los articulos
            $res = ['status'=>'error','mensaje'=>'Error al consultar los articulos: '.$th];
            echo json_encode($res);
          }
        } catch (\Throwable $th) {
          //no se pudo generar la auditoria
          $res = ['status'=>'error','mensaje'=>'No se pudo generar la auditoria'];
          echo json_encode($res);
        }
      }else{
        $res = ['status'=>'error','mensaje'=>'Clave de autorizacion incorrecta.'];
        echo json_encode($res);
      }
    }elseif(!empty($_POST['codigoEscaneo'])){
      //seccion para validar objetos
      $codigo = $_POST['codigoEscaneo'];
      $idSucuCod = $_POST['sucurCodigo'];
      $idAudi = $_POST['idAudiCod'];
      $fecha = date('Y-m-d');
      $hora = date('H:i:s');

      //primero consultamos el codigo, si no existe es por que es un chip
      $sql = "SELECT * FROM ARTICULOS WHERE codigoProducto = '$codigo' AND 
      empresaID = '$idEmpresaSesion'";
      try {
        $query = mysqli_query($conexion, $sql);
        if(mysqli_num_rows($query) == 1){
          //es un articulo normal lo registramos
          $fetch = mysqli_fetch_assoc($query);
          $nombreArti = $fetch['nombreArticulo'];
          $idArti = $fetch['idArticulo'];
          $sql3 = "INSERT INTO DETALLEAUDITORIA (usuarioValida,
          fechaValida,horaValida,codigoEscanea,sucursalID,empresaID,
          auditoriaID,nombreArticulo,articuloID) VALUES ('$usuario',
          '$fecha','$hora','$codigo','$idSucuCod','$idEmpresaSesion',
          '$idAudi','$nombreArti','$idArti')";
          try {
            $query3 = mysqli_query($conexion, $sql3);
            //se inserto la auditoria
            $msn = "Articulo ".$nombreArti." Escaneado";
            $res = ['status'=>'ok','mensaje'=>$msn];
            echo json_encode($res);
          } catch (\Throwable $th) {
            //error al registrar la auditoria
            $res = ['status'=>'error','mensaje'=>'Error al registrar la auditoria. '.$th];
            echo json_encode($res);
          }
        }else{
          //puede ser un chip, consultamos los chips
          $sql2 = "SELECT * FROM DETALLECHIP a INNER JOIN ARTICULOS b ON 
          a.productoID = b.idArticulo WHERE a.codigoChip = '$codigo' AND 
          a.sucursalID = '$idSucuCod' AND a.empresaID = '$idEmpresaSesion'";
          try {
            $query2 = mysqli_query($conexion, $sql2);
            if(mysqli_num_rows($query2) == 1){
              //si existe el codigo, lo registramos
              $fetch2 = mysqli_fetch_assoc($query2);
              $nombreArti = $fetch2['nombreArticulo'];
              $idArti = $fetch2['idArticulo'];
              $sql4 = "INSERT INTO DETALLEAUDITORIA (usuarioValida,
              fechaValida,horaValida,codigoEscanea,sucursalID,empresaID,
              auditoriaID,nombreArticulo,articuloID) VALUES ('$usuario',
              '$fecha','$hora','$codigo','$idSucuCod','$idEmpresaSesion',
              '$idAudi','$nombreArti','$idArti')";
              try {
                $query4 = mysqli_query($conexion, $sql4);
                $msn = "Articulo ".$nombreArti." Escaneado";
                $res = ['status'=>'ok','mensaje'=>$msn];
                echo json_encode($res);
              } catch (\Throwable $th) {
                //error al insertar el articulo
                $res = ['status'=>'error','mensaje'=>'Error al registrar la auditoria. '.$th];
                echo json_encode($res);
              }
            }else{
              //no existe el codigo
              $res = ['status'=>'error','mensaje'=>'Articulo no detectado'];
              echo json_encode($res);
            }
          } catch (\Throwable $th) {
            //error al consultar el codigo chip
            $res = ['status'=>'error','mensaje'=>'Error al consultar chip. '.$th];
            echo json_encode($res);
          }
        }
      } catch (\Throwable $th) {
        //error al consultar el articulo principal
        $res = ['status'=>'error','mensaje'=>'Error al consultar el articulo'.$th];
        echo json_encode($res);
      }
    }else{
      $res = ['status'=>'error','mensaje'=>'Acceso Denegado'];
      echo json_encode($res);
    }
  }else{
    $res = ['status'=>'error','mensaje'=>'Acceso Denegado'];
    echo json_encode($res);
  }


}
?>