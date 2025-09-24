<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Reporte Mensual - PostRepair</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <meta name="description" content="ERP para talleres de reparacion celular">
  <meta name="author" content="Tecuanisoft">    
  <link rel="shortcut icon" href="assets/images/logo.png"> 
  
  <!-- FontAwesome JS-->
  <script defer src="assets/plugins/fontawesome/js/all.min.js"></script>
  
  <!-- App CSS -->  
  <link id="theme-style" rel="stylesheet" href="assets/css/portal.css">
  <link id="theme-style" rel="stylesheet" href="assets/css/prop.css">

  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5183563409044723"
    crossorigin="anonymous"></script>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <style>
    :root{
      --bg-1: #f2fbff;
      --card: #ffffff;
      --accent: #0b74d1;
      --accent-2: #66c6ff;
      --muted: #536e7a;
      --text: #062233;
    }
    body{
      background: linear-gradient(180deg,var(--bg-1) 0%, #eaf6ff 100%);
      color:var(--text);
      font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      padding:20px 0;
    }
    .report-header{
      background: linear-gradient(90deg, rgba(11,116,209,0.12), rgba(46,160,255,0.06));
      border-radius:12px;
      padding:22px;
      display:flex;
      align-items:center;
      gap:18px;
      box-shadow: 0 6px 24px rgba(11,116,209,0.06);
    }
    .logo-box{ width:88px; height:64px; background:linear-gradient(135deg,var(--accent),var(--accent-2)); border-radius:10px; display:flex; align-items:center; justify-content:center; color:white; font-weight:700; }
    .card-light{
      background: var(--card);
      border-radius:12px;
      box-shadow: 0 6px 18px rgba(11,116,209,0.05);
      border:1px solid rgba(11,116,209,0.04);
    }
    .kpi { padding:16px; }
    .kpi .value { font-size:1.45rem; font-weight:700; color:var(--accent); }
    .kpi .label { color:var(--muted); font-size:0.9rem; }
    .small-muted { color:var(--muted); font-size:0.92rem; }
    .chart-card{ min-height:240px; padding:16px; }
    .text-paragraph{ 
      color:var(--muted);
      line-height:1.5;
      font-size: 20px;
    }
    .section-title { display:flex; justify-content:space-between; align-items:center; gap:12px; }
    .chip { background: rgba(11,116,209,0.06); padding:6px 10px; border-radius:999px; font-size:0.85rem; color:var(--text); }
    table.table thead th { border-bottom:1px solid rgba(11,116,209,0.06); color:var(--text); }
    canvas { background: transparent; }
    @media (max-width:768px){ .report-header{ flex-direction:column; align-items:flex-start } }
  </style>

  <?php 
    // consultamos el mes a mostrar
    session_start();
    $idEmpresa = $_SESSION['empresaPOS'];
    // include('includes/conexion.php');
    $mes = $_GET['mes'];
    $anio = $_GET['year'];

    // DATOPS DE CONEXION
      if(gethostname() == "DESKTOP-AUI7NQT"){
        $us = "root";
        $pw = "#Tecuani.Joel";
        $ht = "localhost";
        // $db = "cierre agosto 25";
      }else{
        $us = "u427759545_cierre082025";
        $pw = "#Tecuani.Joel1";
        $ht = "localhost";
      }
    // FIN DATOSS DE CONEXION

    if(empty($_GET['mes']) || empty($_GET['year'])){
      header('location:reportesCaja.php');
    }else{
      //cargamos la conexion depende del mes
      if($mes == "08" && $anio == "2025"){
        $db = "u427759545_cierre082025";
        $conexion = mysqli_connect($ht,$us,$pw)or die
        ("Ocurrio un error al comunicarse con la base de datos: ".mysqli_error($conexion));
        mysqli_select_db($conexion, $db)or die("No se establecio la conexion con la tabla: ".mysqli_error($conexion));
        mysqli_set_charset($conexion, "utf8");
      }else{
      header('location:reportesCaja.php');
        //reporte no cargado
        header('location:reportesCaja.php');
      }
    }

    //Definidio de periodos
    // echo $mes." ".$anio;
    $periodo = $anio."-".$mes."-01";
    $ultimoDia = date('t',strtotime($periodo));
    $periodoFin = $anio."-".$mes."-".$ultimoDia;
    //Periodo anterior
    $fechaAux = new DateTime($periodo);
    $periodoAnterior = $fechaAux->modify('-1 month')->format('Y-m-d');
    $ultimoDiaAnt = date('t', strtotime($periodoAnterior));
    $anioAnterior = $fechaAux->format('Y');
    $mesAnterior = $fechaAux->format('m');

    $ventasTotalesReparacion = 0;
    $numeroTotalesReparacion = 0;
    $ventasTotalesArticulos = 0;
    $numeroTotalesArticulosVendidos = 0;


    // Periodo anterior completo
    $periodoAnteriorInicio = $anioAnterior."-".$mesAnterior."-01";
    $periodoAnteriorFin = $anioAnterior."-".$mesAnterior."-".$ultimoDiaAnt;
    // echo $periodoAnteriorInicio." ".$periodoAnteriorFin;

    //consultamos las ventas totales del mes
    $sql1 = "SELECT SUM(b.subTotalVenta) AS totalVenta FROM VENTAS a INNER JOIN 
    DETALLEVENTA b ON b.ventaID = a.idVenta WHERE a.empresaID = '$idEmpresa' AND 
    a.fechaVenta BETWEEN '$periodo' AND '$periodoFin' ";
    try {
      $query1 = mysqli_query($conexion, $sql1);
      $fetch1 = mysqli_fetch_assoc($query1);
      $totalVenta = $fetch1['totalVenta'];
      $promedioDiario = $totalVenta / $ultimoDia;
      $totalVentaText = number_format($totalVenta,2);
    } catch (Throwable $th) {
      //error al consultar las ventas mensuales
    }


    //Consultamos los gastos del mes
    $sql2 = "SELECT SUM(montoMov) AS totalGasto FROM MOVCAJAS WHERE empresaMovID = '$idEmpresa' AND 
    conceptoMov IN ('14','13','12','11') AND tipoMov = 'S' AND
    fechaMovimiento BETWEEN '$periodo' AND '$periodoFin'";
    try {
      $query2 = mysqli_query($conexion, $sql2);
      $fetch2 = mysqli_fetch_assoc($query2);
      $totalGasto = $fetch2['totalGasto'];
      $totalGastoText = number_format($totalGasto,2);

    } catch (\Throwable $th) {
      //error al consultar los gastos
      echo "Error al consultar los gastos".$th;
    }
    
    //utilidad operativa
    $utilidad = $totalVenta - $totalGasto;
    $utilidadText = number_format($utilidad,2);

    //Numero de reparaciones
    $sql3 = "SELECT COUNT(*) AS totalReparaciones FROM TRABAJOS WHERE empresaID = '$idEmpresa' AND 
    fechaRegistro BETWEEN '$periodo' AND '$periodoFin'";
    try {
      $query3 = mysqli_query($conexion, $sql3);
      $fetch3 = mysqli_fetch_assoc($query3);
      $numReparaciones = $fetch3['totalReparaciones'];
      $numReparacionesText = number_format($numReparaciones);
    } catch (\Throwable $th) {
      //throw $th;
    }

    //consultam9os las ventas totales del mes anterior
    $sql4 = "SELECT SUM(totalVenta) AS totalVenta FROM VENTAS WHERE empresaID = '$idEmpresa' AND 
    fechaVenta BETWEEN '$periodoAnteriorInicio' AND '$periodoAnteriorFin'";
    try {
      $query4 = mysqli_query($conexion, $sql4);
      $fetch4 = mysqli_fetch_assoc($query4);
      $totalAnterior = $fetch4['totalVenta'];
      $totalAnteriorText = number_format($totalAnterior,2);
    } catch (\Throwable $th) {
      //error al consultar el total anterior
    }

    // CONSULTA DE VENTAS DIARIAS
    $sql5 = "SELECT fechaVenta AS 'Fecha',COUNT(*) AS 'numVentas',
    ROUND(SUM(totalVenta), 2) AS 'totalVentasDiarias',ROUND(AVG(totalVenta), 2) AS 'ticketPromedio',
    ROUND(MIN(totalVenta), 2) AS 'ventaMinima',ROUND(MAX(totalVenta), 2) AS 'ventaMaxima'
    FROM VENTAS WHERE empresaID = '$idEmpresa' AND fechaVenta BETWEEN '$periodo' AND '$periodoFin' 
    GROUP BY fechaVenta ORDER BY fechaVenta ASC";
    try {
      $query5 = mysqli_query($conexion, $sql5);
      $ventaMaxima = 0;
      $fechaVentaMax = "";
      $numVentasMax = 0;
      $numVentasMin = 0;
      $ventaMinima = 100000;
      $fechaVentaMin = "";
      $dias = [];
      $montosDias = [];
      while($fetch5 = mysqli_fetch_assoc($query5)){
        if($fetch5['totalVentasDiarias'] > $ventaMaxima){
          $ventaMaxima = $fetch5['totalVentasDiarias'];
          $fechaVentaMax = $fetch5['Fecha'];
          $numVentasMax = $fetch5['numVentas'];
        }
        $dias[] = $fetch5['Fecha'];
        $montosDias[] = $fetch5['totalVentasDiarias'];
        if($fetch5['totalVentasDiarias'] < $ventaMinima){
          $ventaMinima = $fetch5['totalVentasDiarias'];
          $fechaVentaMin = $fetch5['Fecha'];
          $numVentasMin = $fetch5['numVentas'];
        }
      }//fin del while

      // GRAFICA VENTAS DIARIAS
      $chartDailySales = [
        'type' => 'bar',
        'data' => [
          'labels' => $dias,
          'datasets' => [
            [
              'label'=>'Ventas Diarias',
              'data' => $montosDias
            ]
          ]
        ],
        'options' => ['responsive'=>true]
      ];
      $chartDailySales  = json_encode($chartDailySales);
    } catch (\Throwable $th) {
      echo "Error al consultar las ventas diarias ".$th;
    }

    //consultamos datos de la empresa
    $sql6 = "SELECT * FROM EMPRESAS WHERE idEmpresa = '$idEmpresa'";
    try {
      $query6 = mysqli_query($conexion, $sql6);
      $fetch6 = mysqli_fetch_assoc($query6);
      $nombreEmpresa = $fetch6['nombreEmpresa'];
      $logoEmpresa = $fetch6['imgLogoEmpresa'];
    } catch (\Throwable $th) {
      //throw $th;
    }
    // Consultamos sucursales de la empresa
    $sql7 = "SELECT a.*, (SELECT SUM(c.subtotalVenta) FROM DETALLEVENTA c INNER JOIN 
    VENTAS b ON c.ventaID = b.idVenta WHERE c.sucursalID = a.idSucursal AND b.fechaVenta 
    BETWEEN '$periodo' AND '$periodoFin') as vendido FROM SUCURSALES a WHERE a.empresaSucID = '$idEmpresa' 
    ORDER BY vendido DESC";
    try {
      $query7 = mysqli_query($conexion, $sql7);
      $sucursales = [];
      $sucursalesChar = [];
      $valoresSucChar = [];
      $listSucursales = "";
      $nSuc = 0;
      $ubicaciones = "";
      while($fetch7 = mysqli_fetch_assoc($query7)){
        $sucursales[$nSuc]['nombre'] = $fetch7['nombreSuc'];
        $sucursalesChar[] = $fetch7['nombreSuc'];
        $valoresSucChar[] = $fetch7['vendido'];
        $sucursales[$nSuc]['montoVendido'] = $fetch7['vendido'];
        $nSuc++;
        $ubicaciones .= $fetch7['calleSuc'].",";
        $listSucursales .= "<li>".$fetch7['nombreSuc'].": $".number_format($fetch7['vendido'],2)."</li>";
      }//fin del while

      //GRAFICA VENTAS POR SUCURSAL
      $chartSucursales = [
        'type' => 'pie',
        'data' => [
          'labels' => $sucursalesChar,
          'datasets' => [
            [
              'data' => $valoresSucChar
            ]
          ]
        ],
        'options' => ['responsive'=>true]
      ];
      $chartSucursales  = json_encode($chartSucursales);

      $porceMayorSuc = ($sucursales[0]['montoVendido'] / $totalVenta) * 100;
      $textoAuxMayorSuc = "";
      if($porceMayorSuc > 50){
        $textoAuxMayorSuc = "lo que significa mas de la mitad de la facturación general.";
      }elseif($porceMayorSuc < 30){
        $textoAuxMayorSuc = "lo que significa menos del 30% de la facturación general.";
      }else{
        $textoAuxMayorSuc = "lo que significa menos del 50% de la facturación general.";
      }
    } catch (\Throwable $th) {
      //throw $th;
      echo $th;
    }
    
    // TEXTOS PRIMERA GRAFICA
    $mesNombre = "Agosto";
    $mesAnterior = "Julio";
    $cambio = "";
    $tendencia = "";
    if($totalVenta > $totalAnterior){
      $cambio = "incremento";
      $tendencia = "positiva";
    }else{
      $cambio = "decremento";
      $tendencia = "negativa";
    }
    $diferencia = $totalVenta - $totalAnterior;
    $variacion = ($diferencia / $totalAnterior) * 100;


    //consulta de reparaciones del mes
    $sql8 = "SELECT * FROM TRABAJOS a INNER JOIN SERVICIOS b ON a.servicioID = b.idServicio 
    WHERE a.empresaID = '$idEmpresa' AND a.fechaTrabajo BETWEEN '$periodo' AND '$periodoFin'";
    try {
      $query8 = mysqli_query($conexion, $sql8);
      $totRepa = mysqli_num_rows($query8);
      $infoReparaciones = [];
      $estatusTrabajos = ['Finalizado'=>0,'Cancelado'=>0,'En Espera'=>0,'Cobrado'=>0];
      while($fetch8 = mysqli_fetch_assoc($query8)){
        //consultaremos $infoReparaciones si existe el $fetch8['nombreServicio'], sumaremos 1
        $nombreServicio = $fetch8['nombreServicio'];
        // Si no existe el servicio, lo inicializamos
        if (!isset($infoReparaciones[$nombreServicio])) {
          $infoReparaciones[$nombreServicio] = [
            'cantidad' => 0,
            'total' => 0,
            'promedio' => 0
          ];
        }
        // Incrementamos la cantidad de reparaciones
        $infoReparaciones[$nombreServicio]['cantidad']++;
        // Sumamos el total de la reparación
        $infoReparaciones[$nombreServicio]['total'] += $fetch8['costoInicial'];
        //ordenamos por numero de reparaciones
        uasort($infoReparaciones, function($a, $b) {
          return $b['cantidad'] <=> $a['cantidad'];
        });
        //contamos los trabajos por estatus
        if($fetch8['estatusTrabajo'] == "Finalizado" && !empty($fetch8['fechaCobro'])){
          $estatusTrabajos['Cobrado']++;
        }else{
          $estatusTrabajos[$fetch8['estatusTrabajo']]++;
        }
      }//fin del while
      // print_r($infoReparaciones);
      $eficiencia = ($estatusTrabajos['Cobrado'] / $totRepa) * 100;
      $cancelado = ". ";
      if($estatusTrabajos['Cancelado'] > 0){
        $cancelado = ", ademas se registraron un total de ".$estatusTrabajos['Cancelado']." servicios cancelados.";
      }
      //GRAFICA TRABAJOS POR ESTATUS
      $estatusCharLabel = [];
      $estatusCharValue = [];
      // print_r($estatusTrabajos);
      foreach ($estatusTrabajos as $estatusTrab => $value) {
        $estatusCharLabel[] = $estatusTrab;
        $estatusCharValue[] = $value;
      }

      $chartEstatusTrabajo = [
        'type' => 'pie',
        'data' => [
          'labels' => $estatusCharLabel,
          'datasets' => [
            [
              'data' => $estatusCharValue
            ]
          ]
        ],
        'options' => ['responsive'=>true]
      ];
      $chartEstatusTrabajo  = json_encode($chartEstatusTrabajo);
    } catch (\Throwable $th) {
      //throw $th;
    }

    $masSolicitados = "";
    $xx = 0;
    $serviciosLabelChar = [];
    $numServiciosLabelChar = [];
    foreach ($infoReparaciones as $servicio => &$info) {
      $serviciosLabelChar[] = $servicio;
      $numServiciosLabelChar[] = $info['cantidad'];
      if($xx < 5){
        if($xx >= 4){
          $masSolicitados .= " y por ultimo ".$servicio." con ".$info['cantidad']." servicios.";
          // break;
        }else{
          $masSolicitados .= $servicio." con ".$info['cantidad']." servicios, ";
        }
        $xx++;
      }
      
    }
    //grafica de servicios nuevos del mes
    $chartServicios = [
      'type' => 'bar',
      'data' => [
        'labels' => $serviciosLabelChar,
        'datasets' => [
          [
            'label'=>'Servicios Registrados',
            'data' => $numServiciosLabelChar
          ]
        ]
      ],
      'options' => ['responsive'=>true]
    ];
    $chartServicios = json_encode($chartServicios);
    $resolucion = "";
    if($eficiencia >= "80"){
      $resolucion = "alta";
    }elseif($eficiencia >= "50"){
      $resolucion = "media";
    }else{
      $resolucion = "baja";
    }

    //consultamos los trabajos cobrados
    $sql9 = "SELECT * FROM VENTAS a INNER JOIN DETALLEVENTA b ON b.ventaID = a.idVenta 
    INNER JOIN TRABAJOS c ON b.trabajoID = c.idTrabajo 
    INNER JOIN SERVICIOS d ON c.servicioID = d.idServicio
    WHERE a.empresaID = '$idEmpresa' AND a.fechaVenta BETWEEN '$periodo' AND '$periodoFin'";
    try {
      $query9 = mysqli_query($conexion,$sql9);
      $tablaTrabajos = "";
      $totalCobrados = 0;
      $totalMontoCobro = 0;
      $trabajosCobrados = [];
      while($fetch9 = mysqli_fetch_assoc($query9)){
        if(!isset($trabajosCobrados[$fetch9['nombreServicio']])){
          $trabajosCobrados[$fetch9['nombreServicio']] = [
            'servicios' => 0,
            'monto' => 0
          ];
        }//fin del if

        $totalCobrados += 1;
        $totalMontoCobro += $fetch9['totalVenta'];
        $trabajosCobrados[$fetch9['nombreServicio']]['servicios']++;
        $trabajosCobrados[$fetch9['nombreServicio']]['monto'] += $fetch9['costoFinal'];
        $ventasTotalesReparacion += $fetch9['subtotalVenta'];
        $numeroTotalesReparacion += $fetch9['cantidadVenta'];
        
      }//fin del while SQL9
      //ahora con el arreglo, creamos la tabla
      foreach ($trabajosCobrados as $trabajos => &$valor) {
        $tablaTrabajos .= "<tr>
          <td class='text-center'>$trabajos</td>
          <td class='text-center'>".$valor['servicios']."</td>
          <td>$".number_format($valor['monto'],2)."</td>
        </tr>";
      }
    } catch (\Throwable $th) {
      //throw $th;
    }

    //CONSULTAMOS LAS VBENTAS DE ARTICULOS
    $sql10 = "SELECT * FROM VENTAS a INNER JOIN DETALLEVENTA b ON a.idVenta = b.ventaID 
    INNER JOIN ARTICULOS c ON b.articuloID = c.idArticulo 
    INNER JOIN CATEGORIA d ON c.categoriaID = d.idCategoria 
    WHERE a.empresaID = '$idEmpresa' AND a.fechaVenta BETWEEN '$periodo' AND '$periodoFin'";
    try {
      $query10 = mysqli_query($conexion, $sql10);
      $articulosVendidos = [];
      
      $categoriasVendidas = [];
      $totalArticulos = 0;
      $ingresosArticulos = 0;
      while($fetch10 = mysqli_fetch_assoc($query10)){
        $nombreArticulo = $fetch10['nombreArticulo'];
        $nombreCategoria = $fetch10['nombreCategoria'];
        $totalArticulos += $fetch10['cantidadVenta'];
        $ingresosArticulos += $fetch10['subtotalVenta'];
        if(!isset($articulosVendidos[$nombreArticulo])){
          $articulosVendidos[$nombreArticulo] = [
            'cantidadVendida' => 0,
            'montoVendido' => 0
          ];
        }//fin if isset articulos

        if(!isset($categoriasVendidas[$nombreCategoria])){
          $categoriasVendidas[$nombreCategoria] = [
            'cantidadVendida' => 0,
            'montoVendido' => 0
          ];
        }//fin isset categorias

        //sumamos las cantidades
        $articulosVendidos[$nombreArticulo]['cantidadVendida'] += $fetch10['cantidadVenta'];
        $articulosVendidos[$nombreArticulo]['montoVendido'] += $fetch10['subtotalVenta'];
        
        $categoriasVendidas[$nombreCategoria]['cantidadVendida'] += $fetch10['cantidadVenta'];
        $categoriasVendidas[$nombreCategoria]['montoVendido'] += $fetch10['subtotalVenta'];

        uasort($categoriasVendidas, function($a, $b) {
          return $b['cantidadVendida'] <=> $a['cantidadVendida'];
        });

        uasort($articulosVendidos, function($c, $d) {
          return $d['cantidadVendida'] <=> $c['cantidadVendida'];
        });

        $ventasTotalesArticulos += $fetch10['subtotalVenta'];
        $numeroTotalesArticulosVendidos += $fetch10['cantidadVenta'];

      }//fin del while
      // print_r($articulosVendidos);

      //Nos quedamos con el top 7 de articulos mas vendidos
      $top7 = 0;
      $top7ArticulosLabel = [];
      foreach ($articulosVendidos as $articuloName => $value) {
        if($top7 < 7){
          $top7ArticulosLabel[] = $articuloName;
          $top7Cantidad[] = $value['cantidadVendida'];
        }else{
          break;
        }
        $top7++;
        
      }//fin foreach top 7 articulos
      $chartTop7Articulos = [
        'type' => 'bar',
        'data' => [
          'labels' => $top7ArticulosLabel,
          'datasets' => [
            [
              'label'=>'Articulos Vendidos',
              'data' => $top7Cantidad
            ]
          ]
        ],
        'options' => ['responsive'=>true]
      ];
      $chartTop7Articulos = json_encode($chartTop7Articulos);
      // print_r($top7ArticulosLabel);


      //ordenamops las categorias mas vendidas
      $categoriasLabels = [];
      $montoCatLabels = [];
      $numCatLabels = [];
      $nCats = 0;
      $textoCategorias = "";
      foreach ($categoriasVendidas as $categoria => $value) {
        // if($nCats <= 4){
          $categoriasLabels[] = $categoria;
          $montoCatLabels[] = $value['montoVendido'];
          $numCatLabels[] = $value['cantidadVendida'];
          if($nCats == 0){
            $textoCategorias .= $categoria." con un total de ".$value['cantidadVendida']." unidades vendidas, 
            representando $".number_format($value['montoVendido'],2)." del total de ventas";
          }elseif($nCats < 3){
            $textoCategorias .= ", seguido  por ".$categoria." con ".$value['cantidadVendida']." 
            unidades vendidas, representando $".number_format($value['montoVendido'],2)." del total de ventas";
          }elseif($nCats == 4){
            $textoCategorias .= ", Por ultimo tambie tenemos a ".$categoria." con ".$value['cantidadVendida']." 
            unidades vendidas y con un total de $".number_format($value['montoVendido'],2);
          }else{

          }
          $nCats++;
        // }else{
        //   break;
        // }
      }//fin foreach categorias


      // GRAFICA ARTICULOS VENDIDOS
      $chartArticulosByCat = [
        'type' => 'line',
        'data' => [
            'labels' => $categoriasLabels,
            'datasets' => [
                [
                  'label' => 'Monto Vendido',
                  'data' => $montoCatLabels,
                  'borderColor' => 'rgba(75, 192, 192, 1)', // Color de la línea
                  'backgroundColor' => 'rgba(75, 192, 192, 0.2)', // Color de fondo
                  'yAxisID' => 'y-servicios', // ID del eje Y para esta serie
                  'fill' => true
                ],
                [
                    'label' => 'Articulos Vendidos', // Segunda serie de datos
                    'data' => $numCatLabels, // Asegúrate de tener este array
                    'borderColor' => 'rgba(255, 99, 132, 1)', // Otro color
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'yAxisID' => 'y-monto', // ID del eje Y para esta serie
                    'fill' => true
                ]
            ]
        ],
        'options' => [
            'responsive' => true,
            'scales' => [
                'y-servicios' => [
                    'type' => 'linear',
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Número de Servicios'
                    ]
                ],
                'y-monto' => [
                    'type' => 'linear',
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Monto Total ($)'
                    ],
                    'grid' => [
                        'drawOnChartArea' => false // Evita dibujar líneas de grid para este eje
                    ]
                ]
            ],
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'Relacion de Articulos y Monto vendido'
                ],
                'legend' => [
                    'display' => true,
                    'position' => 'top'
                ]
            ]
        ]
    ];

      $chartArticulosByCat = json_encode($chartArticulosByCat);

    } catch (\Throwable $th) {
      //error al consultar las ventas de articulos
    }


    // GRAFICA ARTICULOS VS ATLLER
    //GRAFICA VENTAS POR SUCURSAL
    $chartArtiVSRepa = [
      'type' => 'pie',
      'data' => [
        'labels' => ['Articulos','Reparaciones'],
        'datasets' => [
          [
            'data' => [$ventasTotalesArticulos,$ventasTotalesReparacion]
          ]
        ]
      ],
      'options' => ['responsive'=>true]
    ];
    $chartArtiVSRepa  = json_encode($chartArtiVSRepa);

    $ventasTotalesMes = $ventasTotalesArticulos + $ventasTotalesReparacion;
    // echo $logoEmpresa;
  ?>
