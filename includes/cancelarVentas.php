<?php 
//  -- Tabla espejo de VENTAS
// CREATE TABLE `VENTASCANCELADAS` (
//   `idVentaCancelada` int(11) NOT NULL AUTO_INCREMENT,
//   `idVentaOriginal` int(11) NOT NULL,
//   `num_comprobante` varchar(45) NOT NULL,
//   `fechaVenta` date NOT NULL,
//   `horaVenta` time NOT NULL,
//   `totalVenta` decimal(11,2) NOT NULL,
//   `estatusVenta` int(11) NOT NULL,
//   `descuentoVenta` decimal(11,2) DEFAULT NULL,
//   `montoPago` decimal(11,2) NOT NULL,
//   `cambioPago` decimal(11,2) NOT NULL,
//   `tipoPago` varchar(25) NOT NULL,
//   `clienteID` int(11) NOT NULL,
//   `empresaID` int(11) NOT NULL,
//   `usuarioID` int(11) NOT NULL,
//   `usuarioVenta` int(11) DEFAULT NULL,
//   -- Auditoría de la cancelación
//   `fechaCancelacion` datetime NOT NULL,
//   `usuarioCancela` varchar(50) NOT NULL,
//   `motivoCancelacion` varchar(255) NOT NULL,
//   PRIMARY KEY (`idVentaCancelada`),
//   KEY `idx_venta_original` (`idVentaOriginal`),
//   KEY `idx_empresa_fecha` (`empresaID`, `fechaVenta`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

