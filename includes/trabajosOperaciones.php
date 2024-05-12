<?php 
  session_start();

  if(!empty($_SESSION['usuarioPOS'])){
    //se detecto la sesion, cargamos las funciones
    include("empresas.php");
    include("conexion.php");
    include("usuarios.php");

    $usuario = $_SESSION['usuarioPOS'];
    $empresa = datoEmpresaSesion($usuario,"id");
		$empresa = json_decode($empresa);
		$idEmpresaSesion = $empresa->dato;

    $dataUSer = getDataUser($idEmpresaSesion,$usuario);
		$dataUSer = json_decode($dataUSer);
		$idSucursalN = $dataUSer->sucursalID;

    //verificamos la existencia de metodos
    if(!empty($_POST['servCheck'])){
      //verificamos la existencia y precio de un servicio
      $idServ = $_POST['servCheck'];

      $sql = "SELECT * FROM SERVICIOS WHERE empresaID = '$idEmpresaSesion' 
      AND idServicio = '$idServ'";
      try {
        $query = mysqli_query($conexion, $sql);

        if(mysqli_num_rows($query) == 1){
          $fetch = mysqli_fetch_assoc($query);
          $precio = "";
          if($fetch['precioFijo'] == 1){
            $precio = $fetch['precioFijo'];
          }else{
            $precio = "0";
          }
          $res = ['status'=>'ok','mensaje'=>'operationComplete','data'=>$precio];
          echo json_encode($res);
        }else{
          //servicio no localizado
          $res = ['status'=>'error','mensaje'=>'No se localizo el servicio'];
          echo json_encode($res);
        }
      } catch (\Throwable $th) {
        //error de consulta BD
        $res = ['status'=>'error','mensaje'=>'Ha ocurrido un error al consultar el servicio'];
        echo json_encode($res);
      }
    }
  }
?>