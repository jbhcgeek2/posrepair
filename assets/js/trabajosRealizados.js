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
        let contenido = "";

        if(res.data.length > 0){
          let suma = parseFloat(0);
          for (let x = 0; x < res.data.length; x++) {
            let servicio = res.data[x].nombreServicio;
            let equipo = res.data[x].tipoDispositivo+" "+res.data[x].marca+" "+res.data[x].modelo;
            let precio = parseFloat(res.data[x].costoFinal);
            let fechaTermino = res.data[x].fechaTermino;
            let idTrab = res.data[x].idTrabajo;
            suma = suma+precio;
            

            contenido = contenido+`<tr>
              <td>${servicio}</td>
              <td>${equipo}</td>
              <td>${precio.toLocaleString('en-US', { style: 'currency', currency: 'USD' })}</td>
              <td>${fechaTermino}</td>
              <td>
                <a href="verInfoTrabajo.php?data=${idTrab}" class="btn btn-success">Ver</a>
              </td>
            </tr>`;
          }//fin del for
          contenido = contenido+`<tr>
            <td colspan='2' style='text-align:right;'>TOTAL</td>
            <td style='text-align:left;'>${suma.toLocaleString('en-US', { style: 'currency', currency: 'USD' })}}</td>
            <td colspan='2'></td>
          </tr>`;
        }else{
          //sin datos de consulta
          contenido = `<tr>
            <td colspan="5" style="text-align:center;">SIN RESULTADOS</td>
          </tr>`;
        }
        document.getElementById('bodyTableReport').innerHTML = contenido;
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