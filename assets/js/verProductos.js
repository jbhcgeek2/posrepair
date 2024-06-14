let buscarP = document.getElementById('buscarProducto');

buscarP.addEventListener('change',function(){
    // alert('aa');
});


buscarP.addEventListener('change',function(){
    let buscar = buscarP.value;
    // console.log(buscar);
    let datos = new FormData();
    datos.append('busProds',buscar);

    let envio = new XMLHttpRequest();
    envio.open('POST','../includes/verProducto.php',false);
    envio.send(datos); 

    if(envio.status == 200){
        let res = JSON.parse(envio.responseText);
        let contenido = "";
        if(res.status == "ok"){
            //cargamos los datos a la table
            for(let x = 0; x < res.data.length; x++){

            }//fin del for
        }else{
            //se ha producido un error
        }
        // alert(envio.responseText);
    }else{
        Swal.fire(
            'Servidor Inalcansable',
            'Verifica tu conexion a internet',
            'error'
        )
    }
}); 