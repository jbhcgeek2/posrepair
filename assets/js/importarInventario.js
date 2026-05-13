function subirInventario() {
  let archivo = document.getElementById("archivoInventario").files[0];

  if (!archivo) {
    alert("Selecciona un archivo");
    return;
  }

  let formData = new FormData();
  formData.append("archivoInventario", archivo);

  fetch("../includes/importarInventario.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);

      if (data.status === "ok") {
        Swal.fire({
          title: 'Importacion Completa',
          text: 'Se reaizo correctamente la importacion de tu inventario',
          icon: 'success'
        })
        document.getElementById("resultadoImportacion").innerHTML = `
          <div class="alert alert-success">
            ${data.mensaje}
          </div>`;
      } else {
        Swal.fire({
          title: 'Error',
          text: data.mensaje,
          icon: 'error'
        }).then(function(){
          document.getElementById("resultadoImportacion").innerHTML = `
          <div class="alert alert-danger">
            ${data.mensaje}
          </div>`;
        })
        
      }
    })
    .catch((error) => {
      console.error(error);

      document.getElementById("resultadoImportacion").innerHTML = `
        <div class="alert alert-danger">
          Error al subir archivo
      </div>
      `;
    });
}
