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
      console.log(passAudi);
    }
  })
})