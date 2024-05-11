
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

})
