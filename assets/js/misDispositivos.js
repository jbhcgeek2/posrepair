
function setAccess(dispo){
  //funcion para dar acceso a un dispositivo
  Swal.fire({
    title: 'Otorgar Acceso',
    text: 'Estas seguro de dar acceso al dispositivo seleccionado?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Dar Acceso',
    cancelButtonText: 'Cancelar'
  }).then((res)=>{
    if(res.isConfirmed){
      let datos = new FormData();
      datos.append('setAccess',dispo);
      fetch("../includes/operacionesDispo.php",{
        method: 'POST',
        body: datos
      }).then(function(res){
        return res.json();
      }).then(function(result){
        if(result.status == 'ok'){
          Swal.fire({
            title: 'Dispositivo Autorizado',
            text: 'Se otorgo acceso al dispositivo',
            icon: 'success'
          }).then(function(){
            location.reload();
          })
        }else{
          Swal.fire({
            title: 'Ha ocurrido un error',
            text: result.mensaje,
            icon: 'error'
          })
        }
      })
    }
  });
}

function delAccess(dispo){
  //funcion para eliminar el acceso a un dispositivo
  Swal.fire({
    title: 'Eliminar Acceso',
    text: 'Estas seguro de eliminar el acceso a este dispositivo?',
    icon: 'info',
    showCancelButton: true,
    confirmButtonText: 'Eliminar',
    cancelButtonText: 'Cancelar'
  }).then((res)=>{
    if(res.isConfirmed){
      let datos = new FormData();
      datos.append("delDispo",dispo);

      fetch("../includes/operacionesDispo.php",{
        method: 'POST',
        body: datos
      }).then(function(res){
        return res.json();
      }).then(function(result){
        if(result.status == "ok"){
          Swal.fire({
            title: 'Acceso Eliminado',
            text: 'Se elimino el acceso al dispositivo',
            icon: 'success'
          }).then(function(){
            location.reload();
          })
        }else{
          Swal.fire({
            title: 'Ocurrio un error',
            text: result.mensaje,
            icon: 'error'
          });
        }
      });
    }
  });
}