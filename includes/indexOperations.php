
<?php 

session_start();

if(!empty($_SESSION['usuarioPOS'])){

  if(!empty($_POST['getVentasWeek'])){

    $hoy = date('N'); // Obtener el número del día de la semana actual
    $diaSemana = ['1'=>'lunes', '2'=>'martes', '3'=>'miércoles', '4'=>'jueves', '5'=>'viernes', '6'=>'sábado', '7'=>'domingo'];
    $semanaActual = [];
    
    $auxFec = date('Y-m-d');
    for ($i = $hoy; $i >= 1; $i--) {
      echo $diaSemana[$i] . "<br>";
      $semanaActual[$diaSemana[$i]]=$auxFec;
      $auxFec = date('Y-m-d', strtotime($auxFec. ' - 1 days'));
    }
    $semanaActualF = asort($semanaActual);
    
    
    //Teniendo en cuenta lo anterior calcularemos la semana pasada
    $semanaPasada = [];
    $ultimoDia = $semanaActual['lunes'];
    // echo "<br> Ultio Dia, ".$ultimoDia;
    
    $auxFec2 = $ultimoDia;
    for($x = 7; $x >= 1; $x--){
      $auxFec2 = date('Y-m-d', strtotime($auxFec2. ' - 1 days'));
      $semanaPasada[$diaSemana[$x]] = $auxFec2;
      // echo $diaSemana[$x]."<br>";
    }//fin del for
    // echo "<br>=====<br>";
    $semanaPasadaF = asort($semanaPasada);
    // print_r($semanaPasada);

    $res = ["actual"=>$semanaActualF,"pasada"=>$semanaPasadaF];
    echo json_encode($res);

  }
}


?>