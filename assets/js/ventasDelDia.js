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
      let res = JSON.parse(envio.responseText);
      console.log(res);
      //verificamos la respuesta
      if(res.status == "ok"){
        if(res.data.length > 0){
          //si se tienen datos. hacemos un ciclo for
          //para reconstruir la tabla
          let sumaTotal = 0;
          for (let x = 0; x < res.data.length; x++) {
            let fechaVenta = res.data[x].fechaVenta;
            console.log(fechaVenta);
            
          }//fin del for
          
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