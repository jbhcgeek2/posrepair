let btnNuevaAudi = document.getElementById('newAuditoria');
btnNuevaAudi.addEventListener('click', function(){
  Swal.fire({
    title: 'Generar nueva Auditoria',
    text: 'Ingresa la clave de autorizacion',
    input: 'password',
    showCancelButton: true,
    backdrop: true,
    confirmButtonText: 'Generar'
  }).then((result)=>{
    console.log(result);
    if(result.isConfirmed){
      let passAudi = result.value;
      
      //generamos el formData
      let datos = new FormData();
      datos.append('autorization',passAudi);

      let envio = new XMLHttpRequest();
      envio.open('POST','../includes/operacionesAuditoria.php',false);
      envio.send(datos);

      if(envio.status == 200){
        // console.log(envio.responseText);
        let res = JSON.parse(envio.responseText);
        if(res.status == "ok"){
          console.log(res);
        }else{
          //ocurrio un error
          Swal.fire(
            'Ha ocurrido un error',
            res.mensaje,
            'error'
          )
        }
      }else{
        Swal.fire(
          'Servidor Inalcansable',
          'Verifica tu conexion a internet',
          'error'
        )
      }
      

      
    }
  })
})