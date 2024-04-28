let btnMovs = document.getElementById('btnBuscarMovs');

btnMovs.addEventListener('click', function(){
  //verificamos que las fechas esten correctas
  let fechaIniMov = document.getElementById('fechaIniMov');
  let fechaFinMov = document.getElementById('fechaFinMov');

  if(fechaIniMov != "" && fechaFinMov != ""){
    //si estan capturadas mandamos el xml
    let datos = new FormData();
    datos.append("fechaIniMov",fechaIniMov);
    datos.append("fechaFinMov",fechaFinMov);

    let envio = new XMLHttpRequest();
    envio.open('POST','../includes/reportesCaja.php',false);
    envio.send(datos);
    
    if(envio.status == 200){
      let res = JSON.parse(envio.responseText);
      if(res.status == "ok"){
        //se consulto bien, ahora verificamos si tiene datos
        let tabla = '';
        if(res.data.length > 0){
          //creamos el for de datos
          for (let i = 0; i < res.data.length; i++) {
            console.log(res.data[i]);
            tabla = tabla+`
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            `;
          }//fin del for
        }else{
          //no se tienen datos
          tabla = `<tr>
          <td colpan='6' style='text-align:center;' class='fw-bold'>Sin resultados</td>
        </tr>
        `;
        }
        document.getElementById('bodyTableReport').innerHTML = tabla;
      }else{
        //ocurrio un error en la consulta de datos
        let err = res.mensaje;
        Swal.fire(
          'Ha ocurrido un error',
          'Verificar: '+err,
          'error'
        )
      }
    }else{
      //error de conexion
      Swal.fire(
        'Servidor Inalcansable',
        'Verifica tu conexion',
        'warning'
      )      
    }
  }else{
    //debe tener las 2 fechas capturadas
    Swal.fire(
      'Fechas Invalidas',
      'Asegurate de capturar la fecha de inicio y la fecha fin',
      'warning'
    )
  }
})