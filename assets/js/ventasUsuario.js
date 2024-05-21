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
            let fecha = res.data[z].fechaVenta;
            let prod = res.data[z].nombreArticulo;
            let cant = res.data[z].cantidadVenta;
            let total = res.data[z].subtotalVenta;
            let sucursal = res.data[z].nombreSuc;

            contenido = contenido+`<tr>
              <td>${fecha}</td>
              <td>${prod}</td>
              <td>${cant}</td>
              <td>${total}</td>
              <td>${sucursal}</td>
            </tr>`;
          }
          document.getElementById('resultBusqueda').innerHTML = contenido;
        }else{
          //sin datos
          contenido = `<tr><td colspan='5' style='text-align:center;'>Sin Datos</td></tr>`;
          document.getElementById('resultBusqueda').innerHTML = contenido;
        }
        // insertamos el resultado
        
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