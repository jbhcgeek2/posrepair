let btnNuevaAudi = document.getElementById('newAuditoria');
btnNuevaAudi.addEventListener('click', function(){
  Swal.fire({
    title: 'Generar nueva Auditoria',
    input: 'password',
    showCancelButton: true,
    confirmButtonTExt: 'Generar'
  }).then((result)=>{
    console.log(result);
  })
})