let btnUpdate = document.getElementById('btnUpdateProd');
 
btnUpdate.addEventListener('click', function(){
  let datos = new FormData(document.getElementById('dataProducto'));

  let envio = new XMLHttpRequest();
  envio.open("POST","../includes/modProducto.php",false);

  envio.send(datos);
  if(envio.status == 200){
    let res = JSON.parse(envio.responseText);
    if(res.status == "ok"){
      Swal.fire(
        'Producto Actualizado',
        'Se actualizo correctamente la informacion del producto',
        'success'
      ).then(function(){
        location.reload();
      })
    }else{
      //error al actualizar
      let err = "Verificar: "+res.mensaje;
      Swal.fire(
        'Ha ocurrido un error',
        err,
        'error'
      )
    }
    // console.log(envio.responseText);
  }else{
    //error de comunicacion
    Swal.fire(
      'Servidor inalcansable',
      'Verifica tu conexion a internet',
      'error'
    )
  }
})

function updateDirectCant(campo){
  let auxDato = campo.split('cantidadSuc');
  let idSucursal = auxDato[1];
  let cantidad = document.getElementById(campo).value;
  let articulo = document.getElementById('dataProd').value;
  //preguntamos si desea cambia la cantidad
  Swal.fire({
    title: 'Modificacion directa?',
    text: 'No se recommienda esta accion',
    icon: 'warning',
    showDenyButton: true,
    confirmButtonText: 'Si',
    denyButtonText: `No`
  }).then((result)=>{
    if(result.isConfirmed){
      let datos = new FormData();
      datos.append('idSucursalCantDirect',idSucursal);
      datos.append('cantidad',cantidad);
      datos.append('articuloUpdateDirect',articulo);

      let envio = new XMLHttpRequest();
      envio.open('POST','../includes/modProducto.php',false);
      envio.send(datos);
      if(envio.status == 200){
        let res = JSON.parse(envio.responseText);
        if(res.status == "ok"){
          Swal.fire(
            'Cantidad Actualizada',
            'Se actualizo correctamente la cantidad del producto',
            'success'
          ).then(function(){
            location.reload();
          })
        }else{
          Swal.fire(
            'Ocurrio un error',
            res.mensaje,
            'error'
          )
        }
      }else{
        //no se puede actualizar
        Swal.fire(
          'Error de comunicacion',
          'Verifica tu conexion a internet',
          'error'
        )
      }
    }
  })
}

function insertaChip(){
  let sucursalChip = document.getElementById('sucChip').value;
  let codigoChip = document.getElementById('codigoChip').value;
  let articulo = document.getElementById('dataProd').value;
  

  if(sucursalChip != "" && codigoChip != ""){

    let datos = new FormData();
    datos.append('sucursalChip',sucursalChip);
    datos.append('codigoChip',codigoChip);
    datos.append('articuloID',articulo);

    let envio = new XMLHttpRequest();
    envio.open('POST','../includes/modProducto.php',false);
    envio.send(datos);

    if(envio.status == 200){

      let res = JSON.parse(envio.responseText);
      if(res.status == "ok"){
        //se inserto el chip
        let chipInsert = "<p>"+codigoChip+"</p>";
        document.getElementById('resChips').insertAdjacentHTML('afterbegin',chipInsert);
        document.getElementById('codigoChip').value = "";
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
        'Verifica tu conexion a internet',
        'error'
      )
    }

    
  }else{
    Swal.fire(
      'Datos Faltantes',
      'Verifica la informacion capturada',
      'error'
    )
  }
}