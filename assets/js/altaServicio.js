let precioSel = document.getElementById('precioFijo');

precioSel.addEventListener('change', function(){
  let datoFijo = a;
  //verificaremos si escojio un precio fijo o no, para
  //deshabilitar el campo de precio

  if(datoFijo == "1"){
    //dejamos habilitado el campo removeAttribute
    document.getElementById('precioServ').removeAttribute('disabled');

  }else{
    //lo deshabilitamos y seteamos a vacio
    document.getElementById('precioServ').value = "";
    document.getElementById('precioServ').setAttribute('disabled',true);
  }
})
