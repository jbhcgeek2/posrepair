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
        // console.log(res);

        let contenido = "";
        const servicios = {};
        let modelos = {};
        let texto = "Se muestran los articulos vendidos el dia: "+fechaIni+" "+fechaFin;

        if(res.data.length > 0){
          //trataremos de crear un resumen de los prncipales servicios
          

          for (let i = 0; i < res.data.length; i++) {
            let nombre = res.data[i].nombreDetalle.toUpperCase();
            let servicio = res.data[i].nombreServicio.toUpperCase();
            let modelo = res.data[i].marca+" "+res.data[i].modelo;
            let cantidad = res.data[i].cantidad;
            let trabajo = res.data[i].idTrabajo;
            modelo = modelo.toUpperCase();

            if (servicios[servicio]) {
              servicios[servicio] += 1;
            } else {
              servicios[servicio] = 1;
            }
  
            contenido += `<tr>
            <td>${nombre}</td>
            <td>${servicio}</td>
            <td>${modelo}</td>
            <td>${cantidad}</td>
            <td> 
              <a href='verInfoTrabajo.php?data=${trabajo}' 
              class='btn btn-success'>Ver</a>
            </td>
            </tr>`; 
  
            
          }//fin del for
        }else{
          //sin datos
          contenido += `<tr>
            <td colspan='5' style='text-align:center;'>SIN DATOS</td>
            </tr>`;
        }

        document.getElementById('bodyTableReport').innerHTML = contenido;
        document.getElementById('tituloFiltro').innerHTML = texto;
        
        console.log(servicios);
        for(let key in servicios){

          console.log(key + ": " + servicios[key]);
        }

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
