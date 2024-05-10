let btnUpdate = document.getElementById(updateServ);

btnUpdate.addEventListener('click', function(){
  //preguntamos si desea actualizar el servicio
  Swal.fire({
    title: 'Actualizar Servicio?',
    text: 'Estas seguro de actualizar el servicio?',
    iconHtml: '?',
    showDenyButton: true,
    confirmButtonText: 'Si, Actualizar',
    denyButtonText: 'Cancelar'
  }).then((result) =>{
    if(result.isConfirmed){
      //cargamos el formulario, pero antes verificamos que los datos contengan informacion
      let name = document.getElementById('nombreServ').value;
      let cat = document.getElementById('catServicio').value;
      if(cat != "" && name != ""){
        //ahora si cargamos los datos
        let datos = new FormData(document.getElementById('dataModServ'));

        let envio = new XMLHttpRequest();
        envio.open('POST','../includes/modServicios.js',false);
        envio.send(datos);

        if(envio.status == 200){
          let res = JSON.parse(envio.responseText);
          if(res.status == "ok"){
            Swal.fire(
              'Servicio Actualizado',
              'Se actualizo correctamente el servicio',
              'success'
            )
          }else{
            //ocurrio un error en la actualizacion
            let err = res.mensaje;
            Swal.fire(
              'Ha ocurrido un error en la actualizacion',
              'Verificar: '+err,
              'error'
            )
          }
        }else{
          //ocurrio un error de comunicacion
          Swal.fire(
            'Servidor Inalcansable',
            'Verifica tu conexion a internet',
            'error'
          )
        }
      }else{
        //no capturo la categoria
        Swal.fire(
          'Datos faltantes',
          'Verifica que la categoria y el nombre esten capturados',
          'error'
        )
      }
    }else{
      //no hacemos nada
    }
  })
})