<?php
session_start();
if (!empty($_SESSION['usuarioPOS'])) {
  require("includes/conexion.php");
  require_once("includes/usuarios.php");

  //verificamos los campos
  $catego = $_GET['cate'];
  $sucu = $_GET['suc'];
  $prov = $_GET['prov'];
  $usuario = $_SESSION['usuarioPOS'];


  if (!empty($catego) && !empty($sucu) && !empty($prov)) {

    $empresa = datoEmpresaSesion($usuario, "id");
    $empresa = json_decode($empresa);
    $idEmpresaSesion = $empresa->dato;

    $empresa2 = datoEmpresaSesion($usuario, "nombre");
    $empresa2 = json_decode($empresa2);
    $nombrEmpresa = $empresa2->dato;

    $empresa3 = datoEmpresaSesion($usuario, "logo");
    $empresa3 = json_decode($empresa3);
    $logoEmp = $empresa3->dato;
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Reporte de Inventario - Impresión</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <style>
        /* Estilos para Pantalla */
        body {
          background-color: #f8f9fa;
          font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .paper-sheet {
          width: 279mm;
          /* Aproximadamente ancho carta */
          min-height: 216mm;
          padding: 15mm;
          margin: 10px auto;
          background: white;
          box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
          border-radius: 5px;
        }

        /* Estilos específicos de IMPRESIÓN */
        @media print {
          @page {
            size: letter landscape;
            margin: 15mm;
          }

          body {
            background: none;
            padding: 0;
          }

          .paper-sheet {
            width: 100%;
            margin: 0;
            box-shadow: none;
            padding: 0;
          }

          .no-print {
            display: none !important;
          }

          /* Forzar colores en impresión */
          .table-dark {
            background-color: #212529 !important;
            color: white !important;
          }

          .bg-light {
            background-color: #f8f9fa !important;
          }
        }

        .table thead th {
          background-color: #f2f2f2;
          text-transform: uppercase;
          font-size: 0.85rem;
          letter-spacing: 1px;
        }

        .signature-line {
          border-top: 1px solid #000;
          width: 200px;
          margin-top: 50px;
          display: inline-block;
        }

        .font-normal {
          font-size: 14px;
        }

        .font-condensed {
          font-size: 11px;
          line-height: 1;
        }

        .font-extra-small {
          font-size: 9px;
          line-height: 0.9;
          font-weight: bold;
        }
      </style>
    </head>

    <body>

      <div class="container d-flex justify-content-end my-4 no-print">
        <button onclick="window.print()" class="btn btn-primary btn-lg shadow">
          <i class="bi bi-printer"></i> Imprimir Inventario
        </button>
      </div>

      <div class="paper-sheet">

        <div class="row mb-4">
          <div class="col-8">
            <h2 class="fw-bold text-uppercase m-0">Reporte de Inventario</h2>
            <!-- <p class="text-muted">Nombre de tu Empresa S.A. de C.V.</p> -->
          </div>
          <div class="col-4 text-end small">
            <strong>Fecha:</strong> <?php echo date('Y-m-d'); ?><br>
            <!-- <strong>ID Reporte:</strong> #INV-2026-001<br> -->
            <!-- <strong>Almacén:</strong> Central Puebla -->
          </div>
        </div>

        <?php

        $querySucursales = "";
        $tablaSuc = "";
        $x = 1;
        $auxSuc = "";
        if ($sucu == "all") {
        } else {
          $auxSuc = "AND idSucursal = '$sucu'";
        }

        $sqlSuc = "SELECT * FROM SUCURSALES WHERE empresaSucID = '$idEmpresaSesion' AND estatusSuc = '1' $auxSuc";
        $querySuc = mysqli_query($conexion, $sqlSuc);
        while ($fetchSuc = mysqli_fetch_assoc($querySuc)) {
          $nombreSuc = $fetchSuc['nombreSuc'];
          $idSuc = $fetchSuc['idSucursal'];
          $tablaSuc .= "<th>$nombreSuc</th>";
          $querySucursales .= ", (SELECT $x.existenciaSucursal FROM 
            ARTICULOSUCURSAL $x WHERE $x.sucursalID = '$idSuc' AND $x.articuloID = a.idArticulo) AS arti$idSuc";
          $x++;
        } //fin while Sucursales
        ?>

        <?php
        // ✅ Construir consulta de sucursales de forma segura
        $querySucursales = "";
        $tablaSuc = "";
        $sucursalesData = [];

        $sqlSuc = "SELECT * FROM SUCURSALES WHERE empresaSucID = ? AND estatusSuc = '1' $auxSuc";
        $stmtSuc = mysqli_prepare($conexion, $sqlSuc);
        mysqli_stmt_bind_param($stmtSuc, "i", $idEmpresaSesion);
        mysqli_stmt_execute($stmtSuc);
        $querySuc = mysqli_stmt_get_result($stmtSuc);

        while ($fetchSuc = mysqli_fetch_assoc($querySuc)) {
          $nombreSuc = htmlspecialchars($fetchSuc['nombreSuc']);
          $idSuc = intval($fetchSuc['idSucursal']);

          $tablaSuc .= "<th class='text-center' style='width: 8%'>{$nombreSuc}</th>";

          // ✅ Usar alias más seguros y claros
          $querySucursales .= ", (SELECT existenciaSucursal 
                                FROM ARTICULOSUCURSAL 
                                WHERE sucursalID = {$idSuc} 
                                AND articuloID = a.idArticulo 
                                LIMIT 1) AS stock_suc_{$idSuc}";

          // Guardar IDs de sucursales para el bucle posterior
          $sucursalesData[] = [
            'id' => $idSuc,
            'nombre' => $nombreSuc
          ];
        }
        ?>

        <div class="table-responsive">
          <table class="table table-bordered table-sm align-middle table-hover">
            <thead class="table-light">
              <tr>
                <th class="text-center" style="width: 10%">Código</th>
                <th style="width: 25%">Nombre del Artículo</th>
                <th class="text-center" style="width: 12%">Categoría</th>
                <th class="text-center" style="width: 12%">Proveedor</th>
                <?php echo $tablaSuc; ?>
                <th class="text-center" style="width: 8%">Stock Total</th>
                <th class="text-center" style="width: 10%">Estado</th>
              </tr>
            </thead>
            <tbody>
            <?php
// ✅ Construir consulta con prepared statements
$totalesGeneral = 0; // ❌ Esto lo defines pero no lo usas
$totalStockPorSucursal = []; // ✅ Agregar array para totales por sucursal

// Inicializar totales por sucursal
foreach ($sucursalesData as $sucursal) {
    $totalStockPorSucursal[$sucursal['id']] = 0;
}

$estatusArti = 1;
$sql = "SELECT a.*, b.nombreCategoria, c.nombreProveedor {$querySucursales}
    FROM ARTICULOS a 
    INNER JOIN CATEGORIA b ON a.categoriaID = b.idCategoria 
    INNER JOIN PROVEEDORES c ON a.proveedorID = c.idProveedor
    WHERE a.empresaID = ? AND a.estatusArticulo = ?";

$params = [$idEmpresaSesion,];
$params[] = $estatusArti;
$types = "ii";

// Filtro de proveedor
if (isset($prov) && $prov != "all" && is_numeric($prov)) {
    $sql .= " AND a.proveedorID = ?";
    $params[] = intval($prov);
    $types .= "i";
}

// Filtro de categoría
if (isset($catego) && $catego != "all" && is_numeric($catego)) {
    $sql .= " AND a.categoriaID = ?";
    $params[] = intval($catego);
    $types .= "i";
}

$sql .= " ORDER BY a.nombreArticulo ASC";

// Preparar y ejecutar
$stmt = mysqli_prepare($conexion, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $query = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($query) > 0) {
        $totalArticulos = 0;
        $totalStockGeneral = 0;

        while ($fetch = mysqli_fetch_assoc($query)) {
            $totalArticulos++;

            // Escapar datos
            $nombre = $fetch['nombreArticulo'];
            $codigo = htmlspecialchars($fetch['codigoProducto']);
            $categoria = htmlspecialchars($fetch['nombreCategoria']);
            $nombreProv = htmlspecialchars($fetch['nombreProveedor']);

            // ✅ Calcular stock total (base de datos + sucursales)
            $stockBase = intval($fetch['stock'] ?? 0);
            $stockTotalArticulo = 0; // ✅ Stock total de este artículo

            // Clase según longitud
            $clase = "font-normal";
            if (strlen($nombre) > 50) {
                $clase = "font-extra-small";
            } elseif (strlen($nombre) > 30) {
                $clase = "font-condensed";
            }

            echo '<tr>';
            echo '<td class="text-center"><code class="bg-light px-2 py-1 rounded">' . $codigo . '</code></td>';
            echo '<td class="' . $clase . '" title="' . $nombre . '">' . $nombre . '</td>';
            echo '<td class="text-center"><span class="badge bg-secondary">' . $categoria . '</span></td>';
            echo '<td class="text-center small">' . $nombreProv . '</td>';

            // ✅ Mostrar stock por sucursal
            foreach ($sucursalesData as $sucursal) {
                $idSuc = $sucursal['id'];
                $stockSuc = intval($fetch["stock_suc_{$idSuc}"] ?? 0);

                // ✅ Acumular en totales
                $totalStockPorSucursal[$idSuc] += $stockSuc;
                $stockTotalArticulo += $stockSuc;

                // Color según stock
                $colorStock = 'text-muted';
                if ($stockSuc > 5) {
                    $colorStock = 'text-success fw-bold';
                } elseif ($stockSuc > 0) {
                    $colorStock = 'text-warning fw-bold';
                }

                echo '<td class="text-center ' . $colorStock . '">' . $stockSuc . '</td>';
            }

            // ✅ Stock total de este artículo
            $totalStockGeneral += $stockTotalArticulo;

            // ✅ Determinar estado según stock
            if ($stockTotalArticulo > 10) {
                $estado = '<span class="badge bg-success">Disponible</span>';
                $stockClass = 'text-success';
            } elseif ($stockTotalArticulo > 0) {
                $estado = '<span class="badge bg-warning text-dark">Bajo</span>';
                $stockClass = 'text-warning';
            } else {
                $estado = '<span class="badge bg-danger">Agotado</span>';
                $stockClass = 'text-danger';
            }

            echo '<td class="text-center fw-bold ' . $stockClass . '">' . $stockTotalArticulo . '</td>';
            echo '<td class="text-center">' . $estado . '</td>';
            echo '</tr>';
        }

        // ✅ FILA DE TOTALES CORREGIDA
        echo '<tr class="table-secondary fw-bold">';
        echo '<td colspan="4" class="text-end">Totales</td>';

        // ✅ Mostrar totales por sucursal
        foreach ($sucursalesData as $sucursal) {
            $totalSuc = $totalStockPorSucursal[$sucursal['id']];
            echo '<td class="text-center text-primary">' . $totalSuc . '</td>';
        }

        // ✅ Total general
        echo '<td class="text-center text-primary fw-bold fs-5">' . $totalStockGeneral . '</td>';
        echo '<td></td>';
        echo '</tr>';

    } else {
        $colspan = 7 + count($sucursalesData); // ✅ Ajustar colspan
        echo '<tr>
                <td colspan="' . $colspan . '" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-search fa-3x mb-3 d-block opacity-50"></i>
                        <h5>No se encontraron artículos</h5>
                        <p class="mb-0">Intenta ajustar los filtros de búsqueda</p>
                    </div>
                </td>
            </tr>';
    }

    mysqli_stmt_close($stmt);
} else {
    $colspan = 7 + count($sucursalesData); // ✅ Ajustar colspan
    echo '<tr>
            <td colspan="' . $colspan . '" class="text-center text-danger py-4">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error al cargar los datos
            </td>
        </tr>';
}
              ?>
            </tbody>
          </table>
        </div>

        <style>
          /* Clases personalizadas para texto */
          .font-normal {
            font-size: 14px;
          }

          .font-condensed {
            font-size: 12px;
            line-height: 1.3;
          }

          .font-extra-small {
            font-size: 11px;
            line-height: 1.2;
          }

          /* Hover en filas */
          .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
          }

          /* Código con estilo */
          code {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color:#000;
          }
        </style>

        <!-- <div class="row mt-4">
          <div class="col-6">
            <div class="p-3 bg-light border rounded">
              <h6 class="fw-bold small mb-2">NOTAS ADICIONALES:</h6>
              <p class="small text-muted mb-0">Esta hoja de inventario es válida para la auditoría del primer trimestre. Cualquier discrepancia debe reportarse al supervisor de área inmediatamente.</p>
            </div>
          </div>
          <div class="col-6">
            <table class="table table-sm table-borderless">
              <tr>
                <td class="small">Total de Artículos:</td>
                <td class="text-end fw-bold">48</td>
              </tr>
              <tr>
                <td class="small">Valor Estimado:</td>
                <td class="text-end fw-bold">$12,450.00</td>
              </tr>
            </table>
          </div>
        </div> -->

        <!-- <div class="row mt-5 text-center">
          <div class="col-6">
            <div class="signature-line"></div>
            <p class="small mt-2">Firma Encargado Almacén</p>
          </div>
          <div class="col-6">
            <div class="signature-line"></div>
            <p class="small mt-2">Firma Auditor / Revisión</p>
          </div>
        </div> -->
      </div>

    </body>

    </html>
<?php
  } else {
    //no tiene campos
    echo "Sin datos capturados";
  }
} else {
  header('location:login.php');
}
?>