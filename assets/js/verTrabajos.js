let estatus = document.getElementById('buscarEstatus');
estatus.addEventListener('change', function(){
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
    
    if(res.status == "ok"){
      let contenido = '';
      if(res.mensaje == "dataOk"){
        //si tiene resultados
        
        for (let z = 0; z < res.data.length; z++) {
          console.log(res.data[z]);
          
        }
      }else{
        //no se encontraron resultados
        contenido = '<tr><td colspan="6" style="text-align:center;">Sin Resultados</td></tr>';
      }
    }else{
      //error
    }
  }else{
    //error de comunicacion
    Swal.fire(
      'Servidor Inalcansable',
      'Verifica tu conexion a internet',
      'error'
    )
  }
});