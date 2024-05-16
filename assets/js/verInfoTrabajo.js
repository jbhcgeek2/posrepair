
let estatus = document.getElementById('estatusTrabajo');
estatus.addEventListener('click', function(){
  //metodo para realizar el cambio de estatus de un trabajo
  //primero preguntamos si desea cambiar el estatus
  Swal.fire({
    title: 'Cambio de estatus',
    text: 'Deseas cambiar el estatus del trabajo?',
    icon: 'warning',
    showDenyButton: true,
    confirmButtonText: 'Actualizar',
    denyButtonText: 'Cancelar'
  }).then((result)=>{
    if(result.isConfirmed){
      //enviamos el nuevo estatus
      let nuevoEstatus = estatus.value;
      let trabajo = document.getElementById('datoTrabajo').value;
      let datos = new FormData();

      datos.append('trabajoStatus',trabajo);
      datos.append('nuevoStatus',nuevoEstatus);

      let envio = new XMLHttpRequest();
      envio.open('POST','../includes/trabajosOperaciones.php',false);
      envio.send(datos);

      if(envio.status == 200){
        let res = JSON.parse(envio.responseText);
        if(res.status == 'ok'){
          Swal.fire(
            'Estatus Actualizado',
            'Se completo la actualizacion',
            'success'
          ).then(function(){
            location.reload();
          })
        }else{
          let err = res.mensaje;
          Swal.fire(
            'Ha ocurrido un error',
            'Verificar: '+err,
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

      
    }else{
      //se cancela, no hacemos nada
    }
  })
})