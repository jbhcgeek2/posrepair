<?php 
session_start();

if(!empty($_SESSION['usuarioPOS'])){
  //verificamos ue se encuentre el datos

  if(!empty($_POST['dataTrabajo'])){
    include('includes/conexion.php');



    
    //pagna exclusiba para el envio de correos
    //tendremos que cerar una funcion para hacer el envio de correos
    //la cual llamaremos mailSend
    //$a = $correo --> guardaremos el correo del cliente
    //$b = $asunto --> el asunto del correo
    //$c = $cuerpoCorreo --> indicara el mensaje en formato html
    //$e = $subAsunto --> un mensaje corto del correo
    //$f = $mensajeEnviado --> guardara lo que queramos hacer despues de enviar el correo
    //$g = $adjunto --> indicara si el correo tendra un archivo adjunto
      //puede ser un alert de correo enviado o cualquier cosa.
    
      //IMPORTANTE
      //En caso de no poder hacer el envio de correos se debe verificar
      //la configuracion de la cuenta de google
      //y permitir el uso de aplicaciones menos seguras
    function mailSend($a,$b,$c,$e,$f,$g){
      //hacemos el include de la libreria
      
      // require "PHPMailer-master/PHPMailerAutoload.php";
      require "PHPMailer-master/PHPMailerAutoload.php";
      
      // use PHPMailer\PHPMailer\PHPMailer;
      // use PHPMailer\PHPMailer\Exception;
      // use PHPMailer\PHPMailer\SMTP;
      // require "Mailer/Mailer/src/SMTP.php";
    
      // @set_magic_quotes_runtime(false);
      // ini_set('magic_quotes_runtime', 0);
      $mail = new PHPMailer();
      $mail->SMTPDebug = false;
      $mail->isSMTP();
      $mail->Host = "smtp.hostinger.com";
      $mail->SMTPAuth = true;
      $mail->Username = "contacto@tecuanisoft.com";
      // $mail->Password = "hecj920331";
      $mail->Password = "#Benja.GeeK0";
      $mail->SMTPSecure = "ssl";
      $mail->Port = 465;
      $mail->From = "contacto@tecuanisoft.com";
      $mail->FromName = "Servicel Tepic";
      if(!empty($g)){
        //verificamos si el correo se mandara un dato adjunto
        // $mail->addStringAttachment(file_get_contents($g), 'anexo.pdf');
        if(file_exists($g)){
          $mail->AddAttachment($g);
        }
        // $mail->AddStringAttachment(file_get_contents($g), 'file.pdf', 'base64', 'application/pdf');
      }
      //$mail->AddAddress("$correo");
      $mail->AddAddress("$a");
      // $mail->AddAddress("joelbh92@gmail.com");
      $mail->IsHTML(true);
      $mail->Subject = $b;
      $link = "https://prestavale2.tecuanisoft.com/formatoPago/".$g;
      $mail->MsgHTML ("".$c."<br>
      <p style='text-align:center;font-size:x-large'><b>Atentamente</b><br>
      Servicel Tepic
      </p>
      <p style='text-align:center;'><small>Este correo es enviado automaticamente,
      por lo que no tienes que contestarlo.</small></p>
      ");
      $mail->AltBody = " ".$e." ";
      if(!$mail->send()){
      return "Mailer Error: " . $mail->ErrorInfo;
      // echo "asdasd";
      }else{
        return $f;
      }
      unset($mail);
    }//fin de la funcion
    
    
    //consultamos los datos del trabajo y del cliente
    $idTrabajo = $_POST['dataTrabajo'];

    $sql = "SELECT * FROM TRABAJOS a INNER JOIN CLIENTES b ON a.clienteID = b.idClientes 
    WHERE idTrabajo = '$idTrabajo'";
    try {
      $query = mysqli_query($conexion, $sql);
      if(mysqli_num_rows($query) == 1){
        $fetch = mysqli_fetch_assoc($query);

        $emailCliente = $fetch['emailCliente'];
        if (filter_var($emailCliente, FILTER_VALIDATE_EMAIL)) {
          $domain = substr(strrchr($emailCliente, "@"), 1); // Extraer el dominio
          if(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A")) {
            // echo "El correo electrónico es válido y el dominio existe.";
            $nombreCliente = $fetch['nombreCliente'];
            $dispositivo = $fetch['marca']." ".$fetch['modelo'];
            $estatusTra = $fetch['estatusTrabajo'];
            if($estatusTra == "Finalizado"){
              $auxMen = ", por lo que ya puede acudir a nuestra sucursal a disponer de el";
            }else{
              $auxMen = "";
            }

            $mensaje = "Estimad@ $nombreCliente,<br>
            Le informamos que la reparacion de su dispositivo $dispositivo a cambiado de
            estatus a $estatusTra $auxMen.<br>
            Puedes comunicarte a nuestro numero de atencion o redes sociales para mayor informacion.";

            $seMando = mailSend($emailCliente,"Nuevo estatus de tu reparacion",$mensaje,"Actualizacion de Estatus","operationComplete","");
            if($seMando == "operationComplete"){
              $res = ['status'=>'ok','mensaje'=>'operationComplete'];
              echo json_encode($res);  
            }else{
              $res = ['status'=>'error','mensaje'=>'Ocurrio un error inesperado, reportalo al area de soporte.'];
              echo json_encode($res);
            }
          }else{
            // echo "El correo electrónico es válido, pero el dominio no existe.";
            $res = ['status'=>'error','mensaje'=>'El dominio del correo electronico no es valido'];
            echo json_encode($res);
          }
        }else{
          // echo "El correo electrónico no es válido.";
          $res = ['status'=>'error','mensaje'=>'El correo electronico del cliente no es valido.'];
          echo json_encode($res);
        }
      }else{
        //no se localizo el trabajo
        $res = ['status'=>'error','mensaje'=>'No fue posible localizar la orden de trabajo.'];
        echo json_encode($res);
      }
    } catch (\Throwable $th) {
      //throw $th;
      $res = ['status'=>'error','mensaje'=>'Ocurrio un error inesperado, reportarlo al area de soporte.'];
      echo json_encode($res);
    }

    





  }else{
    //metodo no soportado
  }
}
?>