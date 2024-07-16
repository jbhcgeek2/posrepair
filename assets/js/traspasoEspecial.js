let codigo = document.getElementById('codigoTraspaso');
codigo.addEventListener('change', function(){
  //metodo para registrar un traspaso directo
  if(codigo.value != "" || codigo.value != " "){
    let origen = document.getElementById('sucurOrigen').value;
    let destino = document.getElementById('sucurDestino').value;
  
    if((origen != destino) && origen != "" && destino != ""){
      //generamos el formdata
      let fecha = document.getElementById('fechaTraspaso').value;
      let codigoProd = codigo.value;
  
      let datos = new FormData();
      datos.append('sucOrigenDirecto',origen);
      datos.append('sucDestinoDirecto',destino);
      datos.append('fechaDirecto',fecha);
      datos.append('codigoDirecto',codigoProd);

      let envio = new XMLHttpRequest();
      envio.open('POST','../includes/modProducto.php',false);
      envio.send(datos);

      if(envio.status == 200){
        let res = JSON.parse(envio.responseText);
        console.log(res);
      }else{
        Swal.fire(
          'Servidor Inalcansable',
          'Verifica tu conexion a internet',
          'error'
        )
      }

      
      
    }else{
      Swal.fire(
        'Traspaso Invalido',
        'Asegurate de indicar las sucursales correctamente',
        'error'
      )
    }
  }else{
    //sin codigo para procesar
  }
  
});
