let btnRefa = document.getElementById('btnBuscarVendidos');
btnRefa.addEventListener('click', function(){
  //metodo para filtrar las refacciones por fechas
  let fechaIni = document.getElementById('fechaIni').value;
  let fechaFin = document.getElementById('fechaFin').value;

  if(fechaIni != "" && fechaFin != ""){
    let datos = new FormData();
    datos.append('fechaIniRefa', fechaIni);
    datos.append('fechaFinRefa', fechaFin);

    let envio = new XMLHttpRequest();
    envio.open('POST','../includes/reportesCaja.php',false);
    envio.send(datos);

    if(envio.status == 200){
      let res = JSON.parse(envio.responseText);

      if(res.status == "ok"){
        console.log(res);

        for (let i = 0; i < res.data.length; i++) {
          let nombre = res.data[i].nombreDetalle;
          console.log(nombre);
          
        }//fin del for
      }else{
        //ha ocurrido un error
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
    //sion fechas validas
    Swal.fire(
      'Fechas Invalidas',
      'Asegurate de indicar un rango de fechas valido',
      'warning'
    )
  }
})
