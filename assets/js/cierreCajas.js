
function calculaTotal(dato){

  if(dato > 0){
    let inicial = document.getElementById('montoEfectivoIni').value;
    let montoEfe = document.getElementById('ventaEfectivo').value;
    let montoGas = document.getElementById('gastoCaja').value;
    let montoEnt = document.getElementById('entradaCaja').value;
    let precortes = document.getElementById('precortes').value;
    if(montoEfe == ""){
      montoEfe = 0;
    }
    let elemResult = document.getElementById('montoDife');
    let elemColor = document.getElementById('totalDiferencia');

    inicial = parseFloat(inicial);
    montoEfe = parseFloat(montoEfe);
    montoGas = parseFloat(montoGas);
    montoEnt = parseFloat(montoEnt);
    precortes = parseFloat(precortes);

    dato = parseFloat(dato);
    console.log(dato);
    console.log(inicial);
    console.log(montoEfe);
    console.log(montoGas);
    console.log(montoEnt);
    console.log(precortes);

    let montoTotal = (inicial+montoEfe) - montoGas + montoEnt - precortes;
    montoTotal = parseFloat(montoTotal);
    console.log(montoTotal);
    let diferencia = parseFloat(montoTotal - dato);

    // let suma = dato + inicial;
    if(diferencia != 0){
      elemColor.classList.remove('text-primary');
      elemColor.classList.add('text-danger');
    }else{
      elemColor.classList.add('text-primary');
      elemColor.classList.remove('text-danger');
    }

    elemResult.innerHTML = diferencia;

  }
  
}

function calculaMontoResta(monto){

  if(monto > 0){
    let montoRetira = parseFloat(monto);
    let totalCaja = parseFloat(document.getElementById('montoEfectivo').value);
  
    let restoCaja = parseFloat(totalCaja - montoRetira);

    document.getElementById('saldoDeja').innerHTML = restoCaja;
  }

}

// let btnCerrar = document.getElementById('btnCerrarDia');
// btnCerrar.addEventListener('click', function(){
//   Swal.fire({
//     title: 'Estas seguro de cerrar el dia?',
//     text: 'No podras deshacer esta accion',
//     icon: 'warning',
//     showCancelButton: true,
//     confirmButtonText: 'Cerrar Dia',
//     cancelButtonText: 'Cancelar'
//   }).then((result)=>{
//     if(result.isConfirmed){
//       //mandamos el cierre
//       let efectivoCaja = parseFloat(document.getElementById('montoEfectivo').value);
//       let montoRetiro = document.getElementById('montoRetiroEfe').value;
//       if(montoRetiro == ""){
//         montoRetiro = 0;
//       }else{
//         montoRetiro = parseFloat(montoRetiro);
//       }
//       let observMov = document.getElementById('obervacionCierre').value;
//       let montoDig = document.getElementById('ventaDigital').value;
//       if(montoDig == ""){
//         montoDig = 0;
//       }else{
//         montoDig = parseFloat(montoDig);
//       }
//       let saldoTotalCaja = (document.getElementById('totalCajaSaldo').value);
//       //validamos la informacion capturada
//       let pasa = 0;
//       let pregunta = 0;
//       let titulo = "";
//       let texto = "";

//       if(efectivoCaja == saldoTotalCaja){
//         //los saldos cuadran
//         document.getElementById('montoEfectivo').classList.remove('is-invalid');
//         document.getElementById('montoEfectivo').classList.add('is-valid');
//       }else{
//         //los saldos no coinciden que lo verifique
//         pasa = pasa +1;
//         titulo =  'Los montos de caja no coinciden';
//         texto = 'Verifica la informacion e intentalo de nuevo';
//         document.getElementById('montoEfectivo').classList.add('is-invalid');
//       }

//       //si los saldos cuadran, verifixamos si desea retirar todo el dinero
//       if(montoRetiro == efectivoCaja){
//         titulo = "Deseas retirar todo el efectivo?";
//         texto = "No se contara con efectivo en la apertura del siguiente dia";
//         pasa = pasa +1;
//       }else if(montoRetiro > efectivoCaja){
//         titulo = "Retiro invalido.";
//         texto = "No se contara con efectivo en la apertura del siguiente dia";
//         document.getElementById('montoRetiroEfe').classList.remove('is-valid');
//         document.getElementById('montoRetiroEfe').classList.add('is-invalid');
//         pasa = pasa +1;
//       }else{
        
//       }

//       if(observMov.length > 6){
//         document.getElementById('obervacionCierre').classList.remove('is-invalid');
//         document.getElementById('obervacionCierre').classList.add('is-valid');
//       }else{
//         titulo = "Indique una observacion";
//         texto = "Asegurate de capturar informacion";
//         document.getElementById('obervacionCierre').classList.remove('is-valid');
//         document.getElementById('obervacionCierre').classList.add('is-invalid');
//         pasa = pasa +1;
//       }


