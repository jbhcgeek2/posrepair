document.getElementById('reloadButton').addEventListener('click', function () {
  // Agregar un parámetro temporal único a la URL para forzar la recarga
  const currentUrl = window.location.href.split('?')[0]; // Obtener la URL base sin parámetros
  const uniqueParam = `cachebuster=${new Date().getTime()}`; // Generar un parámetro único basado en el tiempo actual
  window.location.href = `${currentUrl}?${uniqueParam}`;
});