</head>
<body>
  <div class="container">
    <!-- HEADER -->
    <div class="report-header mb-4">
      <div class="logo-box">
         <?php echo $nombreEmpresa; ?>
      </div>
      <div>
        <h3 class="mb-0">REPORTE FINANCIERO</h3>
        <div class="small-muted">Agosto 2025 — Talleres y ventas de accesorios</div>
      </div>
      <div class="ms-auto d-flex gap-2 align-items-center">
        <div class="chip"><i class="bi bi-calendar-event"></i>&nbsp; Mes: Agosto 2025</div>
        <div class="small-muted">Última actualización: <strong id="lastUpdate">06/09/2025 - 23:54:08</strong></div>
      </div>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
      <div class="col-md-6 col-lg-3">
        <div class="card-light kpi">
          <div class="label small-muted">Ventas Mensuales</div>
          <div class="value" id="kpiSales">$<?php echo $totalVentaText; ?></div>
          <div class="small-muted mt-2" id="kpiSalesText">Total de ventas en el mes</div>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="card-light kpi">
          <div class="label small-muted">Gastos Mensuales</div>
          <div class="value" id="kpiExpenses">$<?php echo $totalGastoText; ?></div>
          <div class="small-muted mt-2" id="kpiExpensesText">Gastos operativos en el mes</div>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="card-light kpi">
          <div class="label small-muted">Utilidad Operativa</div>
          <div class="value" id="kpiProfit">$<?php echo $utilidadText; ?></div>
          <div class="small-muted mt-2" id="kpiProfitText">Ventas - Gastos operativos</div>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="card-light kpi">
          <div class="label small-muted">Reparaciones (totales)</div>
          <div class="value" id="kpiRepairs"><?php echo $numReparacionesText; ?></div>
          <div class="small-muted mt-2" id="kpiRepairsText">Trabajos registrados</div>
        </div>
      </div>
    </div>

    <!-- Chart: ventas diarias -->
    <div class="row g-4 mb-4">
      <div class="col-lg-12">
        <div class="card-light chart-card p-3">
          <div class="section-title mb-2">
            <h5 class="mb-0">Ventas diarias del mes</h5>
            <div class="small-muted">Interactúa la leyenda para mostrar/ocultar series</div>
          </div>
          <canvas id="dailySalesChart" style="height:320px;"></canvas>
          <p id="textSalesTotal" class="mt-3 text-paragraph" style="text-align:justify;">
            Las ventas totales de <?php echo $mesNombre; ?> <strong>alcanzaron $<?php echo $totalVentaText; ?>
            Representando un <?php echo $cambio; ?> del  <?php echo number_format($variacion,2) ?>% </strong>
            comparado con <?php echo $mesAnterior; ?> donde las ventas alcanzaron 
            $<?php echo $totalAnteriorText; ?>. <strong>El dia de mayor venta fue el <?php echo explode("-",$fechaVentaMax)[2]; ?> 
            de <?php echo $mesNombre; ?> con un total de <?php echo $numVentasMax; ?> ventas, acumulando un total de 
            $<?php echo number_format($ventaMaxima,2); ?></strong> en comparacion con el 
            dia de menor venta el cual fue el <?php echo explode("-",$fechaVentaMin)[2]." de ".$mesNombre; ?> con un total de <?php echo $numVentasMin; ?> 
            ventas y un acumulado de $<?php echo number_format($ventaMinima,2); ?>. El promedio de ventas diarias fue 
            de <?php echo number_format($promedioDiario,2); ?>. Este mes muestra una tendencia <?php echo $tendencia; ?> 
            de crecimiento con un <?php echo $cambio; ?> del <?php echo number_format($variacion,2) ?>%.
          </p>
        </div>
      </div>


      <!-- Ventas por sucursal texto -->
      <?php 
        if($nSuc > 1){
          //tiene mas de 1 sucursal, mostramos el estadistico de sucursales
          ?>
          <div class="col-lg-8">
            <div class="card-light chart-card p-3">
              <div class="section-title mb-2">
                <h5 class="mb-0">Ventas Por Sucursal</h5>
              </div>
              <p id="textSalesTotalSucursal" class="mt-3 text-paragraph" style="text-align:justify;">
                <?php 
                  // print_r($sucursales);
                  echo "En el mes de $mesNombre del $anio, las ventas de $nombreEmpresa se distribuyeron 
                  entre $nSuc sucursal(es) ubicadas en $ubicaciones a continuacion, se presentan los detalles y 
                  analisis de las ventas por sucursal:<br><br>
                  
                  <strong>Ventas Totales por Sucursal</strong><br>
                  <ul>$listSucursales</ul>";
                ?>
              </p>
            </div>
          </div>
          <!-- ventas por sucursal -->
          <div class="col-lg-4">
            <div class="card-light p-3">
              <div class="section-title">
                <h6 class="mb-0">Ventas por sucursal</h6>
              </div>
              <canvas id="salesByBranchChart" style="height:220px;"></canvas>
            </div>
          </div>
        </div>


        <!-- Ventas por sucursal texto -->
        <div class="col-lg-12 mb-4">
            <div class="card-light chart-card p-3">
              <div class="section-title mb-2">
                <h5 class="mb-0">Resumen Ventas Por Sucursal</h5>
              </div>
              <p id="textSalesTotalSucursal2" class="mt-3 text-paragraph" style="text-align:justify;">
                <?php 
                  // print_r($sucursales);
                  if($nSuc > 1){
                    echo "Las ventas totales consolidadas alcanzaron $$totalVentaText distribuidas entre $nSuc sucursales. 
                    La sucursal de ".$sucursales[0]['nombre']." lideró con $".number_format($sucursales[0]['montoVendido'],2)." 
                    lo que representa un ".number_format($porceMayorSuc,2)."% del total de ventas ".$textoAuxMayorSuc;
                  }
                ?>
              </p>
            </div>
          </div>
          <?php
        }
      ?>
      

    <!-- Servicios registrado y por estatus -->
    <div class="row g-4 mb-4">
      <div class="col-lg-12">
        <div class="card-light chart-card p-3">
          <div class="section-title mb-2">
            <h5 class="mb-0">Servicios de taller registrados</h5>
            <div class="small-muted">Interactúa la leyenda para mostrar/ocultar series</div>
          </div>
          <canvas id="servicesChartBar" style="height:320px;"></canvas>
          <p id="textSalesTotal" class="mt-3 text-paragraph" style="text-align:justify;">
            <?php 
              echo "Durante $mesNombre se registraron un total de <strong>$totRepa servicios tecnicos nuevos</strong> con un
              comportamiento operativo significativo. Del total de servicios <strong>".number_format($estatusTrabajos['Cobrado'])." fueron efectivamente 
              cobrados representando un ".number_format($eficiencia,2)."% de eficiencia operativa.</strong> un total
              de ".$estatusTrabajos['Finalizado']." fueron finalizados y esperan ser recogidos, mientras que ".$estatusTrabajos['En Espera'].
              " permanecen en proceso de atención".$cancelado." El top 5 de servicios mas solicitados fueron: ".$masSolicitados." El 
              taller mostró una <strong>capacidad de resolucion ".$resolucion."</strong>.";
            ?>
          </p>
        </div>
      </div>

      <!-- <div class="col-lg-8">
        <div class="card-light p-3 chart-card">
          <div class="section-title">
            <h6 class="mb-0">Servicios registrados en el taller</h6>
            <div class="small-muted">Cantidad y tendencia semanal</div>
          </div>
          <canvas id="servicesChart" style="height:260px;"></canvas>
          <p id="textServicesRegistered" class="mt-3 text-paragraph"></p>
        </div>
      </div> -->

      

      <!-- Tabla de trabajos cobrados -->
      <div class="col-lg-12">
        <div class="card-light chart-card p-3">
          <div class="section-title mb-2">
            <h5 class="mb-0">Servicios Cobrados en <?php echo $mesNombre;?></h5>
            <div class="small-muted">Interactúa la leyenda para mostrar/ocultar series</div>
          </div>
          <div class="row">
          <div class="col-sm-12 col-md-8">
            <table class="table table-striped mt-5">
              <thead>
                <tr class="table-dark">
                  <th class="text-white">Nombre servicio</th>
                  <th class="text-white">No. Servicios</th>
                  <th class="text-white">Monto cobrado </th>
                </tr>
              </thead>
              <tbody class="">
                <?php 
                  echo $tablaTrabajos."<tr class='fs-5'>
                    <td class='text-end'>Totales</td>
                    <td class='text-center'>$totalCobrados</td>
                    <td>$".number_format($totalMontoCobro,2)."</td>
                  </tr>";

                ?>
              </tbody>
            </table>
          </div>

          <div class="col-md-4 col-lg-4 mt-5">
            <div class="section-title">
              <h6 class="mb-0r">Servicios por estatus</h6>
            </div>
            <canvas id="servicesStatusChart" style="height:220px;"></canvas>
          </div>
            
          </div><!--FIN ROW-->
          
          
        </div>
      </div>




      
    </div>

    <!-- articulos por categoria y top articulos -->
    <div class="row g-4 mb-4">
    <div class="col-lg-12">
        <div class="card-light p-3">
          <div class="section-title">
            <h6 class="mb-0">Venta de artículos (por categoría)</h6>
            <div class="small-muted">Cargadores, fundas, pantallas, baterías</div>
          </div>
          <canvas id="articlesSalesChartCat" style="height:260px;"></canvas>
          <p id="textArticlesSalesChart" class="mt-3 text-paragraph" style="text-align:justify;">
            <?php 
              echo "Durante el mes de $mesNombre el comportamiento de ventas de los articulos 
              mostró un rendimiento notable, con un <strong>total de ".number_format($totalArticulos)." 
              articulos vendidos</strong>, generando <strong>ingresos por $".number_format($ingresosArticulos,2).".</strong> 
              Este desempeño resalta el interes de los clientes en una variedad de productos 
              disponibles en la tienda.<br> 
              Entre las categorias mas destacadas, tenemos $textoCategorias.";
            ?>
          </p>
        </div>
      </div>

      <div class="col-lg-12">
        <div class="card-light p-3">
          <div class="section-title">
            <h6 class="mb-0">Top 7 Articulos Vendidos</h6>
          </div>
          <canvas id="articlesByCategoryChart" style="height:260px;"></canvas>
          <p id="textSalesByCategory" class="mt-3 text-paragraph" style="text-align:justify;">
            <?php 
              echo "El comportamiento en la venta de articulos muestra una clara tendencia 
              en la categoria de ".$categoriasLabels[0].", posicionandola como la mas vendida 
              del mes de ".$mesNombre.", dentro del Top 7 de articulos, mostraremos cuales 
              fueron los articulos mas vendidos independientemente de su categoria.<br> 
              Podemos observar como <strong>en primer lugar tenemos a ". 
              $top7ArticulosLabel[0].",</strong> con un total de ".number_format($top7Cantidad[0])." 
              unidades vendidas en el mes, <strong>en segundo lugar tenemos a ".$top7ArticulosLabel[1]."</strong> 
              con un total de ".number_format($top7Cantidad[1])." articulos vendidos.";
            ?>
          </p>
        </div>
      </div>

      
    </div>

    <!-- comparativa servicios vs articulos -->
    <div class="row g-4 mb-4">
      <div class="col-lg-6 col-md-6">
        <div class="card-light p-3">
          <div class="section-title">
            <h6 class="mb-0">Articulos VS Reparaciones</h6>
          </div>
          <canvas id="repaVSArtiChart" style="height:260px;"></canvas>
          <!-- <p id="textTopItems" class="mt-3 text-paragraph"></p> -->
        </div>
      </div>

      <div class="col-6">
        <div class="card-light p-3">
          <div class="section-title">
            <h6 class="mb-0">Resumen</h6>
          </div>
          <!-- <canvas id="servicesVsArticlesChart" style="height:360px;"></canvas> -->
          <p id="textServicesVsItems" class="mt-3 text-paragraph" style="text-align:justify;">
            <?php 
            $porceTaller = ($ventasTotalesReparacion/$ventasTotalesMes) * 100;
            $porceArti = ($ventasTotalesArticulos/$ventasTotalesMes) * 100;
              echo "Durante el mes de $mesNombre, las sucursales de $nombreEmpresa registraron un total 
              de ".number_format($numeroTotalesReparacion)." servicios de taller, generando 
              ingresos de $".number_format($ventasTotalesReparacion)." En contraste, las ventas 
              de articulos alcanzaron $".number_format($ventasTotalesArticulos)." con 
              ".number_format($numeroTotalesArticulosVendidos)." unidades vendidas. <br>

              Las ventas totales de la empresa alcanzaron ".number_format($ventasTotalesMes,2)." con una 
              distribucion significativa entre ventas de articulos y servicios de taller. 
              El segmento de articulos representó el ".number_format($porceArti,2)."% de los 
              ingresos con ".number_format($ventasTotalesArticulos,2)." y ".
              number_format($numeroTotalesArticulosVendidos)." unidades comercializadas. En 
              contraste el area de taller genero el ".number_format($porceTaller,2)."% de los ingresos 
              con $".number_format($ventasTotalesReparacion,2)." provenientes de ".
              number_format($numeroTotalesReparacion)." servicios tecnicos.";
              
            ?>
          </p>
        </div>
      </div>
    </div>

    <!-- tabla detalle y resumen -->
    

    <footer class="text-center small text-muted mb-5">PostRepair · Reporte generado automáticamente</footer>
  </div>

  <!-- ================= SCRIPT: datos de prueba, cálculos y gráficos ================= -->
  <script>
    /*************************************************************************
     * DATOS DE EJEMPLO (reemplázalos con JSON desde PHP/MySQL)
     * Estructuras simples: currentMonth / previousMonth con:
     * - days: [{date:'2025-08-01', sales:..., repairs:..., accessories:...}, ...]
     * - branches: {Central:..., Norte:..., Sur:...}
     * - statuses: {Pendiente:..., 'En Proceso':..., Completado:...}
     * - categories: {Cargadores:..., Fundas:..., Pantallas:..., Baterias:...}
     * - topItems: [{name, units, revenue, marginPct}, ...]
     *************************************************************************/
    

    // Fill header info
    // document.getElementById('lastUpdate').textContent = new Date().toLocaleString();
    // document.getElementById('kpiSales').textContent = formatMoney(totalSalesCurr);
    // document.getElementById('kpiExpenses').textContent = formatMoney(expensesCurrent);
    // document.getElementById('kpiProfit').textContent = formatMoney(profit);
    // document.getElementById('kpiRepairs').textContent = totalRepairsCurr.toLocaleString();

    /****************************
     * TEXT TEMPLATES (GENERIC)
     * These texts are generic and can be used as base paragraphs.
     ****************************/
    // function formatMoney(n){ return '$' + Number(n).toLocaleString(); }

    // function textVentasTotales(){
    //   const g = growthPct;
    //   return `Las ventas totales del mes alcanzaron ${formatMoney(totalSalesCurr)}. Esto representa ${g >= 0 ? '+' : ''}${g.toFixed(1)}% respecto al mes anterior (${formatMoney(prevAgg.sales)}). Revisa las fechas con mayor movimiento en el gráfico de ventas diarias para identificar picos y campañas efectivas.`;
    // }

    // function textVentasPorSucursal(){
    //   const entries = Object.entries(currentMonth.branches).map(([k,v])=>`${k}: ${formatMoney(v)}`);
    //   return `Ventas por sucursal — ${entries.join(' · ')}. Prioriza reposición y promociones en sucursales con menor desempeño para mejorar cobertura.`;
    // }

    // function textServiciosRegistrados(){
    //   return `Se registraron ${totalRepairsCurr} servicios en el mes. El ticket promedio por reparación fue de ${formatMoney(avgTicket)}. Ajusta horarios y personal según semanas con mayor carga.`;
    // }

    
    // function textVentasPorCategoria(){
    //   const totalCat = Object.values(currentMonth.categories).reduce((a,b)=>a+b,0);
    //   const parts = Object.entries(currentMonth.categories).map(([k,v])=>`${k} (${Math.round((v/totalCat)*100)}%)`);
    //   return `Las ventas por categoría totalizan ${formatMoney(totalCat)}. Principales: ${parts.join(' · ')}. Considera promociones cruzadas en categorías con menor participación.`;
    // }

    // function textTopArticulos(){
    //   return `Top artículos: ${currentMonth.topItems.map(t=>`${t.name} (${t.units} uds, ${formatMoney(t.revenue)})`).join(' · ')}. Mantén stock de estos para evitar rupturas.`;
    // }

    // function textComparativaServiciosVsArticulos(){
    //   const repairs = totalRepairsCurr;
    //   const accessories = accessorySalesCurr;
    //   return `Comparativa: ${repairs} servicios vs ${formatMoney(accessories)} en ventas de artículos. Las acciones combinadas (servicio + accesorio) ayudan a incrementar el ticket promedio.`;
    // }

    // function textResumenGeneral(){
    //   return `Resumen: Ventas ${formatMoney(totalSalesCurr)} (${growthPct>=0?'+':''}${growthPct.toFixed(1)}% vs mes anterior), Utilidad estimada ${formatMoney(profit)}, ${totalRepairsCurr} servicios registrados. Recomendación: ${generateRecommendation()}.`;
    // }

    // function generateRecommendation(){
    //   if(growthPct >= 8) return 'Aumentar stock de top artículos y evaluar promociones pagadas';
    //   if(growthPct < 0 && accessoryPct < 20) return 'Promocionar paquetes servicio + accesorio para elevar ticket';
    //   if(growthPct < 0) return 'Revisar precios y campañas de retención';
    //   return 'Mantener estrategia y optimizar inventario';
    // }

    // Insert text paragraphs
    // document.getElementById('textSalesTotal').textContent = textVentasTotales();
    // document.getElementById('textSalesByBranch').textContent = textVentasPorSucursal();
    // document.getElementById('textServicesRegistered').textContent = textServiciosRegistrados();
    // document.getElementById('textSalesByCategory').textContent = textVentasPorCategoria();
    // document.getElementById('textTopItems').textContent = textTopArticulos();
    // document.getElementById('textServicesVsItems').textContent = textComparativaServiciosVsArticulos();
    // document.getElementById('summaryText').textContent = textResumenGeneral();

    /****************************
     * Fill products table
     ****************************/
    // const products = currentMonth.topItems.map(p => ({ name: p.name, units: p.units, revenue: p.revenue, margin: (p.marginPct || 35) + '%' }));
    // const tbody = document.getElementById('productsTableBody');
    // products.forEach(p=>{
    //   const tr = document.createElement('tr');
    //   tr.innerHTML = `<td>${p.name}</td>
    //                   <td class="text-end">${p.units}</td>
    //                   <td class="text-end">${formatMoney(p.revenue)}</td>
    //                   <td class="text-end">${p.margin}</td>`;
    //   tbody.appendChild(tr);
    //   // console.log('si');
    // });

    /****************************
     * CREATE CHARTS
     ****************************/
    // Daily sales chart (bars) - compare current vs previous (align by index)
    // 🔹 Array global donde guardamos las instancias
