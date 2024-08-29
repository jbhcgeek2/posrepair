let btnFiltro = document.getElementById('btnBuscarTrabajos');
btnFiltro.addEventListener('click', function(){
  //metodo para filtrar los trabajos

  //verificamos las fechas
  let fechaIni = document.getElementById('fechaIni').value;
  let fechaFin = document.getElementById('fechaFin').value;

  if(fechaIni != "" && fechaFin != ""){
    let datos = new FormData();
    datos.append('fechaIniTrab',fechaIni);
    datos.append('fechaFinTrab',fechaFin);

    let envio = new XMLHttpRequest();
    envio.open('POST','../includes/reportesCaja.php',false);
    envio.send(datos);

    if(envio.status == 200){
      let res = JSON.parse(envio.responseText);
      if(res.status == "ok"){
        console.log(res);
      }else{
        Swal.fire(
          'Ha ocurrido un error',
          res.mensaje,
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
    //error de fechas
    Swal.fire(
      'Fechas Invalidas',
      'Asegurate de indicar fechas validas',
      'error'
    )
  }

});