//       if(pasa == 0){
//         //aqui podemos enviar el formulario
//         let datos = new FormData();
//         datos.append("efectivoTotCaja",efectivoCaja);
//         datos.append("montoRetiraEfe",montoRetiro);
//         datos.append("observCierre",observMov);
//         datos.append("montoDigital",montoDig);
  
//         let envio = new XMLHttpRequest();
//         envio.open("POST","../includes/cajas.php",false);
//         envio.send(datos);
  
//         // console.log(envio.responseText);
//         if(envio.status == 200){
//           let res = JSON.parse(envio.responseText);
//           if(res.status == "ok"){
//             Swal.fire(
//               'Cierre aplicado',
//               'No olvides imprimir tus reportes',
//               'success'
//             ).then(function(){
//               //redireccionamos a los reportes
//               window.location = "reportesCaja.php";
//             })
//           }else{
//             let err = res.mensaje;
//             Swal.fire(
//               'Ha ocurrido un error',
//               'Verificar: '+err,
//               'error'
//             )
//           }
//         }else{
//           //error
//           Swal.fire(
//             'Servidor Inalcansable',
//             'Verifica tu conexion a internet',
//             'error'
//           )
//         }
//         //fin de envio de formulario
//       }else{
//         Swal.fire({
//           title: titulo,
//           text: texto,
//           icon: 'error'
//         })
//       }


//     }else{
//       //se cancela el swal
//     }
//   })
// })

let btnCerrar = document.getElementById('btnCerrarDia');
btnCerrar.addEventListener('click', function(){
  Swal.fire({
    title: 'Estas seguro de cerrar el dia?',
    text: 'No podras deshacer esta accion',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Cerrar Dia',
    cancelButtonText: 'Cancelar'
  }).then((result)=>{
    if(result.isConfirmed){
      //mandamos el cierre
      let efectivoCaja = parseFloat(document.getElementById('montoEfectivo').value);
      let montoRetiro = document.getElementById('montoRetiroEfe').value;
      if(montoRetiro == ""){
        montoRetiro = 0;
      }else{
        montoRetiro = parseFloat(montoRetiro);
      }
      let observMov = document.getElementById('obervacionCierre').value;
      let montoDig = document.getElementById('ventaDigital').value;
      if(montoDig == ""){
        montoDig = 0;
      }else{
        montoDig = parseFloat(montoDig);
      }
      let saldoTotalCaja = (document.getElementById('totalCajaSaldo').value);
      //validamos la informacion capturada
      // let pasa = 0;
      // let pregunta = 0;
      // let titulo = "";
      // let texto = "";

      if(efectivoCaja == saldoTotalCaja){
        // los saldos de caja cuadran, ahora comprobamos si desea
        //retirar todo su efectivo
        if(montoRetiro == efectivoCaja){
          Swal.fire({
            title: 'Deseas retirar todo el efectivo de caja?',
            text: 'No quedara con efectivo para aperturar el dia',
            icon: 'warning',
            showDenyButton: true,
            confirmButtonText: 'Si, cerrar',
            denyButtonText: 'Cancelar'
          }).then((result)=>{
            if(result.isConfirmed){
              //si confirmo el cierre en ceros
              cerrarCaja();
            }
          })
        }else if(montoRetiro > efectivoCaja){
          //no es posible realizar el cierre saldos incongruentes
          Swal.fire(
            'Retiro Invalido',
            'El monto indicado es mayor con el que se cuenta',
            'error'
          )
        }else if(montoRetiro < efectivoCaja){
          //cerramos caja sin problema
          cerrarCaja();
        }
      }else{
        //los cierres no cuadran, preguntamos si deseas cerrar
        Swal.fire({
          title: 'Cerrar con diferencia?',
          text: 'Esta diferencia afectara los saldos globales',
          icon: 'warning',
          showDenyButton: true,
          confirmButtonText: 'Si, Cerrar',
          denyButtonText: 'Cancelar'
        }).then((result)=>{
          if(result.isConfirmed){
            cerrarCaja();
          }
        })
        // pasa = pasa +1;
        // titulo =  'Los montos de caja no coinciden';
        // texto = 'Verifica la informacion e intentalo de nuevo';
        // document.getElementById('montoEfectivo').classList.add('is-invalid');
      }

      //si los saldos cuadran, verifixamos si desea retirar todo el dinero
      // if(montoRetiro == efectivoCaja){
      //   titulo = "Deseas retirar todo el efectivo?";
      //   texto = "No se contara con efectivo en la apertura del siguiente dia";
      //   pasa = pasa +1;
      // }else if(montoRetiro > efectivoCaja){
      //   titulo = "Retiro invalido.";
      //   texto = "No se contara con efectivo en la apertura del siguiente dia";
      //   document.getElementById('montoRetiroEfe').classList.remove('is-valid');
      //   document.getElementById('montoRetiroEfe').classList.add('is-invalid');
      //   pasa = pasa +1;
      // }else{
        
      // }

      // if(observMov.length > 6){
      //   document.getElementById('obervacionCierre').classList.remove('is-invalid');
      //   document.getElementById('obervacionCierre').classList.add('is-valid');
      // }else{
      //   titulo = "Indique una observacion";
      //   texto = "Asegurate de capturar informacion";
      //   document.getElementById('obervacionCierre').classList.remove('is-valid');
      //   document.getElementById('obervacionCierre').classList.add('is-invalid');
      //   pasa = pasa +1;
      // }


      // if(pasa == 0){
      //   //aqui podemos enviar el formulario
      //   let datos = new FormData();
      //   datos.append("efectivoTotCaja",efectivoCaja);
      //   datos.append("montoRetiraEfe",montoRetiro);
      //   datos.append("observCierre",observMov);
      //   datos.append("montoDigital",montoDig);
  
      //   let envio = new XMLHttpRequest();
      //   envio.open("POST","../includes/cajas.php",false);
      //   envio.send(datos);
  
      //   // console.log(envio.responseText);
      //   if(envio.status == 200){
      //     let res = JSON.parse(envio.responseText);
      //     if(res.status == "ok"){
      //       Swal.fire(
      //         'Cierre aplicado',
      //         'No olvides imprimir tus reportes',
      //         'success'
      //       ).then(function(){
      //         //redireccionamos a los reportes
      //         window.location = "reportesCaja.php";
      //       })
      //     }else{
      //       let err = res.mensaje;
      //       Swal.fire(
      //         'Ha ocurrido un error',
      //         'Verificar: '+err,
      //         'error'
      //       )
      //     }
      //   }else{
      //     //error
      //     Swal.fire(
      //       'Servidor Inalcansable',
      //       'Verifica tu conexion a internet',
      //       'error'
      //     )
      //   }
      //   //fin de envio de formulario
      // }else{
      //   Swal.fire({
      //     title: titulo,
      //     text: texto,
      //     icon: 'error'
      //   })
      // }


    }else{
      //se cancela el swal
    }
  })
})


