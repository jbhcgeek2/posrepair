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
      let res = JSON.parse(envio.responseText);
      //verificamos la respuesta
      if(res.status == "ok"){
        if(res.data.lengh > 0){
          //si se tienen datos
          console.log("Con datos");
        }else{
          //no se tienen datos
          console.log("Sin datos");
        }
      }else{
        //ocurrio un error en la consulta
        let err = res.mensaje;
        Swal.fire(
          'Ha ocurrido un error',
          'Verificar: '+err,
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
  }else{
    Swal.fire(
      'Fechas Invalidas',
      'Asegurate de capturar fecha inicial y final',
      'warning'
    )
  }
});