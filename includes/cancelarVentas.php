 <!-- -- Tabla espejo de VENTAS
CREATE TABLE `VENTASCANCELADAS` (
  `idVentaCancelada` int(11) NOT NULL AUTO_INCREMENT,
  `idVentaOriginal` int(11) NOT NULL,
  `num_comprobante` varchar(45) NOT NULL,
  `fechaVenta` date NOT NULL,
  `horaVenta` time NOT NULL,
  `totalVenta` decimal(11,2) NOT NULL,
  `estatusVenta` int(11) NOT NULL,
  `descuentoVenta` decimal(11,2) DEFAULT NULL,
  `montoPago` decimal(11,2) NOT NULL,
  `cambioPago` decimal(11,2) NOT NULL,
  `tipoPago` varchar(25) NOT NULL,
  `clienteID` int(11) NOT NULL,
  `empresaID` int(11) NOT NULL,
  `usuarioID` int(11) NOT NULL,
  `usuarioVenta` int(11) DEFAULT NULL,
  -- Auditoría de la cancelación
  `fechaCancelacion` datetime NOT NULL,
  `usuarioCancela` varchar(50) NOT NULL,
  `motivoCancelacion` varchar(255) NOT NULL,
  PRIMARY KEY (`idVentaCancelada`),
  KEY `idx_venta_original` (`idVentaOriginal`),
  KEY `idx_empresa_fecha` (`empresaID`, `fechaVenta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla espejo de DETALLEVENTA
CREATE TABLE `DETALLEVENTASCANCELADAS` (
  `idDetalleVentaCancelada` int(11) NOT NULL AUTO_INCREMENT,
  `idVentaCancelada` int(11) NOT NULL,
  `idDetalleVentaOriginal` int(11) NOT NULL,
  `cantidadVenta` int(11) NOT NULL,
  `precioUnitario` decimal(11,2) NOT NULL,
  `subtotalVenta` decimal(11,2) DEFAULT NULL,
  `descuento` decimal(11,2) DEFAULT NULL,
  `usuarioVenta` varchar(50) NOT NULL,
  `sucursalID` int(11) NOT NULL,
  `articuloID` int(11) DEFAULT NULL,
  `trabajoID` int(11) DEFAULT NULL,
  `ventaID` int(11) DEFAULT NULL,
  `chipID` int(11) DEFAULT NULL,
  PRIMARY KEY (`idDetalleVentaCancelada`),
  KEY `idx_venta_cancelada` (`idVentaCancelada`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; -->

<?php 
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
        $hoy = date('Y-m-d H:i:s');

        //validamos las contras
        if($contraPass == $contraBase){
          //si coinciden las contras, consultamos la venta
          $sqlC1 = "SELECT * FROM VENTAS WHERE idVenta = ? AND empresaID = ? AND fechaVenta = ?";
          $queryC1 = mysqli_prepare($conexion, $sqlC1);
          mysqli_stmt_bind_param($queryC1,"iis",$idVentaCancela,$idEmpresaSesion,$hoy);
          mysqli_stmt_execute($queryC1);
          $resC1 = mysqli_stmt_get_result($queryC1);
          if(mysqli_num_rows($resC1) == 1){
            //comenzamos la transaccion
            $fetchC1 = mysqli_fetch_assoc($resC1);
            mysqli_begin_transaction($conexion);
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

              mysqli_commit($conexion);
            } catch (\Throwable $th) {
              echo $th;
              mysqli_rollback($conexion);
            }
            

          }else{
            //la venta no existe
          }
        }else{
          //las contrasenas no coiciden
        }

      }
    }else{
      //el usuario no tiene permisos para operar
    }
    

    
  }else{
    //sin sesion iniciada
  }

?>