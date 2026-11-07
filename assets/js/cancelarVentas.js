// Abre el modal y rellena los datos de la venta seleccionada
document.addEventListener('DOMContentLoaded', function () {
  const modalEl = document.getElementById('modalCancelar');
  const modal   = new bootstrap.Modal(modalEl);

  document.querySelectorAll('.btn-cancelar').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.getElementById('mVentaID').value        = this.dataset.id;
      document.getElementById('mComprobante').textContent = this.dataset.comprobante;
      document.getElementById('mTotal').textContent       = '$' + this.dataset.total;
      document.getElementById('mMotivo').value            = '';
      modal.show();
    });
  });
});

let btnCancela = document.getElementById('cancelar_venta');
btnCancela.addEventListener('click', function(){
  let datos = new FormData(document.getElementById('formCancelar'));

  fetch("../includes/cancelarVentas.php",{
    method: 'POST',
    body: datos
  }).then(function(res){
    return res.json();
  }).then(function(result){
    if(result.status == "ok"){
      Swal.fire({
        title: 'Venta Cancelada',
        text: 'Se cancelo correctamente la venta.',
        icon: 'success'
      }).then(function(){
        location.reload();
      })
    }else{
      Swal.fire({
        title: 'Ocurrio un error',
        text: result.mensaje,
        icon: 'error'
      })
    }
  })

});