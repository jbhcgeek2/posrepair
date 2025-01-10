
document.addEventListener("DOMContentLoaded", function() {
  Swal.fire({
    title: 'Antes de continuar...',
    text: 'Ya se encuentra registrado el cliente?',
    icon: 'warning',
    showDenyButton: true,
    confirmButtonText: 'Ya esta registrado',
    denyButtonText: 'No, Registrar nuevo cliente'
  }).then((result)=>{
    if(result.isConfirmed){
      //ya esta registrado
    }else{
      //no esta registrado, lo damos de alta
      // window.location = 'altaCliente.php';
      //abrimos el modal para nuevo cliente
      let modalCliente = new bootstrap.Modal(document.getElementById('nuevoCliente'));
      modalCliente.show();

      let btnAlta = document.getElementById('btnAltaCliente');
      btnAlta.addEventListener('click', function(){
        //antes de continuar, verificamos que el nombre
        //y el telefono este capturado
        let nombreCli = document.getElementById('nombreCliente').value;
        let telCli = document.getElementById('telefonoCliente').value;
        let mailCli = document.getElementById('emailCliente').value;
        let direCli = document.getElementById('direccionCliente').value;
        let rfcCli = document.getElementById('rfcCliente').value;
        if(nombreCli != "" && telCli != ""){
          let datos = new FormData();
          datos.append('nombreCliente',nombreCli);
          datos.append('telefonoCliente',telCli);
          datos.append('emailCliente',mailCli);
          datos.append('direccionCliente',direCli);
          datos.append('rfcCliente',rfcCli);
          datos.append('altaCliModal','yes');

          let envio = new XMLHttpRequest();
          envio.open("POST","../includes/operacionesCliente.php",false);
          envio.send(datos);
          if(envio.status == 200){
            let res = JSON.parse(envio.responseText);
            if(res.status == "ok"){
              //ordenamos los clientes
              // console.log(res);
              let clientesContent = "";
              // console.log(res.clientes);
              for(let x = 0; x < res.clientes.length; x++){
                let name = res.clientes[x]['nombreCliente'];
                let idClie = res.clientes[x]['idClientes'];
                // console.log(name);
                if(idClie == res.data){
                  clientesContent = clientesContent+'<option value="'+idClie+'">'+name+'</option>';
                }else{
                  clientesContent = clientesContent+'<option value="'+idClie+'">'+name+'</option>';
                }
              }//fin del for clientes\\for

              document.getElementById('clienteList').innerHTML = clientesContent;
              modalCliente.hide();
              
              console.log(res.data);
              let dataList = document.getElementById('clienteList');
              let selectedOpt = Array.from(dataList.options).find(item => item.value == res.data);
              document.getElementById('NombreclienteTrabajo').value = selectedOpt.textContent;
              document.getElementById('clienteTrabajo').value = res.data;
            }
          }else{
            Swal.fire(
              'Servidor Inalcansable',
              'Verifica tu conexion a internet',
              'error'
            )
          }

        }else{
          Swal.fire(
            'Campos faltantes',
            'Asegurate de almenos capturar el nombre y telefono.',
            'error'
          )
        }

      });
    }
  })

  let tipoServ = document.getElementById('tipoServicio');
  tipoServ.addEventListener('change', function(){
    //buscaremos el precio sugerido del servicio
    let dato = tipoServ.value;
    let datoServ = new FormData();
    datoServ.append('servCheck',dato);

    let envioServ = new XMLHttpRequest();
    envioServ.open('POST','../includes/trabajosOperaciones.php', false);
    envioServ.send(datoServ);

    if(envioServ.status == 200){
      let res = JSON.parse(envioServ.responseText);

      if(res.status == "ok"){
        //se consulto correctamente
        document.getElementById('costoServicio').value = res.data;
      }else{
        //error de operacion
        let err = res.mensaje;
        Swal.fire(
          'Ha ocurrido un error',
          'Verifica: '+err,
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

  })

  let btnAlta = document.getElementById('altaTrabajo');
  btnAlta.addEventListener('click', function(){
    //le preguntamos que si esta seguro registrar

    Swal.fire({
      title: 'Registrar Trabajo?',
      text: 'Estas seguro de registrarlo?',
      icon: 'warning',
      showDenyButton: true,
      confirmButtonText: 'Si, registrar',
      denyButtonText: 'Cancelar'
    }).then((result)=>{
      if(result.isConfirmed){
        //cargamos los datos

        let datos = new FormData(document.getElementById('dataAltaTrab'));
        //verificamos que los datos requeridos esten cargados
        let pasa = 0;
        let camposReq = ['clienteTrabajo','fechaServicio','sucursalServicio','tipoDispositivo','tipoServicio',
        'marcaServicio','modeloServicio','numberDevice','descripcionProblema','fechaEntrega'];
        datos.forEach(function(valor, clave){
          console.log('Valor: '+valor+' del campo '+clave);
          //si el campo esta vacio lo marcaremos como valido
          let campo = document.getElementById(clave);

          if(camposReq.includes(clave)){
            if(valor.trim() == "" ||  valor == " "){
              //campo vacio
              campo.classList.add('is-invalid');
              pasa++;
            }else{
              campo.classList.remove('is-invalid');
              campo.classList.add('is-valid');
            }
          }
          
        })

        if(pasa == 0){
          //carmagos el envio
          let envio = new XMLHttpRequest();
          envio.open('POST','../includes/trabajosOperaciones.php', false);
          envio.send(datos);

          if(envio.status == 200){
            //se completo
            let res = JSON.parse(envio.responseText);
            if(res.status == 'ok'){
              //se completo el proceso
              Swal.fire(
                'Trabajo Registrado',
                'Se ha registrado el trabajo correctamente',
                'success'
              ).then(function(){
                //una vez registrado el trabajo, abrimos una nueva ventana
                //para la impresion del ticket
                let trabajoT = res.data;
                window.open('../printTrabajo.php?t='+trabajoT,'_blank');
                window.location = 'verInfoTrabajo.php?data='+trabajoT;
              })
            }else{
              // ocurrio un error al insertar el trabajo
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

        }else{
          Swal.fire(
            'Campos Incompletos',
            'Completa la informacion requerida',
            'warning'
          )
        }

      }
    })
  });

})


let autoCom = document.getElementById('NombreclienteTrabajo');
autoCom.addEventListener('change', function(){
  document.getElementById('clienteTrabajo').value = autoCom.value;
  
  let dataList = document.getElementById('clienteList');
  let selectedOpt = Array.from(dataList.options).find(item => item.value == autoCom.value);

  autoCom.value = selectedOpt.textContent;
})

autoCom.addEventListener('focusout', function(){
  let valorCliente = document.getElementById('clienteTrabajo').value;
  if(valorCliente > 0){
    document.getElementById('NombreclienteTrabajo').classList.remove('is-invalid');
    document.getElementById('NombreclienteTrabajo').classList.add('is-valid');
  }else{
    //no se idico un nombre valido
    document.getElementById('NombreclienteTrabajo').classList.add('is-invalid');
    const Toast = Swal.mixin({
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: 4000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
      }
    });
    Toast.fire({
      icon: "error",
      title: "Cliente no seleccionado"
    });
  }
})

