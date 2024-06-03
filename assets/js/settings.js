let btnUpdateDatos = document.getElementById('btnUpdateDatos');
btnUpdateDatos.addEventListener('click', function(){
  Swal.fire({
    title: 'Actualizar Datos',
    text: 'Estas seguro de actualizar tus datos?',
    iconHtml: '?',
    showCancelButton: true,
    confirmButtonText: 'Si, actualizar',
    cancelButtonText: 'Cancelar'
  }).then((result)=>{
    if(result.isConfirmed){
      //actualizamos los datos
      let datos = new FormData(document.getElementById('datosEmpresa'));

      let envio = new XMLHttpRequest();
      envio.open('POST','../includes/updateEmpresas.php',false);
      envio.send(datos);

      if(envio.status == 200){
        let res = JSON.parse(envio.responseText);
        if(res.status == "ok"){
          //se actualizo correctamente
          Swal.fire(
            'Informacion actualizada',
            'Se completo correctamente la actualizacion',
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
        //error de comunicacion
        Swal.fire(
          'Servidor Inalcansable',
          'Verifica tu conexion a internet',
          'error'
        )
      }
    }else{
      //no hacemos nada
    }
  })
})

let btnAddCondi = document.getElementById('btnNewCondi');
btnAddCondi.addEventListener('click', function(){
  let condicion = document.getElementById('newCondicion').value;
  if(condicion != ""){
    Swal.fire({
      title: 'Registrar condicion?',
      text: 'Estas seguro de registrar la condicion de servicio?',
      icon: 'warning',
      showDenyButton: true,
      confirmButtonText: 'Si, Registrar',
      denyButtonText: 'No, Cancelar'
    }).then((result)=>{
      if(result.isConfirmed){
        //mandamos el datos
         let datos = new FormData();

         datos.append('newCondicion',condicion);

         let envio = new XMLHttpRequest();
         envio.open('POST','../includes/updateEmpresas.php',false);
         envio.send(datos);

         if(envio.status == 200){
          //verificamos la respuesta
          let res = JSON.parse(envio.responseText);
          if(res.status == "ok"){
            //se actualizo correcto
            Swal.fire(
              'Condicion Registrada',
              '',
              'success'
            ).then(function(){
              location.reload();
            })
          }else{
            //ocurrio un erro de servidor
            let err = res.mensaje;
            Swal.fire(
              'Ha ocurrido un error',
              'Verificar: '+err,
              'error'
            )
          }
         }else{
          //error de comunicacion
          Swal.fire(
            'Servidor Inalcansable',
            'Verifica tu conexion a internet',
            'error'
          )
         }
      }else{
        //no hacemos nada
      }
    })
  }else{

  }
})


function editCondicion(condicion){
  let auxCondi = condicion.split("editCondi|");
  let condi = auxCondi[1];

  //consultamos la condicion y su estatus
  //7-4.30 - Lunes a Viernes
  //7-12.30 - Sabados

  //7.500
  //desarrollotalentohumano@cladimaco.com

  
  const myModal = new bootstrap.Modal(document.getElementById('modalEditCondicion'), {
    keyboard: false
  });

  myModal.show;

}