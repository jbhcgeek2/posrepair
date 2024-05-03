let btnAlta = document.getElementById('btnAltaUser');

btnAlta.addEventListener('click', function(){
  //verificamos que este el nombre, usuario y contra
  let name = document.getElementById('nombreUser').value;
  let usName = document.getElementById('userName').value;
  let passUser = document.getElementById('passwordUser').value;

  if(name != "" && usName != "" && passUser != ""){
    let datos = new FormData(document.getElementById('nuevoUsuarioEmp'));

    let envio = new XMLHttpRequest();
    envio.open('POST','../includes/altaUsuario.php',false);
    envio.send(datos);
  
    if(envio.status == 200){
      console.log(envio.responseText);
    }else{
      //error dfe comunicacion
      Swal.fire(
        'Servidor Inalcansable',
        'Verifica tu conexion a internet',
        'error'
      )
    }
  }else{
    //no se detecto informacion
    Swal.fire(
      'Campos invalidos',
      'Asegurate que todos los campos esten capturados',
      'error'
    )
  }

  
});