let btnBuscar = document.getElementById('btnBuscarMovs');

btnBuscar.addEventListener('click', function(){
  //verificamos que las fechas esten capturadas
  let fechaIni = document.getElementById('fechaIniMov').value;
  let fechaFin = document.getElementById('fechaFinMov').value;

  if(fechaIni != "" && fechaFin != ""){
    let datos = new FormData();
    datos.append('fechaIniMerca',fechaIni);
    datos.append('fechaFinMerca',fechaFin);

    let envio = new XMLHttpRequest();
    envio.open('POST','../includes/reportesCaja.php',false);
    envio.send(datos);

    if(envio.status == 200){
      let res = JSON.parse(envio.responseText);
      console.log(res);

      

    }else{
      //error de comunicacion
      Swal.fire(
        'Servidor Inalcansable',
        'Verifica tu conexion',
        'warning'
      )  
    }
  }else{
    //fechas no capturadas
    Swal.fire(
      'Fechas Invalidas',
      'Asegurate de capturar la fecha de inicio y la fecha fin',
      'warning'
    )
  }
});