let estatus = document.getElementById('buscarEstatus');
estatus.addEventListener('click', function(){
  let estatusSel = estatus.value;
  let nombreCli = document.getElementById('clienteNombre').value;

  let datos = new FormData();
  datos.append('estatusBusqueda',estatusSel);
  datos.append('nombreCli',nombreCli);

  let envio = new XMLHttpRequest();
  envio.open('POST','../includes/trabajosOperaciones.php',false);
  envio.send(datos);
  

  if(envio.status == 200){
    let res = JSON.parse(envio.responseText);
    console.log(res);
  }else{
    //error de comunicacion
    Swal.fire(
      'Servidor Inalcansable',
      'Verifica tu conexion a internet',
      'error'
    )
  }
});