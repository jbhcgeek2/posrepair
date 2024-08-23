let btnBuscar = document.getElementById('btnBuscarVendidos');
btnBuscar.addEventListener('click', function(){
  //verificamos que tengamos fechas

  let fechaIni = document.getElementById('fechaIni').value;
  let fechaFin = document.getElementById('fechaFin').value;
  let sucVentas = document.getElementById('sucVenta').value;

  if(fechaIni != "" && fechaFin != ""){
    let datos = new FormData();
    datos.append('fechaIniVen',fechaIni);
    datos.append('fechaFinVen',fechaFin);
    datos.append('sucVentas',sucVentas);

    let envio = new XMLHttpRequest();
    envio.open('POST','../includes/reportesCaja.php',false);
    envio.send(datos);

    if(envio.status == 200){
      let res = JSON.parse(envio.responseText);
      if(res.status == "ok"){
        if(res.data.length > 0){
          let conTabla = "";
          for (let i = 0; i < res.data.length; i++) {
            let nombre = res.data[i].nombreArticulo;
            conTabla += `<tr>
              <td>${nombre}</td>
              <td></td>
            </tr>`;
          }//fin del for

          document.getElementById('bodyTableReport').innerHTML = conTabla;
        }else{
          //sin resultados
          
        }
      }else{
        //error desconocido
        Swal.fire(
          'Ha ocurrido un error',
          res.mensaje,
          'error'
        )
      }
    }else{
      //error de servidor
      Swal.fire(
        'Servidor Inalcansable',
        'Verifica tu conexion a internet',
        'error'
      )
    }
  }else{
    Swal.fire(
      'Campos Incompletos',
      'Asegurate de indicar fechas validas',
      'error'
    )
  }
})