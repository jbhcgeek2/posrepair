
let estatus = document.getElementById('estatusTrabajo');
estatus.addEventListener('change', function(){
  //metodo para realizar el cambio de estatus de un trabajo
  //primero preguntamos si desea cambiar el estatus
  Swal.fire({
    title: 'Cambio de estatus',
    text: 'Deseas cambiar el estatus del trabajo?',
    icon: 'warning',
    showDenyButton: true,
    confirmButtonText: 'Actualizar',
    denyButtonText: 'Cancelar'
  }).then((result)=>{
    if(result.isConfirmed){
      //enviamos el nuevo estatus
      let nuevoEstatus = estatus.value;
      let trabajo = document.getElementById('datoTrabajo').value;
      let datos = new FormData();

      datos.append('trabajoStatus',trabajo);
      datos.append('nuevoStatus',nuevoEstatus);

      let envio = new XMLHttpRequest();
      envio.open('POST','../includes/trabajosOperaciones.php',false);
      envio.send(datos);

      if(envio.status == 200){
        let res = JSON.parse(envio.responseText);
        if(res.status == 'ok'){
          Swal.fire(
            'Estatus Actualizado',
            'Se completo la actualizacion',
            'success'
          ).then(function(){
            location.reload();
          })
        }else{
          let err = res.mensaje;
          Swal.fire(
            'Ha ocurrido un error',
            'Verificar: '+err,
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
      //se cancela, no hacemos nada
    }
  })
})

var datosArtiConsulta;
let catArti = document.getElementById('catArticulo');
catArti.addEventListener('change', function(){
  //para esta accion mostraremos aquelllos articulos de esa categoria
  //que existan en la sucursal
  let categoria = catArti.value;

  let datos = new FormData();
  datos.append('getAtArti',categoria);

  let envio = new XMLHttpRequest();
  envio.open('POST','../includes/trabajosOperaciones.php',false);

  envio.send(datos);

  if(envio.status == 200){
    //se consulto bien
    let res = JSON.parse(envio.responseText);

    if(res.status == 'ok'){
      //mostramos los resultados en el select
      // console.log(res.data);
      if(res.data != "noData"){
        datosArtiConsulta = res.data;
        let campoSel = "<option value=''>Seleccione...</option>";
        for (let z = 0; z < res.data.length; z++) {
          let nombreArti = res.data[z].nombreArticulo;
          let idArti = res.data[z].idArticulo;
          campoSel = campoSel+`<option value='${idArti}'>${nombreArti}</option>`;
        }//fin del for
        document.getElementById('articuloAgrega').innerHTML = campoSel;
      }else{
        //no se tienen articulos
        Swal.fire(
          'Sin Articulos disponibles',
          'Asegurate de contar con inventario en sucursal',
          'warning'
        )
      }
    }else{
      //ocurrio un error ela consultar los estatus
      let err = res.mensaje;
      Swal.fire(
        'Ha ocurrido un error',
        'Verificar: '+err,
        'error'
      )
    }
  }else{
    //error de servidor
    Swal.fire(
      'Servidor Inalcansable',
      'Verifica tu conexion a internet',
      'error'
    );
  }
})


let selArti = document.getElementById('articuloAgrega');
selArti.addEventListener('change', function(){
  let articuloSel = selArti.value;
  //buscamos en los datos globales
  let auxDatos = datosArtiConsulta;
  for (let x = 0; x < auxDatos.length; x++) {
    // console.log(auxDatos[x]);
    if(auxDatos[x].idArticulo == articuloSel){
      //ponemos el precio sugerido
      document.getElementById('precioArti').value = auxDatos[x].precioUnitario;
    }
  }//fin del for
});


function updateTotal(){
  //funcion para actualizar el total
  //cuando se modifique las cantidades o el precio
  let precio = document.getElementById('precioArti').value;
  let cantidad = document.getElementById('cantidadArti').value;

  if(precio > 0 && cantidad > 0){
    let total = cantidad * precio;
    document.getElementById('totalExtra').value = total;
  }
}

let btnGuardar = document.getElementById('btnSave');
btnGuardar.addEventListener('click', function(){
  //preguntamos si esta seguro
  let cantidad2 = document.getElementById('cantidadArti').value;
  if(cantidad2 > 0 && !isNaN(cantidad2)){
    Swal.fire({
      title: 'Registrar Pieza?',
      text: 'Esto afectara la existencia de tu inventario',
      icon: 'warning',
      showDenyButton: true,
      confirmButtonText: 'Si, Registrar',
      denyButtonText: 'Cancelar'
    }).then((result)=>{
      //si se proceara el movimiento
      let articulo = document.getElementById('articuloAgrega').value;
      let precio = document.getElementById('precioArti').value;
      let cantidad = document.getElementById('cantidadArti').value;
      let total = document.getElementById('totalExtra').value;
      let trabajo = document.getElementById('datoTrabajo').value;
      let idCodEspe = document.getElementById('idCodEspe').value;
  
      let datos = new FormData();
      datos.append('artiServicio',articulo);
      datos.append('precioArtiServ',precio);
      datos.append('cantidadArtiServ',cantidad);
      datos.append('totalArtiServ',total);
      datos.append('trabajoArtiServ',trabajo);
      datos.append('idCodEspe',idCodEspe);
  
      let envio = new XMLHttpRequest();
      envio.open('POST','../includes/trabajosOperaciones.php',false);
      envio.send(datos);
  
      if(envio.status == 200){
        //verificamos la respuesta
        let res = JSON.parse(envio.responseText);
        if(res.status == 'ok'){
          //podemos dar por registrado el articulo
          Swal.fire(
            'Pieza Registrada',
            'Se ha registrado la pieza correctamente',
            'success'
          ).then(function(){
            location.reload();
          })
        }else{
          //error al registrar el articulo
          let err = res.mensaje;
          Swal.fire(
            'Ha ocurrido un error',
            'Verificar: '+err,
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
    })
  }else{
    Swal.fire(
      'Campos Invalidos',
      'Asegurate de indicar el numero de piezas y un monto final valido',
      'error'
    )
  }
  
})


// let btnFinaliza = document.getElementById('btnFinaliza');
// btnFinaliza.addEventListener('click', function(){
//   //seccion para validar el costo de los articulos utilizados y el cobro inicial
//   let estatusTrabajo = document.getElementById('estatusValue').value;
  
//     let totalTotal = document.getElementById('sumaTotalArtis').value;
//     let montoIni = document.getElementById('costoServicio').value;
  
//     let costoFinal = document.getElementById('costoFinal').value;
//     let costoInicial = document.getElementById('costoIniFinal').value;
//     let costoProd = document.getElementById('montoArticulos').value;
  
//     let ganEstimada = costoInicial - costoProd;
//     if(ganEstimada > 0){
//       document.getElementById('gananciaEstimada').classList.add('text-primary');
//     }else{
//       document.getElementById('gananciaEstimada').classList.remove('text-primary');
//       document.getElementById('gananciaEstimada').classList.add('text-danger');
//     }
//     document.getElementById('gananciaEstimada').innerHTML = '$'+ganEstimada;
  
//     if(montoIni >= totalTotal){
//       //el servicio generara ganancia
//     }else{
//       //se gasto de mas, solo lo notificamos
//       Swal.fire(
//         'Incongruencia en el costo',
//         'Se recomienda aumentar el costo del servicio',
//         'warning'
//       )
//     }
  
  
// })

// let costoFin = document.getElementById('costoFinal');
// costoFin.addEventListener('keyup', function(){
//   let costoFinal = document.getElementById('costoFinal').value;
//   let costoInicial = document.getElementById('costoIniFinal').value;
//   let costoProd = document.getElementById('montoArticulos').value;
//   let gananciaEst;
//   if(costoFinal > 0){
//     gananciaEst = costoFinal - costoProd;
//   }else{
//     gananciaEst = costoInicial - costoProd;
//   }

//   if(gananciaEst > 0){
//     document.getElementById('gananciaEstimada').classList.remove('text-danger');
//     document.getElementById('gananciaEstimada').classList.add('text-primary');
//   }else{
//     document.getElementById('gananciaEstimada').classList.remove('text-primary');
//     document.getElementById('gananciaEstimada').classList.add('text-danger');
//   }
//   document.getElementById('gananciaEstimada').innerHTML = '$'+gananciaEst;
  


// });


let btnTermina = document.getElementById('btnTerminaTrabajo');
btnTermina.addEventListener('click', function(){
  //metodo para finalizar el trabajo
  //antes de confirmar, verificamos el costo total
  let costoFinal = parseFloat(document.getElementById('costoFinal').value);
  let costoIni = parseFloat(document.getElementById('costoIniFinal').value);

  if(costoFinal){
    Swal.fire({
      title: 'Finalizar Trabajo?',
      text: 'Estas seguro de terminar el trabajo?',
      icon: 'warning',
      showDenyButton: true,
      confirmButtonText: 'Si, finalizar',
      denyButtonText: 'Cancelar'
    }).then((result)=>{
      if(result.isConfirmed){
        //enviamos los datos

        let trabajo = document.getElementById('datoTrabajo').value;
        let datos = new FormData();
        datos.append('terminaTrabajo',trabajo);
        datos.append('precioFinalTer',costoFinal);

        let envio = new XMLHttpRequest();
        envio.open('POST','../includes/trabajosOperaciones.php',false);
        envio.send(datos);

        if(envio.status == 200){
          let res = JSON.parse(envio.responseText);
          if(res.status == "ok"){
            Swal.fire(
              'Trabajo finalizado',
              'Ya es posible cobrarlo en el area de caja',
              'success'
            ).then(function(){
              window.location = 'verTrabajos.php';
            })
          }else{
            //error al actualizar
            let err = res.mensaje;
            Swal.fire(
              'Ha ocurrido un error',
              'Verificar: '+err,
              'error'
            )
          }
        }else{
          Swal.fire(
            'Servidor inalcansable',
            'Verifica tu conexion a internet',
            'error'
          )
        }
        
      }else{
        //no hacemos nada
      }
    })
  }else{
    Swal.fire(
      'Datos faltantes',
      'Asegurate de indicar un costo final valido',
      'error'
    )
  }
});


let changeTipoServ = document.getElementById('tipoServicio');
changeTipoServ.addEventListener('change', function(){
  //antes de continuar preguntamos si desea hacer la modificacion
  Swal.fire({
    title: 'Modificar Tipo de Servicio?',
    text: 'Deseas modificar el tipo de servicio?',
    icon: 'warning',
    showDenyButton: true,
    confirmButtonText: 'Si, Actualizar',
    denyButtonText: 'Cancelar'
  }).then((result)=>{
    if(result.isConfirmed){
      //acepto modificar el tipo de servicio
      let tipoServNew = changeTipoServ.value;
      let datoTrabajo = document.getElementById('datoTrabajo').value;

      let datos = new FormData();
      datos.append('tipoServUpdate',tipoServNew);
      datos.append('trabajoServUpdate',datoTrabajo);

      let envio = new XMLHttpRequest();
      envio.open('POST','../includes/trabajosOperaciones.php',false);
      envio.send(datos);

      if(envio.status == 200){
        let res = JSON.parse(envio.responseText);
        if(res.status == "ok"){
          //se actualizo correctamente
          Swal.fire(
            'Actualizacion Realizada',
            '',
            'success'
          )
        }else{
          //ocurrio algun error
          let err = res.mensaje;
          Swal.fire(
            'Ha ocurrido un error',
            'Verificar: '+err,
            'error'
          )
        }
      }else{
        // error de comunicaicon
        Swal.fire(
          'Servidor Inalcansable',
          'Verifica tu conexion a internet',
          'error'
        )
      }
    }
  })
});

let solucionTrabajo = document.getElementById('solucionTrabajo');
solucionTrabajo.addEventListener('change', function(){
  //metodo para actualizar la solucion del trabajo

  Swal.fire({
    title: 'Actualizar Solucion',
    text: 'Deseas actualizar la solucion del trabajo?',
    icon: 'warning',
    showDenyButton: true,
    confirmButtonText: 'Si, Actualizar',
    denyButtonText: 'No, Cancelar'
  }).then((result)=>{
    if(result.isConfirmed){
      let solucion = solucionTrabajo.value;
      let trabajoSolucion = document.getElementById('datoTrabajo').value;

      let datos = new FormData();
      datos.append('solucionUpdate',solucion);
      datos.append('datoTrabajoSol',trabajoSolucion);

      let envio = new XMLHttpRequest();
      envio.open('POST','../includes/trabajosOperaciones.php', false);
      envio.send(datos);

      if(envio.status == 200){
        let res = JSON.parse(envio.responseText);
        if(res.status == "ok"){
          Swal.fire(
            'Solucion Actualizada',
            '',
            'success'
          )
        }else{
          //error de proceso
          let err = res.mensaje;
          Swal.fire(
            'No fue posible actualizar',
            'Verificar: '+err,
            'error'
          )
        }
      }else{
        //error de comunicacion
        Swal.fire(
          'Servidor Inalcansable',
          'Verifica tu conexion a internet',
          'error'
        )
      }
    }
  })
  
})

let btnAddGasto = document.getElementById('btnAddGasto');
btnAddGasto.addEventListener('click', function(){
  //pregfuntamos si desea registrar el gasto
  Swal.fire({
    title: 'Esta Seguro de Registrar el Gasto?',
    text: 'Una vez procesado no podra eliminarse',
    icon: 'warning',
    showDenyButton: true,
    confirmButtonText: 'Si, Registrar',
    denyButtonText: 'Cancelar'
  }).then((result)=>{
    if(result.isConfirmed){
      //verificamos que los campos esten capturados
      let motivoGasto = document.getElementById('nombreGasto').value;
      let montoGasto = document.getElementById('montoGasto').value;
      let trabajo = document.getElementById('datoTrabajo').value;

      if(motivoGasto != "" && montoGasto > 0){
        let datos = new FormData();
        datos.append('motivoGastoAdd',motivoGasto);
        datos.append('montoGastioAdd',montoGasto);
        datos.append('trabajoGastoAdd',trabajo);

        let envio = new XMLHttpRequest();
        envio.open('POST','../includes/trabajosOperaciones.php',false);
        envio.send(datos);

        if(envio.status == 200){
          let res = JSON.parse(envio.responseText);
          if(res.status == "ok"){
            //se proceso correcto el cobro
            Swal.fire(
              'Gasto Registrado',
              '',
              'success'
            ).then(function(){
              location.reload();
            })
          }else{
            //ocurrio algun error
            let err = res.mensaje;
            Swal.fire(
              'Ha ocurrido un error',
              'Verificar: '+err,
              'error'
            )
          }
        }else{
          //error de comunicacion
          Swal.fire(
            'Servidor Inalcansable',
            'Verifica tu conexion a internet',
            'error'
          )
        }

      }else{
        //no se tienen capturados datos
        Swal.fire(
          'Campos Incompletos',
          'Capture un motivo y monto validos',
          'error'
        )
      }
    }else{
      //no hacemos nada
    }
  })
})

function buscarCodigo(){
  let codigo = document.getElementById('codigoArticuloAdd').value;


  let datos = new FormData();
  datos.append('codigoProdTrabajo',codigo);

  let envio = new XMLHttpRequest();
  envio.open('POST','../includes/trabajosOperaciones.php',false);
  envio.send(datos);

  if(envio.status == 200){
    let res = JSON.parse(envio.responseText);
    console.log(res);
    if(res.status == 'ok'){
      //consultamos el resulgtado
      console.log(res.data['esChip']);
      if(res.data['esChip'] != null && res.data['codigoProducto'] == codigo){
        //se trata de un chip, debe indicar el codigo
        Swal.fire(
          'Codigo Incorrecto',
          'Asegurate de escanear el codigo del Chip o el IMEI',
          'warning'
        )
      }else{
        //si es un codigo valido de articulo
        let categoria = res.data['categoriaID'];
        let nombre = res.data['nombreArticulo'];
        let precio = res.data['precioUnitario'];
        let idProd = res.data['idArticulo'];

        document.getElementById('catArticulo').value = categoria;
        document.getElementById('precioArti').value = precio;
        let comboProds = "<option value='"+idProd+"' selected>"+nombre+"</option>";
        document.getElementById('articuloAgrega').innerHTML = comboProds;
        if(res.data['esChip'] == 1){
          document.getElementById('idCodEspe').value = res.data['idChip'];
          document.getElementById('cantidadArti').value = "1";
          document.getElementById('totalExtra').value = precio;

          document.getElementById('cantidadArti').setAttribute("readonly", "true");

        }

      }
    }else{
      //ocurrio un error de consulta
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

}

function updateCosto(){
  //metodo para actualizar el costo inicial de un servicio
  let nuevoCosto = document.getElementById('costoServicio').value;
  let trabajo = document.getElementById('datoTrabajo').value;
  if(!isNaN(nuevoCosto)){
    //confirmamos
     Swal.fire({
        title: 'Modificar Costo',
        text: 'Estas seguro de modificar el costo inicial?',
        icon: 'warning',
        showDenyButton: true,
        confirmButtonText: 'Si, modificar',
        denyButtonText: 'Cancelar'
      }).then((result)=>{
        if(result.isConfirmed){
          //hacemos la peticion de actualizacion
          let datos = new FormData();
          datos.append('updateNewCosto',nuevoCosto);
          datos.append('trabajoUpdateCosto',trabajo);

          let envio = new XMLHttpRequest();
          envio.open('POST','../includes/trabajosOperaciones.php',false);
          envio.send(datos);

          if(envio.status == 200){
            let res = JSON.parse(envio.responseText);
            if(res.status == "ok"){
              Swal.fire(
                'Precio Actualizado',
                'Se actualizo el precio correctamente',
                'success'
              )
            }else{
              //ha opcurrido un error
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
          //no se hace nada
        }
      })
  }
}

let inputComent = document.getElementById('newComent');
inputComent.addEventListener('change', function(){
  //metodo para inserta un comentario en el trabajo
  if(inputComent.value != ""){
    //no preguntamos e insertamos el comentario de manera directa
    let datos = new FormData();
    let idTrab = document.getElementById('datoTrabajo').value;
    datos.append('newComent',inputComent.value);
    datos.append('comentDataId',idTrab);

    let envio = new XMLHttpRequest();
    envio.open('POST','../includes/trabajosOperaciones.php',false);
    envio.send(datos);

    if(envio.status == 200){
      let res = JSON.parse(envio.responseText);
      if(res.status == "ok"){
        //si todo salio bien, mostramos los comentarios
        let contenido = '';
        for (let x = 0; x < res.data.length; x++) {
          let fecha = res.data[x]['fechaComentario'];
          let usuarioCom = res.data[x]['usuarioComentario'];
          let comentario = res.data[x]['comentario'];

          contenido = contenido+`<tr>
            <td>${fecha}</td>
            <td>${usuarioCom}</td>
            <td>${comentario}</td>
          </tr>`;
        }//fin del for
        //insertamos el comentario

        document.getElementById('resComentarios').innerHTML = contenido;
        //regrtesamos el campo limpio
        inputComent.value = "";
      }else{
        Swal.fire(
          'Ha ocurrido un error',
          res.mensaje,
          'error'
        )
      }
    }else{
      //ocurrio un error
      Swal.fire(
        'Servidor Inalcansable',
        'Ha ocurrido un error al tratar de insertar el comentario.',
        'error'
      )
    }

  }
})