if (typeof Swal !== 'undefined') {
	//esta definido
}else{
	function cargarSweetAlert2() {
	return new Promise((resolve, reject) => {
			// Verificar si ya está cargado
			if (typeof Swal !== 'undefined') {
					resolve();
					return;
			}

			// Crear un elemento <script> para cargar SweetAlert2
			const script = document.createElement('script');
			script.src = 'assets/js/swetAlert.js'; // CDN de SweetAlert2
			script.onload = () => resolve();
			script.onerror = () => reject(new Error('Error al cargar SweetAlert2.'));

			// Agregar el script al documento
			document.head.appendChild(script);
	});
}

// Usar la función
cargarSweetAlert2()
	.then(() => {
			console.log('SweetAlert2 se cargó correctamente.');
	})
	.catch((error) => {
			console.error(error.message);
	});

}


if(document.getElementById('dispositivoAutorizado')){
  let autorizado = document.getElementById('dispositivoAutorizado').value;
  if(autorizado == "no"){
    //se requiere autorizar el dispositivo
    Swal.fire({
      title: 'Dispositivo no autorizado',
      text: 'Se requiere que este dispositivo este autorizado, ingresa la contrasena del administrador',
      icon: 'warning',
      input: 'password',
      allowOutsideClick: false,
      allowEscapeKey: false,
      showCancelButton: true,
      confirmButtonText: 'Autorizar',
      cancelButtonText: 'Cancelar'
    }).then((result)=>{
      if(result.isConfirmed && result.value != ""){
        //enviamos el texto que indico
        let datos = new FormData();
        datos.append('dispoAutoriza',result.value);
  
        fetch("../includes/operacionesDispo.php",{
          method: 'POST',
          body: datos
        }).then(function(res){
          return res.json();
        }).then(function(result){
          if(result.status == "ok"){
            Swal.fire({
              title: 'Dispositivo Autorizado',
              text: 'Se autorizo correctamente el dispositivo',
              icon: 'success'
            }).then(function(){
              location.reload();
            })
          }else{
            Swal.fire({
              title: 'No autorizado',
              text: result.mensaje,
              icon: 'error'
            }).then(function(){
              location.reload();
            })
          }
        })
        // console.log(result.value);
      }else{
        //cancelo la autorizacion, asi que cerramos la sesion
        window.location = "logOut.php";
      }
    })
  }else if(autorizado == "error"){
    let mensajeErr = document.getElementById('dispositivoData').value;
    Swal.fire({
      title: 'Error de Autorizacion',
      text: 'Verificar: '+mensajeErr,
      icon: 'error'
    })
  }
  }