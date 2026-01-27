
<!DOCTYPE html>
<html lang="en"> 
<?php
// session_start();


 	include("includes/head.php");
?>

<body class="app">   	
  <?php
    include("includes/header.php");
    include("includes/empresas.php");
    include("includes/conexion.php");
    // include("includes/cliente.php");
    // include("includes/ventas.php");

    //verificamos que este la informacion del cliente y pertenesca a la emprsa

  ?>
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    
			    <h1 class="app-page-title">Validacion de Inventario</h1>
			    
			    
			        <div class="col-12 col-lg-12">
				        
              <?php 
                $sqlVal1 = "SELECT * FROM ARTICULOS WHERE empresaID = '$idEmpresaSesion'";
                $queryVal1 = mysqli_query($conexion, $sqlVal1);
                $nProds = 0;
                while($fetchVal1 = mysqli_fetch_assoc($queryVal1)){
                  //verificamos las sucursales para ver que existan
                  $sqlVal3 = "SELECT * FROM SUCURSALES WHERE empresaSucID = '$idEmpresaSesion' AND
                  estatusSuc = '1'";
                  $queryVal3 = mysqli_query($conexion, $sqlVal3);
                  while($fetchVal3 = mysqli_fetch_assoc($queryVal3)){
                    $idSuc = $fetchVal3['idSucursal'];
                    $idArticulo = $fetchVal1['idArticulo'];
                    $sqlVal2 = "SELECT * FROM ARTICULOSUCURSAL WHERE articuloID = '$idArticulo' and sucursalID = '$idSuc'";
                    $queryVal2 = mysqli_query($conexion, $sqlVal2);
                    if(mysqli_num_rows($queryVal2) == 0){
                      //el articulo no existe, por lo que lo tenemos que insertar
                      $sqlInsert = "INSERT INTO ARTICULOSUCURSAL (articuloID,sucursalID,existenciaSucursal) 
                      VALUES ('$idArticulo','$idSuc','0')";
                      try {
                        $queryInsert = mysqli_query($conexion, $sqlInsert);
                        echo "Articulo Corregido<br>";
                        $nProds++;
                      } catch (\Throwable $th) {
                        echo "error: ".$th;
                      }
                    }
                  }//fin del while val 3

                  
                }//fin del while 

                echo "Se procesaron y corrigieron: <strong>".$nProds."</strong> productos mal registrados.";
                ?>
                
			        </div><!--//col-->

              
          <hr class="my-4">
        
          

			    
	    
	    <?php 
        include("includes/footer.php");
      ?>
    </div><!--//app-wrapper-->    					

 
    <!-- Javascript -->          
    <script src="assets/plugins/popper.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>


    
    <!-- Page Specific JS -->
    <script src="assets/js/app.js"></script> 
    <script src="assets/js/swetAlert.js"></script>
    <script src="assets/js/verCliente.js"></script>
    <script src="assets/js/validaDispositivo.js"></script>
</body>
</html> 