// -- Tabla espejo de DETALLEVENTA
// CREATE TABLE `DETALLEVENTASCANCELADAS` (
//   `idDetalleVentaCancelada` int(11) NOT NULL AUTO_INCREMENT,
//   `idVentaCancelada` int(11) NOT NULL,
//   `idDetalleVentaOriginal` int(11) NOT NULL,
//   `cantidadVenta` int(11) NOT NULL,
//   `precioUnitario` decimal(11,2) NOT NULL,
//   `subtotalVenta` decimal(11,2) DEFAULT NULL,
//   `descuento` decimal(11,2) DEFAULT NULL,
//   `usuarioVenta` varchar(50) NOT NULL,
//   `sucursalID` int(11) NOT NULL,
//   `articuloID` int(11) DEFAULT NULL,
//   `trabajoID` int(11) DEFAULT NULL,
//   `ventaID` int(11) DEFAULT NULL,
//   `chipID` int(11) DEFAULT NULL,
//   PRIMARY KEY (`idDetalleVentaCancelada`),
//   KEY `idx_venta_cancelada` (`idVentaCancelada`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 


  session_start();

  if(!empty($_SESSION['usuarioPOS'])){
    include("conexion.php");
    include("usuarios.php");
  
    $usuario = $_SESSION['usuarioPOS'];
  
    $empresa = datoEmpresaSesion($usuario,"id");
    $empresa = json_decode($empresa);
    $idEmpresaSesion = $empresa->dato;

    $dataUSer = getDataUser($usuario,$idEmpresaSesion);
		$dataUSer = json_decode($dataUSer);
    // print_r($dataUSer);
		$idSucursalN = $dataUSer->sucursalID;
		$idUsuarioN = $dataUSer->idUsuario;
    $contraBase = $dataUSer->passwordUser;

    $tipoRol = verTipoUsuario($usuario);
		$tipoUsuario = json_decode($tipoRol);
		$rolUsuario = "";
		if($tipoUsuario->status == "ok"){
			$rolUsuario = $tipoUsuario->data;
		}else{
			$rolUsuario = "error";
		}

    if($rolUsuario == "Administrador"){
      //verificamos el metodo enviado
      if(!empty($_POST['mVentaID'])){
        $idVentaCancela = $_POST['mVentaID'];
        $motivoCancela = $_POST['motivo'];
        $contraPass = $_POST['passCancelUser'];
        $hoy = date('Y-m-d');
        $hora = date('Y-m-d H:i:s');


        //validamos las contras
        if($contraPass == $contraBase){
          //si coinciden las contras, consultamos la venta
          mysqli_begin_transaction($conexion);
          $sqlC1 = "SELECT * FROM VENTAS WHERE idVenta = ? AND empresaID = ? AND fechaVenta = ? FOR UPDATE";
          $queryC1 = mysqli_prepare($conexion, $sqlC1);
          mysqli_stmt_bind_param($queryC1,"iis",$idVentaCancela,$idEmpresaSesion,$hoy);
          mysqli_stmt_execute($queryC1);
          $resC1 = mysqli_stmt_get_result($queryC1);
          if(mysqli_num_rows($resC1) == 1){
            //comenzamos la transaccion
            $fetchC1 = mysqli_fetch_assoc($resC1);
            //duplicamos la venta
            try {
              $sqlC2 = "INSERT INTO VENTASCANCELADAS (idVentaOriginal,num_comprobante,fechaVenta,
              horaVenta,totalVenta,estatusVenta,descuentoVenta,montoPago,cambioPago,tipoPago,
              clienteID,empresaID,usuarioID,usuarioVenta,fechaCancelacion,usuarioCancela,motivoCancelacion) 
              VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
              $queryC2 = mysqli_prepare($conexion, $sqlC2);
              mysqli_stmt_bind_param($queryC2,"isssdidddsiiiisss",$fetchC1['idVenta'],
              $fetchC1['num_comprobante'],$fetchC1['fechaVenta'],$fetchC1['horaVenta'],
              $fetchC1['totalVenta'],$fetchC1['estatusVenta'],$fetchC1['descuentoVenta'],
              $fetchC1['montoPago'],$fetchC1['cambioPago'],$fetchC1['tipoPago'],
              $fetchC1['clienteID'],$fetchC1['empresaID'],$fetchC1['usuarioID'],
              $fetchC1['usuarioVenta'],$hoy,$usuario,$motivoCancela);
              mysqli_stmt_execute($queryC2);

              $idCancelada = mysqli_stmt_insert_id($queryC2);

              $sqlC3 = "INSERT INTO DETALLEVENTASCANCELADAS
              (idVentaCancelada, idDetalleVentaOriginal, cantidadVenta, precioUnitario,
              subtotalVenta, descuento, usuarioVenta, sucursalID, articuloID,
              trabajoID, ventaID, chipID)
              SELECT ?, idDetalleVenta, cantidadVenta, precioUnitario,
              subtotalVenta, descuento, usuarioVenta, sucursalID, articuloID,
              trabajoID, ventaID, chipID
              FROM DETALLEVENTA WHERE ventaID = ?";
              $queryC3 = mysqli_prepare($conexion, $sqlC3);
              mysqli_stmt_bind_param($queryC3,"ii",$idCancelada,$idVentaCancela);
              mysqli_stmt_execute($queryC3);

              //dependiendo de los articulos vendidos los regresamos al inventario
              $sqlR1 = "SELECT * FROM DETALLEVENTA WHERE ventaID = ?";
              $queryR1 = mysqli_prepare($conexion, $sqlR1);
              mysqli_stmt_bind_param($queryR1,"i",$idVentaCancela);
              mysqli_stmt_execute($queryR1);
              $resR1 = mysqli_stmt_get_result($queryR1);
              while($fetchR1 = mysqli_fetch_assoc($resR1)){
                //detectamos si es uin trabajo o un articulo
                $idArticuloV = $fetchR1['articuloID'];
                $idSucursalV = $fetchR1['sucursalID'];
                $idChip = $fetchR1['chipID'];
                $idTrabajo = $fetchR1['trabajoID'];
                $cantidadVenta = $fetchR1['cantidadVenta'];

                if($fetchR1['trabajoID']> 0){
                  //cuando se trata de un trabajo, actualizamos las fechas de cobro
                  $sqlR4 = "UPDATE TRABAJOS SET fechaCobro =  NULL, usuarioCobro = NULL, 
                  horaCobro = NULL WHERE idTrabajo = ?";
                  $queryR4 = mysqli_prepare($conexion, $sqlR4);
                  mysqli_stmt_bind_param($queryR4,"i",$idTrabajo);
                  mysqli_stmt_execute($queryR4);
                }else{
                  //se trata de un articulo, ahora vemos si es un chip o uno
                  if($fetchR1['chipID'] > 0){
                    //se trata de un chip
                    $sqlR3 = "UPDATE DETALLECHIP SET estatusChip = 'Activo', 
                    fechaVenta = NULL, ventaID = NULL WHERE idChip = ?";
                    $queryR3 = mysqli_prepare($conexion, $sqlR3);
                    mysqli_stmt_bind_param($queryR3,"i",$idChip);
                    mysqli_stmt_execute($queryR3);
                  }else{
                    //se trata de un articuilo normal
                    $cantidad = $fetchR1['cantidadVenta'];
                    $sqlR2 = "UPDATE ARTICULOSUCURSAL SET existenciaSucursal = existenciaSucursal + ? 
                    WHERE articuloID = ? AND sucursalID = ?";
                    $queryR2 = mysqli_prepare($conexion, $sqlR2);
                    mysqli_stmt_bind_param($queryR2, "iii",$cantidadVenta, $idArticulo,$idSucursalV);
                    mysqli_stmt_execute($queryR2);

                  }
                }
              }//fin del while

              //se devuelve completo copmpleto el inventario, ahora procedemos a cancelar las ventas originales
              $sqlC4 = "DELETE FROM DETALLEVENTA WHERE ventaID = ?";
              $queryC4 = mysqli_prepare($conexion, $sqlC4);
              mysqli_stmt_bind_param($queryC4,"i",$idVentaCancela);
              mysqli_stmt_execute($queryC4);

              $sqlC5 = "UPDATE VENTAS SET totalVenta = 0, descuentoVenta = 0, montoPago = 0,
              cambioPago = 0, estatusVenta = 1 WHERE idVenta = ?";
              $queryC5 = mysqli_prepare($conexion, $sqlC5);
              mysqli_stmt_bind_param($queryC5,"i",$idVentaCancela);
              mysqli_stmt_execute($queryC5);

              mysqli_commit($conexion);

              // se devio de haber cancelado y regersado
              $result = ['status'=>'ok','mensaje'=>'operationComplete'];
              echo json_encode($result);

            } catch (\Throwable $th) {
              mysqli_rollback($conexion);

              $result = ['status'=>'error','mensaje'=>'Ocurrio un error al cancelar la venta: '.$th];
              echo json_encode($result);
            }
            

          }else{
            //la venta no existe
            mysqli_rollback($conexion);

            $result = ['status'=>'error','mensaje'=>'La venta indicada no existe'];
            echo json_encode($result);
          }
        }else{
          //las contrasenas no coiciden
          $result = ['status'=>'error','mensaje'=>'Las contrasenas indicadas no coinciden.'];
          echo json_encode($result);
        }

      }
    }else{
      //el usuario no tiene permisos para operar
      $result = ['status'=>'error','mensaje'=>'No tienes permisos para cancelar ventas'];
      echo json_encode($result);
    }
    

    
  }else{
    //sin sesion iniciada
    $result = ['status'=>'error','mensaje'=>'Saquese ALV de aqui.'];
    echo json_encode($result);
  }

?>