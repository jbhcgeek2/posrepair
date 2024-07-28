let inputEscaner = document.getElementById('escanear');
inputEscaner.addEventListener('change', function(){
  let codigo = inputEscaner.value.trim();
  let sucur = document.getElementById('sucurArti').value;
  let idAudi = document.getElementById('auditoriaData').value;

  if(sucur != "" && codigo != "" && idAudi != ""){
    //enviamos el codigo al procesados
    let datos = new FormData();
    datos.append('codigoEscaneo',codigo);
    datos.append('sucurCodigo',sucur);
    datos.append('idAudiCod',idAudi);

    let envio = new XMLHttpRequest();
    envio.open('POST','../includes/operacionesAuditoria.php',false);
    envio.send(datos);

    if(envio.status == 200){
      let res = JSON.parse(envio.responseText);
      if(res.status == "ok"){

      }else{
        Swal.fire(
          'Ha ocurrido un error',
          res.mensaje,
          'error'
        )
      }
    }else{
      //erroe de conexion
      Swal.fire(
        'Servidor Inalcansable',
        'Verifica tu conexion a internet',
        'error'
      )
    }
  }else{
    //sucursal codigo no definidos
  }
  

})


let btnTermina = document.getElementById('btnTermina');
btnTermina.addEventListener('click', function(){
  location.reload();
})