const charts = {};

// 🔹 Función para crear o actualizar
function createOrUpdateChart(id, config) {
  const ctx = document.getElementById(id);
  if (!ctx) return;

  // Destruir gráfica anterior si existe
  if (charts[id]) {
    charts[id].destroy();
  }

  // Crear nueva instancia
  charts[id] = new Chart(ctx, config);
}

// 🔹 Esperar que el DOM esté listo
document.addEventListener("DOMContentLoaded", () => {

  // ========================
  // 1. Ventas Totales (Ejemplo: barra comparativa)
  // ========================
  // createOrUpdateChart("dailySalesChart", {
  //   type: "bar",
  //   data: {
  //     labels: ["Mes Anterior", "Mes Actual"],
  //     datasets: [{
  //       label: "Ventas Totales ($)",
  //       data: [12000, 15000], // ← tus datos originales
  //       backgroundColor: ["rgba(143,209,255,0.7)", "rgba(11,116,209,0.85)"]
  //     }]
  //   },
  //   options: { responsive: true }
  // });
    const chartData = <?php echo $chartDailySales; ?>;
    
    const ctx = document.getElementById('dailySalesChart').getContext('2d');
    new Chart(ctx, {
        type: chartData.type,
        data: chartData.data,
        options: chartData.options
    });

  // ========================
  // 2. Ventas por Sucursal
  // ========================
  // createOrUpdateChart("salesByBranchChart", {
  //   type: "pie",
  //   data: {
  //     labels: ["Sucursal Centro", "Sucursal Norte", "Sucursal Sur"],
  //     datasets: [{
  //       data: [5000, 4000, 6000],
  //       backgroundColor: ["#0B74D1", "#8FD1FF", "#444"]
  //     }]
  //   },
  //   options: { responsive: true }
  // });
  const chartArtiVSRepa = <?php echo $chartArtiVSRepa; ?>;
    
  const ctxVS = document.getElementById('repaVSArtiChart').getContext('2d');
  new Chart(ctxVS, {
      type: chartArtiVSRepa.type,
      data: chartArtiVSRepa.data,
      options: chartArtiVSRepa.options
  });

  // SERVICIOS NUEVOS DEL MES
  const chartServicios = <?php echo $chartServicios; ?>;
    
  const ctxServ = document.getElementById('servicesChartBar').getContext('2d');
  new Chart(ctxServ, {
      type: chartServicios.type,
      data: chartServicios.data,
      options: chartServicios.options
  });




  // ========================
  // 4. Servicios por Estatus
  // ========================
  // createOrUpdateChart("servicesStatusChart", {
  //   type: "doughnut",
  //   data: {
  //     labels: ["Pendiente", "En Proceso", "Completado"],
  //     datasets: [{
  //       data: [20, 35, 95],
  //       backgroundColor: ["#8FD1FF", "#0B74D1", "#444"]
  //     }]
  //   },
  //   options: { responsive: true }
  // });
  const chartEstatusTrab = <?php echo $chartEstatusTrabajo; ?>;
    
  const ctxEst = document.getElementById('servicesStatusChart').getContext('2d');
  new Chart(ctxEst, {
      type: chartEstatusTrab.type,
      data: chartEstatusTrab.data,
      options: chartEstatusTrab.options
  });

  // GRAFICA VENTA DE ARTICULOS POR CATEGORIA
  const chartArticulosCat = <?php echo $chartArticulosByCat; ?>;
    
  const ctxCatArti = document.getElementById('articlesSalesChartCat').getContext('2d');
  new Chart(ctxCatArti, {
      type: chartArticulosCat.type,
      data: chartArticulosCat.data,
      options: chartArticulosCat.options
  });

  // ========================
  // 5. Artículos por Categoría
  // ========================
  const chartTop7Articulos = <?php echo $chartTop7Articulos; ?>;
    
  const ctxTop7 = document.getElementById('articlesByCategoryChart').getContext('2d');
  new Chart(ctxTop7, {
      type: chartTop7Articulos.type,
      data: chartTop7Articulos.data,
      options: chartTop7Articulos.options
  });


  const chartDataSuc = <?php echo $chartSucursales; ?>;
    
  const ctxSuc = document.getElementById('salesByBranchChart').getContext('2d');
  new Chart(ctxSuc, {
      type: chartDataSuc.type,
      data: chartDataSuc.data,
      options: chartDataSuc.options
  });

  // createOrUpdateChart("articlesByCategoryChart", {
  //   type: "bar",
  //   data: {
  //     labels: ["Cargadores", "Fundas", "Audífonos", "Pantallas"],
  //     datasets: [{
  //       label: "Ventas por Categoría ($)",
  //       data: [3000, 2500, 4000, 2000],
  //       backgroundColor: "rgba(11,116,209,0.7)"
  //     }]
  //   },
  //   options: { responsive: true }
  // });

  // ========================
  // 6. Top Artículos Vendidos (horizontal)
  // ========================
  // createOrUpdateChart("topArticlesChart", {
  //   type: "bar",
  //   data: {
  //     labels: ["Cargador USB-C", "Funda iPhone", "Audífonos BT", "Protector Pantalla"],
  //     datasets: [{
  //       label: "Unidades Vendidas",
  //       data: [120, 95, 80, 75],
  //       backgroundColor: "#0B74D1"
  //     }]
  //   },
  //   options: {
  //     responsive: true,
  //     indexAxis: "y"
  //   }
  // });

  // ========================
  // 7. Servicios vs Artículos
  // ========================
  // createOrUpdateChart("servicesVsArticlesChart", {
  //   type: "bar",
  //   data: {
  //     labels: ["Servicios", "Artículos"],
  //     datasets: [{
  //       label: "Comparativa",
  //       data: [150, 320],
  //       backgroundColor: ["#0B74D1", "#8FD1FF"]
  //     }]
  //   },
  //   options: { responsive: true }
  // });

});

  // let texto = document.getElementById('textSalesTotal').textContent;

  // let datos = new FormData();
  // datos.append('texto',texto);

  // fetch('../includes/solicitud-IA.php',{
  //   method: 'POST',
  //   body: datos
  // }).then(function(res){
  //   return res.json();
  // }).then(function(result){
  //   console.log(result);
  // })

    // (No infinite loops) - charts created once. If you fetch new data, call updateCharts(newPrev, newCurr).

    /****************************
     * OPTIONAL: function to update charts with real data (call after fetching)
     * Example: updateCharts(prevObj, currObj)
     ****************************/
    // function updateCharts(prev, curr){
    //   // Update KPIs, texts and datasets — straightforward; not automatically triggered anywhere
    //   // Implement as needed when integrating with backend.
    //   console.warn('updateCharts() called — integrate this with your backend to update charts dynamically.');
    // }

    /****************************
     * FINAL: (already populated texts above)
     ****************************/
  </script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
