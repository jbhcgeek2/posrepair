let btnBuscar = document.getElementById('btnBuscarMovs');
btnBuscar.addEventListener('click', function(){
  // metodo para realizar la busqueda de ventas por uisuario y fechas

  let fechaIni = document.getElementById('fechaIniMov').value;
  let fechaFin = document.getElementById('fechaFinMov').value;
  let userBus = document.getElementById('usuarioVenta').value;

  //para realizar la busqueda ninguno de los 3 datos debe estar vacio
  if(fechaIni != "" && fechaFin != "" && userBus != ""){
    let datos = new FormData();
    datos.append('fecIniUser',fechaIni);
    datos.append('fecFinUSer',fechaFin);
    datos.append('repUserVent',userBus);

    let envio = new XMLHttpRequest();
    envio.open('POST','../includes/reportesCaja.php',false);
    envio.send(datos);

    if(envio.status == 200){
      let res = JSON.parse(envio.responseText);
      console.log(res);
      if(res.status == 'ok'){
        let contenido = '';
        if(res.mensaje == "operationSuccess"){
          for (let z = 0; z < res.data.length; z++) {
            console.log(res.data[z]);
          }
        }else{
          //sin datos
        }
      }else{
        //error
        let err = res.mensaje;
        Swal.fire(
          'Ha ocurrido un error',
          'Verificar: '+err,
          'error'
        )
      }
    }else{
      //error de comuniacion
      Swal.fire(
        'Servidor Inalcansable',
        'Verifica tu conexion a internet',
        'error'
      )
    }
  }else{
    //campos incompletos
  }

})