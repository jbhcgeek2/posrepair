let bntMov = document.getElementById('btnSendMov');
bntMov.addEventListener('click', function(){
  let dato = new FormData();

  let tipoMov = document.getElementById('tipoMovReg').value;
  let concep = document.getElementById('concepMovReg').value;
  let observ = document.getElementById('observMov').value;
  let montoMov = document.getElementById('montoMovReg').value;
  let metodoMov = document.getElementById('metodoMovReg').value;

  if(tipoMov != "" && concep != "" && observ != "" && montoMov != "" && metodoMov != ""){
    //preguntamos si desea enviar el dato
    Swal.fire({
      title: 'Procesar Movimiento?',
      text: 'Estas seguro de procesar el movimiento?',
      icon: 'warning',
      showDenyButton: true,
      confirmButtonText: 'Procesar',
      denyButtonText: 'Cancelar'
    }).then((result)=>{
      if(result.isConfirmed){
        dato.append('tipoMov',tipoMov);
        dato.append('concepMov',concep);
        dato.append('observMov',observ);
        dato.append('montoMov',montoMov);
        dato.append('metodoMov',metodoMov);

        let envio = new XMLHttpRequest();
        envio.open('POST','../includes/operacionesSaldos.php',false);
        envio.send(dato);

        if(envio.status == 200){
          let res = JSON.parse(envio.responseText);
          if(res.status == 'ok'){
            Swal.fire(
              'Movimiento Aplicado',
              'Se proceso el movimiento correctamente',
              'success'
            ).then(function(){
              location.reload();
            })
          }else{
            //fallo el proceso
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
      }
    })
    //estan todos los campos capturados 
    

    
  }else{

  }
});


let btnSendGasto = document.getElementById('btnSendMovGasto');
btnSendGasto.addEventListener('click', function(){
  // metodo para registrar gasto a un cajero
  //verificamos que todos los campos esten capturados
  let tipoGasto = document.getElementById('tipoMovRegGasto').value;
  let usuarioGasto = document.getElementById('usuarioGasto').value;
  let observGasto = document.getElementById('observMovGasto').value;
  let montoGasto = document.getElementById('montoMovRegGasto').value;

  if(tipoGasto != "" && usuarioGasto != "" && observGasto != "" && montoGasto > 0){
    //estan completos los campos
    //por ultimo preguntamos si desea capturar el gasto
    Swal.frie({
      title: 'Registrar Gasto?',
      text: 'Estas seguro de registrar el gasto?',
      icon: 'warning',
      showDenyButton: true,
      confirmButtonText: 'Si, registrar',
      denyButtonText: 'Cancelar'
    }).then((result)=>{
      if(result.isConfirmed){
        //ahora si armamos todo
        let datos = new FormData(document.getElementById('regMovGasto'));

        let envio = new XMLHttpRequest();
        envio.open('POST','../includes/operacionesSaldos.php',false);
        envio.send(datos);

        if(envio.status == 200){
          let res = JSON.parse(envio.responseText);
          if(res.status == "ok"){
            Swal.fire(
              'Gasto Asignado',
              'El gasto se registro correctamente',
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
        
      }
    })
  }else{
    Swal.fire(
      'Campos incompletos',
      'Asegurate de capturar todos los campos correctamente',
      'error'
    )
  }
})