<?php

session_start();
set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set('memory_limit', '512M');

header('Content-Type: application/json');

require("usuarios.php");
require("empresas.php");
require("articulos.php");
require_once("conexion.php");
require("SimpleXLSX.php");

use Shuchkin\SimpleXLSX;

if(empty($_SESSION['usuarioPOS'])){
    echo json_encode([
        "status"=>"error",
        "mensaje"=>"Sesión expirada"
    ]);
    exit;
}

if(empty($_FILES['archivoInventario']['name'])){
    echo json_encode([
        "status"=>"error",
        "mensaje"=>"No se recibió archivo"
    ]);
    exit;
}

$usuario = $_SESSION['usuarioPOS'];
// echo $usuario;

$empresa = datoEmpresaSesion($usuario,"id");
$empresa = json_decode($empresa);

$idEmpresa = $empresa->dato;

$idProveedorMaster = 0;

try {

    $tmpFile = $_FILES['archivoInventario']['tmp_name'];

    if(!$xlsx = SimpleXLSX::parse($tmpFile)){
        echo json_encode([
            "status"=>"error",
            "mensaje"=>SimpleXLSX::parseError()
        ]);
        exit;
    }

    $rows = $xlsx->rows();

    // VALIDACION DE FORMATO
    if (count($rows) < 1) {
      echo json_encode(["status" => "error", "mensaje" => "El archivo está vacío"]);
      exit;
    }

    // Definimos el orden exacto de los encabezados que esperas
    $columnasEsperadas = [
        'nombreArticulo', 'descripcion', 'categoria', 'codigoProducto', 
        'precioCompra', 'precioVenta', 'precioMayoreo', 
        'mayoreodesde', 'stock', 'proveedor'
    ];

    $encabezadosReales = $rows[0]; // La primera fila del Excel


    foreach ($columnasEsperadas as $i => $nombreColumna) {
        // Comparamos ignorando mayúsculas/minúsculas y espacios extra
        $headerReal = isset($encabezadosReales[$i]) ? trim($encabezadosReales[$i]) : '';
        
        if (strcasecmp($headerReal, $nombreColumna) !== 0) {
          echo json_encode([
            "status" => "error", 
            "mensaje" => "Formato de Excel inválido. La columna " . ($i + 1) . " debe ser '$nombreColumna' (se encontró '$headerReal')."
          ]);
          exit;
        }
    }
    // FIN VALIDACION

    $creados = 0;
    $actualizados = 0;
    $errores = [];
    $categoriasCache = [];
    $proveedoresCache = [];

    $sqlEX = "SELECT idCategoria,nombreCategoria FROM CATEGORIA WHERE empresaID = '$idEmpresa'";
    $queryEx = mysqli_query($conexion, $sqlEX);
    while($fetchEX = mysqli_fetch_assoc($queryEx)){
      $nombreKey = strtoupper(trim($fetchEX['nombreCategoria']));
      $categoriasCache[$nombreKey] = $fetchEX['idCategoria'];
    }

    $sqlEx2 = "SELECT idProveedor,nombreProveedor FROM PROVEEDORES WHERE provEmpresaID = '$idEmpresa'";
    $queryEx2 = mysqli_query($conexion, $sqlEx2);
    while($fetchEx2 = mysqli_fetch_assoc($queryEx2)){
      $nombreKey2 = strtoupper(trim($fetchEx2['nombreProveedor']));
      $proveedoresCache[$nombreKey2] = $fetchEx2['idProveedor'];
    }//fin while fetchext2

    // mysqli_close($conexion);

    // sucursal principal
    $sucursales = verSucursales($usuario,'');
    $sucursales = json_decode($sucursales);

    if(empty($sucursales->dato[0]->idSucursal)){
        echo json_encode([
            "status"=>"error",
            "mensaje"=>"No hay sucursales registradas"
        ]);
        exit;
    }

    $idSucursal = $sucursales->dato[0]->idSucursal;

    // mysqli_begin_transaction($conexion);


    //VOLCAMOS EL EXCEL
    foreach($rows as $index => $row){

        if($index == 0){
            continue;
        }

        $nombre = htmlentities(trim($row[0] ?? ''));
        $descripcion = htmlentities(trim($row[1] ?? ''));
        $nombreCategoria = htmlentities(trim($row[2] ?? ''));
        $codigo = htmlentities(trim($row[3] ?? ''));
        $precioCompra = htmlentities(floatval($row[4] ?? 0));
        $precioVenta = htmlentities(floatval($row[5] ?? 0));
        $precioMayoreo = htmlentities(floatval($row[6] ?? 0));
        $mayoreoDesde = htmlentities(intval($row[7] ?? 0));
        $stock = htmlentities(intval($row[8] ?? 0));
        $proveedor = htmlentities(trim($row[9] ?? 0));

        if(empty($nombre)){
            $errores[] = "Fila ".($index+1)." sin nombre";
            continue;
        }

        //--------------------------------------
        // BUSCAR O CREAR PROVEEDOR
        //--------------------------------------


        $keyProveedor = strtoupper($proveedor);
        if(empty($proveedor)){
          $proveedor = "MIGRACION";
        }

        if(isset($proveedoresCache[$keyProveedor])){

          // $fetchProv = mysqli_fetch_assoc($resProv);
          // $idProveedor = $fetchProv['idProveedor'];
          $idProveedor = $proveedoresCache[$keyProveedor];

        }else{

          //--------------------------------------
          // CREAR PROVEEDOR
          //--------------------------------------

          $sqlInsertProv = "INSERT INTO PROVEEDORES (nombreProveedor,provEmpresaID) VALUES (?,?)";
          $stmtInsertProv = mysqli_prepare($conexion,$sqlInsertProv);
          mysqli_stmt_bind_param($stmtInsertProv,"si",$proveedor,$idEmpresa);
          mysqli_stmt_execute($stmtInsertProv);
          $idProveedor = mysqli_insert_id($conexion);
          mysqli_stmt_close($stmtInsertProv);

          $proveedoresCache[$proveedor] = $idProveedor;
        }

        //--------------------------------------
        // BUSCAR O CREAR CATEGORIA
        //--------------------------------------

        $keyCategoria = strtoupper($nombreCategoria);

        if(isset($categoriasCache[$keyCategoria])){

            // $fetchCat = mysqli_fetch_assoc($resultCat);
            $idCategoria = $categoriasCache[$keyCategoria];

        }else{

            $newCat = setCategoria($idEmpresa,$nombreCategoria,"1","Importada automáticamente");

            // $queryCat2 = mysqli_query($conexion,$sqlCat);
            // $fetchCat2 = mysqli_fetch_assoc($queryCat2);
            $newCat = json_decode($newCat);
            $idCategoria = $newCat->data;
            $categoriasCache[$keyCategoria] = $idCategoria;
        }

        //--------------------------------------
        // BUSCAR PRODUCTO EXISTENTE
        //--------------------------------------

        $sqlProd = "SELECT * FROM ARTICULOS WHERE codigoProducto = ? AND empresaID = ?";

        $queryProd = mysqli_prepare($conexion,$sqlProd);
        mysqli_stmt_bind_param($queryProd,"si",$codigo,$idEmpresa);
        mysqli_stmt_execute($queryProd);
        $resProd = mysqli_stmt_get_result($queryProd);

        if(mysqli_num_rows($resProd) > 0){

            //--------------------------------------
            // ACTUALIZAR PRODUCTO
            //--------------------------------------

            $fetchProd = mysqli_fetch_assoc($resProd);
            $idArticulo = $fetchProd['idArticulo'];

            $sqlUpdate = "UPDATE ARTICULOS SET precioUnitario = ?,
            precioMayoreo = ?, precioCompra = ?, proveedorID = ?,
            categoriaID = ? WHERE idArticulo = ? AND empresaID = ?";

            $queryUpdateProd = mysqli_prepare($conexion,$sqlUpdate);
            mysqli_stmt_bind_param($queryUpdateProd,"dddiiii",$precioVenta,
            $precioMayoreo,$precioCompra,$idProveedor,$idCategoria,$idArticulo,$idEmpresa);
            mysqli_stmt_execute($queryUpdateProd);
            mysqli_stmt_close($queryUpdateProd);


            //--------------------------------------
            // ACTUALIZAR STOCK
            //--------------------------------------

            $sqlStock = "SELECT idArtiSuc, existenciaSucursal FROM ARTICULOSUCURSAL
            WHERE articuloID = ? AND sucursalID = ?";

            $queryStock = mysqli_prepare($conexion,$sqlStock);
            mysqli_stmt_bind_param($queryStock,"ii",$idArticulo,$idSucursal);
            mysqli_stmt_execute($queryStock);
            $reStock = mysqli_stmt_get_result($queryStock);
            mysqli_stmt_close($queryStock);


            if(mysqli_num_rows($reStock) > 0){

                $fetchStock = mysqli_fetch_assoc($reStock);

                $nuevoStock = $fetchStock['existenciaSucursal'] + $stock;

                setCantidad($nuevoStock,$idArticulo,$idSucursal);

            }else{

                guardarArticuloSuc($stock,$idSucursal,$idArticulo);
            }

            $actualizados++;

        }else{

            //--------------------------------------
            // CREAR NUEVO PRODUCTO
            //--------------------------------------

            if(empty($codigo)){
                $nuevoCodigo = genCodigo($idEmpresa);
                $nuevoCodigo = json_decode($nuevoCodigo);
                $codigo = $nuevoCodigo->data;
            }

            $guardar = guardarProducto($nombre,$descripcion,"1",$idEmpresa,$idCategoria,
            "",$precioVenta,$precioMayoreo,$mayoreoDesde,$codigo,$idProveedor,0);

            $guardar = json_decode($guardar);

            if($guardar->status == "ok"){

                $idArticulo = $guardar->dato;

                guardarArticuloSuc($stock,$idSucursal,$idArticulo);

                $creados++;

            }else{
                $errores[] = "Fila ".($index+1).": ".$guardar->mensaje;
            }
        }
    }

    // mysqli_commit($conexion);

    echo json_encode([
        "status"=>"ok",
        "mensaje"=>"Importación completada",
        "creados"=>$creados,
        "actualizados"=>$actualizados,
        "errores"=>$errores
    ]);

} catch(Exception $e){

  // mysqli_rollback($conexion);

    echo json_encode([
        "status"=>"error",
        "mensaje"=>$e->getMessage()
    ]);
}