let btnBuscar = document.getElementById('btnBuscar');

btnBuscar.addEventListener('click', function(){
  //verificamos que tengamos fecha inicial y final
  let fechaIni = document.getElementById('fechaIni').value;
  let fechaFin = document.getElementById('fechaFin').value;

  if(fechaIni != "" && fechaFin != ""){
    //enviamos la solicitud de datos
    let datos = new FormData();
    datos.append("fechaIniBus",fechaIni);
    datos.append("fechaFinBus",fechaFin);

    let envio = new XMLHttpRequest();
    envio.open('POST','../includes/reportesCaja.php',false);
    envio.send(datos);

    if(envio.status == 200){
      console.log(envio.responseText);
    }else{
      Swal.fire(
        'Servidor Inalcansable',
        'Verifica tu conexion a internet',
        ''
      )
    }
  }else{
    Swal.fire(
      'Fechas Invalidas',
      'Asegurate de capturar fecha inicial y final',
      'warning'
    )
  }
});