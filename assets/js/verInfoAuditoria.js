let inputEscaner = document.getElementById('escanear');
inputEscaner.addEventListener('change', function(){
  let codigo = inputEscaner.value.trim();
  console.log(codigo);
})


let btnTermina = document.getElementById('btnTermina');
btnTermina.addEventListener('click', function(){
  location.reload();
})