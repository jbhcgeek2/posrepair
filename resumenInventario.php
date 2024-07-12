<!DOCTYPE html>
<html lang="en"> 
<?php
 	include("includes/head.php");
?>

<body class="app">   
  <?php
    include("includes/header.php");
		include("includes/conexion.php");
		//este resumen solo estara habilitado para administradores
		// echo $rolUsuario;
		// echo $idEmpresaSesion;
		if($rolUsuario == "Vendedor"){
			header("Location: caja.php");
			?>
			<script>
				window.location = "caja.php";
			</script>
			<?php
		}
		
		
		//consultamos sucursales
		$sqlSuc = "SELECT * FROM SUCURSALES WHERE empresaSucID = '$idEmpresaSesion'";
		$querySuc = mysqli_query($conexion, $sqlSuc);
		$sucSql = "";
		while($fetchSuc = mysqli_fetch_assoc($querySuc)){
			if($sucSql == ""){
				$sucSql = $fetchSuc['idSucursal'];
			}else{
				$sucSql = $sucSql.",".$fetchSuc['idSucursal'];
			}
		}//fin sucursales


		$fechaHoy = date('Y-m-d');
		//realizamos las consultas para ver las ventas totales en el mes
		$sqlMonto = "SELECT a.idARticulo,a.nombreArticulo, (a.precioUnitario *
		(SELECT SUM(b.existenciaSucursal) FROM ARTICULOSUCURSAL b WHERE b.articuloID = a.idArticulo)) AS totalSuma
		FROM ARTICULOS a WHERE a.empresaID = '$idEmpresaSesion' AND a.estatusArticulo = '1'";
		try {
			$queryMonto = mysqli_query($conexion, $sqlMonto);
			$valorInventario = 0;
			while($fetchMonto = mysqli_fetch_assoc($queryMonto)){
				$valor = $fetchMonto['totalSuma'];
				$valorInventario = $valorInventario + $valor;
			}
		} catch (\Throwable $th) {
			//throw $th;
		}

		
		$sqlEntradas = "SELECT SUM(cantidad) AS total_ingresos FROM DETALLEINGRESO 
		WHERE (sucursalID IN ($sucSql)) AND MONTH(fechaMov) = MONTH(CURRENT_DATE()) 
		AND YEAR(fechaMov) = YEAR(CURRENT_DATE()) AND tipoMov = 'Entrada'";
		try {
			$queryEntradas = mysqli_query($conexion, $sqlEntradas);
			$fetchEntradas = mysqli_fetch_assoc($queryEntradas);

			$totalEntradas = $fetchEntradas['total_ingresos'];
		} catch (\Throwable $th) {
			//throw $th;
			echo $th;
			$totalEntradas = "0";
		}

		$sqlProdVenta = "SELECT SUM(b.cantidadVenta) AS ventasMes FROM VENTAS a INNER JOIN DETALLEVENTA b 
		ON b.ventaID = a.idVenta WHERE a.empresaID = '$idEmpresaSesion' AND (MONTH(a.fechaVenta) = MONTH(CURRENT_DATE()) 
		AND YEAR(a.fechaVenta) = YEAR(CURRENT_DATE())) AND b.articuloID IS NOT NULL";
		try {
			$queryProdVenta = mysqli_query($conexion, $sqlProdVenta);
			$fetchProdVenta = mysqli_fetch_assoc($queryProdVenta);

			$ventasMes = $fetchProdVenta['ventasMes'];
		} catch (\Throwable $th) {
			//throw $th;
		}


  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Resumen</h1>
			    
			    
				    
			    <div class="row g-4 mb-4">
				    <div class="col-6 col-lg-4">
					    <div class="app-card app-card-stat shadow-sm h-100">
						    <div class="app-card-body p-3 p-lg-4">
							    <h4 class="stats-type mb-1">Valor de Inventario</h4>
							    <div class="stats-figure">$<?php echo number_format($valorInventario,2); ?></div>
						    	<a class="app-card-link-mask" href="#"></a>
								</div>
					    </div><!--//app-card-->
				    </div><!--//col-->
				    
				    <div class="col-6 col-lg-4">
					    <div class="app-card app-card-stat shadow-sm h-100">
						    <div class="app-card-body p-3 p-lg-4">
							    <h4 class="stats-type mb-1">Articulos Adquiridos (mes)</h4>
							    <div class="stats-figure"><?php echo number_format($totalEntradas); ?></div>
							    
						    </div><!--//app-card-body-->
						    <a class="app-card-link-mask" href="#"></a>
					    </div><!--//app-card-->
				    </div><!--//col-->

				    <div class="col-6 col-lg-4">
					    <div class="app-card app-card-stat shadow-sm h-100">
						    <div class="app-card-body p-3 p-lg-4">
							    <h4 class="stats-type mb-1">Articulos Vendidos (mes)</h4>
							    <div class="stats-figure"><?php echo number_format($ventasMes); ?></div>
							    <div class="stats-meta">
								  </div>
						    </div><!--//app-card-body-->
						    <a class="app-card-link-mask" href="#"></a>
					    </div><!--//app-card-->
				    </div><!--//col-->

				    
			    </div><!--//row-->

			    <div class="row g-4 mb-4">
						<div class="col-12 col-lg-6">
							<div class="app-card app-card-chart h-100 shadow-sm">
								<div class="app-card-header p-3">
									<div class="row justify-content-between align-items-center">
										<div class="col-auto">
											<h4 class="app-card-title">Productos Por Categoria</h4>
										</div><!--//col-->
									</div><!--//row-->
								</div><!--//app-card-header-->
								<div class="app-card-body p-3 p-lg-4">
									<table class="table">
										<thead>
											<tr>
												<th>Categoria</th>
												<th>Existencia Articulos</th>
												<th>Valor de Venta</th>
											</tr>
										</thead>
										<tbody>
											<?php 
												// consultamos las categorias
												$sqlCat = "SELECT idCategoria,nombreCategoria,empresaId FROM CATEGORIA 
												WHERE empresaID = '$idEmpresaSesion' AND estatusCategoria = '1' ORDER BY
												nombreCategoria ASC";
												$totalTotal = 0;
												try {
													$queryCat = mysqli_query($conexion, $sqlCat);
													while($fetchCat = mysqli_fetch_assoc($queryCat)){
														$idCat = $fetchCat['idCategoria'];
														$nombreCat = $fetchCat['nombreCategoria'];
														$cantidadProds = 0;
														
														

														$sqlProd = "SELECT a.idArticulo,(SELECT SUM(b.existenciaSucursal) FROM ARTICULOSUCURSAL b
														WHERE b.articuloID = a.idArticulo) AS existencia FROM ARTICULOS a WHERE a.categoriaID = '$idCat'
														AND a.empresaID = '$idEmpresaSesion' AND a.estatusArticulo = '1'";
														try {
															$queryProd = mysqli_query($conexion, $sqlProd);
															$valorProd = 0;
															while($fetchProd = mysqli_fetch_assoc($queryProd)){
																$cant = $fetchProd['existencia'];
																$valor = $fetchProd['precioUnitario'];
																$valorProd = $valor * $cant;
																$cantidadProds = $cantidadProds + $cant;
																$totalTotal = $totalTotal + $cant;
															}//fin del while prods
														} catch (\Throwable $th) {
															//throw $th;
														}

														echo "<tr>
															<td>$nombreCat</td>
															<td>$cantidadProds</td>
															<td>$".number_format($valorProd,2)."</td>
														</tr>";

													}//fin del while
													echo "<tr>
															<td><strong>Total Global</strong></td>
															<td><strong>$totalTotal</strong></td>
														</tr>";
												} catch (\Throwable $th) {
													//throw $th;
												}
											?>
										</tbody>
									</table>
								</div><!--//app-card-body-->
							</div><!--//app-card-->
						</div><!--//col-->



						<div class="col-12 col-lg-6">
				        <div class="app-card app-card-progress-list h-100 shadow-sm">
					        <div class="app-card-header p-3">
						        <div class="row justify-content-between align-items-center">
							        <div class="col-auto">
						            <h4 class="app-card-title">Productos Mas Vendidos</h4>
							        </div><!--//col-->
						        </div><!--//row-->
					        </div><!--//app-card-header-->
					        <div class="app-card-body">
										<?php 
											$sql4 = "SELECT * FROM SUCURSALES WHERE empresaSucID = '$idEmpresaSesion'";
											$query4 = mysqli_query($conexion,$sql4);
											$sucursales = '';
											while($fetch4 = mysqli_fetch_assoc($query4)){
												$idSucursal = $fetch4['idSucursal'];
												if($sucursales == ""){
													$sucursales = $idSucursal;
												}else{
													$sucursales = $sucursales.",".$idSucursal;
												}
												
											}
											
											$sqlProd = "SELECT SUM(cantidadVenta) AS totales,
											(SELECT c.nombreArticulo FROM ARTICULOS c WHERE c.idArticulo = a.articuloID) AS nameArti FROM DETALLEVENTA a INNER JOIN SUCURSALES b 
											ON a.sucursalID = b.idSucursal WHERE a.sucursalID IN ($sucursales) AND a.articuloID > 0 group by articuloID";
											$queryProd = mysqli_query($conexion, $sqlProd);
											$totalesVentas = 0;

											while($fetch5 = mysqli_fetch_assoc($queryProd)){
												$totalesVentas = $totalesVentas + $fetch5['totales'];
											}
											// echo $totalesVentas;

											$sqlProd2 = "SELECT SUM(cantidadVenta) AS totales,
											(SELECT c.nombreArticulo FROM ARTICULOS c WHERE c.idArticulo = a.articuloID) AS nameArti FROM DETALLEVENTA a INNER JOIN SUCURSALES b 
											ON a.sucursalID = b.idSucursal WHERE a.sucursalID IN ($sucursales) AND a.articuloID > 0 group by articuloID ORDER BY totales DESC LIMIT 10";
											$queryProd2 = mysqli_query($conexion, $sqlProd2);

											while($fetch7 = mysqli_fetch_assoc($queryProd2)){
												$nombreProd = $fetch7['nameArti'];
												$ventasProd = $fetch7['totales'];
												$porcentaje = ($ventasProd / $totalesVentas) * 100;
												$porcentaje2 = number_format($porcentaje,2);
												echo '
												<div class="item p-3">
													<div class="row align-items-center">
														<div class="col">
															<div class="title mb-1 ">'.$nombreProd.' - '.$porcentaje2.'%</div>
															<div class="progress">
																<div class="progress-bar bg-success" role="progressbar" style="width: '.$porcentaje.'%;" aria-valuenow="'.$porcentaje.'" aria-valuemin="0" aria-valuemax="100"></div>
															</div>
														</div><!--//col-->

														<!--<div class="col-auto">
															<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
																<path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
															</svg>
														</div>-->
													</div><!--//row-->

													<a class="item-link-mask" href="#!"></a>
												</div><!--//item-->';
											}


										?>
							    	
							    
							    
							     
		
					        </div><!--//app-card-body-->
				        </div><!--//app-card-->
			        </div><!--//col-->

			        
			        
			    </div><!--//row-->


			    <div class="row g-4 mb-4">
						
				    


			        

							

			    </div><!--//row-->


			    
			    
		    </div><!--//container-fluid-->
	    </div><!--//app-content-->
	    
	    <footer class="">
        <div class="container text-center py-3">
              <!--/* This template is free as long as you keep the footer attribution link. If you'd like to use the template without the attribution link, you can buy the commercial license via our website: themes.3rdwavemedia.com Thank you for your support. :) */-->
          <small class="copyright">Disenado por </i> by <a class="app-link" href="https://www.tecuanisoft.com" target="_blank">TecuaniSoft</a></small>
            
        </div>
      </footer><!--//app-auth-footer-->	
	    
    </div><!--//app-wrapper-->    					

 
    <!-- Javascript -->          
    <script src="assets/plugins/popper.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>  

    <!-- Page Specific JS -->
    <script src="assets/js/app.js"></script> 

</body>
</html> 

