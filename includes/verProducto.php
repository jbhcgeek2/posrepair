<?php 

session_start();

if(!empty($_SESSION['usuarioPOS'])){
    include("empresas.php");
    include("conexion.php");
    include("usuarios.php");
    include("trabajos.php");
    include("articulos.php");

    $usuario = $_SESSION['usuarioPOS'];
    $empresa = datoEmpresaSesion($usuario,"id");
	$empresa = json_decode($empresa);
	$idEmpresaSesion = $empresa->dato;

    $dataUSer = getDataUser($usuario,$idEmpresaSesion);
	$dataUSer = json_decode($dataUSer);
	$idSucursalN = $dataUSer->sucursalID;
    $idUsuario = $dataUSer->idUsuario;
    //detectamos el metodo que desea implementar

    if(!empty($_POST['sendBusqueda'])){
        //metodo para buscar productos
        $producto = $_POST['busProds'];
        $cat = $_POST['catBus'];

        $sql = "";

        if($cat != "" && $producto != ""){
            //esta buscando por categoria y producto
            $sql = "SELECT *,(SELECT SUM(c.existenciaSucursal) FROM ARTICULOSUCURSAL 
            c WHERE c.articuloID = a.idArticulo) AS existencia FROM ARTICULOS a INNER JOIN PROVEEDORES b 
            ON a.proveedorID = b.idProveedor WHERE a.empresaID = '$idEmpresaSesion' 
            AND a.categoriaID = '$cat' AND a.nombreArticulo LIKE '%$producto%' 
            ORDER BY nombreArticulo ASC";
        }elseif($cat != "" && $producto == ""){
            //esta buscando solo por categoria
            $sql = "SELECT *,(SELECT SUM(c.existenciaSucursal) FROM ARTICULOSUCURSAL 
            c WHERE c.articuloID = a.idArticulo) AS existencia FROM ARTICULOS a INNER JOIN PROVEEDORES b 
            ON a.proveedorID = b.idProveedor WHERE a.empresaID = '$idEmpresaSesion' 
            AND a.categoriaID = '$cat' ORDER BY a.nombreArticulo ASC";
        }elseif($cat == "" && $producto != ""){
            //esta buscando solo por el nombre
            $sql = "SELECT *,(SELECT SUM(c.existenciaSucursal) FROM ARTICULOSUCURSAL 
            c WHERE c.articuloID = a.idArticulo) AS existencia FROM ARTICULOS a INNER JOIN PROVEEDORES b 
            ON a.proveedorID = b.idProveedor WHERE a.empresaID = '$idEmpresaSesion' 
            AND a.nombreArticulo LIKE '%$producto%' ORDER BY a.nombreArticulo ASC";
        }else{
            //hacemos una busqueda del nombre
            $sql = "SELECT *,(SELECT SUM(c.existenciaSucursal) FROM ARTICULOSUCURSAL 
            c WHERE c.articuloID = a.idArticulo) AS existencia FROM ARTICULOS a INNER JOIN PROVEEDORES b 
            ON a.proveedorID = b.idProveedor WHERE a.empresaID = '$idEmpresaSesion' 
            AND a.nombreArticulo LIKE '%$producto%' ORDER BY a.nombreArticulo ASC";
        }

        // $sql = "SELECT * FROM ARTICULOS WHERE empresaID = '$idEmpresaSesion' 
        // AND nombreArticulo LIKE '%$producto%'";
        try {
            $query = mysqli_query($conexion,$sql);
            $res = [];
            $x =0;
            if(mysqli_num_rows($query) > 0){
                
                while($fetch = mysqli_fetch_assoc($query)){
                    $res[$x] = $fetch;
                    $x++;
                }
            
            }else{
                //sin datos
                $res = "NoData";
            }
            $data = ["status"=>'ok',"data"=>$res];
            echo json_encode($data);
        } catch (\Throwable $th) {
            //throw $th;
            $data = ["status"=>'error',"mensaje"=>$th];
            echo json_encode($data);
        }
    }else{
        echo "metodo no detectado";
    }
}else{
    echo "sin sesion";
}

?>