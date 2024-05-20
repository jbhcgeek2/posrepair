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
        let colorStatus = {"Activo":'badge rounded-pill text-bg-success',
        "En Proceso":'badge rounded-pill text-bg-warning',
        "En Espera":'badge rounded-pill text-bg-secondary',
        "Finalizado":'badge rounded-pill text-bg-danger',
        "Cancelado":'badge rounded-pill text-bg-dark'};

        for (let z = 0; z < res.data.length; z++) {
          console.log(res.data[z].numTrabajo);
          let nombreCli = res.data[z].nombreCliente;
          let tipoTra = res.data[z].nombreServicio;
          let fechaReg = res.data[z].fechaTrabajo;
          let fechaEnt = res.data[z].fechaEntrega;
          let estatusTra = res.data[z].estatusTrabajo;
          let trabId = res.data[z].idTrabajo;

          let colorEstatus = colorStatus[estatusTra];

          contenido = contenido+`<tr>
            <td>${nombreCli}</td>
            <td>${tipoTra}</td>
            <td>${fechaReg}</td>
            <td>${fechaEnt}</td>
            <td><span class='${colorEstatus}'>${estatusTra}</span></td>
            <td>
              <a href='verInfoTrabajo.php?data=${trabId}' class='btn btn-primary'>Ver</a>
            </td>
          </tr>`;
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