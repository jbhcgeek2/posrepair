
document.addEventListener("DOMContentLoaded", function() {
  Swal.fire({
    title: 'Antes de continuar...',
    text: 'Ya se encuentra registrado el cliente?',
    icon: 'warning',
    showDenyButton: true,
    confirmButtonText: 'Ya esta registrado',
    denyButtonText: 'No, Registrar nuevo cliente'
  }).then((result)=>{
    if(result.isConfirmed){
      //ya esta registrado
    }else{
      //no esta registrado, lo damos de alta
      window.location = 'altaCliente.php';
    }
  })

  let tipoServ = document.getElementById('tipoServicio');
  tipoServ.addEventListener('change', function(){
    //buscaremos el precio sugerido del servicio
    let datoServ = new FormData();
    datoServ.append('servCheck',datoServ);

    let envioServ = new XMLHttpRequest();
    envioServ.open('POST','../includes/trabajosOperaciones.php', false);
    envioServ.send(datoServ);

    if(envioServ.status == 200){
      let res = JSON.parse(envioServ.responseText);

      if(res.status == "ok"){
        //se consulto correctamente
        document.getElementById('costoServicio').value = res.data;
      }else{
        //error de operacion
        let err = res.mensaje;
        Swal.fire(
          'Ha ocurrido un error',
          'Verifica: '+err,
          'error'
        )
      }
    }else{
      //error de servidor
      Swal.fire(
        'Servidor Inalcansable',
        'Verifica tu conexion a internet',
        'error'
      )
    }

  })

})


