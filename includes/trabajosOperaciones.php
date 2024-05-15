<?php 
  session_start();

  if(!empty($_SESSION['usuarioPOS'])){
    //se detecto la sesion, cargamos las funciones
    include("empresas.php");
    include("conexion.php");
    include("usuarios.php");
    include("trabajos.php");

    $usuario = $_SESSION['usuarioPOS'];
    $empresa = datoEmpresaSesion($usuario,"id");
		$empresa = json_decode($empresa);
		$idEmpresaSesion = $empresa->dato;

    $dataUSer = getDataUser($usuario,$idEmpresaSesion);
		$dataUSer = json_decode($dataUSer);
		$idSucursalN = $dataUSer->sucursalID;
    $idUsuario = $dataUSer->idUsuario;
    

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
    }elseif(!empty($_POST['clienteTrabajo'])){
      //seccion para dar de alta un nuevo trabajo
      //verificamos que esten capturados todos los campos
      $requeridos = ['clienteTrabajo','fechaServicio','tipoDispositivo','tipoServicio','marcaServicio',
      'modeloServicio','numberDevice','descripcionProblema'];
      $pasa = 0;

      for ($i=0; $i < count($requeridos); $i++) { 
        $valorCampo = $_POST[$requeridos[$i]];

        if(empty($valorCampo)){
          //campo vacio
          $pasa = 1;
        }
      }//fin del for

      if($pasa == 0){
        //captuiramos los datos en variables
        $cliente = $_POST['clienteTrabajo'];
        $fechaAlta = $_POST['fechaServicio'];
        $tipoDispo = $_POST['tipoDispositivo'];
        $tipoServ = $_POST['tipoServicio'];
        $marca = $_POST['marcaServicio'];
        $modeloServicio = $_POST['modeloServicio'];
        $serieDispo = $_POST['numberDevice'];
        $accesorios = $_POST['accesorioServicio'];
        $problema = $_POST['descripcionProblema'];
        $observacion = $_POST['observServicio'];
        $contraDispo = $_POST['contraDisp'];
        $fechaEntrega = $_POST['fechaEntrega'];
        $costoServ = $_POST['costoServicio'];
        $anticipo = $_POST['anticipoServicio'];
        //verificamos que la fecha no sea pasada
        $fechaActual = date('Y-m-d');

        if($fechaAlta <= $fechaActual){
          if($fechaEntrega >= $fechaActual){
            //las fechas son correectas, podemos registrar el producto
            $nuevoTrabajo = altaTrabajo($cliente,$fechaAlta,$tipoDispo,$tipoServ,$marca,$modeloServicio,
            $serieDispo,$accesorios,$problema,$observacion,$contraDispo,$fechaEntrega,$costoServ,$anticipo,
            $usuario,$idSucursalN,$idEmpresaSesion,$idUsuario);

            echo $nuevoTrabajo;
          }else{
            //fecha de entrega incorrecta
            $res = ['status'=>'error','mensaje'=>'Asegurate de ingresar una fecha de entrega correcta.'];
            echo json_encode($res);
          }
        }else{
          //fecha de alta incorrecta
          $res = ['status'=>'error','mensaje'=>'No se pueden ingresar fechas futuras.'];
          echo json_encode($res);
        }
        
      }else{
        //ocurrieron errores en los campos
        $res = ['status'=>'error','mensaje'=>'Asegurate de capturar todos los campos requeridos'];
        echo json_encode($res);
      }

    }
  }
?>