function cerrarCaja(){
  let efectivoCaja = parseFloat(document.getElementById('montoEfectivo').value);
  let montoRetiro = document.getElementById('montoRetiroEfe').value;
  if(montoRetiro == ""){
    montoRetiro = 0;
  }else{
    montoRetiro = parseFloat(montoRetiro);
  }
  let observMov = document.getElementById('obervacionCierre').value;
  let montoDig = document.getElementById('ventaDigital').value;
  if(montoDig == ""){
    montoDig = 0;
  }else{
    montoDig = parseFloat(montoDig);
  }
  let saldoTotalCaja = (document.getElementById('totalCajaSaldo').value);

  let datos = new FormData();
  datos.append("efectivoTotCaja",efectivoCaja);
  datos.append("montoRetiraEfe",montoRetiro);
  datos.append("observCierre",observMov);
  datos.append("montoDigital",montoDig);

  let envio = new XMLHttpRequest();
  envio.open("POST","../includes/cajas.php",false);
  envio.send(datos);

  // console.log(envio.responseText);
  if(envio.status == 200){
    let res = JSON.parse(envio.responseText);
    if(res.status == "ok"){
      Swal.fire(
        'Cierre aplicado',
        'No olvides imprimir tus reportes',
        'success'
      ).then(function(){
        //redireccionamos a los reportes
        window.location = "reportesCaja.php";
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
    //error
    Swal.fire(
      'Servidor Inalcansable',
      'Verifica tu conexion a internet',
      'error'
    )
  }
}

let btnPrecorte = document.getElementById('btnPreCorte');
btnPrecorte.addEventListener('click',function(){
  let totalEfectivo = document.getElementById('totalCajaSaldo').value;

  Swal.fire({
    title: 'Pre-Corte de Caja',
    text: 'Ingresa el monto a retirar',
    input: 'number',
    showCancelButton: true,
    confirmButtonText: 'Procesar'
  }).then((result)=>{
    if(result.isConfirmed){
      console.log(result.value);
      let retira = result.value;
      if(retira <= totalEfectivo){
        let datos = new FormData();
        datos.append('montoRetiraPreCorte',retira);
        datos.append('montoEfePrecorte',totalEfectivo);

        let envio = new XMLHttpRequest();
        envio.open('POST','../includes/precorteCaja.php',false);
        envio.send(datos);

        if(envio.status == 200){
          let res = JSON.parse(envio.responseText);
          if(res.status == "ok"){
            location.reload();
          }else{
            //ocurrio un error
            Swal.fire(
              'Ha ocurrido un error',
              res.mensaje,
              'error'
            ).then(function(){
              location.reload();
            })
          }
        }else{
          //error al procesar
          Swal.fire(
            'Servidor Inalcansable',
            'Verifica tu conexion a internet',
            'error'
          )
        }
      }else{
        Swal.fire({
          title: 'Movimiento Incorrecto',
          text: 'No puedes retirar mas del efectivo existente',
          icon: 'error'
        })
      }
    }else{
      //no hacemos nada
    }
  })
})