let buscarP = document.getElementById('buscarProducto');

buscarP.addEventListener('change',function(){
    // alert('aa');
});


buscarP.addEventListener('keyup',function(){
    let buscar = buscarP.value;
    // console.log(buscar);
    let datos = new FormData();
    datos.append('busProds',buscar);

    let envio = new XMLHttpRequest();
    envio.open('POST','../includes/verProducto.php',false);
    envio.send(datos); 

    if(envio.status == 200){
        alert(envio.responseText);
    }else{
        Swal.fire(
            'Servidor Inalcansable',
            'Verifica tu conexion a internet',
            'error'
        )
    }
}); 