let btnNuevaAudi = document.getElementById('newAuditoria');
btnNuevaAudi.addEventListener('click', function(){
  Swal.fire({
    title: 'Generar nueva Auditoria',
    input: 'password',
    showCancelButton: true,
    confirmButtonTExt: 'Generar'
  }).then((result)=>{
    console.log(result);
    if(result.isConfirmed){
      let passAudi = result.value;
      console.log(passAudi);
    }
  })
})